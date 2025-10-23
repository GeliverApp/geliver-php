<?php

namespace Geliver\Resources;

use Geliver\Client;

class Webhooks
{
    public function __construct(private Client $client) {}
    public function create(string $url, ?string $type = null): array
    {
        $body = ['url' => $url]; if ($type) $body['type'] = $type;
        return $this->client->request('POST', '/webhook', ['json' => $body]);
    }
    public function list(): array { return $this->client->request('GET', '/webhook'); }
    public function delete(string $webhookId): array { return $this->client->request('DELETE', '/webhook/' . rawurlencode($webhookId)); }
    public function test(string $type, string $url): array { return $this->client->request('PUT', '/webhook', ['json' => ['type' => $type, 'url' => $url]]); }
}

