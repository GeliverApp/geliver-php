<?php

use Geliver\Client;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
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
}

