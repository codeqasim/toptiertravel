<?php
// Required — prevents redirect to 404
define('IGNORE_ROUTES', true);

session_start();

// If your session is stored encoded like your login code does
require_once "../_config.php"; // adjust path if needed

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:3000'); // your Next.js local
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit; // CORS preflight
}

// Check session login
if (isset($_SESSION['phptravels_backend_user'])) {
    echo json_encode([
        "status" => true,
        "message" => "Admin session active"
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Not logged in"
    ]);
}
