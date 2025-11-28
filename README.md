# Geliver PHP SDK

[![Packagist](https://img.shields.io/packagist/v/geliver/sdk.svg)](https://packagist.org/packages/geliver/sdk)

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

- Packagist: `composer require geliver/sdk:^1.0`
- Bu repodaki örnekleri çalıştırmak için: `composer install`

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
  'name' => 'ACME Inc.', 'email' => 'ops@acme.test', 'phone' => '+905051234567', 'address1' => 'Hasan Mahallesi',
  'countryCode' => 'TR', 'cityName' => 'Istanbul', 'cityCode' => '34',
  'districtName' => 'Esenyurt', 'zip' => '34020',
]);
$shipment = $client->shipments()->createTest([
  'senderAddressID' => $sender['id'],
  'recipientAddress' => ['name' => 'John Doe', 'email' => 'john@example.com', 'phone' => '+905051234568', 'address1' => 'Atatürk Mahallesi', 'countryCode' => 'TR', 'cityName' => 'Istanbul', 'cityCode' => '34', 'districtName' => 'Kadıköy', 'zip' => '34000'],
  // İstek alanları string olmalıdır
  'length' => '10.0', 'width' => '10.0', 'height' => '10.0', 'distanceUnit' => 'cm', 'weight' => '1.0', 'massUnit' => 'kg',
  'order' => [
    'orderNumber' => 'WEB-12345',
    // sourceIdentifier alanına mağazanızın tam adresini yazın (ör. https://magazam.com).
    'sourceIdentifier' => 'https://magazam.com',
    'totalAmount' => '150',
    'totalAmountCurrency' => 'TRY',
  ],
]);
```

Canlı ortamda `$client->shipments()->createTest(...)` yerine `$client->shipments()->create(...)` çağırın.

---

## Türkçe Akış (TR)

```php
use Geliver\Client;

$client = new Client('YOUR_TOKEN');

// 1) Gönderici adresi oluşturma. Her gönderici adresi için tek seferlik yapılır. Oluşan gönderici adres ID'sini saklayıp tekrar kullanılır.
$sender = $client->addresses()->createSender([
  'name' => 'ACME Inc.', 'email' => 'ops@acme.test', 'phone' => '+905051234567',
  'address1' => 'Hasan Mahallesi', 'countryCode' => 'TR', 'cityName' => 'Istanbul', 'cityCode' => '34',
  'districtName' => 'Esenyurt', 'zip' => '34020',
]);

// 2) Gönderi oluşturma (iki adım) — Seçenek A: alıcıyı inline verin (kayıt oluşturmadan)
// Canlı ortamda createTest yerine create fonksiyonunu kullanın.
$shipment = $client->shipments()->createTest([
  'senderAddressID' => $sender['id'],
  'recipientAddress' => [
    'name' => 'John Doe', 'email' => 'john@example.com', 'phone' => '+905051234568',
    'address1' => 'Atatürk Mahallesi', 'countryCode' => 'TR', 'cityName' => 'Istanbul', 'cityCode' => '34',
    'districtName' => 'Esenyurt', 'zip' => '34020',
  ],
  'length' => '10.0', 'width' => '10.0', 'height' => '10.0', 'distanceUnit' => 'cm', 'weight' => '1.0', 'massUnit' => 'kg',
  'order' => [
    'orderNumber' => 'WEB-12345',
    // sourceIdentifier alanına mağazanızın tam adresini yazın (ör. https://magazam.com).
    'sourceIdentifier' => 'https://magazam.com',
    'totalAmount' => '150',
    'totalAmountCurrency' => 'TRY',
  ],
]);

// Etiket indirme: Teklif kabulünden sonra (Transaction) gelen URL'leri kullanabilirsiniz de; URL'lere her shipment nesnesinin içinden ulaşılır.

// 3) Alıcı adresi oluşturma (örnek)
$recipient = $client->addresses()->createRecipient([
  'name' => 'John Doe', 'email' => 'john@example.com',
  'address1' => 'Atatürk Mahallesi', 'countryCode' => 'TR', 'cityName' => 'Istanbul', 'cityCode' => '34',
  'districtName' => 'Kadıköy', 'zip' => '34000',
]);

