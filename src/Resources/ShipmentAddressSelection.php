<?php

namespace Geliver\Resources;

final class ShipmentAddressSelection
{
    public static function validate(array $body): void
    {
        foreach (['sender', 'recipient'] as $side) {
            $id = $body[$side . 'AddressID'] ?? null;
            $hasID = is_string($id) && trim($id) !== '';
            $hasAddress = is_array($body[$side . 'Address'] ?? null);
            if (($side === 'recipient' && $hasID === $hasAddress) || ($side === 'sender' && $hasID && $hasAddress)) {
                throw new \InvalidArgumentException("Provide exactly one of {$side}AddressID or {$side}Address");
            }
        }
    }
}
