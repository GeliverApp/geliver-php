# Geliver PHP SDK

Geliver PHP SDK — official PHP client for Geliver Kargo Pazaryeri (Shipping Marketplace) API.
Türkiye’nin e‑ticaret gönderim altyapısı için kolay kargo entegrasyonu sağlar.

• Dokümantasyon (TR/EN): https://docs.geliver.io

---

## İçindekiler

- Kurulum
- Hızlı Başlangıç
- Adım Adım
- Webhooklar
- Testler
- Modeller
- Enum Kullanımı
- Notlar ve İpuçları

---

## Kurulum

- `cd sdks/php && composer install`

---

## Akış (TR)

1. Geliver Kargo API tokenı alın (https://app.geliver.io/apitokens adresinden)
2. Gönderici adresi oluşturun (addresses()->createSender)
3. Gönderiyi alıcıyı ID'si ile ya da adres nesnesi ile vererek oluşturun (shipments()->create)
4. Teklifleri bekleyin ve kabul edin (transactions()->acceptOffer)
5. Barkod, takip numarası, etiket URL’leri Transaction içindeki Shipment’te bulunur
6. Test gönderilerinde her GET /shipments isteği kargo durumunu bir adım ilerletir; prod'da webhook kurun
7. Etiketleri indirin (downloadLabel, downloadResponsiveLabel)
8. İade gönderisi gerekiyorsa shipments()->createReturn kullanın

---

## Hızlı Başlangıç

```php
use Geliver\Client;

$client = new Client('YOUR_TOKEN');
$sender = $client->addresses()->createSender([
  'name' => 'ACME Inc.', 'email' => 'ops@acme.test', 'address1' => 'Street 1',
  'countryCode' => 'TR', 'cityName' => 'Istanbul', 'cityCode' => '34',
  'districtName' => 'Esenyurt', 'districtID' => 107605, 'zip' => '34020',
]);
$shipment = $client->shipments()->createTest([
  'sourceCode' => 'API', 'senderAddressID' => $sender['id'],
  'recipientAddress' => ['name' => 'John Doe', 'email' => 'john@example.com', 'address1' => 'Dest St 2', 'countryCode' => 'TR', 'cityName' => 'Istanbul', 'cityCode' => '34', 'districtName' => 'Kadikoy', 'districtID' => 100000, 'zip' => '34000'],
  'length' => 10, 'width' => 10, 'height' => 10, 'distanceUnit' => 'cm', 'weight' => 1, 'massUnit' => 'kg',
]);
```

---

## Türkçe Akış (TR)

```php
use Geliver\Client;

$client = new Client('YOUR_TOKEN');

// 1) Gönderici adresi oluşturma
$sender = $client->addresses()->createSender([
  'name' => 'ACME Inc.', 'email' => 'ops@acme.test', 'phone' => '+905051234567',
  'address1' => 'Street 1', 'countryCode' => 'TR', 'cityName' => 'Istanbul', 'cityCode' => '34',
  'districtName' => 'Esenyurt', 'districtID' => 107605, 'zip' => '34020',
]);

// 2) Gönderi oluşturma (iki adım) — Seçenek A: alıcıyı inline verin (kayıt oluşturmadan)
$shipment = $client->shipments()->create([
  'sourceCode' => 'API',
  'senderAddressID' => $sender['id'],
  'recipientAddress' => [
    'name' => 'John Doe', 'email' => 'john@example.com', 'phone' => '+905051234568',
    'address1' => 'Dest St 2', 'countryCode' => 'TR', 'cityName' => 'Istanbul', 'cityCode' => '34',
    'districtName' => 'Esenyurt', 'districtID' => 107605, 'zip' => '34020',
  ],
  'length' => 10, 'width' => 10, 'height' => 10, 'distanceUnit' => 'cm', 'weight' => 1, 'massUnit' => 'kg',
]);

// Etiketler bazı akışlarda create sonrasında hazır olabilir; varsa hemen indirin
if (!empty($shipment['labelURL'])) {
  file_put_contents('label_pre.pdf', $client->shipments()->downloadLabel($shipment['id']));
}
if (!empty($shipment['responsiveLabelURL'])) {
  file_put_contents('label_pre.html', $client->shipments()->downloadResponsiveLabel($shipment['id']));
}

// 3) Alıcı adresi oluşturma (örnek)
$recipient = $client->addresses()->createRecipient([
  'name' => 'John Doe', 'email' => 'john@example.com',
  'address1' => 'Dest St 2', 'countryCode' => 'TR', 'cityName' => 'Istanbul', 'cityCode' => '34',
  'districtName' => 'Kadikoy', 'districtID' => 100000, 'zip' => '34000',
]);

// 4) Teklifleri kontrol et: create yanıtında hazır olabilir
$offers = $shipment['offers'] ?? null;
if (!($offers && ((int)($offers['percentageCompleted'] ?? 0) >= 99 || isset($offers['cheapest'])))) {
  // Hazır değilse, >= %99 olana kadar 1 sn aralıkla sorgulayın (backend 99'da kalabilir)
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
echo 'Tracking number: ' . ($tx['shipment']['trackingNumber'] ?? '') . PHP_EOL;
echo 'Label URL: ' . ($tx['shipment']['labelURL'] ?? '') . PHP_EOL;
echo 'Tracking URL: ' . ($tx['shipment']['trackingUrl'] ?? '') . PHP_EOL;

// Test gönderilerinde her GET /shipments isteği kargo durumunu bir adım ilerletir (prod'da webhook önerilir)
for ($i=0; $i<5; $i++) { sleep(1); $client->shipments()->get($shipment['id']); }
$latest = $client->shipments()->get($shipment['id']);
$ts = $latest['trackingStatus'] ?? [];
echo 'Final tracking status: ' . ($ts['trackingStatusCode'] ?? '') . ' ' . ($ts['trackingSubStatusCode'] ?? '') . PHP_EOL;

// Download labels
file_put_contents('label.pdf', $client->shipments()->downloadLabel($shipment['id']));
file_put_contents('label.html', $client->shipments()->downloadResponsiveLabel($shipment['id']));
```

---

## Alıcı ID'si ile oluşturma (recipientAddressID)

```php
// Önce alıcı adresini kaydedin ve ID alın
$recipient = $client->addresses()->createRecipient([
  'name' => 'John Doe', 'email' => 'john@example.com', 'address1' => 'Dest St 2',
  'countryCode' => 'TR', 'cityName' => 'Istanbul', 'cityCode' => '34',
  'districtName' => 'Kadikoy', 'districtID' => 100000, 'zip' => '34000',
]);

// Ardından recipientAddressID ile gönderi oluşturun
$client->shipments()->create([
  'sourceCode' => 'API',
  'senderAddressID' => $sender['id'],
  'recipientAddressID' => $recipient['id'],
  'providerServiceCode' => 'MNG_STANDART',
  'length' => 10, 'width' => 10, 'height' => 10, 'distanceUnit' => 'cm', 'weight' => 1, 'massUnit' => 'kg',
]);
```

---

## Webhooklar

- `/webhooks/geliver` gibi bir endpoint yayınlayın ve JSON içeriği işleyin. Doğrulama için `Geliver\Webhooks::verify($rawBody, $headers, false)` kullanabilirsiniz (şimdilik devre dışı).
- Webhook yönetimi: `$client->webhooks()->create('https://yourapp.test/webhooks/geliver');`

---

## Testler

- Testlerde Guzzle MockHandler kullanabilirsiniz.
- Üretilmiş model sınıfları `Geliver\Models` altında bulunur (OpenAPI’den otomatik üretilir).

Manuel takip kontrolü (isteğe bağlı)

```php
$s = $client->shipments()->get($shipment['id']);
$ts = $s['trackingStatus'] ?? null;
echo 'Status: ' . ($ts['trackingStatusCode'] ?? '') . ' ' . ($ts['trackingSubStatusCode'] ?? '') . PHP_EOL;
```

---

## Modeller

- Shipment, Transaction, TrackingStatus, Address, ParcelTemplate, ProviderAccount, Webhook, Offer, PriceQuote ve daha fazlası.
- Tam liste: `src/Models/Models.php`.

## Enum Kullanımı (TR)

```php
use Geliver\Models\ShipmentLabelFileType;

$s = $client->shipments()->get($shipment['id']);
if (($s['labelFileType'] ?? null) === ShipmentLabelFileType::PDF->value) {
  echo "PDF etiket hazır" . PHP_EOL;
}
```

---

## Notlar ve İpuçları (TR)

- Ondalıklı sayılar string olarak gelir; hesaplama için BCMath veya GMP kullanın.
- Teklif beklerken 1 sn aralıkla tekrar sorgulayın; gereksiz yükten kaçının.
- Test gönderisi: `$client->shipments()->create(['test' => true, ...])` veya `createTest([...])`.
- İlçe seçimi: districtID (number) tercih sebebidir; districtName her zaman birebir eşleşmeyebilir.
- Şehir/İlçe seçimi: cityCode ve cityName birlikte/ayrı kullanılabilir; cityCode daha güvenlidir. Listeler için API'yi kullanın:

```php
$cities = $client->geo()->listCities('TR');
$districts = $client->geo()->listDistricts('TR', '34');
```

Diğer Örnekler (PHP)

- Sağlayıcı Hesapları (Provider Accounts)

```php
$acc = $client->providers()->createAccount([
  'username' => 'user', 'password' => 'pass', 'name' => 'My Account', 'providerCode' => 'SURAT',
  'version' => 1, 'isActive' => true, 'isPublic' => false, 'sharable' => false, 'isDynamicPrice' => false,
]);
$list = $client->providers()->listAccounts();
$client->providers()->deleteAccount($acc['id'], true);
```

- Kargo Şablonları (Parcel Templates)

```php
$tpl = $client->parcelTemplates()->create([
  'name'=>'Small Box','distanceUnit'=>'cm','massUnit'=>'kg','height'=>4,'length'=>4,'weight'=>1,'width'=>4,
]);
$tpls = $client->parcelTemplates()->list();
$client->parcelTemplates()->delete($tpl['id']);
```

[![Geliver Kargo Pazaryeri](https://geliver.io/geliverlogo.png)](https://geliver.io/)
Geliver Kargo Pazaryeri: https://geliver.io/

Etiketler (Tags): php, composer, sdk, api-client, geliver, kargo, kargo-pazaryeri, shipping, e-commerce, turkey
