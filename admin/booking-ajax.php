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
        "supplier" => "hotels",
        // "send_email" => isset($_POST['send_email']) ? 1 : 0, 
        // "send_sms" => isset($_POST['send_sms']) ? 1 : 0, 
        // "send_whatsapp" => isset($_POST['send_whatsapp']) ? 1 : 0    

        "booking_date" => date('Y-m-d'),
    ];

    // Fetch the hotel name using the hotel_id
    $hotel_id = $_POST['hotel'];
    $hotel_data = $db->select("hotels", ["name"], ["id" => $hotel_id]);

    if (!empty($hotel_data)) {
        $params['hotel_name'] = $hotel_data[0]['name'];
    }
    // Fetch the currencies 
    $currency = $db->select("currencies", ["name"], ["default" => 1]);

    if (!empty($currency)) {
        $params['currency_markup'] = $currency[0]['name'];
    }

    // $adults = $db->select("hotels_rooms_options", "*", ["room_id" => $room_id]);
    // if (!empty($adults)) {
    //     $params['adults'] = ['adults'];
    // }

    // $childs = $db->select("hotels_rooms_options", "*", ["room_id" => $room_id]);
    // if (!empty($childs)) {
    //     $params['childs'] = $childs['childs'];
    // }

    // Collect guest details
    $guest_details = [];
    if (isset($_POST['adults_data'])) {
        foreach ($_POST['adults_data'] as $adult) {
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
        foreach ($_POST['childs_data'] as $child) {
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

    // Fetch room details including extra bed charges
    if (isset($_POST['room'])) {
        $room_id = $_POST['room'];

        $room_details = $db->select("hotels_rooms", [
            "[>]hotels_settings" => ["room_type_id" => "id"]
        ], [
            "hotels_rooms.id",
            "hotels_settings.name",
            "hotels_rooms.extra_bed_charges",
            "hotels_rooms.extra_bed",
        ], [
            "hotels_rooms.id" => $room_id,
            "hotels_rooms.status" => 1
        ]);

        $room_options = $db->select("hotels_rooms_options", ["price", "quantity"], [
            "room_id" => $room_id
        ]);

        if (!empty($room_details)) {
            $room_data = [
                "room_id" => $room_details[0]['id'],
                "room_name" => $room_details[0]['name'],
                "room_price" => !empty($room_options) ? $room_options[0]['price'] : "0.00",
                "room_quantity" => !empty($room_options) ? $room_options[0]['quantity'] : "1",
                "room_extrabed_price" => $room_details[0]['extra_bed_charges'],
                "room_extrabed" => $room_details[0]['extra_bed'],
                "room_actual_price" => !empty($room_options) ? $room_options[0]['price'] : "0.00"
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
