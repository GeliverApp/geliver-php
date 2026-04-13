# Changelog

Bu dosya SDK'daki önemli değişiklikleri listeler.

This file documents notable changes in the SDK.

## Sürüm / Version

- Türkçe: Bu değişiklikler `1.3.0` sürümü için hazırlandı.
- English: These changes are prepared for version `1.3.0`.

## Türkçe

### 1.3.0

#### Eklendi

- `transactions()->createReturn(...)` ile iadeyi oluşturup etiketi hemen satın alma akışı eklendi.
- İki yeni iade örneği eklendi:
  - `examples/return_shipment.php`
  - `examples/return_transaction.php`

#### Değişti

- `shipments()->createReturn(...)` artık shipment-only iade akışıdır ve etiketi satın almaz.
- İade dokümanı iki akışı ayrı anlatır.
- README örnekleri, etiketin daha sonra `transactions()->acceptOffer(...)` ile satın alınabileceğini açıklar.

#### Düzeltildi

- `transactions()->acceptOffer(...)` transaction payload'larını tutarlı şekilde ayıklar.
- `transactions()->create(...)` transaction payload'larını tutarlı şekilde ayıklar.

### 1.0.0

- Geliver PHP SDK'nin ilk genel kullanıma açık sürümü.

## English

### 1.3.0

#### Added

- Added `transactions()->createReturn(...)` for creating a return shipment and purchasing the label immediately.
- Added return examples for:
  - `examples/return_shipment.php`
  - `examples/return_transaction.php`

#### Changed

- `shipments()->createReturn(...)` now represents the shipment-only return flow and does not purchase the label.
- Return documentation now explains the two return flows separately.
- README examples now document that label purchase can be performed later with `transactions()->acceptOffer(...)`.

#### Fixed

- `transactions()->acceptOffer(...)` now unwraps transaction payloads consistently.
- `transactions()->create(...)` now unwraps transaction payloads consistently.

### 1.0.0

- First public release of the Geliver PHP SDK.
