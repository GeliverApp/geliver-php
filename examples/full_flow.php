<?php

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
} else {
    require __DIR__ . '/../src/Client.php';
    require __DIR__ . '/../src/Resources/Shipments.php';
    require __DIR__ . '/../src/Resources/Addresses.php';
    require __DIR__ . '/../src/Resources/Webhooks.php';
    require __DIR__ . '/../src/Resources/ParcelTemplates.php';
    require __DIR__ . '/../src/Resources/Providers.php';
    require __DIR__ . '/../src/Resources/Prices.php';
    require __DIR__ . '/../src/Resources/Geo.php';
    require __DIR__ . '/../src/Resources/Organizations.php';
}

use Geliver\Client;

$token = getenv('GELIVER_TOKEN') ?: 'YOUR_TOKEN';
$client = new Client($token);

$sender = $client->addresses()->createSender([
  'name' => 'ACME Inc.', 'email' => 'ops@acme.test', 'phone' => '+905051234567',
  'address1' => 'Street 1', 'countryCode' => 'TR', 'cityName' => 'Istanbul', 'cityCode' => '34',
  'districtName' => 'Esenyurt', 'districtID' => 107605, 'zip' => '34020',
]);

$shipment = $client->shipments()->createTest([
  'sourceCode' => 'API', 'senderAddressID' => $sender['id'],
  'recipientAddress' => [
    'name' => 'John Doe', 'email' => 'john@example.com', 'phone' => '+905051234568',
    'address1' => 'Dest St 2', 'countryCode' => 'TR', 'cityName' => 'Istanbul', 'cityCode' => '34',
    'districtName' => 'Esenyurt', 'districtID' => 107605, 'zip' => '34020',
  ],
  'length' => 10, 'width' => 10, 'height' => 10, 'distanceUnit' => 'cm', 'weight' => 1, 'massUnit' => 'kg',
]);

// Etiketler bazı akışlarda create sonrasında hazır olabilir; varsa hemen indirin
if (!is_dir('sdks/output')) { @mkdir('sdks/output', 0777, true); }
if (!empty($shipment['labelURL'])) {
  file_put_contents('sdks/output/label_pre.pdf', $client->shipments()->downloadLabel($shipment['id']));
}
if (!empty($shipment['responsiveLabelURL'])) {
  file_put_contents('sdks/output/label_pre.html', $client->shipments()->downloadResponsiveLabel($shipment['id']));
}

// Teklifler create yanıtında hazır olabilir; önce onu kontrol edin
$offers = $shipment['offers'] ?? null;
if (!($offers && ((int)($offers['percentageCompleted'] ?? 0) >= 99 || isset($offers['cheapest'])))) {
  do {
    $s = $client->shipments()->get($shipment['id']);
    $offers = $s['offers'] ?? null;
    $pc = (int)($offers['percentageCompleted'] ?? 0);
    if ($pc >= 99 || isset($offers['cheapest'])) break;
    usleep(1000000);
  } while (true);
}

$tx = $client->transactions()->acceptOffer($offers['cheapest']['id']);
echo 'Transaction: ' . $tx['id'] . PHP_EOL;
echo 'Barcode: ' . ($tx['shipment']['barcode'] ?? '') . PHP_EOL;
echo 'Label URL: ' . ($tx['shipment']['labelURL'] ?? '') . PHP_EOL;
echo 'Tracking URL: ' . ($tx['shipment']['trackingUrl'] ?? '') . PHP_EOL;

// Test gönderilerinde her GET /shipments isteği kargo durumunu bir adım ilerletir; prod'da webhook önerilir.
for ($i=0; $i<5; $i++) { sleep(1); $client->shipments()->get($shipment['id']); }
$tracked = $client->shipments()->get($shipment['id']);
echo 'Tracking number (refresh): ' . ($tracked['trackingNumber'] ?? '') . PHP_EOL;
if (!empty($tracked['trackingStatus'])) {
  echo 'Final tracking status: ' . ($tracked['trackingStatus']['trackingStatusCode'] ?? '') . ' ' . ($tracked['trackingStatus']['trackingSubStatusCode'] ?? '') . PHP_EOL;
}
// Manual tracking check
$latest = $client->shipments()->get($shipment['id']);
$ts = $latest['trackingStatus'] ?? [];
echo 'Status: ' . ($ts['trackingStatusCode'] ?? '') . ' ' . ($ts['trackingSubStatusCode'] ?? '') . PHP_EOL;
