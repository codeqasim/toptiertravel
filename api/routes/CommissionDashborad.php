<?php
// HEADERS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header("X-Frame-Options: SAMEORIGIN");


/*==================
AGENT SALES AND COMMISSIONS API
==================*/
$router->post('agent/dashboard/commission', function () {
    
    // INCLUDE CONFIG
    include "./config.php";

    required('user_id');

    $user_id = $_POST["user_id"];
    $interval = isset($_POST['interval']) ? $_POST['interval'] : null ;

    // CHECK USER
    $user = $db->select("users", "*", [ "user_id" => $user_id]);

        if(isset($user[0])){
            $user_data = (object)$user[0];
            
            if ($user_data->user_type == 'Agent') {
                if ($user_data->status == 1) {

                    // GET CURRENT AND LAST MONTH DATE RANGES
                    $current_month_start = date('Y-m-01');
                    $current_month_end = date('Y-m-t');

                    $last_month_start = date('Y-m-01', strtotime('first day of last month'));
                    $last_month_end = date('Y-m-t', strtotime('last day of last month'));

                    $conditions = ["agent_id" => $user_id];

                    if (isset($interval) && $interval != null) {
                        $today = date('Y-m-d');

                        if ($interval == '1 day') {
                            $from_date = date('Y-m-d', strtotime('-1 day'));
                        } elseif ($interval == '5 days') {
                            $from_date = date('Y-m-d', strtotime('-5 days'));
                        } elseif ($interval == '1 month') {
                            $from_date = date('Y-m-d', strtotime('-1 month'));
                        } else {
                            $from_date = null;
                        }

                        // Only apply booking_date filter if $from_date is set
                        if ($from_date) {
                            $conditions["booking_date[>=]"] = $from_date;
                            $conditions["booking_date[<=]"] = $today;
                        }
                    }

                    // FETCH ALL BOOKINGS FOR THIS AGENT WITH CONDITIONS
                    $hotel_sales = $db->select("hotels_bookings", "*", $conditions);

                    // INITIALIZE TOTAL VARIABLES
                    $total_sales = 0;
                    $total_commission = 0;
                    $total_partner_commission = 0;
                    $total_bookings = 0;

                    $total_paid_commission_amount = 0;
                    $total_pending_commission_amount = 0;

                    $top_bookings = [];

                    // LOOP THROUGH ALL HOTEL BOOKINGS
                    foreach ($hotel_sales as $hotel_sale) {

                        // VALIDATE THAT agent_fee AND price_original ARE SET AND NUMERIC
                        $agent_fee = isset($hotel_sale['agent_fee']) && is_numeric($hotel_sale['agent_fee']) ? $hotel_sale['agent_fee'] : 0;
                        $price_original = isset($hotel_sale['price_original']) && is_numeric($hotel_sale['price_original']) ? $hotel_sale['price_original'] : 0;

                        // CALCULATE COMMISSION ONLY IF BOTH VALUES ARE VALID AND GREATER THAN ZERO
                        $commission = 0;
                        if ($agent_fee > 0) {
                            $commission = $agent_fee;
                        }

                        // ADD TO TOTAL SALES AND COMMISSION
                        $total_sales += isset($hotel_sale['price_markup']) && is_numeric($hotel_sale['price_markup']) ? $hotel_sale['price_markup'] : 0;
                        $total_commission += $commission;

                        // CHECK IF COMMISSION IS PAID OR PENDING AND ADD TO RESPECTIVE TOTAL
                        if (isset($hotel_sale['agent_commission_status']) && $hotel_sale['agent_commission_status'] === 'paid') {
                            $total_paid_commission_amount += $commission;
                        } else {
                            $total_pending_commission_amount += $commission;
                        }

                        // INCREMENT TOTAL BOOKINGS COUNT
                        $total_bookings++;
                    }

                    // PERCENT CHANGE FUNCTION
                    function percentChange($current, $last) {
                        if ($last == 0) return $current > 0 ? 100 : 0;
                        return round((($current - $last) / $last) * 100);
                    }
                    
                    // AVERAGE COMMISSION CALCULATION
                    $average_commission_amount = $total_commission > 0 ? $total_commission / count($hotel_sales) : 0 ; // PREVENT DIVISION BY ZERO

                    //FETCH NEW NOTIFICATIONS
                    $notifications = $db->count("notifications", "*", ["status" => 1]);

                    // FINAL DATA RESPONSE
                    $data = [
                        'user' => $user,
                        'notifications' => $notifications,
                        'total_sales' => number_format($total_sales,2),
                        'total_commissions' => number_format($total_commission,2),
                        'total_bookings' => $total_bookings,
                        'average_commission_amount' => $average_commission_amount,
                    ];

                    // FINAL RESPONSE
                    $response = array("status" => true,"message" => "data has be retrieved","data" => $data);

                } else {
                    // USER IS NOT ACTIVATED
                    $response = array("status" => false,"message" => "not_activated","data" => null);
                }
            } else {
                $response = array ( "status"=>false, "message"=>"This user is not an agent", "data"=> null );
            }
        } else {
            $response = array ( "status"=>false, "message"=>"no user found", "data"=> null );
        }
    echo json_encode($response);
});

