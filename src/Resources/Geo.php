<?php

namespace Geliver\Resources;

use Geliver\Client;

class Geo
{
    public function __construct(private Client $client) {}
    public function listCities(string $countryCode): array { return $this->client->request('GET', '/cities', ['query' => ['countryCode' => $countryCode]]); }
    public function listDistricts(string $countryCode, string $cityCode): array { return $this->client->request('GET', '/districts', ['query' => ['countryCode' => $countryCode, 'cityCode' => $cityCode]]); }
}

