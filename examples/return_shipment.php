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
$returned = $client->shipments()->createReturn($shipmentId, []);
echo 'Return shipment: ' . ($returned['id'] ?? '') . PHP_EOL;
echo "Label is not purchased yet. This example waits for offers and buys it with acceptOffer." . PHP_EOL;

$current = $returned;
$offers = $current['offers'] ?? null;
$deadline = time() + 60;

while (!is_array($offers) || empty($offers['cheapest']['id'])) {
    if (time() >= $deadline) {
        fwrite(STDERR, "Timed out waiting for return offers.\n");
        exit(1);
    }
    echo 'Waiting offers... ' . (($offers['percentageCompleted'] ?? 0)) . '%' . PHP_EOL;
    sleep(1);
    $current = $client->shipments()->get($returned['id']);
    $offers = $current['offers'] ?? null;
}

$tx = $client->transactions()->acceptOffer($offers['cheapest']['id']);
echo 'Transaction: ' . ($tx['id'] ?? '') . PHP_EOL;
echo 'Purchased return shipment: ' . (($tx['shipment']['id'] ?? '')) . PHP_EOL;
