
<?php
require_once '_config.php';

use Twilio\Rest\Client;

$account_sid = "";
$auth_token = "";
$twilio_number = "+19477777293"; 

function sendSMS($to_number, $message) {
    global $account_sid, $auth_token, $twilio_number; 
    
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
        return "Error: " . $e->getMessage();
    }
}
?>
