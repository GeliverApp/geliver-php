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
        $shipment = (isset($body['shipment']) && is_array($body['shipment'])) ? $body['shipment'] : $body;
        if (isset($shipment['order']) && is_array($shipment['order'])) {
            if (!isset($shipment['order']['sourceCode']) || !$shipment['order']['sourceCode']) {
                $shipment['order']['sourceCode'] = 'API';
            }
        }
        if (isset($shipment['recipientAddress']) && is_array($shipment['recipientAddress'])) {
            if (!isset($shipment['recipientAddress']['phone']) || !$shipment['recipientAddress']['phone']) {
                throw new \InvalidArgumentException('recipientAddress.phone is required');
            }
        }
        foreach (['length','width','height','weight'] as $k) {
            if (array_key_exists($k, $shipment) && $shipment[$k] !== null) {
                $shipment[$k] = (string)$shipment[$k];
            }
        }

        $providerAccountID = $body['providerAccountID'] ?? ($shipment['providerAccountID'] ?? null);
        if ($providerAccountID !== null) unset($shipment['providerAccountID']);

        $providerServiceCode = $body['providerServiceCode'] ?? ($shipment['providerServiceCode'] ?? null);
        if ($providerServiceCode !== null) unset($shipment['providerServiceCode']);

        $wrapper = ['shipment' => $shipment];
        if ($providerAccountID !== null) {
            $wrapper['providerAccountID'] = $providerAccountID;
        }
        if ($providerServiceCode !== null) {
            $wrapper['providerServiceCode'] = $providerServiceCode;
        }
        return $this->client->request('POST', '/transactions', ['json' => $wrapper]);
    }
}
