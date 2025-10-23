<?php

namespace Geliver\Resources;

use Geliver\Client;

class Organizations
{
    public function __construct(private Client $client) {}
    public function getBalance(string $organizationId): array { return $this->client->request('GET', '/organizations/' . rawurlencode($organizationId) . '/balance'); }
}

