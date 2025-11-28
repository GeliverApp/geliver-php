<?php
// Auto-generated from openapi.yaml
namespace Geliver\Models;

/** Address model */
class Address {
    /** @var string */
    public ?string $address1;
    /** @var string */
    public ?string $address2;
    /** @var City */
    public ?City $city;
    /** @var string */
    public ?string $cityCode;
    /** @var string */
    public ?string $cityName;
    /** @var string */
    public ?string $countryCode;
    /** @var string */
    public ?string $countryName;
    /** @var string */
    public ?string $createdAt;
    /** @var District */
    public ?District $district;
    /** @var int */
    public ?int $districtID;
    /** @var string */
    public ?string $districtName;
    /** @var string */
    public ?string $email;
    /** @var string */
    public ?string $id;
    /** @var bool */
    public ?bool $isActive;
    /** @var bool */
    public ?bool $isDefaultReturnAddress;
    /** @var bool */
    public ?bool $isDefaultSenderAddress;
    /** @var bool */
    public ?bool $isInvoiceAddress;
    /** @var bool */
    public ?bool $isRecipientAddress;
    /** @var JSONContent */
    public ?JSONContent $metadata;
    /** @var string */
    public ?string $name;
    /** @var string */
    public ?string $owner;
    /** @var string */
    public ?string $phone;
    /** @var string */
    public ?string $shortName;
    /** @var string */
    public ?string $source;
    /** @var string */
    public ?string $state;
    /** @var string */
    public ?string $streetID;
    /** @var string */
    public ?string $streetName;
    /** @var bool */
    public ?bool $test;
    /** @var string */
    public ?string $updatedAt;
    /** @var string */
    public ?string $zip;
}

/** City model */
class City {
    /** @var string */
    public ?string $areaCode;
    /** @var string */
    public ?string $cityCode;
    /** @var string */
    public ?string $countryCode;
    /** @var string Model */
    public ?string $name;
}

/** DbStringArray model */
class DbStringArray {
}

/** District model */
class District {
    /** @var string */
    public ?string $cityCode;
    /** @var string */
    public ?string $countryCode;
    /** @var int */
    public ?int $districtID;
    /** @var string Model */
    public ?string $name;
    /** @var string */
    public ?string $regionCode;
}

// Duration model removed; API returns integer timestamp for duration fields.

/** Item model */
class Item {
    /** @var string */
    public ?string $countryOfOrigin;
    /** @var string */
    public ?string $createdAt;
    /** @var string */
    public ?string $currency;
    /** @var string */
    public ?string $currencyLocal;
    /** @var string */
    public ?string $id;
    /** @var string */
    public ?string $massUnit;
    /** @var string */
    public ?string $maxDeliveryTime;
    /** @var string */
    public ?string $maxShipTime;
    /** @var string */
    public ?string $owner;
    /** @var int */
    public ?int $quantity;
    /** @var string */
    public ?string $sku;
    /** @var bool */
    public ?bool $test;
    /** @var string */
    public ?string $title;
    /** @var string */
    public ?string $totalPrice;
    /** @var string */
    public ?string $totalPriceLocal;
    /** @var string */
    public ?string $unitPrice;
    /** @var string */
    public ?string $unitPriceLocal;
    /** @var string */
    public ?string $unitWeight;
    /** @var string */
    public ?string $updatedAt;
    /** @var string */
    public ?string $variantTitle;
}

/** JSONContent model */
class JSONContent {
}

/** Offer model */
class Offer {
    /** @var string */
    public ?string $amount;
    /** @var string */
    public ?string $amountLocal;
    /** @var string */
    public ?string $amountLocalOld;
    /** @var string */
    public ?string $amountLocalTax;
    /** @var string */
    public ?string $amountLocalVat;
    /** @var string */
    public ?string $amountOld;
    /** @var string */
    public ?string $amountTax;
    /** @var string */
    public ?string $amountVat;
    /** @var int */
    public ?int $averageEstimatedTime;
    /** @var string */
    public ?string $averageEstimatedTimeHumanReadible;
    /** @var string */
    public ?string $bonusBalance;
    /** @var string */
    public ?string $createdAt;
    /** @var string */
    public ?string $currency;
    /** @var string */
    public ?string $currencyLocal;
    /** @var string */
    public ?string $discountRate;
    /** @var string */
    public ?string $durationTerms;
    /** @var string */
    public ?string $estimatedArrivalTime;
    /** @var string */
    public ?string $id;
    /** @var string */
    public ?string $integrationType;
    /** @var bool */
    public ?bool $isAccepted;
    /** @var bool */
    public ?bool $isC2C;
    /** @var bool */
    public ?bool $isGlobal;
    /** @var bool */
    public ?bool $isMainOffer;
    /** @var bool */
    public ?bool $isProviderAccountOffer;
    /** @var int */
    public ?int $maxEstimatedTime;
    /** @var int */
    public ?int $minEstimatedTime;
    /** @var string */
    public ?string $owner;
    /** @var string */
    public ?string $predictedDeliveryTime;
    /** @var string */
    public ?string $providerAccountID;
    /** @var string */
    public ?string $providerAccountName;
    /** @var string */
    public ?string $providerAccountOwnerType;
    /** @var string */
    public ?string $providerCode;
    /** @var string */
    public ?string $providerServiceCode;
    /** @var string */
    public ?string $providerTotalAmount;
    /** @var string */
    public ?string $rating;
    /** @var string */
    public ?string $scheduleDate;
    /** @var string */
    public ?string $shipmentTime;
    /** @var bool */
    public ?bool $test;
    /** @var string */
    public ?string $totalAmount;
    /** @var string */
    public ?string $totalAmountLocal;
    /** @var string */
    public ?string $updatedAt;
}

