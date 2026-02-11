<?php
// HEADERS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header("X-Frame-Options: SAMEORIGIN");


/*==================
THIS FUNCTION RETRIEVES A LIST OF ALL ACTIVE HOTEL MODULES
==================*/
function gethotelmodules()
{
    $conn = openconn(); // Open a connection to the database.
// Select all modules of type 'hotels' from the database
    $respose = $conn->select("modules", ['id', 'name'], ["type" => 'hotels', 'status' => 1]);
    // Create an empty array to store the module data
    $data = [];
    // Loop through the results and add any active modules (excluding the 'flights' module itself)
    foreach ($respose as $module) {
        if ($module['name'] != 'hotels') {
            $data[] = array(
                'name' => $module['name'],
                'id' => $module['id']
            );
        }
    }
    // Return the list of active modules
    return $data;
}

/*==================
THIS FUNCTION RETRIEVES THE HOTEL MODULES DATA FROM DATABASE
==================*/
function gethotelmoduledata($modulename)
{
    $conn = openconn(); // Open a connection to the database.
    // Use the database connection to select data from the 'modules' table where the name matches the input.
    $response = $conn->select("modules", "*", ["name" => $modulename,"active" => "1"]);
    // Return the results.
    return $response;
}

/*==================
THIS FUNCTION IS USED TO SEND A CURL REQUEST TO FETCH THE DATA OF OTHER API'S BY PASSING THE REQUIRED PARAMETERS
==================*/
function sendhotelRequest($req_method = 'GET', $service = '', $payload = [], $_headers = [])
{
    // Get the URL from the payload and remove it from the array
    $url = $payload['endpoint'];
    unset($payload['endpoint']);

    // Set up cURL options
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Return the response instead of outputting it
    curl_setopt($curl, CURLOPT_ENCODING, ""); // Handle all encodings
    curl_setopt($curl, CURLOPT_MAXREDIRS, 10); // Follow up to 10 redirects
    curl_setopt($curl, CURLOPT_TIMEOUT, 120); // Time out after 30 seconds
    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1); // Use HTTP/1.1

    // Set the request method and payload

    if ($req_method == 'POST') {
        curl_setopt($curl, CURLOPT_POST, true); // Use POST method
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload); // Include payload in request
    } else {
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET"); // Use GET method
        $url = $url . "?" . http_build_query($payload); // Add payload to URL as query string
    }

    // Set headers
    $headers[] = "cache-control: no-cache"; // Add default cache control header
    if (!empty($headers)) {
        $headers = array_merge($headers, $_headers); // Merge additional headers
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); // Set headers in request
    }
    // Set the URL and execute the request
    curl_setopt($curl, CURLOPT_URL, $url);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    // Handle errors
    if ($err) {
        $response = $err;
    }

    // Return the response or error message
    return $response;
}

/*==================
THIS FUNCTION IS USED TO SET THE MARKUP PRICE
==================*/
function markup_price($module_id, $price, $date, $location, $user_id = null)
{
    $conn = openconn();
    $type = 'hotels';
    $is_agent = false;

    $response = [
        'price' => $price,
        'markup_type' => 'none',
        'markup_value' => 0,
        'markup_amount' => 0
    ];

    $location = ($conn->select('locations', ['id'], ['city' => $location])[0]['id'] ?? '');

    // Inline markup calculator
    $calculate_markup = function($price, $markup_value, $markup_type) use (&$response) {
        $markup_amount = !empty($markup_value) ? ($price * $markup_value) / 100 : 0;
        $final_price = $price + $markup_amount;

        $response['price'] = $final_price;
        $response['markup_type'] = $markup_type;
        $response['markup_value'] = (float) $markup_value;
        $response['markup_amount'] = round($markup_amount, 2);

        return $response;
    };

    // 1. Check user type if user_id provided
    if ($user_id !== null && $user_id !== '') {
        $user = $conn->select('users', '*', ['user_id' => $user_id, 'status' => 1]);
        
        if (!empty($user)) {
            $is_agent = ($user[0]['user_type'] == 'Agent');

            // Check for user-specific markup first
            $user_markup = $conn->select('markups', '*', [
                'type' => $type,
                'user_id' => $user_id,
                'status' => 1
            ]);

            if (!empty($user_markup)) {
                $markup_value = '';
                if ($is_agent) {
                    $markup_value = $user_markup[0]['user_markup'] ?? $user_markup[0]['b2b_markup'];
                    return $calculate_markup($price, $markup_value, 'user_markup');
                }
            }
        }
    }

    // Prepare date conditions if available
    $date_conditions = [];
    if (!empty($date) && count($date) === 2) {
        $formDate = DateTime::createFromFormat('Y-m-d', $date[0]);
        $formDate = $formDate ? $formDate->format('Y-m-d') : '';
        $toDate = DateTime::createFromFormat('Y-m-d', $date[1]);
        $toDate = $toDate ? $toDate->format('Y-m-d') : '';
        $date_conditions = [
            'from_date[<=]' => $formDate,
            'to_date[>=]' => $toDate,
        ];
    }

    // Determine which markup field to use based on user type
    $markup_field = $is_agent ? 'b2b_markup' : 'b2c_markup';
    $markup_type_label = $is_agent ? 'b2b' : 'b2c';

    // 2. MODULE + LOCATION + DATE MATCH
    if ($module_id && $location) {
        $markup = $conn->select('markups', '*', array_merge([
            'type' => $type,
            'module_id' => $module_id,
            'location' => $location,
            'user_id' => null,
            'status' => 1
        ], $date_conditions));

        if (!empty($markup)) {
            return $calculate_markup($price, $markup[0][$markup_field], $markup_type_label);
        }
    }

    // 3. MODULE + DATE MATCH (no location)
    if ($module_id) {
        $markup = $conn->select('markups', '*', array_merge([
            'type' => $type,
            'module_id' => $module_id,
            'user_id' => null,
            'status' => 1
        ], $date_conditions));

        if (!empty($markup)) {
            return $calculate_markup($price, $markup[0][$markup_field], $markup_type_label);
        }
    }

    // 4. MODULE + LOCATION (no date)
    if ($module_id && $location) {
        $markup = $conn->select('markups', '*', [
            'type' => $type,
            'module_id' => $module_id,
            'location' => $location,
            'user_id' => null,
            'status' => 1
        ]);

        if (!empty($markup)) {
            return $calculate_markup($price, $markup[0][$markup_field], $markup_type_label);
        }
    }

    // 5. MODULE MARKUP (no location, no date)
    if ($module_id) {
        $markup = $conn->select('markups', '*', [
            'type' => $type,
            'module_id' => $module_id,
            'user_id' => null,
            'location' => null,
            'from_date' => null,
            'to_date' => null,
            'status' => 1
        ]);

        if (!empty($markup)) {
            return $calculate_markup($price, $markup[0][$markup_field], $markup_type_label);
        }
    }

    // 6. DEFAULT MARKUP
    $default_markup = $conn->select('markups', '*', [
        'type' => $type,
        'module_id' => null,
        'user_id' => null,
        'location' => null,
        'from_date' => null,
        'to_date' => null,
        'status' => 1
    ]);

    if (!empty($default_markup)) {
        return $calculate_markup($price, $default_markup[0][$markup_field], $markup_type_label . '_default');
    }

    // 7. FINAL FALLBACK
    return $response;
}

function calculateBookingFinancials($markupPrice, $actualPrice, $days, $rooms, $taxPercent = 14) {
    
    $markupPrice = str_replace(',', '', $markupPrice);
    $markupPrice = (float) $markupPrice;

    $actualPrice = str_replace(',', '', $actualPrice);
    $actualPrice = (float) $actualPrice;
    $days        = (int) $days;
    $rooms       = (int) $rooms;
    $taxPercent  = (float) $taxPercent;
    
    // Multiplier for entire stay
    $multiplier = $days * $rooms;
    
    // Calculate per night values
    $markupPerNight = $markupPrice - $actualPrice; 
    $taxAmountPerNight = $actualPrice * ($taxPercent / 100);
    
    // Total selling price per night (markup price + tax calculated on supplier cost)
    $totalSellingPricePerNight = $markupPrice + $taxAmountPerNight;
    
    // Calculate totals for the entire stay
    $totalActualPrice = $actualPrice * $multiplier;
    $totalMarkup = $markupPerNight * $multiplier;
    $totalTax = $taxAmountPerNight * $multiplier;
    $totalSellingPrice = $totalSellingPricePerNight * $multiplier;
    
    // Credit card fee on total selling price (what customer pays)
    $ccFee = ($totalSellingPrice * 0.029) + 0.30;
    
    // Net Profit calculation (matching frontend):
    // Your markup + Tax collected - Credit card fees
    // Note: Agent commission and IATA are excluded as they're not in parameters
    $netProfit = $totalMarkup + $totalTax - $ccFee;
    
    // Subtotal (supplier cost + your markup - tax exclusive)
    $subtotal = $totalActualPrice + $totalMarkup;
    
    return [
        'total_markup_price' => $totalSellingPrice,
        'total_actual_price' => $totalActualPrice,
        'subtotal' => $subtotal,
        'subtotal_per_night' => $markupPrice,
        'cc_fee' => $ccFee,
        'net_profit' => $netProfit,
        'total_tax' => $totalTax,
        'total_markup' => $totalMarkup,
        'selling_price_per_night' => $totalSellingPricePerNight
    ];
}

