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

        echo json_encode(['status' => 'success', 'hotels' => $hotels]);
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

        echo json_encode(['status' => 'success', 'rooms' => $rooms]);
        exit;
    }

    if ($action === 'get_agent_markup' && isset($_POST['agent_id'])) {
        $agent_id = $_POST['agent_id'];
        $markup = $db->get("markups", "user_markup", [
            "user_id" => $agent_id,
            "status" => 1
        ]);

        if ($markup !== null) {
            echo json_encode(['status' => 'success', 'markup' => $markup]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No markup found for the selected agent.']);
        }
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
