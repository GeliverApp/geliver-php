<?php

namespace Geliver\Models;

class CreateAddressRequest
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone,
        public string $address1,
        public ?string $address2,
        public string $countryCode,
        public string $cityName,
        public string $cityCode,
        public string $districtName,
        public int $districtID,
        public string $zip,
        public ?string $shortName = null,
        public ?bool $isRecipientAddress = null,
    ) {}
}

class CreateShipmentRequestBase
{
    public function __construct(
        public string $sourceCode,
        public string $senderAddressID,
        public ?float $length = null,
        public ?float $width = null,
        public ?float $height = null,
        public ?string $distanceUnit = null,
        public ?float $weight = null,
        public ?string $massUnit = null,
        public ?string $providerServiceCode = null,
    ) {}
}

class CreateShipmentWithRecipientID extends CreateShipmentRequestBase
{
    public function __construct(
        string $sourceCode,
        string $senderAddressID,
        public string $recipientAddressID,
        ?float $length = null,
        ?float $width = null,
        ?float $height = null,
        ?string $distanceUnit = null,
        ?float $weight = null,
        ?string $massUnit = null,
        ?string $providerServiceCode = null,
    ) { parent::__construct($sourceCode, $senderAddressID, $length, $width, $height, $distanceUnit, $weight, $massUnit, $providerServiceCode); }
}

class CreateShipmentWithRecipientAddress extends CreateShipmentRequestBase
{
    public function __construct(
        string $sourceCode,
        string $senderAddressID,
        public array $recipientAddress,
        ?float $length = null,
        ?float $width = null,
        ?float $height = null,
        ?string $distanceUnit = null,
        ?float $weight = null,
        ?string $massUnit = null,
        ?string $providerServiceCode = null,
    ) { parent::__construct($sourceCode, $senderAddressID, $length, $width, $height, $distanceUnit, $weight, $massUnit, $providerServiceCode); }
}

class UpdatePackageRequest
{
    public function __construct(
        public ?float $height = null,
        public ?float $width = null,
        public ?float $length = null,
        public ?string $distanceUnit = null,
        public ?float $weight = null,
        public ?string $massUnit = null,
    ) {}
}