// ======================== APP
$router->post('featured', function () {

    // INCLUDE CONFIG
    include "./config.php";
    AUTH_CHECK();

    $params = array(
        "status" => 1,
        "listing_type" => "hotels",
    );

    $response = $db->select("hotels", "*", $params);
    echo json_encode($response);

});

/*==================
HOTEL SEARCH API
==================*/
$router->post('hotel_search', function () {

    // INCLUDE CONFIG FILE
    include "./config.php";

    // VALIDATION
    required('city');
    required('checkin');
    required('checkout');
    required('nationality');
    required('adults');
    required('childs');
    required('rooms');
    required('currency');

    $user_id = $_POST['user_id'] ?? "";

    //SAVING DATA FOR AUTHENTICATION PURPOSE
    $location_id = str_replace("-"," ",$_POST["city"]);
    $currency_id = $_POST["currency"];
    $checkin_date = $_POST["checkin"];
    $checkout_date = $_POST["checkout"];
    $adults = $_POST["adults"];
    $childs = $_POST["childs"];

    // Split the REQUEST_URI into an array of strings using the forward slash (/) character as the separator
    $uri = explode('/', $_SERVER['REQUEST_URI']);
// Check if the HTTP_HOST value matches the string "localhost"
    if ($_SERVER['HTTP_HOST'] == 'localhost') {
        // Set the root variable to the concatenation of the protocol (http or https), the current HTTP_HOST value, and the first component of the REQUEST_URI array
        $root = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . '/' . $uri[1];
    } else {
        // Set the root variable to the concatenation of the protocol (http or https) and the current HTTP_HOST value
        $root = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'];
    }

    $rate = $db->select("currencies", ['rate'], ["name" => $currency_id]); // GET RATE FROM CURRENCIES TABLE

    $booking_tax = $db->get('settings',['booking_tax'],['id' => 1]);
    
    //CALULATING DAYS
    $checkin  = new DateTime($_POST['checkin']);
    $checkout = new DateTime($_POST['checkout']);
    $interval = $checkin->diff($checkout);
    $days = $interval->days; 
    $rooms = $_POST['rooms'];
    $data = $db->select("locations", "*", ["city[~]" => $location_id]); // GET CITY DATA FROM THE DATABASE
    if(!empty($data)){
        $city_data = $data;
    }else{
        $city_data = $db->select("locations", "*", ["city[~]" => $_POST["city"]]);
    }
    (isset($city_data[0]['id'])) ? $city_id = $city_data[0]['city'] : $city_id = ""; // CONDITION IF CITY ID DO NOT EXIST
    if($_POST['module_name'] == "hotels"){

        $page_number = $_POST['pagination']; // You can set this dynamically based on user input
        $items_per_page = 50;

        // Calculate the offset based on the page number
        $offset = ($page_number - 1) * $items_per_page;

    // GET DATA OF LOCATION TABLE AND HOTEL TABLE FROM DATABASE
    $hotels = $db->select(
        "hotels",
        [
            "[>]locations" => ["location" => "city"]
        ],
        [
            "hotels.id",
            "locations.city",
            "locations.country",
            "hotels.name",
            "hotels.img",
            "hotels.location",
            "hotels.stars",
            "hotels.status",
            "hotels.location_cords",
            "hotels.address",
            "hotels.user_id"
        ],
        [
            "location" => $city_id,'LIMIT' => [$offset, $items_per_page]
        ]
    );

    foreach ($hotels as $value) {
        
        //GET THE LOWEST PRICE OF ROOM FROM DATABASE ACCORDING TO ROOM ID AND SHOW IT IN HOTEL SEARCH RESULT
        $room_data = $db->select('hotels_rooms', [
            "[>]hotels_rooms_options" => ["id" => "room_id"]
        ], [
            'hotels_rooms_options.price',
            'hotels_rooms_options.adults',
            'hotels_rooms_options.childs'
        ], ['hotel_id' => $value['id']]);
        
        $has_suitable_room = false;
        $lowest_price = null;
        
        if (!empty($room_data)) {
            foreach ($room_data as $room) {
                // Skip if price is null or zero
                if ($room['price'] === null || $room['adults'] === null || $room['childs'] === null || $room['price'] == 0) {
                    continue;
                }
                
                $room_adults = (int)($room['adults'] ?? 0);
                $room_childs = (int)($room['childs'] ?? 0);
                
                // Check if this room can accommodate the requested guests
                if ($room_adults >= $adults && $room_childs >= $childs) {
                    $has_suitable_room = true;
                    // Get the lowest price among suitable rooms
                    if ($lowest_price === null || $room['price'] < $lowest_price) {
                        $lowest_price = $room['price'];
                    }
                }
            }
        }
        
        // Skip this hotel if it doesn't have suitable rooms or no valid price found
        if (!$has_suitable_room || $lowest_price === null) {
            continue;
        }
        
        // CHECK IF LOCATION EXIST
        if (!empty($value['location_cords'])) {
            $cords = explode(",", $value['location_cords']);
        }
        !empty($cords[0]) ? $latitude = $cords[0] : $latitude = null; // CONDITION IF LOCATION LATITUDE CORDS EXIST
        !empty($cords[1]) ? $longitude = $cords[1] : $longitude = null; // CONDITION IF LOCATION LATITUDE CORDS EXIST
        
        // Use the lowest valid price found
        $actual_room_price = $lowest_price * $rate[0]['rate']; // CONVERTING ACTUAL PRICE ACCORDING TO USER SELECTED CURRENCY
        
        //GET MODULE_ID FROM MODULES TABLE
        $module = $db->select('modules', ['id', 'status', 'module_color'], ['name' => 'hotels', 'type' => 'hotels']);
        
        //THIS FUNCTION GETS THE REQUIRED PARAMETERS AND RETURNS THE MARKUP PRICE
        $mprice_markup = markup_price($module[0]['id'], $actual_room_price, array(0 => $checkin_date, 1 => $checkout_date), $city_id, $user_id);
        $markup_mprice = $mprice_markup['price'];
        $markup_value = $mprice_markup['markup_value'] ?? 0;
        $markup_amount = $mprice_markup['markup_amount'] ?? 0;

        $financials = calculateBookingFinancials(
            $markup_mprice, 
            $actual_room_price, 
            (int)$days, 
            (int)$rooms,
            $booking_tax['booking_tax'] ?? 14
        );
        if (!empty($rate[0]['rate'])) {
            if ($actual_room_price != null && $module[0]['status'] == 1) {

                // Check if hotel has a rating
                if (!empty($_POST['rating'])) {
                    $rating = round($value['stars']);
                    if ($rating != $_POST['rating']) {
                        continue; // Skip this hotel if not rated as specified
                    }
                }

                if(!empty($_POST['price_from']) && !empty($_POST['price_to'])){
                    if ( round($markup_mprice * $days, 2) < $_POST['price_from'] || round($markup_mprice * $days, 2) > $_POST['price_to']) {
                        continue; // Skip this hotel if not within the price range
                    }
                }

                (isset($value['img']))?$img = $root."/uploads/".$value['img']:$img = $root."/assets/img/hotel.jpg";

                $hotel_amenities = $db->select('hotels_amenties_fk', [
                    "[>]hotels_settings" => ["amenity_id" => "id"]
                ], [
                    'hotels_settings.name'
                ], [
                    'hotels_amenties_fk.hotel_id' => $value['id']
                ]);

                // Create amenities array
                $amenities = array();
                if (!empty($hotel_amenities)) {
                    foreach ($hotel_amenities as $amenity) {
                        $amenities[] = $amenity['name'];
                    }
                }

                $is_favorite = 0; // Default to not favorite
                if ($user_id != "") { 
                    $favorite_check = $db->select("user_favourites", "*", [
                        "user_id" => $user_id,
                        "item_id" => $value['id'],
                        "module" => "tours"
                    ]);
                    
                    if (!empty($favorite_check)) {
                        $is_favorite = 1;
                    }
                }

                //THIS RESPONSE IS FROM LOCAL DATABSE NAMED AS MANUAL RESPONSE
                $m_response[] = (object)[
                    "hotel_id" => $value['id'],
                    "img" =>$img,
                    "name" => $value['name'],
                    "location" => $value['city'] . ' ' . $value['country'],
                    "address" => $value['address'],
                    "stars" => $value['stars'],
                    "rating" => $value['stars'],
                    "latitude" => $latitude,
                    "longitude" => $longitude,
                    "actual_price" => number_format($actual_room_price*$days*$rooms, 2),
                    "actual_price_per_night" => number_format($actual_room_price, 2),
                    "markup_price" => number_format($financials['total_markup_price'], 2),
                    "markup_price_per_night" => number_format($financials['selling_price_per_night'], 2),
                    "markup_percentage" => $markup_value,
                    "markup_amount" => number_format($markup_amount, 2),
                    "currency" => $currency_id,
                    "booking_currency" => $currency_id,
                    "service_fee" => "0",
                    "supplier_name" => "hotels",
                    "supplier_id" => $module[0]['id'],
                    "redirect" => "",
                    "booking_data" => (object)[],
                    "color" => $module[0]['module_color'],
                    "favorite" => $is_favorite,
                    "amenities" => $amenities
                ];
            }
        } else {
            $m_response = [];
        }
    }
}else {
        /*=======================
        HOTEL SEARCH RESPONSE OF OF API'S THROUGH CURL REQUEST
        =======================*/

        // Split the REQUEST_URI into an array of strings using the forward slash (/) character as the separator
        $uri = explode('/', $_SERVER['REQUEST_URI']);
        // Check if the HTTP_HOST value matches the string "localhost"
        if ($_SERVER['HTTP_HOST'] == 'localhost') {
            // Set the root variable to the concatenation of the protocol (http or https), the current HTTP_HOST value, and the first component of the REQUEST_URI array
            $root = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . '/' . $uri[1];
        } else {
            // Set the root variable to the concatenation of the protocol (http or https) and the current HTTP_HOST value
            $root = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'];
        }

        //$Multithreadinghotel = gethotelmodules(); // CALLS THE FUNTION TO GET ALL THE ACTIVE HOTEL MODULES
        //if (!empty($Multithreadinghotel)) {
        //foreach ($Multithreadinghotel as $value) {

        $getvalue = gethotelmoduledata($_POST['module_name']); // Get module data for each active module
        if(empty($getvalue)){
            $response = array("status" => false, "message" => "No Hotels data", "response" => (object)[],'total'=>'');
            echo json_encode($response);
            exit;
        }
        $module_name = $getvalue[0]['name'];
        $module_id = $getvalue[0]['id'];
        // Determine whether the module is in development or production mode
        if ($getvalue[0]['dev_mode'] == 1) {
            $env = 'production';
        } else {
            $env = 'dev';
        }

        //VALIDATION
        $param = array(
            "endpoint" => api_modules ."/hotels/" . strtolower($module_name) . "/api/v1/hotel_search",
            "city" => $_POST['city'],
            "checkin" => $checkin_date,
            "checkout" => $checkout_date,
            "nationality" => $_POST['nationality'],
            "adults" => $_POST['adults'],
            "childs" => $_POST['childs'],
            "child_age" => $_POST['child_age'],
            "rooms" => $_POST['rooms'],
            "language" => $_POST['language'],
            "currency" => $currency_id,
            'c1' => $getvalue[0]['c1'],
            'c2' => $getvalue[0]['c2'],
            'c3' => $getvalue[0]['c3'],
            'c4' => $getvalue[0]['c4'],
            'c5' => $getvalue[0]['c5'],
            "env" => $env,
            "pagination" => $_POST['pagination'],
            "rating" => $_POST['rating'],
            "price_from" => $_POST['price_from'],
            "price_to" => $_POST['price_to'],
            "required_stars" => "('4','5')",
        );

        if(empty($getvalue[0]['c1'])) {
            include "creds.php";
        }

        $c_response = (array) json_decode(sendhotelRequest('POST', 'hotel_search', $param), true); // Adding true to json_decode to ensure associative array
        
        // Check if 'response' key exists and is an array
        if (!empty($c_response) && isset($c_response['response']) && is_array($c_response['response'])) {

            foreach ($c_response['response'] as $values) {
                $actual_price = $values['price'] * $rate[0]['rate']; //ACTUAL PRICE OF HOTEL

                //THIS FUNCTION GETS THE REQUIRED PARAMETERS AND RETURNS THE MARKUP PRICE
                $cprice_markup = markup_price($module_id, $actual_price, array(0 => $checkin_date, 1 => $checkout_date), $city_id, $user_id);
                $markup_cprice = $cprice_markup['price'];
                $markup_value = $cprice_markup['markup_value'] ?? 0;
                $markup_amount = $cprice_markup['markup_amount'] ?? 0;
                $amenities = array();

                $financials = calculateBookingFinancials(
                    $markup_cprice / $days, 
                    $actual_price / $days, 
                    (int)$days, 
                    (int)$rooms,
                    $booking_tax['booking_tax'] ?? 14
                );
                
                $is_favorite = 0; // Default to not favorite
                if ($user_id != "") { 
                    $favorite_check = $db->select("user_favourites", "*", [
                        "user_id" => $user_id,
                        "item_id" => $values['hotel_id'],
                        "module" => "tours"
                    ]);
                    
                    if (!empty($favorite_check)) {
                        $is_favorite = 1;
                    }
                }

                // MAKING FINAL RESPONSE OF HOTEL SEARCH OF CURL REQUEST FOR MERGING INTO MANUAL HOTEL SEARCH
                $curl_response[] = [
                    "hotel_id" => $values['hotel_id'],
                    "img" => $values['img'],
                    "name" => $values['name'],
                    "location" => $values['location'],
                    "address" => $values['address'],
                    "stars" => $values['stars'],
                    "rating" => $values['rating'],
                    "latitude" => $values['latitude'],
                    "longitude" => $values['longitude'],
                    "actual_price" => number_format($actual_price * $rooms, 2),
                    "actual_price_per_night" => number_format($actual_price / $days, 2),
                    "markup_price" => number_format($financials['total_markup_price'], 2),
                    "markup_price_per_night" => number_format($financials['selling_price_per_night'], 2),
                    "markup_percentage" => $markup_value,
                    "markup_amount" => number_format($markup_amount, 2),
                    "currency" => $currency_id,
                    "booking_currency" => $currency_id,
                    "service_fee" => $values['service_fee'],
                    "supplier_name" => $module_name,
                    "supplier_id" => $module_id,
                    "redirect" => $values['redirect'],
                    "booking_data" => $values['booking_data'],
                    "color" => $getvalue[0]['module_color'],
                    "favorite" => $is_favorite,
                    "amenities" => $amenities
                ];
            }
            //}
            //}
        }
    }
    //THIS CODE CHECKS THAT MANUAL AND CURL RESPONSES ARE AVAILABLE IF AVAILABLE THEN IT MERGES THEM AND SHOW THE FINAL RESPONSE


    if (!empty($m_response) && !empty($curl_response)) {
        $response = array("status" => true, "message" => "Hotels Data", "response" => array_merge($m_response, $curl_response));
    } elseif (!empty($m_response)) {
        $response = array("status" => true, "message" => "Curl Hotels Data", "response" => $m_response,'total'=>count($m_response));
    } elseif (!empty($curl_response)) {
        $response = array("status" => true, "message" => "Curl Hotels Data", "response" => $curl_response,'total'=>$c_response['total']);
    } else {
        $response = array("status" => false, "message" => "No Hotels data", "response" => (object)[],'total'=>'');
    }

    echo json_encode($response);

});