/** OfferList model */
class OfferList {
    /** @var bool */
    public ?bool $allowOfferFallback;
    /** @var Offer */
    public ?Offer $cheapest;
    /** @var string */
    public ?string $createdAt;
    /** @var Offer */
    public ?Offer $fastest;
    /** @var string */
    public ?string $height;
    /** @var array */
    public ?array $itemIDs;
    /** @var string */
    public ?string $length;
    /** @var array */
    public ?array $list;
    /** @var string */
    public ?string $owner;
    /** @var array */
    public ?array $parcelIDs;
    /** @var string */
    public ?string $parcelTemplateID;
    /** @var string */
    public ?string $percentageCompleted;
    /** @var array */
    public ?array $providerAccountIDs;
    /** @var array */
    public ?array $providerCodes;
    /** @var array */
    public ?array $providerServiceCodes;
    /** @var bool */
    public ?bool $test;
    /** @var int */
    public ?int $totalOffersCompleted;
    /** @var int */
    public ?int $totalOffersRequested;
    /** @var string */
    public ?string $updatedAt;
    /** @var string */
    public ?string $weight;
    /** @var string */
    public ?string $width;
}

/** Order model */
class Order {
    /** @var string */
    public ?string $buyerShipmentMethod;
    /** @var string */
    public ?string $buyerShippingCost;
    /** @var string */
    public ?string $buyerShippingCostCurrency;
    /** @var string */
    public ?string $createdAt;
    /** @var string */
    public ?string $id;
    /** @var DbStringArray */
    public ?DbStringArray $itemIDs;
    /** @var string */
    public ?string $merchantCode;
    /** @var string */
    public ?string $notes;
    /** @var string */
    public ?string $orderCode;
    /** @var string */
    public ?string $orderNumber;
    /** @var string */
    public ?string $orderStatus;
    /** @var string */
    public ?string $organizationID;
    /** @var string */
    public ?string $owner;
    /** @var Shipment */
    public ?Shipment $shipment;
    /** @var string */
    public ?string $sourceCode;
    /** @var string */
    public ?string $sourceIdentifier;
    /** @var bool */
    public ?bool $test;
    /** @var string */
    public ?string $totalAmount;
    /** @var string */
    public ?string $totalAmountCurrency;
    /** @var string */
    public ?string $totalTax;
    /** @var string */
    public ?string $updatedAt;
}

