<?php

namespace Geliver;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;

class ApiException extends \RuntimeException
{
    public ?string $codeStr;
    public ?string $additionalMessage;
    public int $status;
    public $body;
    public function __construct(string $message, int $status = 0, ?string $codeStr = null, ?string $additionalMessage = null, $body = null)
    {
        parent::__construct($message, $status);
        $this->status = $status;
        $this->codeStr = $codeStr;
        $this->additionalMessage = $additionalMessage;
        $this->body = $body;
    }
}

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
                if ($res->getStatusCode() >= 400) {
                    $code = is_array($json) && isset($json['code']) ? $json['code'] : null;
                    $msg = is_array($json) && isset($json['message']) ? $json['message'] : ("HTTP " . $res->getStatusCode());
                    $addl = is_array($json) && isset($json['additionalMessage']) ? $json['additionalMessage'] : null;
                    throw new ApiException($msg, $res->getStatusCode(), $code, $addl, $json ?? $body);
                }
                if (is_array($json)) {
                    if (array_key_exists('result', $json) && $json['result'] === false) {
                        $code = $json['code'] ?? null;
                        $msg = $json['message'] ?? 'API error';
                        $addl = $json['additionalMessage'] ?? null;
                        throw new ApiException($msg, $res->getStatusCode(), $code, $addl, $json);
                    }
                    // Preserve full envelope when present (result/message/additionalMessage or pagination keys)
                    $hasEnvelope = array_key_exists('result', $json) || array_key_exists('message', $json) || array_key_exists('additionalMessage', $json)
                        || array_key_exists('limit', $json) || array_key_exists('page', $json) || array_key_exists('totalRows', $json) || array_key_exists('totalPages', $json);
                    if ($hasEnvelope) return $json;
                    if (array_key_exists('data', $json)) return $json['data'];
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
                // Try to surface API error details if available
                $code = null; $msg = $e->getMessage(); $addl = null; $payload = null;
                if ($res) {
                    $text = (string) $res->getBody();
                    $payload = json_decode($text, true);
                    if (is_array($payload)) {
                        $code = $payload['code'] ?? null;
                        $msg = $payload['message'] ?? $msg;
                        $addl = $payload['additionalMessage'] ?? null;
                    }
                }
                throw new ApiException($msg, $status, $code, $addl, $payload);
            }
        }
    }

    private function shouldRetry(int $status): bool
    {
        return $status === 429 || $status >= 500;
    }
}
