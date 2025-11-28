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
  'address1' => 'Hasan Mahallesi', 'countryCode' => 'TR', 'cityName' => 'Istanbul', 'cityCode' => '34',
  'districtName' => 'Esenyurt', 'zip' => '34020',
]);

$shipment = $client->shipments()->createTest([
  'senderAddressID' => $sender['id'],
  'recipientAddress' => [
    'name' => 'John Doe', 'email' => 'john@example.com', 'phone' => '+905051234568',
    'address1' => 'Atatürk Mahallesi', 'countryCode' => 'TR', 'cityName' => 'Istanbul', 'cityCode' => '34',
    'districtName' => 'Esenyurt', 'zip' => '34020',
  ],
  'order' => [ 'orderNumber' => 'ABC12333322', 'sourceIdentifier' => 'https://magazaadresiniz.com', 'totalAmount' => 150, 'totalAmountCurrency' => 'TRY' ],
  'length' => '10.0', 'width' => '10.0', 'height' => '10.0', 'distanceUnit' => 'cm', 'weight' => '1.0', 'massUnit' => 'kg',
]);

// Etiket indirme: Teklif kabulünden sonra (Transaction) gelen URL'leri kullanabilirsiniz de; URL'lere her shipment nesnesinin içinden ulaşılır.

// Teklifler create yanıtındaki offers alanında gelir
$offers = $shipment['offers'] ?? null;
if (empty($offers['cheapest'])) {
  echo "Error: No cheapest offer available (henüz hazır değil)" . PHP_EOL;
  exit(1);
}

try {
  $tx = $client->transactions()->acceptOffer($offers['cheapest']['id']);
} catch (Exception $e) {
  echo "Accept offer error: " . $e->getMessage() . PHP_EOL;
  // Try to get more details if available
  if (method_exists($e, 'getResponse')) {
    $response = $e->getResponse();
    if ($response) {
      echo "API Error: " . $response->getStatusCode() . " - " . $response->getBody() . PHP_EOL;
    }
  }
  exit(1);
}
echo 'Transaction: ' . $tx['id'] . PHP_EOL;
echo 'Barcode: ' . ($tx['shipment']['barcode'] ?? '') . PHP_EOL;
echo 'Label URL: ' . ($tx['shipment']['labelURL'] ?? '') . PHP_EOL;
echo 'Tracking URL: ' . ($tx['shipment']['trackingUrl'] ?? '') . PHP_EOL;

// Etiket indirme: LabelFileType kontrolü
// Eğer LabelFileType "PROVIDER_PDF" ise, LabelURL'den indirilen PDF etiket kullanılmalıdır.
// Eğer LabelFileType "PDF" ise, responsiveLabelURL (HTML) dosyası kullanılabilir.
if (!empty($tx['shipment'])) {
  $labelFileType = $tx['shipment']['labelFileType'] ?? null;
  if ($labelFileType === 'PROVIDER_PDF') {
    // PROVIDER_PDF: Sadece PDF etiket kullanılmalı
    if (!empty($tx['shipment']['labelURL'])) {
      file_put_contents('label.pdf', $client->shipments()->downloadLabelByUrl($tx['shipment']['labelURL']));
      echo "PDF etiket indirildi (PROVIDER_PDF)" . PHP_EOL;
    }
  } elseif ($labelFileType === 'PDF') {
    // PDF: ResponsiveLabel (HTML) kullanılabilir
    if (!empty($tx['shipment']['responsiveLabelURL'])) {
      file_put_contents('label.html', $client->shipments()->downloadResponsiveLabelByUrl($tx['shipment']['responsiveLabelURL']));
      echo "HTML etiket indirildi (PDF)" . PHP_EOL;
    }
    // İsteğe bağlı olarak PDF de indirilebilir
    if (!empty($tx['shipment']['labelURL'])) {
      file_put_contents('label.pdf', $client->shipments()->downloadLabelByUrl($tx['shipment']['labelURL']));
    }
  }
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
