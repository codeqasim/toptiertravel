<?php
// HEADERS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header("X-Frame-Options: SAMEORIGIN");


/*==================
RSERVATION DASHBOARD API
==================*/
$router->post('agent/dashboard/reservations', function () {
    
    // INCLUDE CONFIG
    include "./config.php";

    required('user_id');

    $user_id = $_POST["user_id"];
    $interval = isset($_POST['interval']) ? $_POST['interval'] : null ;

    // CHECK USER
    $user = $db->select("users", "*", [ "user_id" => $user_id]);
    $settings = $db->select("settings", "*", [ "user_id" => $user_id]);
    if (isset($user[0])) {
        $user[0]['profile_photo'] = !empty($user[0]['profile_photo']) 
            ? 'https://toptiertravel.site/assets/uploads/' . $user[0]['profile_photo']
            : null; 
    }
    if (isset($user[0]) && isset($settings[0])) {
        $user[0]['business_logo'] = !empty($settings[0]['header_logo_img']) 
            ? 'https://toptiertravel.site/assets/uploads/' . $settings[0]['header_logo_img']
            : null; 
            
        $user[0]['favicon'] = !empty($settings[0]['favicon_img']) 
            ? 'https://toptiertravel.site/assets/uploads/' . $settings[0]['favicon_img']
            : null; 
    }
        if(isset($user[0])){
            $user_data = (object)$user[0];
            $conditions = ["agent_id" => $user_id];

            // FETCH ALL BOOKINGS FOR THIS AGENT WITH CONDITIONS
            $hotel_sales = $db->select("hotels_bookings", "*", $conditions);

            if(isset($hotel_sales) && !empty($hotel_sales)){

                $total_reservations = 0;
                $total_revenue = 0;
                $average_sale_amount = 0;
                $avg_stay_length = 0;
                $total_sales = 0;
                $total_bookings = 0;
                $total_stay_length = 0;
                $sale_data = [];

                $total_revenue = 0.0;

                foreach ($hotel_sales as $hotel_sale) {
                    $price_markup  = isset($hotel_sale['price_markup']) ? (float)$hotel_sale['price_markup'] : 0.0;
                    $price_original = isset($hotel_sale['price_original']) ? (float)$hotel_sale['price_original'] : 0.0;
                    $agent_fee      = isset($hotel_sale['agent_fee']) ? (float)$hotel_sale['agent_fee'] : 0.0;

                    $total_revenue += $price_markup - $price_original - $agent_fee;
                    $total_sales += $price_markup;
                    $total_bookings++;

                    // --- Stay length calculation ---
                    if (!empty($hotel_sale['checkin']) && !empty($hotel_sale['checkout'])) {
                        $checkin  = DateTime::createFromFormat('d-m-Y', $hotel_sale['checkin']);
                        $checkout = DateTime::createFromFormat('d-m-Y', $hotel_sale['checkout']);

                        if ($checkin && $checkout) {
                            $interval = $checkin->diff($checkout);
                            $total_stay_length += $interval->days; // add number of days for this booking
                        }
                    }
                }

                // Avoid division by zero
                $avg_stay_length = $total_bookings > 0 ? round($total_stay_length / $total_bookings) : 0;

                // AVERAGE SALE AMOUNT CALCULATION
                $average_sale_amount = $total_bookings > 0 ? round($total_sales / $total_bookings,2) : 0; // PREVENT DIVISION BY ZERO

                $total_reservations = count($hotel_sales);

                $this_week = date('Y-m-d', strtotime('last sunday', strtotime('tomorrow')));

                $last_seven_days_sales = array_filter($hotel_sales, function($sale) use ($this_week) {
                    return isset($sale['booking_date']) && $sale['booking_date'] >= $this_week;
                });

                // Re-index array so keys are sequential
                $last_seven_days_sales = array_values($last_seven_days_sales);
                
                if(!empty($last_seven_days_sales)){
                    foreach ($last_seven_days_sales as $last_seven_days_sale) {
                        
                        // Guest
                        $guest_name = 'N/A';
                        if (!empty($last_seven_days_sale['guest'])) {
                            $guest = json_decode($last_seven_days_sale['guest']);
                            if (!empty($guest[0]->title) && !empty($guest[0]->first_name) && !empty($guest[0]->last_name)) {
                                $guest_name = $guest[0]->title . ' ' . $guest[0]->first_name . ' ' . $guest[0]->last_name;
                            }
                        }

                        // CALCULATE STAY DURATION
                        $duration_nights = 0;
                        if (!empty($last_seven_days_sale['checkin']) && !empty($last_seven_days_sale['checkout'])) {
                            try {
                                $checkinDate  = new DateTime($last_seven_days_sale['checkin']);
                                $checkoutDate = new DateTime($last_seven_days_sale['checkout']);
                                $duration_nights = $checkinDate->diff($checkoutDate)->days;
                            } catch (Exception $e) {
                                $duration_nights = 0;
                            }
                        }


                        $sale_data[] = [
                            'guest' => $guest_name,
                            'hotel_name' => $last_seven_days_sale['hotel_name'],
                            'location' => $last_seven_days_sale['location'],
                            'travel_date' => date('M d-m, Y',strtotime($last_seven_days_sale['booking_date'])),
                            'nights' => $duration_nights,
                            'status' => $last_seven_days_sale['booking_status']
                        ];
                    }
                }

                $data = [
                    'total_reservations' => $total_reservations,
                    'total_revenue' => $total_revenue,
                    'average_sale_amount' => $average_sale_amount,
                    'avg_stay_length' => $avg_stay_length,
                    'last_seven_days_sales' => $sale_data
                ];

                $response = array ( "status"=>true, "message"=>"data is retrieved", "data"=> $data );
            } else {
                $response = array ( "status"=>false, "message"=>"no sales found", "data"=> null );
            }
        } else {
            $response = array ( "status"=>false, "message"=>"no user found", "data"=> null );
        }
    echo json_encode($response);
});

