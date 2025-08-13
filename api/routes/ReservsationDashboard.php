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

        if(isset($user[0])){
            $user_data = (object)$user[0];
            
            $conditions = ["agent_id" => $user_id];

            // FETCH ALL BOOKINGS FOR THIS AGENT WITH CONDITIONS
            $hotel_sales = $db->select("hotels_bookings", "*", $conditions);

            if(isset($hotel_sales) && !empty($hotel_sales)){

                $total_reservations = 0;
                $total_revenue = 0;
                $occupancy_rate = 0;
                $avg_stay_length = 0;
                $sale_data = [];

                $total_revenue = 0.0;

                foreach ($hotel_sales as $hotel_sale) {
                    $price_markup  = isset($hotel_sale['price_markup']) ? (float)$hotel_sale['price_markup'] : 0.0;
                    $price_original = isset($hotel_sale['price_original']) ? (float)$hotel_sale['price_original'] : 0.0;
                    $agent_fee      = isset($hotel_sale['agent_fee']) ? (float)$hotel_sale['agent_fee'] : 0.0;

                    $total_revenue += $price_markup - $price_original - $agent_fee;
                }

                $total_reservations = count($hotel_sales);

                $this_week = date('Y-m-d', strtotime('-7 days'));

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


                        $sale_data[] = [
                            'guest' => $guest_name,
                            'hotel_name' => $last_seven_days_sale['hotel_name'],
                            'location' => $last_seven_days_sale['location'],
                            'travel_date' => date('M d-m, Y',strtotime($last_seven_days_sale['booking_date']))
                        ];
                    }
                }

                $data = [
                    'total_reservations' => $total_reservations,
                    'total_revenue' => $total_revenue,
                    'occupancy_rate' => $occupancy_rate,
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
    
    // INCLUDE CONFIG
    include "./config.php";

    //PARAMS
    required('user_id');

    $user_id         = $_POST["user_id"];
    $filter_type     = $_POST["filter_type"] ?? "today"; // today | this_week | pending_checkin
    $page            = (int)($_POST["page"] ?? 1);
    $per_page        = 10;

    // Base conditions
    $conditions = [
        "agent_id" => $user_id,
        "booking_status" => ["confirmed", "pending"] // default
    ];

    // Date filters (only one applies)
    $today = date("Y-m-d");

    // if ($filter_type === "today") {
    //     $conditions["booking_date"] = $today;

    // } elseif ($filter_type === "this_week") {
    //     $monday = date("Y-m-d", strtotime("monday this week"));
    //     $sunday = date("Y-m-d", strtotime("sunday this week"));
    //     $conditions["booking_date[<>]"] = [$monday, $sunday];

    // } elseif ($filter_type === "pending_checkin") {
    //     $conditions["checkin[>=]"] = $today;
    // }

    // Pagination
    $offset = ($page - 1) * $per_page;
    $conditions["ORDER"] = ["booking_date" => "DESC"];
    $conditions["LIMIT"] = [$offset, $per_page];

    // Fetch bookings
    $all_sales = $db->select("hotels_bookings", "*", $conditions);

    // Count for pagination
    $count_conditions = $conditions;
    unset($count_conditions["LIMIT"], $count_conditions["ORDER"]);
    $total_count = $db->count("hotels_bookings", $count_conditions);
    $total_pages = ceil($total_count / $per_page);

    // Prepare response
    if (!empty($all_sales)) {
        $data = [];

        foreach ($all_sales as $hotel_sale) {
            // Guest
            $guest_name = 'N/A';
            if (!empty($hotel_sale['guest'])) {
                $guest = json_decode($hotel_sale['guest']);
                if (!empty($guest[0]->title) && !empty($guest[0]->first_name) && !empty($guest[0]->last_name)) {
                    $guest_name = $guest[0]->title . ' ' . $guest[0]->first_name . ' ' . $guest[0]->last_name;
                }
            }

            // Duration
            $duration = 0;
            if (!empty($hotel_sale['checkin']) && !empty($hotel_sale['checkout'])) {
                try {
                    $checkinDate  = new DateTime($hotel_sale['checkin']);
                    $checkoutDate = new DateTime($hotel_sale['checkout']);
                    $duration = $checkinDate->diff($checkoutDate)->days;
                } catch (Exception $e) {
                    $duration = 0;
                }
            }
            
            //REVENUE CALCULATION
            $price_markup  = isset($hotel_sale['price_markup']) ? (float)$hotel_sale['price_markup'] : 0.0;
            $price_original = isset($hotel_sale['price_original']) ? (float)$hotel_sale['price_original'] : 0.0;
            $agent_fee      = isset($hotel_sale['agent_fee']) ? (float)$hotel_sale['agent_fee'] : 0.0;

            $revenue = 0;
            $revenue = $price_markup - $price_original - $agent_fee;

            $data[] = [
                'id'            => $hotel_sale['booking_id'] ?? null,
                'booking_id'    => $hotel_sale['booking_ref_no'],
                'guest'         => $guest_name,
                'hotel'         => $hotel_sale['hotel_name'] ?? 'N/A',
                'city'   => $hotel_sale['location'] ?? 'N/A',
                'checkin'       => $hotel_sale['checkin'] ?? 'N/A',
                'checkout'      => $hotel_sale['checkout'] ?? 'N/A',
                'nights'        => $duration,
                'subtotal'      => $hotel_sale['subtotal'] ?? 0,
                'commission'    => $hotel_sale['agent_fee'] ?? 0,
                'revenue'       => $revenue
            ];
        }

        $response = [
            "status"     => true,
            "message"    => "DATA IS RETRIEVED",
            "pagination" => [
                "current_page"  => $page,
                "total_pages"   => $total_pages,
                "total_records" => $total_count
            ],
            "data" => $data
        ];
    } else {
        $response = [
            "status"  => false,
            "message" => "NO RECORD FOUND",
            "data"    => null
        ];
    }

    echo json_encode($response);
});

?>