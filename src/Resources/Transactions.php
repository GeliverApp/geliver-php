<?php

namespace Geliver\Resources;

use Geliver\Client;

class Transactions
{
    public function __construct(private Client $client) {}

    public function acceptOffer(string $offerId): array
    {
        $response = $this->client->request('POST', '/transactions', ['json' => ['offerID' => $offerId]]);
        return (is_array($response) && array_key_exists('data', $response)) ? $response['data'] : $response;
    }

    /** Create a return shipment and purchase the label immediately. Returns Transaction payload. */
    public function createReturn(string $shipmentId, array $body): array
    {
        $payload = array_merge($body, [
            'isReturn' => true,
            'willAccept' => true,
        ]);
        if (!array_key_exists('count', $payload) || $payload['count'] === null || (is_numeric($payload['count']) && (int)$payload['count'] <= 0)) {
            $payload['count'] = 1;
        }
        $response = $this->client->request('POST', '/shipments/' . rawurlencode($shipmentId), ['json' => $payload]);
        return (is_array($response) && array_key_exists('data', $response)) ? $response['data'] : $response;
    }

    /**
     * One-step label purchase: post shipment details directly to /transactions.
     */
    public function create(array $body): array
    {
        $shipment = (isset($body['shipment']) && is_array($body['shipment'])) ? $body['shipment'] : $body;
        if (isset($shipment['order']) && is_array($shipment['order'])) {
            if (!isset($shipment['order']['sourceCode']) || !$shipment['order']['sourceCode']) {
                $shipment['order']['sourceCode'] = 'SDK';
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
        $response = $this->client->request('POST', '/transactions', ['json' => $wrapper]);
        return (is_array($response) && array_key_exists('data', $response)) ? $response['data'] : $response;
    }
}