/** Parcel model */
class Parcel {
    /** @var string */
    public ?string $amount;
    /** @var string */
    public ?string $amountLocal;
    /** @var string */
    public ?string $amountLocalOld;
    /** @var string */
    public ?string $amountLocalTax;
    /** @var string */
    public ?string $amountLocalVat;
    /** @var string */
    public ?string $amountOld;
    /** @var string */
    public ?string $amountTax;
    /** @var string */
    public ?string $amountVat;
    /** @var string */
    public ?string $barcode;
    /** @var string */
    public ?string $bonusBalance;
    /** @var string */
    public ?string $commercialInvoiceUrl;
    /** @var string */
    public ?string $createdAt;
    /** @var string */
    public ?string $currency;
    /** @var string */
    public ?string $currencyLocal;
    /** @var string */
    public ?string $customsDeclaration;
    /** @var string Desi of parcel */
    public ?string $desi;
    /** @var string */
    public ?string $discountRate;
    /** @var string Distance unit of parcel */
    public ?string $distanceUnit;
    /** @var string */
    public ?string $eta;
    /** @var JSONContent */
    public ?JSONContent $extra;
    /** @var string Height of parcel */
    public ?string $height;
    /** @var bool */
    public ?bool $hidePackageContentOnTag;
    /** @var string */
    public ?string $id;
    /** @var bool */
    public ?bool $invoiceGenerated;
    /** @var string */
    public ?string $invoiceID;
    /** @var bool */
    public ?bool $isMainParcel;
    /** @var array */
    public ?array $itemIDs;
    /** @var string */
    public ?string $labelFileType;
    /** @var string */
    public ?string $labelURL;
    /** @var string Length of parcel */
    public ?string $length;
    /** @var string Weight unit of parcel */
    public ?string $massUnit;
    /** @var JSONContent */
    public ?JSONContent $metadata;
    /** @var string Meta string to add additional info on your shipment/parcel */
    public ?string $metadataText;
    /** @var string */
    public ?string $oldDesi;
    /** @var string */
    public ?string $oldWeight;
    /** @var string */
    public ?string $owner;
    /** @var string */
    public ?string $parcelReferenceCode;
    /** @var string Instead of setting parcel size manually, you can set this to a predefined Parcel Template */
    public ?string $parcelTemplateID;
    /** @var bool */
    public ?bool $productPaymentOnDelivery;
    /** @var string */
    public ?string $providerTotalAmount;
    /** @var string */
    public ?string $qrCodeUrl;
    /** @var string */
    public ?string $refundInvoiceID;
    /** @var string */
    public ?string $responsiveLabelURL;
    /** @var string */
    public ?string $shipmentDate;
    /** @var string */
    public ?string $shipmentID;
    /** @var string */
    public ?string $stateCode;
    /** @var string */
    public ?string $template;
    /** @var bool */
    public ?bool $test;
    /** @var string */
    public ?string $totalAmount;
    /** @var string */
    public ?string $totalAmountLocal;
    /** @var string Tracking number */
    public ?string $trackingNumber;
    /** @var Tracking */
    public ?Tracking $trackingStatus;
    /** @var string */
    public ?string $trackingUrl;
    /** @var string */
    public ?string $updatedAt;
    /** @var bool If true, auto calculates total parcel size using the size of items */
    public ?bool $useDimensionsOfItems;
    /** @var bool If true, auto calculates total parcel weight using the weight of items */
    public ?bool $useWeightOfItems;
    /** @var string Weight of parcel */
    public ?string $weight;
    /** @var string Width of parcel */
    public ?string $width;
}