/*=======================
HOTELS_DETAILS API
=======================*/
$router->post('hotel_details', function () {
    
    // INCLUDE CONFIG
    include "./config.php";

    // VALIDATION
    required('hotel_id');
    required('checkin');
    required('checkout');
    required('adults');
    required('childs');
    required('child_age');
    required('rooms');
    required('language');
    required('currency');
    required('nationality');

    // SAVING DATA GOT FROM VALIDATION
    $hotel_id = $_POST["hotel_id"];
    $checkin = $_POST["checkin"];
    $checkout = $_POST["checkout"];
    $adults = $_POST["adults"];
    $childs = $_POST["childs"];
    $child_age = $_POST["child_age"];
    $rooms = $_POST["rooms"];
    $language_name = $_POST["language"];
    $currency = $_POST["currency"];
    $nationality = $_POST["nationality"];
    $supplier_name = $_POST["supplier_name"];
    $user_id = $_POST['user_id'] ?? "";

    $rate = $db->select("currencies", ["rate", "name"], ["name" => $currency]);
    $booking_tax = $db->get('settings',['booking_tax'],['id' => 1]);
    
    //CALULATING DAYS
    $checkin_date  = new DateTime($_POST['checkin']);
    $checkout_date = new DateTime($_POST['checkout']);
    $interval = $checkin_date->diff($checkout_date);
    $days = $interval->days; 
    //GET DATA FROM hotels_ROOMS TABLE AND hotels TABLE
    $details = $db->select("hotels", "*", [
        "id" => $hotel_id
    ]);

    if (!empty($details)) {
        // GET DATA FROM LOCATIONS TABLE AND hotels TABLE
//        $locations = $db->select(
//            "locations",
//            [
//                "[>]hotels" => ["city" => "location"]
//            ],
//            [
//                "hotels.id",
//                "locations.city",
//                "locations.country"
//            ]
//
//        );

        $locations = $db->select("locations","*",["city" => $details[0]['location']]);

        //GET DATA FROM hotels_IMAGES TABLE
        $room_imgs = $db->select("hotels_images", ['img'], ["hotel_id" => $hotel_id]);
        $room_imgs1 = "";
        foreach ($room_imgs as $value) {
            foreach ($value as $index) {
                $room_imgs1 .= upload_url.''.$index . " ";
            }
        }

        $room_imgs_array = explode(" ", trim($room_imgs1));

        //GET HOTEL AMENITIES FROM hotels TABLE AND hotels_SETINGS TABLE
        $hotel_amenities = $db->select("hotels_amenties_fk", ['amenity_id'], ["hotel_id" => $hotel_id]);
        if (!empty($hotel_amenities[0]['amenity_id'])) {
            $test = [];
            foreach ($hotel_amenities as $values) {
//                foreach ($values as $value) {
//                    $test = str_replace('"', '', $value);
//                    $test = str_replace('[', '', $test);
//                    $test = str_replace(']', '', $test);
//                    $test = explode(',', $test);
//                }
                $hotel_amenities_array = $db->select("hotels_settings", ['name'], ["id" => $values['amenity_id']]);
//               dd();
//                foreach ($hotel_amenities_array as $values) {
                    $amenities[] = $hotel_amenities_array[0]['name'];
                //}
            }

        } else {
            $amenities = [];
        }

        //GET ROOM AMENITIES FROM hotels_SETTINGS TABLE
        $room_amenities = $db->select("hotels_settings", ['name'], ["setting_type" => "room_amenities"]);

        $room_amenities1 = "";
        foreach ($room_amenities as $value) {
            foreach ($value as $index) {
                $room_amenities1 .= $index . "_";
            }
        }

        $room_amenities_array = explode("_", trim($room_amenities1));

        //GET MODULE_ID FROM MODULES TABLE
        $module_id = $db->select('modules', ['id'], ['name' => $supplier_name, 'type' => 'hotels']);
        $no_of_rooms = $rooms;
        //GET DATA FROM hotels_ROOMS TABLE AND hotels_SETTINGS TABLE OF ROOMS
        $rooms = $db->select(
            "hotels_rooms",
            [
                "[>]hotels_settings" => ["room_type_id" => "id"]
            ],
            [
                "hotels_rooms.id",
                "hotels_settings.name",
                "hotels_rooms.thumb_img",
                "hotels_rooms.room_quantity",
                "hotels_rooms.extra_bed",
                "hotels_rooms.extra_bed_charges",
            ],
            [
                "setting_type" => "rooms_type",
                "hotel_id" => $hotel_id
            ]
        );

        $room_details = []; $actual_room_price = 0; $markup_price = 0;
        foreach ($rooms as $index) {
            $room_price = $db->select("hotels_rooms_options", ['room_id','price', 'adults', 'childs'], ["room_id" => $index['id']]);
        
            // Check if there are any valid room options that meet capacity requirements
            $has_valid_option = false;
            $lowest_valid_price = null;
            
            foreach ($room_price as $rp) {
                // Skip null values
                if ($rp['price'] === null || $rp['adults'] === null || $rp['childs'] === null || $rp['price'] == 0) {
                    continue;
                }
                
                // Check capacity
                if ((int)$rp['adults'] >= $adults && (int)$rp['childs'] >= $childs) {
                    $has_valid_option = true;
                    if ($lowest_valid_price === null || $rp['price'] < $lowest_valid_price) {
                        $lowest_valid_price = $rp['price'];
                    }
                }
            }
            
            // Skip this room if no valid options found
            if (!$has_valid_option || $lowest_valid_price === null) {
                continue;
            }
            
            $actual_room_price = $lowest_valid_price * $rate[0]['rate']; // CONVERTING ACTUAL PRICE ACCORDING TO USER SELECTED CURRENCY
            $price_markup = markup_price($module_id[0]['id'], $actual_room_price, array(0 => $checkin, 1 => $checkout), $locations[0]['city'], $user_id);
            $markup_price = $price_markup['price'];
            $markup_type = $price_markup['markup_type'] ?? 0; // Fixed typo: was '$markup_type'
            $markup_value = $price_markup['markup_value'] ?? 0;
            $markup_amount = $price_markup['markup_amount'] ?? 0;

            $room_financials = calculateBookingFinancials(
                    $markup_price, 
                    $actual_room_price, 
                    (int)$days, 
                    (int)$no_of_rooms,
                    $booking_tax['booking_tax'] ?? 14
                );
            
            //GET ROOM OPTIONS FROM hotels_ROOMS_OPTIONS TABLE
            $room_options = $db->select(
                "hotels_rooms_options",
                [
                    "room_id",
                    "price",
                    "quantity",
                    "adults",
                    "childs",
                    "cancellation",
                    "breakfast",
                ],
                [
                    "room_id" => $index['id']
                ]
            );
            
            $options = [];
            foreach ($room_options as $key => $value) {
                // Skip if price, adults, or childs are null or if price is zero
                if ($value['price'] === null || $value['adults'] === null || $value['childs'] === null || $value['price'] == 0) {
                    continue;
                }
                
                // Check if this room option can accommodate the requested guests
                $room_adults = (int)$value['adults'];
                $room_childs = (int)$value['childs'];
                
                // Skip if this option doesn't meet the capacity requirements
                if ($room_adults < $adults || $room_childs < $childs) {
                    continue;
                }
        
                $option_price = $value['price'] * $rate[0]['rate'];
                //THIS FUNCTION GETS THE REQUIRED PARAMETERS AND RETURNS THE MARKUP PRICE
        
                $option_price_markup_room = markup_price($module_id[0]['id'], $option_price, array(0 => $checkin, 1 => $checkout), $locations[0]['city'], $user_id);
                $markup_room_option_price = $option_price_markup_room['price'];
                $option_markup_type = $option_price_markup_room['markup_type'];
                $option_markup_value = $option_price_markup_room['markup_value'];
                $option_markup_amount = $option_price_markup_room['markup_amount'];
        
                $financials = calculateBookingFinancials(
                    $markup_room_option_price, 
                    $option_price, 
                    (int)$days, 
                    (int)$no_of_rooms,
                    $booking_tax['booking_tax'] ?? 14
                );

                $netProfit = (float) ($financials['net_profit'] ?? 0);
                $markupValue = (float) ($option_markup_value ?? 0);

                $net_profit = $option_markup_type === 'user_markup'
                        ? $netProfit - $markupValue
                        : $netProfit;
                
                $options[] = [
                    "id" => (string) $value['room_id'],
                    "currency" => $rate[0]['name'],
                    "price" => number_format($option_price * $days * $no_of_rooms, 2),
                    "per_day" => number_format($option_price, 2),
                    "markup_price" => number_format($financials['total_markup_price'], 2), 
                    "markup_price_per_night" => number_format($markup_room_option_price, 2),
                    "service_fee" => 10,
                    "quantity" => $value['quantity'],
                    "adults" => $value['adults'],
                    "child" => $value['childs'],
                    "children_ages" => $child_age,
                    "bookingurl" => "",
                    "booking_data" => "",
                    "extrabeds_quantity" => $rooms[0]['extra_bed'],
                    "extrabed_price" => $rooms[0]['extra_bed_charges'],
                    "cancellation" => $value['cancellation'],
                    "breakfast" => $value['breakfast'],
                    "dinner" => "",
                    "board" => "",
                    "markup_type" => $option_markup_type,
                    "markup_percentage" => $option_markup_value,
                    "markup_amount" => number_format($option_markup_amount * $days, 2),
                    "room_booked" => false,
                    "subtotal" => number_format($financials['subtotal'], 2),
                    "subtotal_per_night" => number_format($financials['subtotal_per_night'], 2),
                    "cc_fee" => number_format($financials['cc_fee'], 2),
                    "net_profit" => number_format($net_profit , 2),
                ];
            }
            
            // If no suitable options found after filtering, skip this room
            if (empty($options)) {
                continue;
            }
        
            isset($details[0]['refundable']) ? $refundable = $details[0]['refundable'] : $refundable = "";
        
            $room_amenitie = [];
            $room_amenities = $db->select("rooms_amenties_fk", ['amenity_id'], ["room_id" => $index['id']]);
            if (!empty($room_amenities[0]['amenity_id'])) {
                foreach ($room_amenities as $values) {
                    $room_amenities_array = $db->select("hotels_settings", ['name'], ["id" => $values['amenity_id']]);
                    $room_amenitie[] = $room_amenities_array[0]['name'];
                }
            } else {
                $room_amenitie = [];
            }
        
            //MAKING OBJECT OF ROOMS
            $room_details[] = (object) [
                "id" => (string) $index['id'],
                "name" => $index['name'],
                "actual_price" => number_format($actual_room_price * $days * $no_of_rooms, 2),
                "actual_price_per_night" => number_format($actual_room_price, 2),
                "markup_price" => number_format($room_financials['total_markup_price'], 2),
                "markup_price_per_night" => number_format($markup_price, 2),
                "markup_type" => $markup_type,
                "markup_percentage" => $markup_value,
                "markup_amount" => number_format($markup_amount * $days * $no_of_rooms, 2),
                "service_fee" => 0,
                "currency" => $currency,
                "refundable" => $refundable,
                "refundable_date" => "",
                "img" => (!empty($index['thumb_img']) && @getimagesize(upload_url . $index['thumb_img'])) ? upload_url . $index['thumb_img'] : "https://toptiertravel.vip/assets/img/hotel.jpg",
                "amenities" => $room_amenitie,
                "options" => $options
            ];
        }

        $lang = $_POST['language'];
        $language_id = $db->select("languages", "*", array('language_code' => strtolower($lang)));
        $hotel_translation = $db->select("hotels_translations", "*", array("hotel_id" => $details[0]['id'],'language_id'=>$language_id[0]['id']));
        
        //SHOWING FINAL RESULTS AS AN OBJECT OF HOTEL ROOMS
        $response = (object) [
            "id" => $details[0]['id'],
            "name" => $details[0]['name'],
            "city" => $locations[0]['city'],
            "country" => $locations[0]['country'],
            "address" => $details[0]['address'],
            "stars" => $details[0]['stars'],
            "ratings" => $details[0]['rating'],
            "longitude" => $details[0]['location_cords'],
            "latitude" => $details[0]['location_cords'],
            "desc" => !empty($hotel_translation[0]['desc']) ? htmlentities(strip_tags($hotel_translation[0]['desc'])) : html_entity_decode($details[0]['desc']),
            "img" => $room_imgs_array,
            "amenities" => $amenities,
            "supplier_name" => "hotels",
            "supplier_id" => $module_id[0]['id'],
            "rooms" => $room_details,
            "checkin" => $details[0]['checkin'],
            "checkout" => $details[0]['checkout'],
            "policy" => !empty($hotel_translation[0]['policy']) ? htmlentities(strip_tags($hotel_translation[0]['policy'])) : html_entity_decode($details[0]['policy']),
            "booking_age_requirement" => $details[0]['booking_age_requirement'],
            "cancellation" => $details[0]['cancellation'],
            "tax_percentage" => $booking_tax['booking_tax'] ?? 14,
            "hotel_phone" => $details[0]['hotel_phone'],
            "hotel_email" => $details[0]['hotel_email'],
            "hotel_website" => $details[0]['hotel_website'],
            "discount" => 0,
        ];
    } else {
        // Get module data for each active module
        $getvalue = gethotelmoduledata($supplier_name);

        // Determine whether the module is in development or production mode
        if ($getvalue[0]['dev_mode'] == 1) {
            $env = 'production';
        } else {
            $env = 'dev';
        }

        //VALIDATION
        $param = array(
           "endpoint" => api_modules ."/hotels/" . strtolower($supplier_name) . "/api/v1/hotel_details",
           // "endpoint" => "https://api.phptravels.com/hotels/".strtolower($supplier_name)."/api/v1/hotel_details",
            "hotel_id" => $hotel_id,
            "checkin" => $checkin,
            "checkout" => $checkout,
            "nationality" => $nationality,
            "adults" => $adults,
            "childs" => $childs,
            "child_age" => $child_age,
            "rooms" => $rooms,
            "language" => $language_name,
            "currency" => $currency,
            "supplier_name" => $supplier_name,
            'c1' => $getvalue[0]['c1'],
            'c2' => $getvalue[0]['c2'],
            'c3' => $getvalue[0]['c3'],
            'c4' => $getvalue[0]['c4'],
            'c5' => $getvalue[0]['c5'],
            "env" => $env
        );

        $no_of_rooms = $rooms;

        if(empty($getvalue[0]['c1'])) {
            include "creds.php";
        }
        $curl = sendhotelRequest('POST', 'hotel_details', $param); //SENDS A CURL REQUEST TO FETCH THE HOTEL DETAIL RESPONSE ACCORDING TO GIVEN SUPPLIER NAME

        $hotel_details = json_decode($curl); //DECODE THE FETCHED RESPONSE
        
        if ($hotel_details != null) {
            //DEFINING THE REQUIRED VARIABLES
            $rooms = [];
            $option_price_markup_room = [];
            $markup_type = '';
            $price_markup = [];
            $actual_price = 0;
            $actual_option_price = 0;
            $markup_price = 0;
            $markup_room_option_price = 0;

            //MAKING ROOM AND ROOM OPTION RESPONSE WITH MARKUP PRICES
            foreach ($hotel_details->rooms as $value) {
                $markup_room_price = 0;
                $actual_price = $value->price * $rate[0]['rate']; //ACTUAL PRICE OF HOTEL ROOMS ACCORDING TO USER SELECTED CURRENCY
                
                //MARKUP PRICE FOR HOTEL ROOMS
                $price_markup = markup_price($getvalue[0]['id'], $actual_price, array(0 => $checkin, 1 => $checkout), $hotel_details->city, $user_id);
                $markup_price = $price_markup['price'];
                $room_markup_type = $price_markup['markup_type'];
                $room_markup_value = $price_markup['markup_value'];
                $room_markup_amount = $price_markup['markup_amount'];

                $room_financials = calculateBookingFinancials(
                        number_format($markup_price / $days, 2), 
                        number_format($actual_price / $days, 2), 
                        (int)$days, 
                        (int)$no_of_rooms,
                        $booking_tax['booking_tax'] ?? 14
                    );
                
                $options = [];
                $options_array = $value->options;

                foreach ($options_array as $key => $values) {
                    //MARKUP PRICE FOR HOTEL ROOM OPTIONS
                    $actual_option_price = $values->price * $rate[0]['rate']; //ACTUAL PRICE OF HOTEL ROOMS ACCORDING TO USER SELECTED CURRENCY
                    $option_price_markup_room = markup_price($getvalue[0]['id'], $actual_option_price, array(0 => $checkin, 1 => $checkout), $hotel_details->city, $user_id);
                    $markup_room_option_price = $option_price_markup_room['price'];
                    $markup_type = $option_price_markup_room['markup_type'];
                    $markup_value = $option_price_markup_room['markup_value'];
                    $markup_amount = $option_price_markup_room['markup_amount'];
                   
                    $financials = calculateBookingFinancials(
                        number_format($markup_room_option_price / $days, 2), 
                        number_format($actual_option_price / $days, 2), 
                        (int)$days, 
                        (int)$no_of_rooms,
                        $booking_tax['booking_tax'] ?? 14
                    );

                    $netProfit = (float) ($financials['net_profit'] ?? 0);
                    $markupValue = (float) ($markup_value ?? 0);

                    $net_profit = $markup_type === 'user_markup'
                        ? $netProfit - $markupValue
                        : $netProfit;
                    
                    $options[] = [
                        "id" => $values->id,
                        "currency" => $param['currency'],
                        "price" => number_format($actual_option_price * $no_of_rooms, 2),
                        "per_day" => number_format($actual_option_price / $days, 2),
                        "markup_price" => number_format($financials['total_markup_price'], 2), 
                        "markup_price_per_night" => number_format($markup_room_option_price / $days, 2),
                        "service_fee" => $values->service_fee,
                        "quantity" => $values->quantity,
                        "adults" => $values->adults,
                        "child" => $values->child,
                        "children_ages" => $values->children_ages,
                        "bookingurl" => $values->bookingurl,
                        "booking_data" => $values->booking_data,
                        "extrabeds_quantity" => $values->extrabeds_quantity,
                        "extrabed_price" => $values->extrabed_price,
                        "cancellation" => $values->cancellation,
                        "breakfast" => $values->breakfast,
                        "dinner" => isset($values->dinner) ? $values->dinner : "0",
                        "board" => $values->board,
                        "room_booked" => $values->room_booked,
                        "child_ages" => $child_age,
                        "markup_type" => $markup_type,
                        "markup_percentage" => $markup_value,
                        "markup_amount" => number_format($markup_amount * $no_of_rooms, 2),
                        "ratecomments" => isset($values->rateComments) ? $values->rateComments : '',
                        "subtotal" => number_format($financials['subtotal'], 2),
                        "subtotal_per_night" => number_format($financials['subtotal_per_night'], 2),
                        "cc_fee" => number_format($financials['cc_fee'], 2),
                        "net_profit" => number_format($net_profit, 2),
                        "cancellation_policy" => $values->cancellation_policy ?? '',
                        "additional_info" => $values->additional_info ?? '',
                    ];
                }

                if ($actual_price != 0.00 && !empty($options)) {
                    $rooms[] = [
                        "id" => $value->id,
                        "name" => $value->name,
                        "actual_price" => number_format($actual_price * $no_of_rooms, 2),
                        "actual_price_per_night" => number_format($actual_price / $days, 2),
                        "markup_price" => number_format($room_financials['total_markup_price'], 2),
                        "markup_price_per_night" => number_format($markup_price / $days, 2),
                        "markup_type" => $room_markup_type,
                        "markup_percentage" => $room_markup_value,
                        "markup_amount" => number_format($room_markup_amount * $no_of_rooms, 2),
                        "service_fee" => $value->service_fee,
                        "currency" => $param['currency'],
                        "refundable" => $value->refundable,
                        "refundable_date" => $value->refundable_date,
                        "img" => $value->images,
                        "amenities" => $value->amenities,
                        "options" => $options,
                    ];
                }
            }

            //FINAL RESPONSE OF HOTEL DETAILS
            $response = (object) [
                "id" => $hotel_details->id,
                "name" => $hotel_details->name,
                "city" => $hotel_details->city,
                "country" => $hotel_details->country,
                "address" => $hotel_details->address,
                "stars" => $hotel_details->stars,
                "ratings" => $hotel_details->rating,
                "longitude" => $hotel_details->longitude.",".$hotel_details->latitude,
                "latitude" => $hotel_details->latitude,
                "desc" => $hotel_details->desc,
                "img" => $hotel_details->img,
                "amenities" => $hotel_details->amenities,
                "supplier_name" => $getvalue[0]['name'],
                "supplier_id" => $getvalue[0]['id'],
                "rooms" => $rooms,
                "checkin" => $hotel_details->checkin,
                "checkout" => $hotel_details->checkout,
                "booking_age_requirement" => $hotel_details->booking_age_requirement,
                "policy" => $hotel_details->policy,
                "cancellation" => $hotel_details->cancellation,
                "tax_percentage" => $booking_tax['booking_tax'] ?? 14,
                "hotel_phone" => $hotel_details->hotel_phone,
                "hotel_email" => $hotel_details->hotel_email,
                "hotel_website" => $hotel_details->hotel_website,
                "discount" => $hotel_details->discount,
            ];

        } else {
            $response = "SOMETHING WENT WRONG PLEASE CONTACT SUPPORT FOR FURTHER ASSISSTENCE";
        }
    }

    //INBFO HUB
    if (is_object($response)) {
        $brand_stories = $db->select('brand_stories', '*', ['status' => 1]);

        foreach ($brand_stories as &$story) {
            if (!empty($story['picture'])) {
                $story['picture'] = upload_url . '' . $story['picture'];
            }
        }

        // Get only active hotel FAQs (optional: filter by category if needed)
        $faqs = $db->select('faqs', '*');

        // Append to response object
        $response->brand_stories = $brand_stories;
        $response->faqs = $faqs;
    }

    echo json_encode($response);
});

