<?php

use Medoo\Medoo;

require_once '../_config.php'; // Centralized configuration file

// Headers for JSON output and CORS
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');

// Function to output JSON and halt
function dd($data) {
    echo json_encode($data);
    exit;
}

// Determine host based on whitelist checking
$whitelist = ['127.0.0.1', '::1'];
$host = in_array($_SERVER['REMOTE_ADDR'], $whitelist) ? "localhost" : "api-4b8a059e84";

// Database connection setup and handling
function openConn() {
    global $host; // Ensure to use the global variable
    // Use Medoo for database operations
    return new Medoo([
        'type' => 'mysql',
        'host' => "localhost",
        'database' => dbname,
        'username' => username,
        'password' => password
    ]);
}

$db = openConn(); // Centralized database connection

// MAILER function to handle email sending
function MAILER($template, $titles, $content, $receiver_email, $receiver_name) {
    $db = openConn();
    $res = $db->get("settings", "*", ["LIMIT" => 1]); // More efficient query

    $sender_name = $res['email_sender_name'];
    $sender_email = $res['email_sender_email'];
    $website_url = $res['site_url'];
    $appname = $res['business_name'];

    ob_start();
    include "../email/{$template}.php";
    $views = ob_get_clean();

    $params = [
        "api_key" => $res['email_api_key'],
        "to" => ["{$receiver_name} <{$receiver_email}>"],
        "sender" => "{$sender_name} <{$sender_email}>",
        "subject" => $titles,
        "html_body" => $views,
    ];


    switch ($res['default_email_service']) {
        case "smtp":
            include "_smtp.php";
            break;
        case "smtp2go":
            $ch = curl_init("https://api.smtp2go.com/v3/email/send");
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => json_encode($params),
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            ]);
            $result = curl_exec($ch);
            curl_close($ch);
            break;
    }
}

function PUSHER() {
    // Open DB connection
    $db = openConn();

    // Fetch settings
    $res = $db->get("settings", "*", ["LIMIT" => 1]);

    if (!$res) {
        throw new Exception("Pusher settings not found in database.");
    }

    // Extract credentials dynamically
    $app_id = $res['pusher_app_id'];
    $app_key = $res['pusher_key'];
    $app_secret = $res['pusher_secret'];
    $cluster = $res['pusher_cluster'] ?: 'ap2'; // fallback cluster

    // Create Pusher instance
    $options = [
        'cluster' => $cluster,
        'useTLS' => true
    ];

    return new Pusher\Pusher($app_key, $app_secret, $app_id, $options);
}

// Log function to insert logs into database
function logs($user_id, $log_type, $datetime, $desc) {
    $db = openConn();
    $db->insert("logs", [
        "user_ip" => $_SERVER['REMOTE_ADDR'],
        "user_id" => $user_id,
        "type" => $log_type,
        "datetime" => $datetime,
        "description" => $desc
    ]);
}

// Function to check if required parameters are set
function required($val) {
    if (!isset($_REQUEST[$val]) || trim($_REQUEST[$val]) === "") {
        echo "$val - param or value missing";
        exit;
    }
}

// Retrieve client IP address
function get_client_ip() {
    foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'] as $key) {
        if ($ip = getenv($key)) {
            return $ip;
        }
    }
    return 'UNKNOWN';
}

// Authentication check function placeholder
function AUTH_CHECK() {

// API KEY AUTH LOOKUP
// if(isset($_POST['api_key']) && trim($_POST['api_key']) !== "") {} else {
//   $respose = array ( "status"=>false, "message"=>"api_key param or value missing" );
//   echo json_encode($respose);
// die; }

// if ($_POST['api_key'] == api_key) {} else  {
//   $respose = array ( "status"=>false, "message"=>"api_key invalid" );
//   echo json_encode($respose);
// die; }

}

