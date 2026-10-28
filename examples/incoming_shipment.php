<?php

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
} else {
    require __DIR__ . '/../src/Client.php';
    require __DIR__ . '/../src/Resources/Shipments.php';
}

use Geliver\Client;

$token = getenv('GELIVER_TOKEN');
if (!$token) {
    fwrite(STDERR, "Set GELIVER_TOKEN.\n");
    exit(1);
}

$client = new Client($token);
$shipment = $client->shipments()->create([
    'isReturn' => true,
    'test' => true,
    'recipientAddress' => [
        'name' => 'Müşteri Adı', 'email' => 'customer@example.com', 'phone' => '+905051234567',
        'address1' => 'Örnek Mahallesi 1', 'countryCode' => 'TR',
        'cityCode' => '34', 'districtName' => 'Kadıköy', 'zip' => '34000',
    ],
    'length' => '10', 'width' => '10', 'height' => '10',
    'distanceUnit' => 'cm', 'weight' => '1', 'massUnit' => 'kg',
]);

echo 'Gelecek gönderi: ' . ($shipment['data']['id'] ?? $shipment['id'] ?? '') . PHP_EOL;