/*=======================
HOTEL_BOOKING REQUEST API
=======================*/
$router->post('hotel_booking', function () {
    // CONFIG FILE
    include "./config.php";

    // Prepare sanitized input
    $param = array(
        'booking_ref_no'      => $_POST["booking_ref_no"] ?? '',
        'booking_date'        => date('Y-m-d'),
        'booking_status'      => 'pending',
        'price_original'      => $_POST["price_original"] ?? '0',
        'price_markup'        => $_POST["price_markup"] ?? '0',
        'toptier_fee'         => $_POST["toptier_fee"] ?? '0',
        'agent_fee'           => $_POST["agent_fee"] ?? '0',
        'vat'                 => $_POST["vat"] ?? '0',
        'tax'                 => $_POST["tax"] ?? '0',
        'gst'                 => $_POST["gst"] ?? '0',
        'first_name'          => trim($_POST["first_name"] ?? ''),
        'last_name'           => trim($_POST["last_name"] ?? ''),
        'email'               => filter_var($_POST["email"] ?? '', FILTER_SANITIZE_EMAIL),
        'address'             => trim($_POST["address"] ?? ''),
        'phone_country_code'  => trim($_POST["phone_country_code"] ?? ''),
        'phone'               => trim($_POST["phone"] ?? ''),
        'country'             => trim($_POST["country"] ?? ''),
        'stars'               => $_POST["stars"] ?? '0',
        'hotel_id'            => $_POST["hotel_id"] ?? '',
        'hotel_name'          => trim($_POST["hotel_name"] ?? ''),
        'hotel_phone'         => trim($_POST["hotel_phone"] ?? ''),
        'hotel_email'         => filter_var($_POST["hotel_email"] ?? '', FILTER_SANITIZE_EMAIL),
        'hotel_website'       => trim($_POST["hotel_website"] ?? ''),
        'hotel_address'       => trim($_POST["hotel_address"] ?? ''),
        'room_data'           => $_POST["room_data"] ?? '',
        'location'            => trim($_POST["location"] ?? ''),
        'location_cords'      => $_POST["location_cords"] ?? '',
        'hotel_img'           => $_POST["hotel_img"] ?? '',
        'checkin'             => $_POST["checkin"] ?? '',
        'checkout'            => $_POST["checkout"] ?? '',
        'booking_nights'      => $_POST["booking_nights"] ?? '1',
        'adults'              => $_POST["adults"] ?? '1',
        'childs'              => $_POST["childs"] ?? '0',
        'child_ages'          => $_POST["child_ages"] ?? '',
        'currency_original'   => $_POST["currency_original"] ?? 'USD',
        'currency_markup'     => $_POST["currency_markup"] ?? 'USD',
        'payment_date'        => $_POST["payment_date"] ?? null,
        'cancellation_request' => '0',
        'cancellation_status' => '0',
        'cancellation_response' => null,
        'cancellation_date'   => null,
        'cancellation_error'  => null,
        'booking_data'        => $_POST["booking_data"] ?? '',
        'payment_status'      => 'unpaid',
        'supplier'            => $_POST["supplier"] ?? '',
        'transaction_id'      => $_POST["transaction_id"] ?? '',
        'user_id'             => $_POST["user_id"] ?? '',
        'user_data'           => $_POST["user_data"] ?? '',
        'guest'               => $_POST["guest"] ?? '',
        'nationality'         => $_POST["nationality"] ?? '',
        'payment_gateway'     => $_POST["payment_gateway"] ?? '',
        'module_type'         => 'hotels',
        'pnr'                 => $_POST["pnr"] ?? '',
        'booking_response'    => $_POST["booking_response"] ?? '',
        'error_response'      => $_POST["error_response"] ?? '',
        'agent_id'            => $_POST["agent_id"] ?? '',
        'net_profit'          => $_POST["net_profit"] ?? '0',
        'booking_note'        => $_POST["booking_note"] ?? '',
        'supplier_payment_status' => $POST['supplier_payment_status'] ?? 'unpaid',
        'supplier_due_date'   => $_POST['supplier_due_date'] ?? date('Y-m-d', strtotime('+3 days')),
        'cancellation_terms'  => $_POST["cancellation_terms"] ?? '',
        'supplier_cost'       => $_POST["supplier_cost"] ?? '0',
        'supplier_id'         => $_POST["supplier_id"] ?? '',
        'supplier_payment_type' => $_POST["supplier_payment_type"] ?? '',
        'customer_payment_type' => $_POST["customer_payment_type"] ?? '',
        'iata'                => $_POST["iata"] ?? '',
        'agent_commission_status' => $_POST['agent_commission_status'] ?? 'pending',
        'subtotal'            => $_POST["subtotal"] ?? '0',
        'agent_payment_type'  => 'pending',
        'agent_payment_status' => 'pending',
        'agent_payment_date'  => null,
    );

    if (!empty($_POST['supplier'])) {

        $supplierName = trim($_POST['supplier']);
    
        $supplierId = $db->get('user', 'user_id', [
            'first_name' => $supplierName
        ]);
    
        if ($supplierId !== false) {
            $param['supplier_id'] = $supplierId;
        }
    }

    if (empty($_POST["agent_id"])) {
        unset($param['agent_id']);
    }

    // Check if booking_ref_no is provided
    $bookingRef = trim($param['booking_ref_no']);
    
    // Check if booking exists
    $existing = $db->get("hotels_bookings", "*", ["booking_ref_no" => $bookingRef]);

    if ($existing) {
        // Update existing booking
        $db->update("hotels_bookings", $param, ["booking_ref_no" => $bookingRef]);
        $action = "updated";
        $booking_id = $existing["booking_id"];
        
    } else {
        // Insert new booking (if ref doesn't exist)
        $db->insert("hotels_bookings", $param);
        $action = "created";
        $booking_id = $db->id();

        $data = (json_decode($_POST["user_data"]));
        $data = (object) array_merge((array) $data, array('booking_ref_no' => $param['booking_ref_no'],'hotel_name' => $param['hotel_name']));
        // HOOK
        $hook = "hotels_booking";
        include "./hooks.php";
    }
    
    // $data = (json_decode($_POST["user_data"]));
    // $data = (object) array_merge((array) $data, array('booking_ref_no' => $param['booking_ref_no'],'hotel_name' => $param['hotel_name']));
    // // HOOK
    // $hook = "hotels_booking";
    // include "./hooks.php";
    echo json_encode(array('status' => true, 'id' => $booking_id, 'booking_ref_no' => $param['booking_ref_no'], 'user_email' => $param['email']));
});

