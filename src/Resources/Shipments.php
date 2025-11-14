<?php

namespace Geliver\Resources;

use Geliver\Client;

class Shipments
{
    /** @var Client */
    public function __construct(private Client $client) {}

    /** Create shipment; recipient can be provided inline or via recipientAddressID. @return array Shipment */
    public function create(array $body): array {
        if (isset($body['order']) && is_array($body['order'])) {
            if (!isset($body['order']['sourceCode']) || !$body['order']['sourceCode']) {
                $body['order']['sourceCode'] = 'API';
            }
        }
        if (isset($body['recipientAddress']) && is_array($body['recipientAddress'])) {
            if (!isset($body['recipientAddress']['phone']) || !$body['recipientAddress']['phone']) {
                throw new \InvalidArgumentException('recipientAddress.phone is required');
            }
        }
        foreach (['length','width','height','weight'] as $k) {
            if (array_key_exists($k, $body) && $body[$k] !== null) {
                $body[$k] = (string)$body[$k];
            }
        }
        return $this->client->request('POST', '/shipments', ['json' => $body]);
    }

    /** Get shipment by ID. @return array Shipment */
    public function get(string $shipmentId): array { return $this->client->request('GET', '/shipments/' . rawurlencode($shipmentId)); }

    /** List shipments. @return array Envelope with pagination and data list */
    public function list(array $params = []): array { return $this->client->request('GET', '/shipments', ['query' => $params]); }

    /** Update package dimensions/weight for not-yet-purchased shipment. @return array Shipment */
    public function updatePackage(string $shipmentId, array $body): array {
        foreach (['length','width','height','weight'] as $k) {
            if (array_key_exists($k, $body) && $body[$k] !== null) {
                $body[$k] = (string)$body[$k];
            }
        }
        return $this->client->request('PATCH', '/shipments/' . rawurlencode($shipmentId), ['json' => $body]);
    }

    /** Cancel shipment. @return array Shipment */
    public function cancel(string $shipmentId): array { return $this->client->request('DELETE', '/shipments/' . rawurlencode($shipmentId)); }

    /** Clone shipment. @return array Shipment */
    public function clone(string $shipmentId): array { return $this->client->request('POST', '/shipments/' . rawurlencode($shipmentId)); }

    /** Poll until offers are complete or timeout. @return array offers payload */
    public function waitOffers(string $shipmentId, int $intervalSeconds = 1, int $timeoutSeconds = 60): array
    {
        $start = time();
        while (true) {
            $s = $this->get($shipmentId);
            $offers = $s['offers'] ?? null;
            $pc = (int)($offers['percentageCompleted'] ?? 0);
            if ($offers && ($pc == 100 || isset($offers['cheapest']))) return $offers;
            if (time() - $start > $timeoutSeconds) throw new \RuntimeException('Timed out waiting for offers');
            sleep($intervalSeconds);
        }
    }

    /** Poll until tracking number available or timeout. @return array Shipment */
    public function waitTrackingNumber(string $shipmentId, int $intervalSeconds = 3, int $timeoutSeconds = 180): array
    {
        $start = time();
        while (true) {
            $s = $this->get($shipmentId);
            if (!empty($s['trackingNumber'])) return $s;
            if (time() - $start > $timeoutSeconds) throw new \RuntimeException('Timed out waiting for tracking number');
            sleep($intervalSeconds);
        }
    }

    /** Create return shipment (PATCH with isReturn=true). @return array Shipment */
    public function createReturn(string $shipmentId, array $body): array
    {
        $payload = array_merge($body, ['isReturn' => true]);
        return $this->client->request('PATCH', '/shipments/' . rawurlencode($shipmentId), ['json' => $payload]);
    }

    /** Convenience method to create test shipments without changing the body. */
    public function createTest(array $body): array
    {
        $body['test'] = true;
        return $this->create($body);
    }

    /** Download PDF label by URL. @return string binary content */
    public function downloadLabelByUrl(string $url): string
    {
        $res = (new \GuzzleHttp\Client())->request('GET', $url);
        if ($res->getStatusCode() >= 400) throw new \RuntimeException('download failed');
        return (string) $res->getBody();
    }

    /** Download PDF label for shipment. @return string binary content */
    public function downloadLabel(string $shipmentId): string
    {
        $s = $this->get($shipmentId);
        $url = $s['labelURL'] ?? null;
        if (!$url) throw new \RuntimeException('No labelURL');
        return $this->downloadLabelByUrl($url);
    }

    /** Download responsive label HTML by URL. */
    public function downloadResponsiveLabelByUrl(string $url): string
    {
        return $this->downloadLabelByUrl($url);
    }

    /** Download responsive label HTML for shipment. */
    public function downloadResponsiveLabel(string $shipmentId): string
    {
        $s = $this->get($shipmentId);
        $url = $s['responsiveLabelURL'] ?? ($s['responsiveLabelUrl'] ?? null);
        if (!$url) throw new \RuntimeException('No responsiveLabelURL');
        return $this->downloadResponsiveLabelByUrl($url);
    }
}
