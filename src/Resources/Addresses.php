<?php

namespace Geliver\Resources;

use Geliver\Client;

class Addresses
{
    public function __construct(private Client $client) {}

    public function create(array $body): array { return $this->client->request('POST', '/addresses', ['json' => $body]); }
    public function createSender(array $body): array {
        $b = $body;
        if (!isset($b['zip']) || !$b['zip']) {
            throw new \InvalidArgumentException('zip is required for sender addresses');
        }
        $b['isRecipientAddress'] = false;
        return $this->create($b);
    }
    public function createRecipient(array $body): array {
        $b = $body;
        if (!isset($b['phone']) || !$b['phone']) {
            throw new \InvalidArgumentException('phone is required for recipient addresses');
        }
        $b['isRecipientAddress'] = true;
        return $this->create($b);
    }
    public function list(array $params = []): array { return $this->client->request('GET', '/addresses', ['query' => $params]); }
    public function get(string $addressId): array { return $this->client->request('GET', '/addresses/' . rawurlencode($addressId)); }
    public function delete(string $addressId): array { return $this->client->request('DELETE', '/addresses/' . rawurlencode($addressId)); }
}