/*=======================
HOTEL_BOOKING INVOICE API
=======================*/
$router->post('hotels/invoice', function () {

    // CONFIG FILE
    include "./config.php";

    // VALIDATION
    required('booking_ref_no');
    $booking_ref_no = $_POST["booking_ref_no"];

    $response = $db->select("hotels_bookings", "*", ['booking_ref_no' => $booking_ref_no]); // SELECT THE BOOKING DATA FROM DATABASE ACCORDING TO BOOKING REFERENCE NUMBER
    if (!empty($response)) {
        echo json_encode(array('status' => true, 'response' => $response)); // RETURN INVOICE IF BOOKING REFERENCE NUMBER IS CORRECT
    } else {
        echo json_encode(array('status' => false, 'response' => 'The booking reference number in invalid')); // RETURN IF BOOKING REFERENCE NUMBER IS CORRECT
    }
});

/*=======================
HOTEL_BOOKING PAYMENT UPDATE API
=======================*/
$router->post('hotels/booking_update', function () {
    include "./config.php";

    $booking_ref_no = $_POST['booking_ref_no'];
    $data_hotel = $db->select("hotels_bookings", "*", ['booking_ref_no' => $booking_ref_no]); // SELECT THE BOOKING DATA FROM DATABASE ACCORDING TO BOOKING REFERENCE NUMBER
    
    if (!empty($data_hotel)) {
        $gethotels = gethotelmoduledata($data_hotel[0]['supplier']);

        if ($gethotels[0]['dev_mode'] == 1) {
            $evn_hotel = 'pro';
        } else {
            $evn_hotel = 'dev';
        }


        $param = array(
            'c1' => $gethotels[0]['c1'],
            'c2' => $gethotels[0]['c2'],
            'c3' => $gethotels[0]['c3'],
            'c4' => $gethotels[0]['c4'],
            'c5' => $gethotels[0]['c5'],
            'env' => $evn_hotel,
            "booking_ref_no" => $data_hotel[0]['booking_ref_no'],
            "booking_data" => $data_hotel[0]['booking_data'],
            "guest" => $data_hotel[0]['guest'],
            "nationality" => $data_hotel[0]['nationality'],
            "user_data" => $data_hotel[0]['user_data'],
            "hotel_id" => $data_hotel[0]['hotel_id'],
            "checkin" => $data_hotel[0]['checkin'],
            "checkout" => $data_hotel[0]['checkout'],

        );

        if(empty($getvalue[0]['c1'])) {
            include "creds.php";
        }
            $url = api_modules ."/hotels/".strtolower($gethotels[0]['name'])."/api/v1/booking";
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param)); // Encode the data in the proper format
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $resp_hotel = curl_exec($ch);
            if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);
            }
            curl_close($ch);

        $booking_hotel = json_decode($resp_hotel);

        $db->update("hotels_bookings", [
            "pnr" => $booking_hotel->Prn,
            "booking_response" => $resp_hotel,
            "payment_status" => $_POST['payment_status'],
            "booking_status" => $_POST['booking_status'],
            "transaction_id" => $_POST['transaction_id'],
            "payment_gateway" => $_POST['payment_gateway'],
            "error_response" => $booking_hotel->response_error,
        ], [
            "booking_ref_no" => $_POST['booking_ref_no']
        ]);

        $db->insert("transactions", [
            "description" => $_POST['transaction_desc'],
            "user_id" =>  $_POST['transaction_user_id'],
            "trx_id" => $_POST['transaction_id'],
            "type" => $_POST['transaction_type'],
            "date" => date('Y-m-d'),
            "amount" => $_POST['transaction_amount'],
            "payment_gateway" =>  $_POST['transaction_payment_gateway'],
            "currency" => $_POST['transaction_currency'] ,
        ]);

        $user = (json_decode($data_hotel[0]['user_data']));
        $data = (object)array('booking_ref_no' => $param['booking_ref_no']);
        $hook = "hotels/update_booking";
        include "./hooks.php";

        if ($booking_hotel->Prn) {
            echo json_encode(array('status' => true, 'Prn' => $booking_hotel->Prn));
        } else {
            echo json_encode(array('status' => false, 'Prn' => $booking_hotel->Prn));
        }
    }else{
        echo json_encode(array('status' => false, 'response' => 'Please valid booking ref no'));
    }
});

