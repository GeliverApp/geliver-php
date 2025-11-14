<?php

namespace Geliver\Resources;

use Geliver\Client;

class Transactions
{
    public function __construct(private Client $client) {}

    public function acceptOffer(string $offerId): array
    {
        return $this->client->request('POST', '/transactions', ['json' => ['offerID' => $offerId]]);
    }

    /**
     * One-step label purchase: post shipment details directly to /transactions.
     */
    public function create(array $body): array
    {
        return $this->client->request('POST', '/transactions', ['json' => $body]);
    }
}
