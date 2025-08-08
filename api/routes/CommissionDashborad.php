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

                    //GET THE PARTNER AGENTS
                    $partners = $db->select('users' , '*' , ['ref_id' => $user_id]);

                    //LOOP THROUGH ALL THE PARTNERS BOOKINGS TO CALACULATE THE PARTNER COMMISSION
                    if(isset($partners)){
                        foreach ($partners as $partner) {
                            $hotels_bookings = $db->select("hotels_bookings", "*", ["agent_id" => $partner->user_id]);
                            if(isset($hotel_bookings)){
                                foreach ($hotel_bookings as $hotel_booking) {
                                    $total_partner_commission += (1 * $hotel_sale['price_original']) / 100;
                                }
                            }
                        }
                    }

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
                    $average_commission_rate = $total_commission > 0 ? ($total_commission * 100 ) / $total_sales : 0; // PREVENT DIVISION BY ZERO

                    //FETCH NEW NOTIFICATIONS
                    $notifications = $db->count("notifications", "*", ["status" => 1]);

                    // FINAL DATA RESPONSE
                    $data = [
                        'user' => $user,
                        'notifications' => $notifications,
                        'total_sales' => number_format($total_sales,2),
                        'total_commissions' => number_format($total_commission,2),
                        'total_bookings' => $total_bookings,
                        'average_commission_rate' => $average_commission_rate,
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
                $subtotal = isset($hotel_sale['subtotal']) && is_numeric($hotel_sale['subtotal']) ? $hotel_sale['subtotal'] : 0;
                $rate = ($agent_fee > 0 && $subtotal > 0) ? ($agent_fee * 100) / $subtotal : 0;

                // ADD DATA TO RESPONSE ARRAY
                $data[] = [
                    'id' => $hotel_sale['booking_id'] ?? null,
                    'guest' => $guest_name,
                    'hotel' => $hotel_sale['hotel_name'] ?? 'N/A',
                    'destination' => $hotel_sale['location'] ?? 'N/A',
                    'checkin' => $hotel_sale['checkin'] ?? 'N/A',
                    'nights' => $duration,
                    'value' => $price_original,
                    'rate' => $rate,
                    'commission' => $agent_fee,
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
            $top_commissions = array_slice($data, 0, 5);

            // SET SUCCESS RESPONSE
            $response = ["status" => true,"message" => 'DATA IS RETRIEVED',"data" => $top_commissions];

    }else{
        $response = array ( "status" => false, "message"=>"no record found", "data"=> null );
    }
    echo json_encode($response);
});

$router->post('agent/dashboard/commissions/trends', function () {

    // INCLUDE CONFIG
    include "./config.php";

    required('user_id');

    $user_id = $_POST["user_id"];
    
    // Fetch all matching rows first
    $all_sales = $db->select("hotels_bookings", "*", ['agent_id' => $user_id,'agent_fee[!]' => null]);
    
    $data = [];
    
    echo json_encode($response);
});



?>