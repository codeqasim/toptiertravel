<?php
require_once '_config.php';
require_once '../vendor/autoload.php';

header('Content-Type: application/json');

try {
    $db = openConn();
    $res = $db->get("settings", "*", ["LIMIT" => 1]);

    if (!$res) {
        throw new Exception("Pusher settings not found in DB.");
    }

    $options = [
        'cluster' => $res['pusher_cluster'] ?: 'ap2',
        'useTLS' => true
    ];

    $pusher = new Pusher\Pusher(
        $res['pusher_key'],
        $res['pusher_secret'],
        $res['pusher_app_id'],
        $options
    );

    $channel = 'test_channel';
    $event   = 'test_event';
    $payload = [
        'message' => '✅ Pusher connection successful!',
        'timestamp' => date('Y-m-d H:i:s')
    ];

    $pusher->trigger($channel, $event, $payload);

    echo json_encode([
        'success' => true,
        'message' => 'Test event sent successfully!',
        'channel' => $channel,
        'event' => $event
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to send Pusher test event.',
        'error' => $e->getMessage()
    ]);
}