/*==================
RESERVATION DASHBOARD CALENDER API
==================*/
$router->post('agent/dashboard/reservations/calender', function () {
    
    include "./config.php";

    required('user_id');

    $user_id = $_POST["user_id"];
    $month = isset($_POST['month']) ? $_POST['month'] : null;
    $year = isset($_POST['year']) ? $_POST['year'] : null;

    // Check if user exists
    $user = $db->select("users", "*", ["user_id" => $user_id]);
    if (!isset($user[0])) {
        $response = ["status" => false, "message" => "no user found", "data" => null];
        echo json_encode($response);
        return;
    }

    $conditions = ["agent_id" => $user_id];

    // Default to current month/year if not provided
    if (empty($month) || empty($year)) {
        $month = date("m");
        $year = date("Y");
    }

    $startDate = date("Y-m-01", strtotime("$year-$month-01"));
    $endDate   = date("Y-m-t", strtotime("$year-$month-01"));

    $conditions["booking_date[>=]"] = $startDate;
    $conditions["booking_date[<=]"] = $endDate;

    // Fetch all bookings
    $hotel_sales = $db->select("hotels_bookings", "*", $conditions);

    if (!empty($hotel_sales)) {
        $groupedData = [];

        foreach ($hotel_sales as $hotel_sale) {
            $dateKey = date("Y-m-d", strtotime($hotel_sale['booking_date']));

            // Initialize the date key if it doesn't exist
            if (!isset($groupedData[$dateKey])) {
                $groupedData[$dateKey] = [];
            }

            // Prepare guest name
            $guest_name = 'N/A';
            if (!empty($hotel_sale['guest'])) {
                $guest = json_decode($hotel_sale['guest']);
                if (!empty($guest[0]->title) && !empty($guest[0]->first_name) && !empty($guest[0]->last_name)) {
                    $guest_name = $guest[0]->title . ' ' . $guest[0]->first_name . ' ' . $guest[0]->last_name;
                }
            }

            // Push only the required data
            $groupedData[$dateKey][] = [
                'guest'        => $guest_name,
                'hotel'        => $hotel_sale['hotel_name'] ?? 'N/A',
                'booking_date' => $dateKey
            ];
        }

        $response = ["status" => true,"message" => "bookings found","data" => $groupedData];
    } else {
        $response = ["status" => false,"message" => "no sales found","data" => null];
    }

    echo json_encode($response);
});

