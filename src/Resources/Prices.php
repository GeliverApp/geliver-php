<?php

namespace Geliver\Resources;

use Geliver\Client;

class Prices
{
    public function __construct(private Client $client) {}
    public function listPrices(array $params): array { return $this->client->request('GET', '/priceList', ['query' => $params]); }
}

