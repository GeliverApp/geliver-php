<?php
require __DIR__ . '/../../vendor/autoload.php';

use Geliver\Client;

$token = getenv('GELIVER_TOKEN');
if (!$token) { fwrite(STDERR, "GELIVER_TOKEN required\n"); exit(1); }
$client = new Client($token);

$sender = $client->addresses()->createSender([
  'name' => 'POD Sender', 'email' => 'sender@example.com', 'phone' => '+905000000098',
  'address1' => 'Hasan Mahallesi', 'countryCode' => 'TR', 'cityName' => 'Istanbul', 'cityCode' => '34', 'districtName' => 'Esenyurt', 'zip' => '34020',
]);

$tx = $client->transactions()->create([
  'senderAddressID' => $sender['id'],
  'recipientAddress' => [
    'name' => 'POD Recipient', 'phone' => '+905000000001', 'address1' => 'Atatürk Mahallesi', 'countryCode' => 'TR', 'cityName' => 'Istanbul', 'cityCode' => '34', 'districtName' => 'Esenyurt',
  ],
  'length' => '10.0', 'width' => '10.0', 'height' => '10.0', 'distanceUnit' => 'cm', 'weight' => '1.0', 'massUnit' => 'kg',
  'providerServiceCode' => 'PTT_KAPIDA_ODEME',
  'productPaymentOnDelivery' => true,
  'order' => [ 'orderNumber' => 'POD-12345', 'totalAmount' => '150', 'totalAmountCurrency' => 'TRY' ],
]);
echo 'transaction id: ' . ($tx['id'] ?? '') . PHP_EOL;
