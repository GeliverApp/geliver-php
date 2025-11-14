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
        public ?int $districtID = null,
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
        public ?string $length = null,
        public ?string $width = null,
        public ?string $height = null,
        public ?string $distanceUnit = null,
        public ?string $weight = null,
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
        ?string $length = null,
        ?string $width = null,
        ?string $height = null,
        ?string $distanceUnit = null,
        ?string $weight = null,
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
        ?string $length = null,
        ?string $width = null,
        ?string $height = null,
        ?string $distanceUnit = null,
        ?string $weight = null,
        ?string $massUnit = null,
        ?string $providerServiceCode = null,
    ) { parent::__construct($sourceCode, $senderAddressID, $length, $width, $height, $distanceUnit, $weight, $massUnit, $providerServiceCode); }
}

class UpdatePackageRequest
{
    public function __construct(
        public ?string $height = null,
        public ?string $width = null,
        public ?string $length = null,
        public ?string $distanceUnit = null,
        public ?string $weight = null,
        public ?string $massUnit = null,
    ) {}
}