/*==================
AGENT RECENT BOOKINGS API
==================*/
$router->post('agent/dashboard/commissions/bookings/active', function () {
    
    // INCLUDE CONFIG FILE
    include "./config.php";

    // REQUIRED PARAMETERS
    required('user_id');

    // GET POST PARAMETERS
    $user_id         = $_POST["user_id"];
    $value           = $_POST["value"] ?? null; // MINIMUM COMMISSION VALUE
    $month           = $_POST["month"] ?? null; // FORMAT: YYYY-MM
    $status          = $_POST["status"] ?? null; // BOOKING STATUS
    $commission_rate = $_POST["commission_rate"] ?? []; // ARRAY FROM CHECKBOXES
    $booking_value   = $_POST["booking_value"] ?? [];   // ARRAY FROM CHECKBOXES
    $search          = $_POST["search"] ?? null; // SEARCH TERM (NEW FEATURE)
    $page            = (int)($_POST["page"] ?? 1);
    $per_page        = 5;

    // BASE CONDITIONS FOR QUERY
    $conditions = [
        "agent_id" => $user_id
    ];

    // BOOKING STATUS FILTER
    if (!empty($status)) {
        $conditions["booking_status"] = $status;
    } else {
        $conditions["booking_status"] = ["confirmed", "pending"];
    }

    // MONTH FILTER
    if (!empty($month)) {
        $start_date = $month . "-01";
        $end_date   = date("Y-m-t", strtotime($start_date));
        $conditions["booking_date[<>]"] = [$start_date, $end_date];
    }

    // BOOKING VALUE FILTER
    if (!empty($booking_value) && is_array($booking_value)) {
        $value_or = [];
        foreach ($booking_value as $bv) {
            if ($bv === 'premium') {
                $value_or[] = ["price_markup[>=]" => 10000];
            } elseif ($bv === 'standard') {
                $value_or[] = ["price_markup[>=]" => 5000, "price_markup[<]" => 10000];
            }
        }
        if (!empty($value_or)) {
            $conditions["OR #booking_value"] = $value_or;
        }
    }

    // SEARCH FILTER (NEW FEATURE)
    if (!empty($search)) {
        $conditions["OR #search"] = [
            "hotel_name[~]"      => $search,
            "location[~]"        => $search,
            "booking_ref_no[~]"  => $search
        ];
    }

    // PAGINATION
    $offset = ($page - 1) * $per_page;
    $conditions["ORDER"] = ["booking_date" => "DESC"];
    $conditions["LIMIT"] = [$offset, $per_page];

    // FETCH MATCHING BOOKINGS FROM DATABASE
    $all_sales = $db->select("hotels_bookings", "*", $conditions);

    // COMMISSION RATE FILTER
    if (!empty($commission_rate) && is_array($commission_rate)) {
        $all_sales = array_filter($all_sales, function($row) use ($commission_rate) {
            if (empty($row['price_original']) || $row['price_original'] == 0) {
                return false; // AVOID DIVISION BY ZERO
            }
            $commission_percentage = ($row['agent_fee'] / $row['price_original']) * 100;

            foreach ($commission_rate as $rate) {
                if ($rate === 'high' && $commission_percentage >= 15) {
                    return true;
                }
                if ($rate === 'standard' && $commission_percentage >= 10 && $commission_percentage <= 14) {
                    return true;
                }
            }
            return false;
        });
    }

    // COUNT TOTAL RECORDS FOR PAGINATION
    $count_conditions = $conditions;
    unset($count_conditions["LIMIT"], $count_conditions["ORDER"]);
    $total_count = $db->count("hotels_bookings", $count_conditions);
    $total_pages = ceil($total_count / $per_page);

    // MINIMUM COMMISSION VALUE FILTER
    if (!empty($value)) {
        $all_sales = array_filter($all_sales, function($sale) use ($value) {
            $commission = ($sale['price_original'] * $sale['agent_fee']) / 100;
            return $commission >= $value;
        });
    }

    // PREPARE RESPONSE DATA
    if (!empty($all_sales)) {
        $data = [];

        foreach ($all_sales as $hotel_sale) {
            // GUEST NAME
            $guest_name = 'N/A';
            if (!empty($hotel_sale['guest'])) {
                $guest = json_decode($hotel_sale['guest']);
                if (!empty($guest[0]->title) && !empty($guest[0]->first_name) && !empty($guest[0]->last_name)) {
                    $guest_name = $guest[0]->title . ' ' . $guest[0]->first_name . ' ' . $guest[0]->last_name;
                }
            }

            // DURATION IN NIGHTS
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

            // ADD BOOKING DATA TO RESPONSE ARRAY
            $data[] = [
                'id'            => $hotel_sale['booking_id'] ?? null,
                'booking_id'    => $hotel_sale['booking_ref_no'],
                'guest'         => $guest_name,
                'hotel'         => $hotel_sale['hotel_name'] ?? 'N/A',
                'destination'   => $hotel_sale['location'] ?? 'N/A',
                'checkin'       => $hotel_sale['checkin'] ?? 'N/A',
                'checkout'      => $hotel_sale['checkout'] ?? 'N/A',
                'nights'        => $duration,
                'subtotal'      => $hotel_sale['subtotal'] ?? 0,
                'commission'    => $hotel_sale['agent_fee'] ?? 0,
                'payment_status'=> $hotel_sale['agent_payment_status'],
                'payment_type'  => $hotel_sale['agent_payment_type'],
                'payment_date'  => $hotel_sale['payment_date'],
            ];
        }

        // SUCCESS RESPONSE
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
        // NO RECORD FOUND RESPONSE
        $response = [
            "status"  => false,
            "message" => "NO RECORD FOUND",
            "data"    => null
        ];
    }

    // OUTPUT JSON RESPONSE
    echo json_encode($response);
});

