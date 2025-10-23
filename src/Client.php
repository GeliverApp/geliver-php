<?php

namespace Geliver;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;

class Client
{
    public const DEFAULT_BASE_URL = 'https://api.geliver.io/api/v1';

    private string $baseUrl;
    private string $token;
    private GuzzleClient $http;
    private int $maxRetries = 2;

    public function __construct(string $token, ?string $baseUrl = null, ?GuzzleClient $http = null)
    {
        $this->baseUrl = rtrim($baseUrl ?? self::DEFAULT_BASE_URL, '/');
        $this->token = $token;
        $this->http = $http ?? new GuzzleClient([
            'base_uri' => $this->baseUrl,
            'timeout' => 30.0,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    // No global test mode; set per shipment via body ['test' => true]

    public function shipments(): Resources\Shipments
    {
        return new Resources\Shipments($this);
    }

    public function transactions(): Resources\Transactions
    {
        return new Resources\Transactions($this);
    }

    public function addresses(): Resources\Addresses
    {
        return new Resources\Addresses($this);
    }

    public function webhooks(): Resources\Webhooks
    {
        return new Resources\Webhooks($this);
    }

    public function parcelTemplates(): Resources\ParcelTemplates
    {
        return new Resources\ParcelTemplates($this);
    }

    public function providers(): Resources\Providers
    {
        return new Resources\Providers($this);
    }

    public function prices(): Resources\Prices
    {
        return new Resources\Prices($this);
    }

    public function geo(): Resources\Geo
    {
        return new Resources\Geo($this);
    }

    public function organizations(): Resources\Organizations
    {
        return new Resources\Organizations($this);
    }

    public function request(string $method, string $path, array $options = [])
    {
        $attempt = 0;
        $uri = $this->baseUrl . $path;
        if (isset($options['query'])) {
            $qs = http_build_query($options['query']);
            if ($qs) { $uri .= '?' . $qs; }
            unset($options['query']);
        }
        if (isset($options['json'])) {
            $options['body'] = json_encode($options['json']);
            unset($options['json']);
        }
        while (true) {
            try {
                $res = $this->http->request($method, $uri, $options);
                $body = (string) $res->getBody();
                $json = json_decode($body, true);
                if (is_array($json) && array_key_exists('data', $json)) {
                    return $json['data'];
                }
                return $json ?? $body;
            } catch (RequestException $e) {
                $res = $e->getResponse();
                $status = $res ? $res->getStatusCode() : 0;
                if ($this->shouldRetry($status) && $attempt < $this->maxRetries) {
                    $attempt++;
                    usleep((int) min(2000000, 200000 * (2 ** ($attempt - 1))));
                    continue;
                }
                throw $e;
            }
        }
    }

    private function shouldRetry(int $status): bool
    {
        return $status === 429 || $status >= 500;
    }
}