/** Shipment model */
class Shipment {
    /** @var Offer */
    public ?Offer $acceptedOffer;
    /** @var string */
    public ?string $acceptedOfferID;
    /** @var string */
    public ?string $amount;
    /** @var string */
    public ?string $amountLocal;
    /** @var string */
    public ?string $amountLocalOld;
    /** @var string */
    public ?string $amountLocalTax;
    /** @var string */
    public ?string $amountLocalVat;
    /** @var string */
    public ?string $amountOld;
    /** @var string */
    public ?string $amountTax;
    /** @var string */
    public ?string $amountVat;
    /** @var string */
    public ?string $barcode;
    /** @var string */
    public ?string $bonusBalance;
    /** @var string */
    public ?string $buyerNote;
    /** @var string */
    public ?string $cancelDate;
    /** @var string */
    public ?string $categoryCode;
    /** @var string */
    public ?string $commercialInvoiceUrl;
    /** @var bool */
    public ?bool $createReturnLabel;
    /** @var string */
    public ?string $createdAt;
    /** @var string */
    public ?string $currency;
    /** @var string */
    public ?string $currencyLocal;
    /** @var string */
    public ?string $customsDeclaration;
    /** @var string Desi of parcel */
    public ?string $desi;
    /** @var string */
    public ?string $discountRate;
    /** @var string Distance unit of parcel */
    public ?string $distanceUnit;
    /** @var bool */
    public ?bool $enableAutomation;
    /** @var string */
    public ?string $eta;
    /** @var array */
    public ?array $extraParcels;
    /** @var bool */
    public ?bool $hasError;
    /** @var string Height of parcel */
    public ?string $height;
    /** @var bool */
    public ?bool $hidePackageContentOnTag;
    /** @var string */
    public ?string $id;
    /** @var bool */
    public ?bool $invoiceGenerated;
    /** @var string */
    public ?string $invoiceID;
    /** @var bool */
    public ?bool $isRecipientSmsActivated;
    /** @var bool */
    public ?bool $isReturn;
    /** @var bool */
    public ?bool $isReturned;
    /** @var bool */
    public ?bool $isTrackingOnly;
    /** @var array */
    public ?array $items;
    /** @var string */
    public ?string $labelFileType;
    /** @var string */
    public ?string $labelURL;
    /** @var string */
    public ?string $lastErrorCode;
    /** @var string */
    public ?string $lastErrorMessage;
    /** @var string Length of parcel */
    public ?string $length;
    /** @var string Weight unit of parcel */
    public ?string $massUnit;
    /** @var JSONContent */
    public ?JSONContent $metadata;
    /** @var string Meta string to add additional info on your shipment/parcel */
    public ?string $metadataText;
    /** @var OfferList */
    public ?OfferList $offers;
    /** @var string */
    public ?string $oldDesi;
    /** @var string */
    public ?string $oldWeight;
    /** @var Order */
    public ?Order $order;
    /** @var string */
    public ?string $orderID;
    /** @var int */
    public ?int $organizationShipmentID;
    /** @var string */
    public ?string $owner;
    /** @var string */
    public ?string $packageAcceptedAt;
    /** @var string Instead of setting parcel size manually, you can set this to a predefined Parcel Template */
    public ?string $parcelTemplateID;
    /** @var bool */
    public ?bool $productPaymentOnDelivery;
    /** @var string */
    public ?string $providerAccountID;
    /** @var array */
    public ?array $providerAccountIDs;
    /** @var string */
    public ?string $providerBranchName;
    /** @var string */
    public ?string $providerCode;
    /** @var array */
    public ?array $providerCodes;
    /** @var string */
    public ?string $providerInvoiceNo;
    /** @var string */
    public ?string $providerReceiptNo;
    /** @var string */
    public ?string $providerSerialNo;
    /** @var string */
    public ?string $providerServiceCode;
    /** @var array */
    public ?array $providerServiceCodes;
    /** @var string */
    public ?string $providerTotalAmount;
    /** @var string */
    public ?string $qrCodeUrl;
    /** @var Address */
    public ?Address $recipientAddress;
    /** @var string */
    public ?string $recipientAddressID;
    /** @var string */
    public ?string $refundInvoiceID;
    /** @var string */
    public ?string $responsiveLabelURL;
    /** @var string */
    public ?string $returnAddressID;
    /** @var string */
    public ?string $sellerNote;
    /** @var Address */
    public ?Address $senderAddress;
    /** @var string */
    public ?string $senderAddressID;
    /** @var string */
    public ?string $shipmentDate;
    /** @var string */
    public ?string $statusCode;
    /** @var array */
    public ?array $tags;
    /** @var string */
    public ?string $tenantId;
    /** @var bool */
    public ?bool $test;
    /** @var string */
    public ?string $totalAmount;
    /** @var string */
    public ?string $totalAmountLocal;
    /** @var string Tracking number */
    public ?string $trackingNumber;
    /** @var Tracking */
    public ?Tracking $trackingStatus;
    /** @var string */
    public ?string $trackingUrl;
    /** @var string */
    public ?string $updatedAt;
    /** @var bool If true, auto calculates total parcel size using the size of items */
    public ?bool $useDimensionsOfItems;
    /** @var bool If true, auto calculates total parcel weight using the weight of items */
    public ?bool $useWeightOfItems;
    /** @var string Weight of parcel */
    public ?string $weight;
    /** @var string Width of parcel */
    public ?string $width;
}

/** ShipmentResponse model */
class ShipmentResponse {
    /** @var string */
    public ?string $additionalMessage;
    /** @var string */
    public ?string $code;
    /** @var Shipment */
    public ?Shipment $data;
    /** @var string */
    public ?string $message;
    /** @var bool */
    public ?bool $result;
}

/** Tracking model */
class Tracking {
    /** @var string */
    public ?string $createdAt;
    /** @var string */
    public ?string $hash;
    /** @var string */
    public ?string $id;
    /** @var string */
    public ?string $locationLat;
    /** @var string */
    public ?string $locationLng;
    /** @var string */
    public ?string $locationName;
    /** @var string */
    public ?string $owner;
    /** @var string */
    public ?string $statusDate;
    /** @var string */
    public ?string $statusDetails;
    /** @var bool */
    public ?bool $test;
    /** @var string */
    public ?string $trackingStatusCode;
    /** @var string */
    public ?string $trackingSubStatusCode;
    /** @var string */
    public ?string $updatedAt;
}

/** WebhookUpdateTrackingRequest model */
class WebhookUpdateTrackingRequest {
    /** @var string */
    public ?string $event;
    /** @var string */
    public ?string $metadata;
    /** @var Shipment */
    public ?Shipment $data;
}