// 4) Teklifleri kontrol et: create yanıtındaki offers alanını kullanın
$offers = $shipment['offers'] ?? null;
if (empty($offers['cheapest'])) {
  throw new RuntimeException('Teklifler henüz hazır değil; GET /shipments ile tekrar kontrol edin.');
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
// Teklif kabulünden sonra Transaction içindeki Shipment alanındaki URL'leri kullanın (ek GET yok)
file_put_contents('label.pdf', $client->shipments()->downloadLabelByUrl($tx['shipment']['labelURL']));
file_put_contents('label.html', $client->shipments()->downloadResponsiveLabelByUrl($tx['shipment']['responsiveLabelURL']));
```

## Alıcı ID'si ile oluşturma (recipientAddressID)

```php
// Önce alıcı adresini kaydedin ve ID alın
$recipient = $client->addresses()->createRecipient([
  'name' => 'John Doe', 'email' => 'john@example.com', 'address1' => 'Atatürk Mahallesi',
  'countryCode' => 'TR', 'cityName' => 'Istanbul', 'cityCode' => '34',
  'districtName' => 'Kadıköy', 'zip' => '34000',
]);

// Ardından recipientAddressID ile gönderi oluşturun
$client->shipments()->create([
  'senderAddressID' => $sender['id'],
  'recipientAddressID' => $recipient['id'],
  'providerServiceCode' => 'MNG_STANDART',
  'length' => '10.0', 'width' => '10.0', 'height' => '10.0', 'distanceUnit' => 'cm', 'weight' => '1.0', 'massUnit' => 'kg',
]);
```

---

---

## İade Gönderisi Oluşturun

```php
$returned = $client->shipments()->createReturn($shipment['id'], [
  'willAccept' => true,
  'providerServiceCode' => 'SURAT_STANDART',
  'count' => 1,
]);
```

Not:

- `providerServiceCode` alanı opsiyoneldir. Varsayılan olarak orijinal gönderinin sağlayıcısı kullanılır; dilerseniz bu alanı vererek değiştirebilirsiniz.
- `senderAddress` alanı opsiyoneldir. Varsayılan olarak orijinal gönderinin alıcı adresi kullanılır; dilerseniz bu alanı vererek değiştirebilirsiniz.

---

## Webhooklar

- `/webhooks/geliver` gibi bir endpoint yayınlayın ve JSON içeriğini `WebhookUpdateTrackingRequest` modeli ile ayrıştırın. Doğrulama için `Geliver\Webhooks::verify($rawBody, $headers, false)` kullanabilirsiniz (şimdilik devre dışı).

```php
use Geliver\Webhooks;
use Geliver\Models\WebhookUpdateTrackingRequest;
use Geliver\Models\Shipment;

$raw = file_get_contents('php://input');
if (!Webhooks::verify($raw, getallheaders(), false)) {
  http_response_code(400);
  exit('invalid');
}
$payload = json_decode($raw, true) ?: [];
$evt = new WebhookUpdateTrackingRequest();
$evt->event = $payload['event'] ?? '';
$evt->metadata = $payload['metadata'] ?? null;
$evt->data = new Shipment();
foreach (($payload['data'] ?? []) as $key => $value) {
  if (property_exists($evt->data, $key)) {
    $evt->data->{$key} = $value;
  }
}
if ($evt->event === 'TRACK_UPDATED') {
  echo 'Tracking update: ' . ($evt->data->trackingUrl ?? '') . ' ' . ($evt->data->trackingNumber ?? '') . PHP_EOL;
}
```

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

### Gönderi Listeleme, Getir, Güncelle, İptal, Klonla

- Listeleme (docs): https://docs.geliver.io/docs/shipments_and_transaction/list_shipments
- Gönderi getir (docs): https://docs.geliver.io/docs/shipments_and_transaction/list_shipments
- Paket güncelle (docs): https://docs.geliver.io/docs/shipments_and_transaction/update_package_shipment
- Gönderi iptal (docs): https://docs.geliver.io/docs/shipments_and_transaction/cancel_shipment
- Gönderi klonla (docs): https://docs.geliver.io/docs/shipments_and_transaction/clone_shipment

```php
// Listeleme (sayfalandırma)
$list = $client->shipments()->list(['page' => 1, 'limit' => 20]);
foreach ($list['data'] ?? [] as $shipment) {
  echo $shipment['id'] ?? '' . PHP_EOL;
}

// Getir
$fetched = $client->shipments()->get('SHIPMENT_ID');
$ts = $fetched['trackingStatus'] ?? [];
echo 'Tracking: ' . ($ts['trackingStatusCode'] ?? '') . ' ' . ($ts['trackingSubStatusCode'] ?? '') . PHP_EOL;

// Paket güncelle (eni, boyu, yüksekliği ve ağırlığı string gönderin)
$client->shipments()->updatePackage($fetched['id'], [
  'length' => '12.0',
  'width' => '12.0',
  'height' => '10.0',
  'distanceUnit' => 'cm',
  'weight' => '1.2',
  'massUnit' => 'kg',
]);

// İptal
$client->shipments()->cancel($fetched['id']);

// Klonla
$cloned = $client->shipments()->clone($fetched['id']);
echo 'Cloned shipment: ' . ($cloned['id'] ?? '') . PHP_EOL;
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

- İstek tarafında `length`, `width`, `height`, `weight` değerleri string gönderilmelidir; örn. `'10.0'`.
- Ondalıklı sayılar response tarafında genelde string olarak gelir; hesaplama için BCMath veya GMP kullanın.
- Teklif beklerken 1 sn aralıkla tekrar sorgulayın; gereksiz yükten kaçının.
- Test gönderisi: `$client->shipments()->create(['test' => true, ...])` veya `createTest([...])`; canlı ortamda `createTest` yerine `$client->shipments()->create(...)` kullanın.

- Takip numarası ile takip URL'si bazı kargo firmalarında teklif kabulünün hemen ardından oluşmayabilir. Paketi kargo şubesine teslim ettiğinizde veya kargo sizden teslim aldığında bu alanlar tamamlanır. Webhooklar ile değerleri otomatik çekebilir ya da teslimden sonra `shipment` GET isteği yaparak güncel bilgileri alabilirsiniz.
- Şehir/İlçe seçimi: cityCode ve cityName birlikte/ayrı kullanılabilir; cityCode daha güvenlidir. Listeler için API'yi kullanın:

```php
$cities = $client->geo()->listCities('TR');
$districts = $client->geo()->listDistricts('TR', '34');
```

- Adres kuralları: phone alanı hem gönderici hem alıcı adresleri için zorunludur. Zip alanı gönderici adresi için zorunludur; alıcı adresi için opsiyoneldir. `$client->addresses()->createSender([...])` phone/zip eksikse, `$client->addresses()->createRecipient([...])` phone eksikse hata verir.

## Örnekler

- Tam akış: `examples/full_flow.php` (composer install sonrası)
- Tek aşamada gönderi (Create Transaction): `examples/onestep.php`
- Kapıda ödeme: `examples/pod.php`
- Kendi anlaşmanızla etiket satın alma: `examples/ownagreement.php`

---

## Hatalar ve İstisnalar

- İstemci şu durumlarda `ApiException` fırlatır: (1) HTTP 4xx/5xx; (2) JSON envelope `result === false`.
- Hata alanları: `$e->codeStr`, `$e->additionalMessage`, `$e->status`, `$e->getMessage()`, `$e->body`.

```php
try {
  $client->shipments()->create([/* ... */]);
} catch (\Geliver\ApiException $e) {
  error_log('code: ' . $e->codeStr);
  error_log('message: ' . $e->getMessage());
  error_log('additional: ' . $e->additionalMessage);
  error_log('status: ' . $e->status);
}
```

Diğer Örnekler (PHP)

- Kendi Kargo Anlaşmanız (Provider Accounts)

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
