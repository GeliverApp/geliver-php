<?php

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
} else {
    require __DIR__ . '/../src/Client.php';
    require __DIR__ . '/../src/Resources/Shipments.php';
    require __DIR__ . '/../src/Resources/Transactions.php';
    require __DIR__ . '/../src/Resources/Addresses.php';
    require __DIR__ . '/../src/Resources/Webhooks.php';
    require __DIR__ . '/../src/Resources/ParcelTemplates.php';
    require __DIR__ . '/../src/Resources/Providers.php';
    require __DIR__ . '/../src/Resources/Prices.php';
    require __DIR__ . '/../src/Resources/Geo.php';
    require __DIR__ . '/../src/Resources/Organizations.php';
}

use Geliver\Client;

$token = getenv('GELIVER_TOKEN') ?: null;
$shipmentId = getenv('GELIVER_RETURN_SHIPMENT_ID') ?: ($argv[1] ?? null);

if (!$token || !$shipmentId) {
    fwrite(STDERR, "Set GELIVER_TOKEN and GELIVER_RETURN_SHIPMENT_ID, or pass the shipment ID as the first argument.\n");
    exit(1);
}

$client = new Client($token);
$tx = $client->transactions()->createReturn($shipmentId, []);
echo 'Transaction: ' . ($tx['id'] ?? '') . PHP_EOL;
echo 'Purchased return shipment: ' . (($tx['shipment']['id'] ?? '')) . PHP_EOL;
