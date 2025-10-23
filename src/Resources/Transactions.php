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
}

