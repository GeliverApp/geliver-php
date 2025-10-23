<?php

namespace Geliver;

final class Webhooks
{
    /**
     * Future signature verification. Disabled by default; returns true when disabled.
     */
    public static function verify(string $body, array $headers, bool $enableVerification = false, ?string $secret = null): bool
    {
        if (!$enableVerification) { return true; }
        $sig = $headers['X-Signature'] ?? ($headers['x-signature'] ?? null);
        $ts  = $headers['X-Timestamp'] ?? ($headers['x-timestamp'] ?? null);
        if (!$sig || !$ts || !$secret) { return false; }
        // Future: compute HMAC(secret, "$ts." . $body) and compare
        return false;
    }
}

