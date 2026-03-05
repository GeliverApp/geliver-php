<?php

use Geliver\Client;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;

final class TransactionsTest extends TestCase
{
    public function testCreateWrapsShipment(): void
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'result' => true,
                'data' => ['id' => 'tx1', 'offerID' => 'offer-123', 'isPayed' => true],
            ])),
        ]);

        $historyContainer = [];
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($historyContainer));
        $http = new GuzzleClient(['handler' => $handlerStack, 'base_uri' => Client::DEFAULT_BASE_URL]);
        $client = new Client('test', Client::DEFAULT_BASE_URL, $http);

        $client->transactions()->create([
            'senderAddressID' => 'sender-1',
            'recipientAddress' => ['name' => 'R', 'phone' => '+905000000000'],
            'length' => 10.5,
            'weight' => 1.25,
            'distanceUnit' => 'cm',
            'massUnit' => 'kg',
            'test' => true,
            'providerServiceCode' => 'SURAT_STANDART',
            'providerAccountID' => 'acc-1',
            'order' => ['orderNumber' => 'ORDER-1'],
        ]);

        $this->assertCount(1, $historyContainer);
        $req = $historyContainer[0]['request'];
        $body = json_decode((string) $req->getBody(), true);

        $this->assertArrayHasKey('shipment', $body);
        $this->assertArrayNotHasKey('test', $body);

        $shipment = $body['shipment'];
        $this->assertTrue($shipment['test']);
        $this->assertSame('SURAT_STANDART', $body['providerServiceCode'] ?? null);
        $this->assertSame('acc-1', $body['providerAccountID'] ?? null);
        $this->assertArrayNotHasKey('providerServiceCode', $shipment);
        $this->assertArrayNotHasKey('providerAccountID', $shipment);
        $this->assertSame('SDK', $shipment['order']['sourceCode']);
        $this->assertIsString($shipment['length']);
        $this->assertIsString($shipment['weight']);
    }
}
