<?php
// Run with: php -S 127.0.0.1:3000 examples/webhook_server.php
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
} else {
    require __DIR__ . '/../src/Webhooks.php';
    require __DIR__ . '/../src/Models/Models.php';
}

use Geliver\Webhooks;
use Geliver\Models\WebhookUpdateTrackingRequest;
use Geliver\Models\Shipment;

function hydrateShipment(array $data): Shipment {
    $shipment = new Shipment();
    foreach ($data as $key => $value) {
        if (property_exists($shipment, $key)) {
            $shipment->{$key} = $value;
        }
    }
    return $shipment;
}

if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($path !== '/webhooks/geliver' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(404); echo 'not found'; return;
    }
    $raw = file_get_contents('php://input');
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    if (!Webhooks::verify($raw, $headers, false)) {
        http_response_code(400);
        echo 'invalid';
        return;
    }
    $payload = json_decode($raw, true) ?: [];
    $evt = new WebhookUpdateTrackingRequest();
    $evt->event = $payload['event'] ?? '';
    $evt->metadata = $payload['metadata'] ?? null;
    $evt->data = hydrateShipment($payload['data'] ?? []);
    if ($evt->event === 'TRACK_UPDATED') {
        $url = $evt->data->trackingUrl ?? '';
        $number = $evt->data->trackingNumber ?? '';
        error_log("Tracking update: {$url} {$number}");
    }
    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok']);
}
