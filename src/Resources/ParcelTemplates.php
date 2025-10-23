<?php

namespace Geliver\Resources;

use Geliver\Client;

class ParcelTemplates
{
    public function __construct(private Client $client) {}
    public function create(array $body): array { return $this->client->request('POST', '/parceltemplates', ['json' => $body]); }
    public function list(): array { return $this->client->request('GET', '/parceltemplates'); }
    public function delete(string $templateId): array { return $this->client->request('DELETE', '/parceltemplates/' . rawurlencode($templateId)); }
}