/*==================
AGENT RECENT BOOKINGS API
==================*/
$router->post('agent/dashboard/commissions/top_bookings', function () {

    // INCLUDE CONFIG
    include "./config.php";

    required('user_id');

    $user_id = $_POST["user_id"];
    $interval = isset($_POST['interval']) && !empty($_POST['interval']) ? $_POST['interval'] : null ;

    $conditions = [
        'agent_id'   => $user_id,
        'agent_fee[!]' => null
    ];

    $today = date('Y-m-d');

    // Default to MTD
    $start_date = date('Y-m-01');

    // Apply filters based on interval
    if (!empty($interval)) {
        if (strcasecmp($interval, '7 days') === 0) {
            $start_date = date('Y-m-d', strtotime('-7 days'));
            $conditions['booking_date[>=]'] = $start_date;
            $conditions['booking_date[<=]'] = $today;

        } elseif (strcasecmp($interval, 'YTD') === 0) {
            $start_date = date('Y-01-01');
            $conditions['booking_date[>=]'] = $start_date;
            $conditions['booking_date[<=]'] = $today;

        } elseif (strcasecmp($interval, 'All time') === 0) {
            // No date filter applied for all time

        } else { // Default to MTD if unrecognized
            $conditions['booking_date[>=]'] = $start_date;
            $conditions['booking_date[<=]'] = $today;
        }

    } else {
        // Default MTD when interval is empty
        $conditions['booking_date[>=]'] = $start_date;
        $conditions['booking_date[<=]'] = $today;
    }

    // Fetch matching rows
    $all_sales = $db->select("hotels_bookings", "*", $conditions);

    $data = [];
    if(isset($all_sales) && !empty($all_sales)){
            // INITIALIZE DATA ARRAY
            $data = [];
            
            // LOOP THROUGH EACH HOTEL BOOKING
            foreach ($all_sales as $hotel_sale) {

                // VALIDATE AND DECODE GUEST JSON DATA
                $guest = [];
                if (isset($hotel_sale['guest'])) {
                    $guest = json_decode($hotel_sale['guest']);
                }

                // EXTRACT GUEST NAME SAFELY
                $guest_name = 'N/A';
                if (!empty($guest) && isset($guest[0]->title, $guest[0]->first_name, $guest[0]->last_name)) {
                    $guest_name = $guest[0]->title . ' ' . $guest[0]->first_name . ' ' . $guest[0]->last_name;
                }
                
                // VALIDATE FEE AND VALUE BEFORE CALCULATION
                $agent_fee = isset($hotel_sale['agent_fee']) && is_numeric($hotel_sale['agent_fee']) ? $hotel_sale['agent_fee'] : 0;
                $subtotal = isset($hotel_sale['subtotal']) && is_numeric($hotel_sale['subtotal']) ? $hotel_sale['subtotal'] : 0;
                $rate = ($agent_fee > 0 && $subtotal > 0) ? ($agent_fee * 100) / $subtotal : 0;

                // ADD DATA TO RESPONSE ARRAY
                $data[] = [
                    'guest' => $guest_name,
                    'hotel' => $hotel_sale['hotel_name'] ?? 'N/A',
                    'rate' => number_format($rate,2),
                    'commission' => $agent_fee,
                ];
            }

            // SORT DATA ARRAY BY COMMISSION IN DESCENDING ORDER
            usort($data, function ($a, $b) {
                return $b['commission'] <=> $a['commission'];
            });

            // GET TOP 5 COMMISSION ENTRIES
            $top_commissions = array_slice($data, 0, 6);

            // SET SUCCESS RESPONSE
            $response = ["status" => true,"message" => 'DATA IS RETRIEVED',"data" => $top_commissions];

    }else{
        $response = array ( "status" => false, "message"=>"no record found", "data"=> null );
    }
    echo json_encode($response);
});
?>