/*==================
 RECENT RESERVATION API
==================*/
$router->post('agent/dashboard/reservations/recent', function () {
    
    // INCLUDE CONFIG FILE
    include "./config.php";

    // REQUIRED PARAMETER CHECK
    required('user_id');

    // GET POST PARAMETERS
    $user_id         = $_POST["user_id"];
    $duration        = $_POST["duration"] ?? null; // today | week
    $status          = $_POST["status"] ?? null; // pending_checkin or booking status
    $commission_rate = $_POST["commission_rate"] ?? []; // ARRAY FROM CHECKBOXES
    $booking_value   = $_POST["booking_value"] ?? [];   // ARRAY FROM CHECKBOXES
    $search          = $_POST["search"] ?? null; // SEARCH TERM
    $page            = (int)($_POST["page"] ?? 1);
    $per_page        = isset($_POST['limit']) && is_numeric($_POST['limit']) ? (int) $_POST['limit'] : 5;

    // FIX: Handle JSON data from frontend
    if (!empty($commission_rate) && is_string($commission_rate)) {
        $commission_rate = json_decode($commission_rate, true) ?? [];
    }
    
    if (!empty($booking_value) && is_string($booking_value)) {
        $booking_value = json_decode($booking_value, true) ?? [];
    }

    // FIX: Handle comma-separated strings in arrays
    if (!empty($commission_rate) && is_array($commission_rate)) {
        $fixed_commission_rate = [];
        foreach ($commission_rate as $rate) {
            if (is_string($rate) && strpos($rate, ',') !== false) {
                // Split comma-separated values
                $split_rates = explode(',', $rate);
                foreach ($split_rates as $split_rate) {
                    $trimmed = trim($split_rate);
                    if (!empty($trimmed)) {
                        $fixed_commission_rate[] = $trimmed;
                    }
                }
            } else {
                $fixed_commission_rate[] = trim($rate);
            }
        }
        $commission_rate = $fixed_commission_rate;
    }

    if (!empty($booking_value) && is_array($booking_value)) {
        $fixed_booking_value = [];
        foreach ($booking_value as $value_item) {
            if (is_string($value_item) && strpos($value_item, ',') !== false) {
                // Split comma-separated values
                $split_values = explode(',', $value_item);
                foreach ($split_values as $split_value) {
                    $trimmed = trim($split_value);
                    if (!empty($trimmed)) {
                        $fixed_booking_value[] = $trimmed;
                    }
                }
            } else {
                $fixed_booking_value[] = trim($value_item);
            }
        }
        $booking_value = $fixed_booking_value;
    }

    // BASE CONDITIONS FOR QUERY
    $conditions = [
        "agent_id" => $user_id
    ];

    // BOOKING STATUS FILTER
    if (!empty($status)) {
        if ($status === 'pending_checkin') {
            // For pending checkin, filter by confirmed/pending bookings with future checkin
            $conditions["booking_status"] = ["confirmed", "pending"];
            $today = date("Y-m-d");
            $conditions["checkin[>=]"] = $today;
        } else {
            // Regular status filter
            $conditions["booking_status"] = $status;
        }
    } else {
        $conditions["booking_status"] = ["confirmed", "pending"];
    }

    // DURATION FILTER (DATE RANGE)
    if (!empty($duration)) {
        $today = date("Y-m-d");
        
        if ($duration === "today") {
            // FILTER FOR BOOKINGS MADE TODAY
            $conditions["booking_date"] = $today;
        } elseif ($duration === "week") {
            // FILTER FOR BOOKINGS IN CURRENT WEEK
            $monday = date("Y-m-d", strtotime("monday this week"));
            $sunday = date("Y-m-d", strtotime("sunday this week"));
            $conditions["booking_date[<>]"] = [$monday, $sunday];
        }
    }

    // Get all records first
    $conditions["ORDER"] = ["booking_date" => "DESC"];
    $all_records = $db->select("hotels_bookings", "*", $conditions);

    // APPLY FILTERS IN PHP - OR LOGIC BETWEEN FILTER TYPES
    $filtered_records = [];
    
    foreach ($all_records as $record) {
        $include_record = false;
        
        // If no filters applied, include all records
        if (empty($booking_value) && empty($commission_rate) && empty($search)) {
            $include_record = true;
        } else {
            // Check each filter type - if ANY match, include the record
            
            // BOOKING VALUE FILTER
            if (!empty($booking_value) && is_array($booking_value)) {
                $markup = (float)($record['price_markup'] ?? 0);
                
                foreach ($booking_value as $bv) {
                    if (($bv === 'premium' && $markup >= 10000) || 
                        ($bv === 'standard' && $markup >= 5000 && $markup < 10000)) {
                        $include_record = true;
                        break;
                    }
                }
            }
            
            // COMMISSION RATE FILTER
            if (!$include_record && !empty($commission_rate) && is_array($commission_rate)) {
                $original_price = (float)($record['price_original'] ?? 0);
                $agent_fee = (float)($record['agent_fee'] ?? 0);
                
                if ($original_price > 0 && $agent_fee > 0) {
                    $commission_percentage = ($agent_fee / $original_price) * 100;
                    
                    foreach ($commission_rate as $rate) {
                        if (($rate === 'high' && $commission_percentage >= 15) ||
                            ($rate === 'standard' && $commission_percentage >= 10 && $commission_percentage < 15)) {
                            $include_record = true;
                            break;
                        }
                    }
                }
            }
            
            // SEARCH FILTER
            if (!$include_record && !empty($search)) {
                // Calculate revenue for search
                $price_markup = isset($record['price_markup']) ? (float)$record['price_markup'] : 0.0;
                $price_original = isset($record['price_original']) ? (float)$record['price_original'] : 0.0;
                $agent_fee = isset($record['agent_fee']) ? (float)$record['agent_fee'] : 0.0;
                $revenue = $price_markup - $price_original - $agent_fee;
                
                // Get guest email from user_data
                $guest_email = '';
                if (!empty($record['user_data'])) {
                    $user_data = json_decode($record['user_data']);
                    if (!empty($user_data->email)) {
                        $guest_email = $user_data->email;
                    }
                }
                
                // Get room name from room_data
                $room_name = '';
                if (!empty($record['room_data'])) {
                    $room_data = json_decode($record['room_data']);
                    if (!empty($room_data[0]->room_name)) {
                        $room_name = $room_data[0]->room_name;
                    }
                }
                
                // Check regular fields
                if (stripos($record['hotel_name'] ?? '', $search) !== false ||
                    stripos($record['location'] ?? '', $search) !== false ||
                    stripos($record['booking_ref_no'] ?? '', $search) !== false ||
                    stripos($record['booking_id'] ?? '', $search) !== false ||
                    stripos($guest_email, $search) !== false ||
                    stripos($room_name, $search) !== false ||
                    stripos($record['subtotal'] ?? '', $search) !== false ||
                    stripos($record['agent_fee'] ?? '', $search) !== false ||
                    stripos((string)$revenue, $search) !== false ||
                    stripos($record['booking_status'] ?? '', $search) !== false) {
                    $include_record = true;
                }
                
                // Check guest JSON if no match yet
                if (!$include_record && !empty($record['guest'])) {
                    $guest = json_decode($record['guest']);
                    if (!empty($guest[0])) {
                        $guest_obj = $guest[0];
                        
                        $title = $guest_obj->title ?? '';
                        $first_name = $guest_obj->first_name ?? '';
                        $last_name = $guest_obj->last_name ?? '';
                        $full_name = trim($title . ' ' . $first_name . ' ' . $last_name);
                        
                        if (stripos($title, $search) !== false ||
                            stripos($first_name, $search) !== false ||
                            stripos($last_name, $search) !== false ||
                            stripos($full_name, $search) !== false) {
                            $include_record = true;
                        }
                    }
                }
}
        }
        
        if ($include_record) {
            $filtered_records[] = $record;
        }
    }

    // Calculate pagination after filtering
    $total_count = count($filtered_records);
    $total_pages = ceil($total_count / $per_page);
    $offset = ($page - 1) * $per_page;
    $paginated_records = array_slice($filtered_records, $offset, $per_page);

    // PREPARE RESPONSE DATA
    if (!empty($paginated_records)) {
        $data = [];

        foreach ($paginated_records as $hotel_sale) {

            // GUEST NAME EXTRACTION
            $guest_name = 'N/A';
            if (!empty($hotel_sale['guest'])) {
                $guest = json_decode($hotel_sale['guest']);
                if (!empty($guest[0]->title) && !empty($guest[0]->first_name) && !empty($guest[0]->last_name)) {
                    $guest_name = $guest[0]->title . ' ' . $guest[0]->first_name . ' ' . $hotel_sale['last_name'];
                }
            }

            $guest_email = 'N/A';
            if (!empty($hotel_sale['user_data'])) {
                $guest = json_decode($hotel_sale['user_data']);
                if (!empty($guest->email)) {
                    $guest_email = $guest->email;
                }
            }

            // CALCULATE STAY DURATION
            $duration_nights = 0;
            if (!empty($hotel_sale['checkin']) && !empty($hotel_sale['checkout'])) {
                try {
                    $checkinDate  = new DateTime($hotel_sale['checkin']);
                    $checkoutDate = new DateTime($hotel_sale['checkout']);
                    $duration_nights = $checkinDate->diff($checkoutDate)->days;
                } catch (Exception $e) {
                    $duration_nights = 0;
                }
            }

            $room_data = 'N/A';
            if (!empty($hotel_sale['room_data'])) {
                $room = json_decode($hotel_sale['room_data']);
                if (!empty($room[0]->room_name)) {
                    $room_data = $room[0]->room_name;
                }
            }
            
            // CALCULATE REVENUE
            $price_markup   = isset($hotel_sale['price_markup']) ? (float)$hotel_sale['price_markup'] : 0.0;
            $price_original = isset($hotel_sale['price_original']) ? (float)$hotel_sale['price_original'] : 0.0;
            $agent_fee      = isset($hotel_sale['agent_fee']) ? (float)$hotel_sale['agent_fee'] : 0.0;
            $revenue        = $price_markup - $price_original - $agent_fee;

            // APPEND BOOKING DATA
            $data[] = [
                'id'            => $hotel_sale['booking_id'] ?? null,
                'booking_id'    => $hotel_sale['booking_ref_no'],
                'guest'         => $guest_name,
                'guest_email'   => $guest_email,
                'hotel'         => $hotel_sale['hotel_name'] ?? 'N/A',
                'room_data'     => $room_data,
                'city'          => $hotel_sale['location'] ?? 'N/A',
                'checkin'       => date('Y-m-d',strtotime($hotel_sale['checkin'])) ?? 'N/A',
                'checkout'      => date('Y-m-d',strtotime($hotel_sale['checkout'])) ?? 'N/A',
                'nights'        => $duration_nights,
                'subtotal'      => $hotel_sale['subtotal'] ?? 0,
                'commission'    => $hotel_sale['agent_fee'] ?? 0,
                'revenue'       => $revenue,
                'booking_status'=> $hotel_sale['booking_status']
            ];
        }

        // SUCCESS RESPONSE
        $response = [
            "status"     => true,
            "message"    => "DATA IS RETRIEVED",
            "pagination" => [
                "current_page"  => $page,
                "total_pages"   => $total_pages,
                "total_records" => $total_count,
                "per_page"      => $per_page
            ],
            "data" => $data
        ];
    } else {
        // NO RECORD FOUND RESPONSE - ZERO OUT PAGINATION
        $response = [
            "status"  => false,
            "message" => "NO RECORD FOUND",
            "pagination" => [
                "current_page"  => 1,
                "total_pages"   => 0,
                "total_records" => 0,
                "per_page"      => $per_page
            ],
            "data"    => []
        ];
    }

    // CALCULATE FILTER COUNTS (FOR SIDEBAR FILTERS)
    $filter_counts = [
        'commission_rate' => ['high' => 0, 'standard' => 0],
        'booking_value' => ['premium' => 0, 'standard' => 0]
    ];
      
    foreach ($all_records as $record) {
        // Count commission rates
        if (!empty($record['price_original']) && $record['price_original'] > 0 &&
            !empty($record['agent_fee']) && $record['agent_fee'] > 0) {
            $commission_percentage = ($record['agent_fee'] / $record['price_original']) * 100;
            if ($commission_percentage >= 15) {
                $filter_counts['commission_rate']['high']++;
            } elseif ($commission_percentage >= 10 && $commission_percentage < 15) {
                $filter_counts['commission_rate']['standard']++;
            }
        }

        // Count booking values
        if (!empty($record['price_markup']) && $record['price_markup'] > 0) {
            if ($record['price_markup'] >= 10000) {
                $filter_counts['booking_value']['premium']++;
            } elseif ($record['price_markup'] >= 5000 && $record['price_markup'] < 10000) {
                $filter_counts['booking_value']['standard']++;
            }
        }
    }

    // Add filter counts to response
    $response['filter_counts'] = $filter_counts;

    // RETURN RESPONSE AS JSON
    echo json_encode($response);
});
?>