<?php

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
} else {
    require __DIR__ . '/../src/Client.php';
    require __DIR__ . '/../src/Resources/Shipments.php';
}

use Geliver\Client;

$token = getenv('GELIVER_TOKEN');
$recipientAddressID = getenv('GELIVER_MERCHANT_ADDRESS_ID');
if (!$token || !$recipientAddressID) {
    fwrite(STDERR, "Set GELIVER_TOKEN and GELIVER_MERCHANT_ADDRESS_ID.\n");
    exit(1);
}

$client = new Client($token);
$shipment = $client->shipments()->create([
    'isReturn' => true,
    'test' => true,
    'senderAddress' => [
        'name' => 'Müşteri Adı', 'phone' => '+905051234567',
        'address1' => 'Örnek Mahallesi 1', 'countryCode' => 'TR',
        'cityCode' => '34', 'districtName' => 'Kadıköy',
    ],
    'recipientAddressID' => $recipientAddressID,
    'length' => '10', 'width' => '10', 'height' => '10',
    'distanceUnit' => 'cm', 'weight' => '1', 'massUnit' => 'kg',
]);

echo 'Gelecek gönderi: ' . ($shipment['data']['id'] ?? $shipment['id'] ?? '') . PHP_EOL;
