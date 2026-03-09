<?php
// It's recommended to store secrets as environment variables
// instead of hardcoding them in the script.
$secret = getenv('GITHUB_WEBHOOK_SECRET');

// A dedicated log file for this script.
$log_file = 'webhook_debug.log';

// Log incoming requests
error_log(date('Y-m-d H:i:s') . " - Webhook triggered for " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown IP') . "\n", 3, $log_file);

// --- Initial Checks ---

// Only respond to POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    error_log("Request method was not POST.\n", 3, $log_file);
    exit('Method Not Allowed');
}

$signature = $_SERVER['HTTP_X_HUB_SIGNATURE'] ?? '';
$payload = file_get_contents('php://input');

// Verify GitHub signature
if (!$secret) {
    http_response_code(500);
    error_log("Server configuration error: GITHUB_WEBHOOK_SECRET is not set.\n", 3, $log_file);
    exit('Internal Server Error: Secret not configured.');
}

$hash = 'sha1=' . hash_hmac('sha1', $payload, $secret);
if (!hash_equals($hash, $signature)) {
    http_response_code(403);
    error_log("Invalid signature. Received: '$signature', Calculated: '$hash'\n", 3, $log_file);
    exit('Invalid signature');
}

// Run deploy script
error_log("Signature verified. Executing deployment script.\n", 3, $log_file);
$deploy_script = "/home/sandsl23/public_html/gitcontrol.sandslab.com/deploy.sh";
exec("$deploy_script 2>&1", $output, $return_code);

// Log output
error_log("Deployment script output (Exit Code: $return_code):\n" . implode("\n", $output) . "\n", 3, $log_file);

echo "Deployment triggered";