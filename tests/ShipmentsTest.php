<?php

use Geliver\Client;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;

final class ShipmentsTest extends TestCase
{
    public function testNewShipmentFlowsRequireRecipientAndAllowDefaultSender(): void
    {
        $client = new Client('test');
        $invalid = [
            ['senderAddressID' => 'merchant-1', 'senderAddress' => ['address1' => 'A'], 'recipientAddressID' => 'customer-1'],
            ['senderAddressID' => 'merchant-1'],
            ['senderAddressID' => 'merchant-1', 'recipientAddressID' => 'customer-1', 'recipientAddress' => ['address1' => 'B']],
        ];

        foreach ($invalid as $body) {
            foreach ([
                fn () => $client->shipments()->create($body),
                fn () => $client->transactions()->create(['shipment' => $body]),
            ] as $create) {
                try {
                    $create();
                    $this->fail('Invalid address selection was accepted');
                } catch (\InvalidArgumentException $exception) {
                    $this->assertStringContainsString('exactly one', $exception->getMessage());
                }
            }
        }
    }

    public function testIncomingShipmentAddressCombinations(): void
    {
        $response = json_encode(['result' => true, 'data' => ['id' => 'incoming-1']]);
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], $response),
            new Response(200, ['Content-Type' => 'application/json'], $response),
            new Response(200, ['Content-Type' => 'application/json'], $response),
            new Response(200, ['Content-Type' => 'application/json'], $response),
            new Response(200, ['Content-Type' => 'application/json'], $response),
        ]);
        $history = [];
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($history));
        $client = new Client('test', Client::DEFAULT_BASE_URL, new GuzzleClient([
            'handler' => $stack, 'base_uri' => Client::DEFAULT_BASE_URL,
        ]));

        $merchant = ['name' => 'Merchant', 'phone' => '+905051234567', 'address1' => 'A'];
        $customer = ['name' => 'Customer', 'phone' => '+905051234568', 'address1' => 'B'];
        $cases = [
            ['recipientAddress' => $customer],
            ['senderAddressID' => 'merchant-1', 'recipientAddressID' => 'customer-1'],
            ['senderAddressID' => 'merchant-1', 'recipientAddress' => $customer],
            ['senderAddress' => $merchant, 'recipientAddressID' => 'customer-1'],
            ['senderAddress' => $merchant, 'recipientAddress' => $customer],
        ];

        foreach ($cases as $index => $addresses) {
            $client->shipments()->create(['isReturn' => true] + $addresses);
            $body = json_decode((string) $history[$index]['request']->getBody(), true);
            $this->assertTrue($body['isReturn']);
            foreach (['senderAddressID', 'senderAddress', 'recipientAddressID', 'recipientAddress'] as $key) {
                $this->assertSame(array_key_exists($key, $addresses), array_key_exists($key, $body));
            }
        }
    }

    public function testCreateIncomingShipmentKeepsAddressDirection(): void
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'result' => true, 'data' => ['id' => 'incoming-1'],
            ])),
        ]);
        $history = [];
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($history));
        $client = new Client('test', Client::DEFAULT_BASE_URL, new GuzzleClient([
            'handler' => $stack, 'base_uri' => Client::DEFAULT_BASE_URL,
        ]));

        $shipment = $client->shipments()->create([
            'isReturn' => true,
            'recipientAddress' => ['name' => 'Customer', 'phone' => '+905051234567', 'address1' => 'A'],
        ]);

        $this->assertSame('incoming-1', $shipment['data']['id']);
        $this->assertSame('POST', $history[0]['request']->getMethod());
        $this->assertStringEndsWith('/shipments', $history[0]['request']->getUri()->getPath());
        $body = json_decode((string) $history[0]['request']->getBody(), true);
        $this->assertTrue($body['isReturn']);
        $this->assertSame('Customer', $body['recipientAddress']['name']);
        $this->assertArrayNotHasKey('senderAddress', $body);
        $this->assertArrayNotHasKey('senderAddressID', $body);
    }

    public function testListShipments(): void
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'result' => true,
                'limit' => 2,
                'page' => 1,
                'totalRows' => 2,
                'totalPages' => 1,
                'data' => [['id' => 's1'], ['id' => 's2']],
            ])),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $http = new GuzzleClient(['handler' => $handlerStack, 'base_uri' => Client::DEFAULT_BASE_URL]);
        $client = new Client('test', Client::DEFAULT_BASE_URL, $http);
        $resp = $client->shipments()->list(['limit' => 2]);
        $this->assertCount(2, $resp['data']);
    }

    public function testCreateReturn_UsesPostAndDefaults(): void
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'result' => true,
                'data' => ['id' => 'ret-1'],
            ])),
        ]);

        $historyContainer = [];
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($historyContainer));
        $http = new GuzzleClient(['handler' => $handlerStack, 'base_uri' => Client::DEFAULT_BASE_URL]);
        $client = new Client('test', Client::DEFAULT_BASE_URL, $http);

        $returned = $client->shipments()->createReturn('shp-1', [
            'providerServiceCode' => 'SURAT_STANDART',
            // willAccept intentionally omitted (defaults to false on backend)
        ]);

        $this->assertCount(1, $historyContainer);
        $req = $historyContainer[0]['request'];

        $this->assertSame('POST', $req->getMethod());
        $this->assertStringEndsWith('/shipments/shp-1', $req->getUri()->getPath());
        $this->assertSame('geliver-php/' . Client::VERSION, $req->getHeaderLine('User-Agent'));

        $body = json_decode((string) $req->getBody(), true);
        $this->assertTrue($body['isReturn']);
        $this->assertSame(1, $body['count']);
        $this->assertSame('SURAT_STANDART', $body['providerServiceCode']);
        $this->assertArrayNotHasKey('willAccept', $body);
        $this->assertSame('ret-1', $returned['id']);
    }

    public function testCreateReturn_WithWillAccept_Throws(): void
    {
        $client = new Client('test', Client::DEFAULT_BASE_URL, new GuzzleClient(['base_uri' => Client::DEFAULT_BASE_URL]));
        $this->expectException(\InvalidArgumentException::class);
        $client->shipments()->createReturn('shp-1', [
            'willAccept' => true,
        ]);
    }

    public function testTransactionsCreateReturn_ForcesWillAccept(): void
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'result' => true,
                'data' => ['id' => 'tx-1', 'offerID' => 'offer-1', 'transactionType' => 'CREATE_SHIPMENT'],
            ])),
        ]);

        $historyContainer = [];
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($historyContainer));
        $http = new GuzzleClient(['handler' => $handlerStack, 'base_uri' => Client::DEFAULT_BASE_URL]);
        $client = new Client('test', Client::DEFAULT_BASE_URL, $http);

        $returned = $client->transactions()->createReturn('shp-1', [
            'providerServiceCode' => 'SURAT_STANDART',
        ]);

        $this->assertCount(1, $historyContainer);
        $req = $historyContainer[0]['request'];
        $this->assertSame('POST', $req->getMethod());
        $this->assertStringEndsWith('/shipments/shp-1', $req->getUri()->getPath());

        $body = json_decode((string) $req->getBody(), true);
        $this->assertTrue($body['isReturn']);
        $this->assertTrue($body['willAccept']);
        $this->assertSame(1, $body['count']);
        $this->assertSame('SURAT_STANDART', $body['providerServiceCode']);

        $this->assertSame('tx-1', $returned['id']);
    }
}
