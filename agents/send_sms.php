<?php
require_once '_config.php';

use Twilio\Rest\Client;

$account_sid = "";
$auth_token = "";
$twilio_number = "+19477777293"; 

function sendSMS($to_number, $message) {
    global $account_sid, $auth_token, $twilio_number;

    if (empty($to_number)) {
        return "Error: The recipient phone number is empty.";
    }
    if (empty($message)) {
        return "Error: The message is empty.";
    }

    try {
        $client = new Client($account_sid, $auth_token);
        
        $sms = $client->messages->create(
            $to_number,
            [
                'from' => $twilio_number,
                'body' => $message,
            ]
        );
        
        return "Message sent to $to_number: {$sms->sid}";
    } catch (Exception $e) {
        error_log("Error sending SMS: " . $e->getMessage());
        return "Error: " . $e->getMessage();
    }
}
?>

