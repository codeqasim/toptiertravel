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

                    // FETCH ALL BOOKINGS FOR THIS AGENT
                    $hotel_sales = $db->select("hotels_bookings", "*", [
                        "agent_id" => $user_id
                    ]);

                    // INITIALIZE TOTAL VARIABLES
                    $total_sales = 0;
                    $total_commission = 0;
                    $total_partner_commission = 0;
                    $total_bookings = 0;

                    $total_paid_commission_amount = 0;
                    $total_pending_commission_amount = 0;

                    $current_total_sales = 0;
                    $current_total_commissions = 0;
                    $current_total_partner_commission = 0;
                    $$current_month_bookings = 0;

                    $last_total_sales = 0;
                    $last_total_commissions = 0;
                    $last_total_partner_commission = 0;
                    $last_month_bookings= 0;

                    //GET THE PARTNER AGENTS
                    $partners = $db->select('partners' , '*' , ['parent_id' => $user_id]);

                    //LOOP THROUGH ALL THE PARTNERS BOOKINGS TO CALACULATE THE PARTNER COMMISSION
                    if(isset($partners)){
                        foreach ($partners as $partner) {
                            $hotels_bookings = $db->select("hotels_bookings", "*", ["agent_id" => $partner->parent_id]);
                            if(isset($hotel_bookings)){
                                foreach ($hotel_bookings as $hotel_booking) {
                                    $total_partner_commission += ($hotel_sale['partner_fee'] * $hotel_sale['price_original']) / 100;
                                    
                                    // CURRENT MONTH CALCULATION
                                    if ($booking_date >= $current_month_start && $booking_date <= $current_month_end) {
                                        $current_total_partner_commission += ($hotel_sale['partner_fee'] * $hotel_sale['price_original']) / 100;
                                    }

                                    // LAST MONTH CALCULATION
                                    if ($booking_date >= $last_month_start && $booking_date <= $last_month_end) {
                                        $last_total_partner_commission += ($hotel_sale['partner_fee'] * $hotel_sale['price_original']) / 100;
                                    }
                                }
                            }
                        }
                    }

                    // LOOP THROUGH ALL HOTEL BOOKINGS
                    foreach ($hotel_sales as $hotel_sale) {

                        // FORMAT THE BOOKING DATE TO 'Y-M-D' FOR DATE COMPARISON
                        $booking_date = date('Y-m-d', strtotime($hotel_sale['booking_date']));

                        // VALIDATE THAT agent_fee AND price_original ARE SET AND NUMERIC
                        $agent_fee = isset($hotel_sale['agent_fee']) && is_numeric($hotel_sale['agent_fee']) ? $hotel_sale['agent_fee'] : 0;
                        $price_original = isset($hotel_sale['price_original']) && is_numeric($hotel_sale['price_original']) ? $hotel_sale['price_original'] : 0;

                        // CALCULATE COMMISSION ONLY IF BOTH VALUES ARE VALID AND GREATER THAN ZERO
                        $commission = 0;
                        if ($agent_fee > 0 && $price_original > 0) {
                            $commission = ($agent_fee * $price_original) / 100;
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

                        // CHECK IF BOOKING IS IN CURRENT MONTH AND ADD TO CURRENT TOTALS
                        if ($booking_date >= $current_month_start && $booking_date <= $current_month_end) {
                            $current_total_sales += isset($hotel_sale['price_markup']) && is_numeric($hotel_sale['price_markup']) ? $hotel_sale['price_markup'] : 0;
                            $current_total_commissions += $commission;
                            $current_month_bookings++;
                        }

                        // CHECK IF BOOKING IS IN LAST MONTH AND ADD TO LAST MONTH TOTALS
                        if ($booking_date >= $last_month_start && $booking_date <= $last_month_end) {
                            $last_total_sales += isset($hotel_sale['price_markup']) && is_numeric($hotel_sale['price_markup']) ? $hotel_sale['price_markup'] : 0;
                            $last_total_commissions += $commission;
                            $last_month_bookings++;
                        }

                        // INCREMENT TOTAL BOOKINGS COUNT
                        $total_bookings++;
                    }

                    

                    // PERCENT CHANGE FUNCTION
                    function percentChange($current, $last) {
                        if ($last == 0) return $current > 0 ? 100 : 0;
                        return round((($current - $last) / $last) * 100);
                    }
                    
                    // CURRENT MONTH AVERAGE COMMISSION CALCULATION
                    $current_month_average_commission_rate = $cuurent_total_commissions > 0 ? ($current_total_commissions * 100 ) / $current_total_sales : 0; // PREVENT DIVISION BY ZERO
                    
                    // LAST MONTH AVERAGE COMMISSION CALCULATION
                    $last_month_average_commission_rate = $last_total_commissions > 0 ? ($last_total_commissions * 100 ) / $last_total_sales : 0; // PREVENT DIVISION BY ZERO

                    // PERCENT CHANGE CALCULATIONS
                    $sales_change = percentChange($current_total_sales, $last_total_sales);
                    $commissions_change = percentChange($current_total_commissions, $last_total_commissions);
                    $partner_commissions_change = percentChange($current_total_partner_commission, $last_total_partner_commission);
                    $bookings_change = percentChange($current_month_bookings, $last_month_bookings);
                    $average_commission_change_percent = percentChange($current_month_average_commission_rate, $last_month_average_commission_rate);

                    // AVERAGE COMMISSION CALCULATION
                    $average_commission_rate = $total_commission > 0 ? ($total_commission * 100 ) / $total_sales : 0; // PREVENT DIVISION BY ZERO

                    //FETCH NEW NOTIFICATIONS
                    $notifications = $db->count("notifications", "*", ["status" => 1]);

                    // FINAL DATA RESPONSE
                    $data = [
                        'user' => $user,
                        'notifications' => $notifications,
                        'total_sales' => $total_sales,
                        'sales_change_percent' => $sales_change,
                        'total_commissions' => $total_commission,
                        'commissions_change_percent' => $commissions_change,
                        'partner_total_commissions' => $total_partner_commission,
                        'partner_commissions_change_percent' => $partner_commissions_change,
                        'total_paid_commission_amount' => $total_paid_commission_amount,
                        'total_pending_commission_amount' => $total_pending_commission_amount,
                        'total_bookings' => $total_bookings,
                        'booking_change_percent' => $bookings_change,
                        'average_commission_rate' => $average_commission_rate,
                        'average_commission_rate_change_percent' => $average_commission_change_percent,
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

    // INCLUDE CONFIG
    include "./config.php";

    required('user_id');

    $user_id = $_POST["user_id"];
    $value = $_POST["value"] ?? null;     // Minimum commission value
    $month = $_POST["month"] ?? null;     // Format: YYYY-MM
    $status = $_POST["status"] ?? null;   // Can be 'confirmed', 'pending', or null (both)

    // Base conditions
    $conditions = [
        "agent_id" => $user_id,
    ];

    // Add booking status filter
    if (!empty($status)) {
        $conditions["booking_status"] = $status;
    } else {
        $conditions["booking_status"] = ["confirmed", "pending"];
    }

    // Add month filter
    if (!empty($month)) {
        $start_date = $month . "-01";
        $end_date = date("Y-m-t", strtotime($start_date));
        $conditions["booking_date[<>]"] = [$start_date, $end_date];
    }

    // Fetch all matching rows first
    $all_sales = $db->select("hotels_bookings", "*", $conditions);
    $data = [];
    if(isset($all_sales) && !empty($all_sales)){
        // Filter by calculated commission if needed
        $hotel_sales = array_filter($all_sales, function($sale) use ($value) {
            if ($value === null) return true;

            // Commission = price_original * agent_fee / 100
            $commission = ($sale['price_original'] * $sale['agent_fee']) / 100;
            return $commission >= $value;
        });

        // CHECK IF HOTEL SALES ARRAY IS NOT EMPTY
        if (!empty($hotel_sales)) {

            // INITIALIZE DATA ARRAY
            $data = [];

            // LOOP THROUGH EACH HOTEL BOOKING
            foreach ($hotel_sales as $hotel_sale) {

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

                // FORMAT CHECK-IN AND CHECK-OUT DATES
                $checkin = isset($hotel_sale['checkin']) ? date('M d Y', strtotime($hotel_sale['checkin'])) : 'N/A';
                $checkout = isset($hotel_sale['checkout']) ? date('M d Y', strtotime($hotel_sale['checkout'])) : 'N/A';

                // CONVERT TO DATETIME OBJECTS TO CALCULATE DURATION
                $duration = 0;
                if (!empty($hotel_sale['checkin']) && !empty($hotel_sale['checkout'])) {
                    try {
                        $checkinDate = new DateTime($hotel_sale['checkin']);
                        $checkoutDate = new DateTime($hotel_sale['checkout']);
                        $interval = $checkinDate->diff($checkoutDate);
                        $duration = $interval->days;
                    } catch (Exception $e) {
                        $duration = 0;
                    }
                }

                // VALIDATE FEE AND VALUE BEFORE CALCULATION
                $agent_fee = isset($hotel_sale['agent_fee']) && is_numeric($hotel_sale['agent_fee']) ? $hotel_sale['agent_fee'] : 0;
                $price_original = isset($hotel_sale['price_original']) && is_numeric($hotel_sale['price_original']) ? $hotel_sale['price_original'] : 0;
                $commission = ($agent_fee > 0 && $price_original > 0) ? ($agent_fee * $price_original) / 100 : 0;

                // ADD DATA TO RESPONSE ARRAY
                $data[] = [
                    'id' => $hotel_sale['booking_id'] ?? null,
                    'guest' => $guest_name,
                    'hotel' => $hotel_sale['hotel_name'] ?? 'N/A',
                    'destination' => $hotel_sale['location'] ?? 'N/A',
                    'checkin' => $hotel_sale['checkin'] ?? 'N/A',
                    'nights' => $duration,
                    'value' => $price_original,
                    'rate' => $agent_fee,
                    'commission' => $commission,
                ];
            }

            // SET SUCCESS RESPONSE
            $response = [
                "status" => true,
                "message" => 'DATA IS RETRIEVED',
                "data" => $data
            ];
        } else {
            // SET ERROR RESPONSE
            $response = [
                "status" => false,
                "message" => "NO RECORD FOUND",
                "data" => null
            ];
        }

    }else{
        $response = array ( "status" => false, "message"=>"no record found", "data"=> null );
    }

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
    
    // Fetch all matching rows first
    $all_sales = $db->select("hotels_bookings", "*", ['agent_id' => $user_id,'agent_fee[!]' => null]);
    
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
                $price_original = isset($hotel_sale['price_original']) && is_numeric($hotel_sale['price_original']) ? $hotel_sale['price_original'] : 0;
                $commission = ($agent_fee > 0 && $price_original > 0) ? ($agent_fee * $price_original) / 100 : 0;

                // ADD DATA TO RESPONSE ARRAY
                $data[] = [
                    'guest' => $guest_name,
                    'hotel' => $hotel_sale['hotel_name'] ?? 'N/A',
                    'rate' => $agent_fee,
                    'commission' => $commission,
                ];
                
            }

            // SORT DATA ARRAY BY COMMISSION IN DESCENDING ORDER
            usort($data, function ($a, $b) {
                return $b['commission'] <=> $a['commission'];
            });

            // GET TOP 5 COMMISSION ENTRIES
            $top_commissions = array_slice($data, 0, 5);

            // SET SUCCESS RESPONSE
            $response = ["status" => true,"message" => 'DATA IS RETRIEVED',"data" => $top_commissions];

    }else{
        $response = array ( "status" => false, "message"=>"no record found", "data"=> null );
    }
    echo json_encode($response);
});

?>