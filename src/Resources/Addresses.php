<?php

namespace Geliver\Resources;

use Geliver\Client;

class Addresses
{
    public function __construct(private Client $client) {}

    public function create(array $body): array { return $this->client->request('POST', '/addresses', ['json' => $body]); }
    public function createSender(array $body): array { $b = $body; $b['isRecipientAddress'] = false; return $this->create($b); }
    public function createRecipient(array $body): array { $b = $body; $b['isRecipientAddress'] = true; return $this->create($b); }
    public function list(array $params = []): array { return $this->client->request('GET', '/addresses', ['query' => $params]); }
    public function get(string $addressId): array { return $this->client->request('GET', '/addresses/' . rawurlencode($addressId)); }
    public function delete(string $addressId): array { return $this->client->request('DELETE', '/addresses/' . rawurlencode($addressId)); }
}
