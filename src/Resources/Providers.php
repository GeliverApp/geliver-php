<?php

namespace Geliver\Resources;

use Geliver\Client;

class Providers
{
    public function __construct(private Client $client) {}
    public function listAccounts(): array { return $this->client->request('GET', '/provideraccounts'); }
    public function createAccount(array $body): array { return $this->client->request('POST', '/provideraccounts', ['json' => $body]); }
    public function deleteAccount(string $providerAccountId, bool $isDeleteAccountConnection = null): array
    {
        $query = $isDeleteAccountConnection === null ? [] : ['isDeleteAccountConnection' => $isDeleteAccountConnection ? 'true' : 'false'];
        return $this->client->request('DELETE', '/provideraccounts/' . rawurlencode($providerAccountId), ['query' => $query]);
    }
}

