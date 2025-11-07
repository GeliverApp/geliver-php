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
  'senderAddressID' => $sender['id'],
  'recipientAddress' => [
    'name' => 'John Doe', 'email' => 'john@example.com', 'phone' => '+905051234568',
    'address1' => 'Dest St 2', 'countryCode' => 'TR', 'cityName' => 'Istanbul', 'cityCode' => '34',
    'districtName' => 'Esenyurt', 'districtID' => 107605, 'zip' => '34020',
  ],
  'order' => [ 'orderNumber' => 'ABC12333322', 'sourceIdentifier' => 'https://magazaadresiniz.com', 'totalAmount' => 150, 'totalAmountCurrency' => 'TL' ],
  'length' => '10.0', 'width' => '10.0', 'height' => '10.0', 'distanceUnit' => 'cm', 'weight' => '1.0', 'massUnit' => 'kg',
]);

// Etiket indirme: Teklif kabulünden sonra (Transaction) gelen URL'leri kullanabilirsiniz de; URL'lere her shipment nesnesinin içinden ulaşılır.

// Teklifler create yanıtında hazır olabilir; önce onu kontrol edin
$offers = $shipment['offers'] ?? null;
if (!($offers && ((int)($offers['percentageCompleted'] ?? 0) == 100 || isset($offers['cheapest'])))) {
  do {
    $s = $client->shipments()->get($shipment['id']);
    $offers = $s['offers'] ?? null;
    $pc = (int)($offers['percentageCompleted'] ?? 0);
    if ($pc == 100 || isset($offers['cheapest'])) break;
    usleep(1000000);
  } while (true);
}

$tx = $client->transactions()->acceptOffer($offers['cheapest']['id']);
echo 'Transaction: ' . $tx['id'] . PHP_EOL;
echo 'Barcode: ' . ($tx['shipment']['barcode'] ?? '') . PHP_EOL;
echo 'Label URL: ' . ($tx['shipment']['labelURL'] ?? '') . PHP_EOL;
echo 'Tracking URL: ' . ($tx['shipment']['trackingUrl'] ?? '') . PHP_EOL;
// Download labels directly using URLs from transaction (no extra GET)
if (!empty($tx['shipment']['labelURL'])) {
  file_put_contents('label.pdf', $client->shipments()->downloadLabelByUrl($tx['shipment']['labelURL']));
}
if (!empty($tx['shipment']['responsiveLabelURL'])) {
  file_put_contents('label.html', $client->shipments()->downloadResponsiveLabelByUrl($tx['shipment']['responsiveLabelURL']));
}

// Test gönderilerinde her GET /shipments isteği kargo durumunu bir adım ilerletir; prod'da webhook önerilir.
/*for ($i=0; $i<5; $i++) { sleep(1); $client->shipments()->get($shipment['id']); }
$tracked = $client->shipments()->get($shipment['id']);
echo 'Tracking number (refresh): ' . ($tracked['trackingNumber'] ?? '') . PHP_EOL;
if (!empty($tracked['trackingStatus'])) {
  echo 'Final tracking status: ' . ($tracked['trackingStatus']['trackingStatusCode'] ?? '') . ' ' . ($tracked['trackingStatus']['trackingSubStatusCode'] ?? '') . PHP_EOL;
}*/

// Manual tracking check
$latest = $client->shipments()->get($shipment['id']);
$ts = $latest['trackingStatus'] ?? [];
echo 'Status: ' . ($ts['trackingStatusCode'] ?? '') . ' ' . ($ts['trackingSubStatusCode'] ?? '') . PHP_EOL;
