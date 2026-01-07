<?php
require __DIR__ . '/../../vendor/autoload.php';

use Geliver\Client;

$token = getenv('GELIVER_TOKEN');
if (!$token) { fwrite(STDERR, "GELIVER_TOKEN required\n"); exit(1); }
$client = new Client($token);

$sender = $client->addresses()->createSender([
  'name' => 'OwnAg Sender', 'email' => 'sender@example.com', 'phone' => '+905000000097',
  'address1' => 'Hasan Mahallesi', 'countryCode' => 'TR', 'cityName' => 'Istanbul', 'cityCode' => '34', 'districtName' => 'Esenyurt', 'zip' => '34020',
]);

$tx = $client->transactions()->create([
  'senderAddressID' => $sender['id'],
  'recipientAddress' => [
    'name' => 'OwnAg Recipient', 'phone' => '+905000000002', 'address1' => 'Atatürk Mahallesi', 'countryCode' => 'TR', 'cityName' => 'Istanbul', 'cityCode' => '34', 'districtName' => 'Esenyurt',
  ],
  'length' => '10.0', 'width' => '10.0', 'height' => '10.0', 'distanceUnit' => 'cm', 'weight' => '1.0', 'massUnit' => 'kg',
  'providerServiceCode' => 'SURAT_STANDART',
  'providerAccountID' => 'c0dfdb42-012d-438c-9d49-98d13b4d4a2b',
]);
echo 'transaction id: ' . ($tx['id'] ?? '') . PHP_EOL;
