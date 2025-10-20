<?php
// Secret key must match the one used in login
$jwt_secret_key = "testingthelogicofjwt129873456";

$allowed_origins = [
    'http://localhost:8888/toptiertravel',
    'http://localhost:3000',          // local dev
    'https://toptiertravel.vercel.app' // production
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
    header('Access-Control-Allow-Credentials: true');
}

header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, OPTIONS');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

// ------------------ Helper ------------------
function base64UrlDecode($input){
    $remainder = strlen($input) % 4;
    if ($remainder) $input .= str_repeat('=', 4 - $remainder);
    return base64_decode(strtr($input, '-_', '+/'));
}

// ------------------ Get JWT cookie ------------------
$jwt = $_COOKIE['admin_jwt'] ?? null;

if (!$jwt) {
    echo json_encode(["status" => false, "message" => "Not logged in"]);
    exit;
}

// Split JWT
$tokenParts = explode('.', $jwt);
if (count($tokenParts) !== 3) {
    echo json_encode(["status" => false, "message" => "Invalid token"]);
    exit;
}

list($header64, $payload64, $signature64) = $tokenParts;

// Verify signature
$expectedSignature = hash_hmac('sha256', "$header64.$payload64", $jwt_secret_key, true);
$expectedSignature64 = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($expectedSignature));

if (!hash_equals($expectedSignature64, $signature64)) {
    echo json_encode(["status" => false, "message" => "Invalid signature"]);
    exit;
}

// Decode payload
$payload = json_decode(base64UrlDecode($payload64), true);

// Check expiration
if ($payload['exp'] < time()) {
    echo json_encode(["status" => false, "message" => "Token expired"]);
    exit;
}

// ------------------ Success ------------------
echo json_encode([
    "status" => true,
    "message" => "Admin session active",
    "user" => $payload
]);