/*=======================
HOTEL_CANCELLATION REQUEST API
=======================*/

$router->post('hotels/cancellation', function () {
    include "./config.php";

    // VALIDATION
    required('booking_ref_no');
    $booking_ref_no = $_POST["booking_ref_no"];

    // Get booking data from database
    $data_hotel = $db->select("hotels_bookings", "*", ['booking_ref_no' => $booking_ref_no]);
    
    if (empty($data_hotel)) {
        echo json_encode(array('status' => false, 'message' => 'Invalid booking reference number'));
        return;
    }

    // Check if already cancelled
    if ($data_hotel[0]['booking_status'] == 'cancelled') {
        echo json_encode(array('status' => false, 'message' => 'Booking is already cancelled'));
        return;
    }

    // Check if cancellation request already exists
    if ($data_hotel[0]['cancellation_request'] == 1) {
        echo json_encode(array('status' => false, 'message' => 'Cancellation request already exists'));
        return;
    }
    
    // Update database with cancellation request flag
    $db->update("hotels_bookings", [
        "cancellation_request" => 1,
    ], [
        "booking_ref_no" => $booking_ref_no
    ]);

    $booking = $db->select("hotels_bookings", '*', ["booking_ref_no" => $booking_ref_no]);
    $user = (json_decode($booking[0]['user_data']));
    $data = (object) array('booking_ref_no' => $booking_ref_no);
    
    // HOOK for cancellation request
    $hook = "hotels/cancellation_request";
    include "./hooks.php";

    echo json_encode(array(
        'status' => true, 
        'message' => 'Cancellation request received successfully',
        'booking_ref_no' => $booking_ref_no,
    ));
});

