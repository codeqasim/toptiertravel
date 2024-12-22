<?php

require_once '_config.php';

header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');

    if (isset($_POST['hotel_id'])) {
        $hotel_id = $_POST['hotel_id'];
        $rooms = $db->select("hotels_rooms", [
            "[>]hotels_settings" => ["room_type_id" => "id"],
            "[>]hotels_rooms_options" => ["room_type_id" => "id"],
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
    }