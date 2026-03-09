<?php
$secret = "S@nds1@b";

// Log incoming requests
file_put_contents('webhook_debug.log', date('Y-m-d H:i:s') . " - webhook triggered\n", FILE_APPEND);

$signature = $_SERVER['HTTP_X_HUB_SIGNATURE'] ?? '';
$payload = file_get_contents('php://input');

// Verify GitHub signature
if ($signature) {
    $hash = 'sha1=' . hash_hmac('sha1', $payload, $secret);
    if (!hash_equals($hash, $signature)) {
        file_put_contents('webhook_debug.log', "Invalid signature\n", FILE_APPEND);
        http_response_code(403);
        exit('Invalid signature');
    }
}

// Run deploy script
exec("/home/sandsl23/public_html/gitcontrol.sandslab.com/deploy.sh 2>&1", $output);

// Log output
file_put_contents('webhook_debug.log', implode("\n", $output) . "\n", FILE_APPEND);

echo "Deployment triggered";