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
AGENT RECENT BOOKINGS API - USING MEDOO/XCRUD STYLE
==================*/
$router->post('agent/dashboard/commissions/bookings/active', function () {
    
    // INCLUDE CONFIG FILE
    include "./config.php";

    // REQUIRED PARAMETERS
    required('user_id');

    // GET POST PARAMETERS
    $user_id         = $_POST["user_id"];
    $value           = $_POST["value"] ?? null;
    $month           = $_POST["month"] ?? null;
    $status          = $_POST["status"] ?? null;
    $commission_rate = $_POST["commission_rate"] ?? [];
    $booking_value   = $_POST["booking_value"] ?? [];
    $search          = $_POST["search"] ?? null;
    $page            = (int)($_POST["page"] ?? 1);
    $per_page        = isset($_POST['limit']) && is_numeric($_POST['limit']) ? (int) $_POST['limit'] : 5;

    // FIX: Handle JSON data from frontend
    if (!empty($commission_rate) && is_string($commission_rate)) {
        $commission_rate = json_decode($commission_rate, true) ?? [];
    }
    
    if (!empty($booking_value) && is_string($booking_value)) {
        $booking_value = json_decode($booking_value, true) ?? [];
    }

    // FIX: Handle comma-separated strings in arrays (keep existing logic for backward compatibility)
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
        $conditions["booking_status"] = $status;
    } else {
        $conditions["booking_status"] = ["confirmed", "pending"];
    }

    // MONTH FILTER
    if (!empty($month)) {
        $start_date = $month . "-01";
        $end_date = date("Y-m-t", strtotime($start_date));
        $conditions["booking_date[<>]"] = [$start_date, $end_date];
    }

    // MINIMUM COMMISSION VALUE FILTER
    if (!empty($value)) {
        $conditions["price_original[>]"] = 0;
        $conditions["agent_fee[>=]"] = $value;
    }

    // Get all records first
    $conditions["ORDER"] = ["booking_date" => "DESC"];
    $all_records = $db->select("hotels_bookings", "*", $conditions);

    // FILTER LOGIC
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
                // Check regular fields
                if (stripos($record['hotel_name'] ?? '', $search) !== false ||
                    stripos($record['location'] ?? '', $search) !== false ||
                    stripos($record['booking_ref_no'] ?? '', $search) !== false) {
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

    // Calculate pagination
    $total_count = count($filtered_records);
    $total_pages = ceil($total_count / $per_page);
    $offset = ($page - 1) * $per_page;
    $paginated_records = array_slice($filtered_records, $offset, $per_page);

    // PREPARE RESPONSE DATA
    if (!empty($paginated_records)) {
        $data = [];

        foreach ($paginated_records as $hotel_sale) {
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
                $checkinDate  = new DateTime($hotel_sale['checkin']);
                $checkoutDate = new DateTime($hotel_sale['checkout']);
                $duration = $checkinDate->diff($checkoutDate)->days;
            }

            // CALCULATE COMMISSION PERCENTAGE FOR DISPLAY
            $commission_percentage = 0;
            if (!empty($hotel_sale['price_original']) && $hotel_sale['price_original'] > 0) {
                $commission_percentage = ($hotel_sale['agent_fee'] / $hotel_sale['price_original']) * 100;
            }

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
                'commission_rate' => round($commission_percentage, 2),
                'payment_status'=> $hotel_sale['agent_payment_status'],
                'payment_type'  => $hotel_sale['agent_payment_type'],
                'payment_date'  => $hotel_sale['payment_date'],
            ];
        }

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
        $response = [
            "status"  => false,
            "message" => "NO RECORD FOUND",
            "pagination" => [
                "current_page"  => $page,
                "total_pages"   => 0,
                "total_records" => 0,
                "per_page"      => $per_page
            ],
            "data"    => []
        ];
    }
    
    // Calculate filter counts
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

    $response['filter_counts'] = $filter_counts;

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

