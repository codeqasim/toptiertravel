<?php

require_once '_config.php';

header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');

if (isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'get_hotels' && isset($_POST['location'])) {
        $location = $_POST['location'];
        $hotels = $db->select("hotels", ["id", "name"], [
            "location" => $location,
            "status" => 1
        ]);

        echo json_encode([
            'status' => 'success',
            'hotels' => $hotels
        ]);
        exit;
    }

    if ($action === 'get_rooms' && isset($_POST['hotel_id'])) {
        $hotel_id = $_POST['hotel_id'];
        $rooms = $db->select("hotels_rooms", [
            "[>]hotels_settings" => ["room_type_id" => "id"],
        ], [
            "hotels_rooms.id",
            "hotels_settings.name",
        ], [
            "hotels_rooms.hotel_id" => $hotel_id,
            "hotels_rooms.status" => 1
        ]);

        echo json_encode([
            'status' => 'success',
            'rooms' => $rooms
        ]);
        exit;
    }

    if ($action === 'get_room_options' && isset($_POST['room_id'])) {
        $room_id = $_POST['room_id'];
        
        $room_options = $db->select("hotels_rooms_options", "*", ["room_id" => $room_id]);
        
        $default_currency = $db->get("currencies", "name", ["default" => 1]);
    
        echo json_encode([
            'status' => 'success',
            'options' => $room_options,
            'currency_name' => $default_currency
        ]);
        exit;
    }
    
}

echo json_encode([
    'status' => 'error',
    'message' => 'Invalid request'
]);