/*=======================
HOTEL_CANCELLATION CONFIRMATION API
=======================*/
$router->post('hotels/cancellation/confirm', function () {
    include "./config.php";

    // VALIDATION
    required('booking_ref_no');
    $booking_ref_no = $_POST["booking_ref_no"];

    // Get booking data from database
    $data_hotel = $db->select("hotels_bookings", "*", ['booking_ref_no' => $booking_ref_no]);
    
    if (empty($data_hotel)) {
        echo json_encode(array('status' => false, 'message' => 'Invalid booking reference number'));
        return;
    }

    // Check if cancellation request exists
    if ($data_hotel[0]['cancellation_request'] != 1) {
        echo json_encode(array('status' => false, 'message' => 'No cancellation request found. Please create a cancellation request first.'));
        return;
    }

    // Check if already cancelled
    if ($data_hotel[0]['booking_status'] == 'cancelled') {
        echo json_encode(array('status' => false, 'message' => 'Booking is already cancelled'));
        return;
    }

    // Check if supplier requires manual processing
    if (strtolower($data_hotel[0]['supplier']) == 'hotels' || empty($data_hotel[0]['pnr']) || $data_hotel[0]['pnr'] == null) {
        // Manual cancellation - just update status
        $db->update("hotels_bookings", [
            "cancellation_status" => 1,
            "booking_status" => "cancelled",
            "cancellation_date" => date('Y-m-d H:i:s'),
        ], [
            "booking_ref_no" => $booking_ref_no
        ]);

        $booking = $db->select("hotels_bookings", '*', ["booking_ref_no" => $booking_ref_no]);
        $user = (json_decode($booking[0]['user_data']));
        $data = (object) array('booking_ref_no' => $param['booking_ref_no']);
        // HOOK for manual cancellation confirmation
        $hook = "hotels/cancellation_confirmed";
        include "./hooks.php";

        echo json_encode(array(
            'status' => true, 
            'message' => 'Cancellation confirmed. Manual processing completed.',
            'booking_ref_no' => $booking_ref_no,
        ));
        return;
    }

    // Get hotel module data
    $gethotels = gethotelmoduledata($data_hotel[0]['supplier']);

    if ($gethotels[0]['dev_mode'] == 1) {
        $evn_hotel = 'pro';
    } else {
        $evn_hotel = 'dev';
    }

    // Prepare parameters for cancellation API
    if(strtolower($data_hotel[0]['supplier']) == 'stuba'){
        $booking_response = json_decode($data_hotel[0]['booking_response'], true);
        $api_booking_response = json_decode($booking_response['response'], true);
        $booking_id = $api_booking_response['BookingCreateResult']['Booking']['Id'];
        $param = array(
            'c1' => $gethotels[0]['c1'],
            'c2' => $gethotels[0]['c2'],
            'c3' => $gethotels[0]['c3'],
            'c4' => $gethotels[0]['c4'],
            'c5' => $gethotels[0]['c5'],
            'env' => $evn_hotel,
            'booking_ref_no' => $data_hotel[0]['booking_ref_no'],
            'booking_id' => $booking_id,
            'hotel_id' => $data_hotel[0]['hotel_id'],
            'checkin' => $data_hotel[0]['checkin'],
            'checkout' => $data_hotel[0]['checkout'],
        );
    } else {
        $param = array(
            'c1' => $gethotels[0]['c1'],
            'c2' => $gethotels[0]['c2'],
            'c3' => $gethotels[0]['c3'],
            'c4' => $gethotels[0]['c4'],
            'c5' => $gethotels[0]['c5'],
            'env' => $evn_hotel,
            'booking_ref_no' => $data_hotel[0]['booking_ref_no'],
            'booking_id' => $data_hotel[0]['pnr'], // PNR from supplier
            'hotel_id' => $data_hotel[0]['hotel_id'],
            'checkin' => $data_hotel[0]['checkin'],
            'checkout' => $data_hotel[0]['checkout'],
        );
    }

    // Include credentials if needed
    if(empty($gethotels[0]['c1'])) {
        include "creds.php";
    }

    // Call cancellation API
    $url = api_modules . "/hotels/" . strtolower($gethotels[0]['name']) . "/api/v1/booking-cancellation";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    
    $resp_cancel = curl_exec($ch);
    
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        echo json_encode(array('status' => false, 'message' => 'cURL error: ' . $error));
        return;
    }
    
    curl_close($ch);

    // Decode response
    $cancel_response = json_decode($resp_cancel);

    // Update database based on cancellation result
    if ($cancel_response && $cancel_response->status === true) {
        // Successful cancellation
        $db->update("hotels_bookings", [
            "cancellation_status" => 1,
            "booking_status" => "cancelled",
            "cancellation_response" => $resp_cancel,
            "cancellation_date" => date('Y-m-d H:i:s'),
        ], [
            "booking_ref_no" => $booking_ref_no
        ]);

        $booking = $db->select("hotels_bookings", '*', ["booking_ref_no" => $booking_ref_no]);
        $user = (json_decode($booking[0]['user_data']));
        $data = (object) array('booking_ref_no' => $booking_ref_no);
        // HOOK for successful cancellation
        $hook = "hotels/cancellation_confirmed";
        include "./hooks.php";

        echo json_encode(array(
            'status' => true, 
            'message' => 'Booking cancelled successfully',
            'booking_ref_no' => $booking_ref_no,
            'response' => $cancel_response
        ));
    } else {
        // Cancellation failed
        $error_message = isset($cancel_response->message) ? $cancel_response->message : 'Cancellation failed';
        
        $db->update("hotels_bookings", [
            "cancellation_response" => $resp_cancel,
            "cancellation_error" => $error_message,
        ], [
            "booking_ref_no" => $booking_ref_no
        ]);

        $booking = $db->select("hotels_bookings", '*', ["booking_ref_no" => $booking_ref_no]);

        echo json_encode(array(
            'status' => false, 
            'message' => $error_message,
            'booking_ref_no' => $booking_ref_no,
            'response' => $cancel_response
        ));
    }
});