/*=================
COMMISSION DASHBOARD STATUS GRAPH API
=================*/
$router->post('agent/dashboard/commissions/status_graph', function () {
    include "./config.php";

    required('user_id');

    $user_id     = $_POST["user_id"];
    $filter_type = $_POST["filter_type"] ?? '7_days';

    $today = date('Y-m-d');
    $start_date = null;
    $end_date = $today;

    // DETERMINE DATE RANGE FOR CURRENT PERIOD
    switch ($filter_type) {
        case '7_days':
            $start_date = date('Y-m-d', strtotime('-6 days', strtotime($today)));
            $days_count = 7;
            $group_by = 'day';
            break;
        case '30_days':
            $start_date = date('Y-m-d', strtotime('-29 days', strtotime($today)));
            $days_count = 30;
            $group_by = 'week';
            break;
        case '90_days':
            // Start from 3 months ago (including current month = 3 total months)
            $start_date = date('Y-m-01', strtotime('-2 months', strtotime($today)));
            $days_count = 90;
            $group_by = 'month';
            break;
        case 'all_time':
            $earliest = $db->get("hotels_bookings", "booking_date", [
                "agent_id" => $user_id,
                "ORDER"    => ["booking_date" => "ASC"]
            ]);
            $start_date = $earliest ?: $today;
            $days_count = (strtotime($today) - strtotime($start_date)) / (60*60*24) + 1;
            $group_by = 'month';
            break;
        default:
            echo json_encode([
                "status"  => false,
                "message" => "INVALID FILTER TYPE",
                "data"    => []
            ]);
            return;
    }

    // CALCULATE PREVIOUS MONTH DATES FOR GROWTH CALCULATION
    $prev_month_start = date('Y-m-01', strtotime('-1 month', strtotime($today)));
    $prev_month_end = date('Y-m-t', strtotime('-1 month', strtotime($today)));

    // GET CURRENT PERIOD DATA
    $current_conditions = [
        "agent_id"         => $user_id,
        "booking_date[>=]" => $start_date,
        "booking_date[<=]" => $end_date
    ];
    $current_sales = $db->select("hotels_bookings", '*', $current_conditions);

    // GET PREVIOUS MONTH DATA FOR GROWTH CALCULATION
    $previous_month_conditions = [
        "agent_id"         => $user_id,
        "booking_date[>=]" => $prev_month_start,
        "booking_date[<=]" => $prev_month_end
    ];
    $previous_month_sales = $db->select("hotels_bookings", '*', $previous_month_conditions);

    // FUNCTION TO GET GROUP KEY
    function getGroupKey($date, $group_by, $start_date = null) {
        switch ($group_by) {
            case 'day':
                return date('Y-m-d', strtotime($date));
            case 'week':
                // Calculate week number within the period
                if ($start_date) {
                    $period_start = new DateTime($start_date);
                    $current_date = new DateTime($date);
                    $week_diff = $period_start->diff($current_date)->days;
                    $week_number = floor($week_diff / 7) + 1;
                    return 'Week ' . $week_number;
                }
                return 'Week';
            case 'month':
                return date('Y-m', strtotime($date));
            default:
                return date('Y-m-d', strtotime($date));
        }
    }

    // FUNCTION TO GET DISPLAY LABEL
    function getDisplayLabel($key, $group_by, $start_date = null) {
        switch ($group_by) {
            case 'day':
                return date('M d', strtotime($key));
            case 'week':
                // Return the week key as is (Week 1, Week 2, etc.)
                return $key;
            case 'month':
                return date('F', strtotime($key . '-01')); // Full month name like "January"
            default:
                return $key;
        }
    }

    // PRE-FILL RESULT ARRAY FOR CURRENT PERIOD
    $result = [];
    
    if ($group_by === 'day') {
        // For days, create entry for each day
        $period = new DatePeriod(
            new DateTime($start_date),
            new DateInterval('P1D'),
            (new DateTime($end_date))->modify('+1 day')
        );
        
        foreach ($period as $dt) {
            $key = $dt->format('Y-m-d');
            $result[$key] = [
                'label'             => getDisplayLabel($key, $group_by, $start_date),
                'total_commission'  => 0,
                'paid_commission'   => 0,
                'pending_commission'=> 0
            ];
        }
    } elseif ($group_by === 'week') {
        // For weeks, create entry for each week using relative week numbers
        $current_date = new DateTime($start_date);
        $end_date_obj = new DateTime($end_date);
        $week_counter = 1;
        
        while ($current_date <= $end_date_obj) {
            $week_key = 'Week ' . $week_counter;
            
            if (!isset($result[$week_key])) {
                $result[$week_key] = [
                    'label'             => $week_key,
                    'total_commission'  => 0,
                    'paid_commission'   => 0,
                    'pending_commission'=> 0
                ];
            }
            
            $current_date->modify('+7 days');
            $week_counter++;
        }
    } else {
        // For months, create entry for each month (including current month)
        $current_date = new DateTime($start_date);
        $end_date_obj = new DateTime($end_date);
        
        // Ensure we include the current month
        $current_month = date('Y-m');
        $start_month = $current_date->format('Y-m');
        
        while ($current_date->format('Y-m') <= $current_month) {
            $month_key = $current_date->format('Y-m');
            
            if (!isset($result[$month_key])) {
                $result[$month_key] = [
                    'label'             => getDisplayLabel($month_key, $group_by, $start_date),
                    'total_commission'  => 0,
                    'paid_commission'   => 0,
                    'pending_commission'=> 0
                ];
            }
            
            $current_date->modify('+1 month');
        }
    }

    // CALCULATE TOTALS
    $current_total = 0;
    $previous_month_total = 0;

    // PROCESS CURRENT PERIOD DATA
    if (!empty($current_sales)) {
        foreach ($current_sales as $hotel_sale) {
            $group_key = getGroupKey($hotel_sale['booking_date'], $group_by, $start_date);
            $agent_fee = (float)$hotel_sale['agent_fee'];

            // Make sure the group key exists in result array
            if (!isset($result[$group_key])) {
                $result[$group_key] = [
                    'label'             => getDisplayLabel($group_key, $group_by, $start_date),
                    'total_commission'  => 0,
                    'paid_commission'   => 0,
                    'pending_commission'=> 0
                ];
            }

            $result[$group_key]['total_commission'] += $agent_fee;
            $current_total += $agent_fee;

            if (strtolower($hotel_sale['agent_payment_status']) === 'paid') {
                $result[$group_key]['paid_commission'] += $agent_fee;
            } else {
                $result[$group_key]['pending_commission'] += $agent_fee;
            }
        }
        $message = "DATA IS RETRIEVED";
    } else {
        $message = "NO DATA FOUND FOR SELECTED RANGE";
    }

    // PROCESS PREVIOUS MONTH DATA FOR GROWTH CALCULATION
    if (!empty($previous_month_sales)) {
        foreach ($previous_month_sales as $hotel_sale) {
            $agent_fee = (float)$hotel_sale['agent_fee'];
            $previous_month_total += $agent_fee;
        }
    }

    // CALCULATE GROWTH COMPARED TO PREVIOUS MONTH
    $growth = 0;
    if ($previous_month_total > 0) {
        $growth = round((($current_total - $previous_month_total) / $previous_month_total) * 100, 2);
    } else {
        $growth = $current_total > 0 ? 100 : 0;
    }

    // SORT RESULT - Custom sorting for weeks
    if ($group_by === 'week') {
        // Sort weeks by week number
        uksort($result, function($a, $b) {
            $week_a = (int) str_replace('Week ', '', $a);
            $week_b = (int) str_replace('Week ', '', $b);
            return $week_a - $week_b;
        });
    } else {
        // Default sort for other groupings
        ksort($result);
    }

    $response = [
        "status"  => true,
        "message" => $message,
        "data"    => array_values($result),
        "growth"  => $growth, // Growth compared to previous month
        "group_by" => $group_by,
        "previous_month_total" => $previous_month_total, // Added for reference
        "current_total" => $current_total // Added for reference
    ];

    echo json_encode($response);
});
?>