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

        $client->shipments()->createReturn('shp-1', [
            'providerServiceCode' => 'SURAT_STANDART',
            // willAccept intentionally omitted (defaults to false on backend)
        ]);

        $this->assertCount(1, $historyContainer);
        $req = $historyContainer[0]['request'];

        $this->assertSame('POST', $req->getMethod());
        $this->assertStringEndsWith('/shipments/shp-1', $req->getUri()->getPath());
        $this->assertSame('geliver-php/1.2.1', $req->getHeaderLine('User-Agent'));

        $body = json_decode((string) $req->getBody(), true);
        $this->assertTrue($body['isReturn']);
        $this->assertSame(1, $body['count']);
        $this->assertSame('SURAT_STANDART', $body['providerServiceCode']);
        $this->assertArrayNotHasKey('willAccept', $body);
    }
}
