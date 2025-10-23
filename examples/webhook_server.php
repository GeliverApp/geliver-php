<?php
// Run with: php -S 127.0.0.1:3000 sdks/php/examples/webhook_server.php
if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($path !== '/webhooks/geliver' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(404); echo 'not found'; return;
    }
    $raw = file_get_contents('php://input');
    // TODO: verify signature (disabled for now)
    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok']);
}