$router->post('financial_details', function () {
    
    // INCLUDE CONFIG
    include "./config.php";

    // VALIDATION
    required('checkin');
    required('checkout');
    required('rooms');
    required('user_id');
    required('option');

    // SAVING DATA GOT FROM VALIDATION
    $checkin = $_POST["checkin"];
    $checkout = $_POST["checkout"];
    $rooms = $_POST["rooms"];
    $user_id = $_POST['user_id'];
    $option_data = json_decode($_POST['option'], true); // DECODE OPTION JSON

    // Validate option data
    if (!$option_data || !isset($option_data['id'])) {
        $response = [
            "status" => "error",
            "message" => "Invalid option data"
        ];
        echo json_encode($response);
        return;
    }

    $booking_tax = $db->get('settings',['booking_tax'],['id' => 1]);
    
    // CALCULATING DAYS
    $checkin_date  = new DateTime($checkin);
    $checkout_date = new DateTime($checkout);
    $interval = $checkin_date->diff($checkout_date);
    $days = $interval->days; 
    $no_of_rooms = $rooms;

    // Extract data from option
    $option_id = $option_data['id'];
    $currency = $option_data['currCode'] ?? 'USD';
    $actual_price_per_night = floatval($option_data['price']) / $no_of_rooms; // Calculate actual price per night
    $actual_price_total = $actual_price_per_night * $days * $no_of_rooms;

    // Get currency rate
    $rate = $db->select("currencies", ["rate", "name"], ["name" => $currency]);
    if (empty($rate)) {
        // Use default rate 1 if currency not found
        $rate = [['rate' => 1, 'name' => $currency]];
    }

    // Apply markup to the actual price
    $markup_result = markup_price('', $actual_price_per_night, array(0 => $checkin, 1 => $checkout), '', $user_id);
    $markup_price_per_night = $markup_result['price'];
    $markup_type = $markup_result['markup_type'] ?? '';
    $markup_value = $markup_result['markup_value'] ?? 0;
    $markup_amount = $markup_result['markup_amount'] ?? 0;

    // Calculate financials
    $financials = calculateBookingFinancials(
        $markup_price_per_night, 
        $actual_price_per_night, 
        (int)$days, 
        (int)$no_of_rooms,
        $booking_tax['booking_tax'] ?? 14
    );

    $netProfit = (float) ($financials['net_profit'] ?? 0);
    $markupValue = (float) ($markup_value ?? 0);

    $net_profit = $markup_type === 'user_markup'
        ? $netProfit - $markupValue
        : $netProfit;

    // Build updated option response
    $updated_option = [
        "id" => $option_id,
        "currency" => $currency,
        "price" => number_format($actual_price_total, 2),
        "per_day" => number_format($actual_price_per_night, 2),
        "markup_price" => number_format($financials['total_markup_price'], 2),
        "markup_price_per_night" => number_format($markup_price_per_night, 2),
        "service_fee" => $option_data['service_fee'] ?? 0,
        "quantity" => $option_data['quantity'] ?? 1,
        "adults" => $option_data['adults'] ?? 2,
        "child" => $option_data['child'] ?? 0,
        "children_ages" => $option_data['children_ages'] ?? "",
        "bookingurl" => $option_data['bookingurl'] ?? "",
        "booking_data" => $option_data['booking_data'] ?? [],
        "extrabeds_quantity" => $option_data['extrabeds_quantity'] ?? 0,
        "extrabed_price" => $option_data['extrabed_price'] ?? 0,
        "cancellation" => $option_data['cancellation'] ?? "0",
        "breakfast" => $option_data['breakfast'] ?? "0",
        "dinner" => $option_data['dinner'] ?? "0",
        "board" => $option_data['board'] ?? "Room only",
        "room_booked" => $option_data['room_booked'] ?? false,
        "markup_type" => $markup_type,
        "markup_percentage" => $markup_value,
        "markup_amount" => number_format($markup_amount * $days, 2),
        "ratecomments" => $option_data['ratecomments'] ?? '',
        "subtotal" => number_format($financials['subtotal'], 2),
        "subtotal_per_night" => number_format($financials['subtotal_per_night'], 2),
        "cc_fee" => number_format($financials['cc_fee'], 2),
        "net_profit" => number_format($net_profit, 2),
        "total_tax" => number_format($financials['total_tax'], 2),
        "total_markup_amount" => number_format($financials['total_markup'], 2),
    ];

    $response = [
        "status" => "success",
        "data" => $updated_option
    ];

    echo json_encode($response);
});
?>