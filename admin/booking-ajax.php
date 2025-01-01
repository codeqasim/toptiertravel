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

        echo json_encode([
            'status' => 'success',
            'options' => $room_options
        ]);
        exit;
    }
}
if ($action === 'submit_booking') {

    $params = [
        "booking_ref_no" => date('Ymdhis').rand(),
        "location" => $_POST['location'],
        "hotel_id" => $_POST['hotel'], 
        // "room_data" => $_POST['room'], 
        // "room_option" => $_POST['room_option'],  
        "checkin" => $_POST['checkin'],
        "checkout" => $_POST['checkout'],
        // "travelers" => $_POST['travelers'],
        "adults" => $_POST['adults'],
        "childs" => $_POST['childs'],
        "price_markup" => $_POST['price'],
        "first_name" => $_POST['first_name'],
        "last_name" => $_POST['last_name'],
        "email" => $_POST['email'],
        "phone" => $_POST['phone'],
        // "send_email" => isset($_POST['send_email']) ? 1 : 0, 
        // "send_sms" => isset($_POST['send_sms']) ? 1 : 0, 
        // "send_whatsapp" => isset($_POST['send_whatsapp']) ? 1 : 0    

        "booking_date" => date('Y-m-d'),
    ];

    // Fetch the hotel name using the hotel_id
    $hotel_id = $_POST['hotel'];
    $hotel_data = $db->select("hotels", ["name"], ["id" => $hotel_id]);

    // Add hotel name to the parameters
    if (!empty($hotel_data)) {
        $params['hotel_name'] = $hotel_data[0]['name'];
    }

    // Collect guest details for adults and children
    $guest_details = [];
    if (isset($_POST['adults_data'])) {
        foreach ($_POST['adults_data'] as $index => $adult) {
            $guest_details[] = [
                "traveller_type" => "adult",
                "title" => $adult['title'],
                "first_name" => $adult['firstname'],
                "last_name" => $adult['lastname'],
                "age" => ""
            ];
        }
    }
    if (isset($_POST['childs_data'])) {
        foreach ($_POST['childs_data'] as $index => $child) {
            $guest_details[] = [
                "traveller_type" => "child",
                "title" => "",
                "first_name" => $child['firstname'],
                "last_name" => $child['lastname'],
                "age" => $child['age']
            ];
        }
    }
    $params['guest'] = json_encode($guest_details);

    if (isset($_POST['room'])) {
        $room_id = $_POST['room'];

        $room_details = $db->select("hotels_rooms", [
            "[>]hotels_settings" => ["room_type_id" => "id"]
        ], [
            "hotels_rooms.id",
            "hotels_settings.name",
            "hotels_rooms_options.price"
            // "hotels_rooms.quantity",
            // "hotels_rooms.extrabed_price",
            // "hotels_rooms.actual_price"
        ], [
            "hotels_rooms.id" => $room_id,
            "hotels_rooms.status" => 1
        ]);

        if (!empty($room_details)) {
            $room_data = [
                "room_id" => $room_details[0]['id'],
                "room_name" => $room_details[0]['name'],
                "room_price" => $room_details[0]['price'],
                // "room_quantity" => $room_details[0]['quantity'],
                // "room_extrabed_price" => $room_details[0]['extrabed_price'],
                // "room_extrabed" => 0,  
                // "room_actual_price" => $room_details[0]['actual_price']
            ];

            $params['room_data'] = json_encode([$room_data]);
        }
    }

    $user_data = [
        "first_name" => $_POST['first_name'],
        "last_name" => $_POST['last_name'],
        "email" => $_POST['email'],
        "phone" => $_POST['phone'],
        "address" => $_POST['address'],
        "nationality" => $_POST['nationality'],
        "country_code" => $_POST['country_code'],
        "user_id" => $_POST['user_id']
    ];
    $params['user_data'] = json_encode($user_data);

    $result = $db->insert("hotels_bookings", $params);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Booked successfully!'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed booking. Please try again.'
        ]);
    }
    exit;
}

echo json_encode([
    'status' => 'error',
    'message' => 'Invalid request'
]);
