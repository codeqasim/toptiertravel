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

                    // LOOP THROUGH ALL BOOKINGS
                    foreach ($hotel_sales as $hotel_sale) {
                        $booking_date = $hotel_sale['booking_date'];

                        // TOTAL SALES AND COMMISSIONS CALCULATION
                        $total_sales += $hotel_sale['price_markup'];
                        $total_commission += ($hotel_sale['agent_fee'] * $hotel_sale['price_original']) / 100;

                        // COMMISSION PAID VS PENDING CALCULATION
                        if ($hotel_sale['payment_status'] == 'paid') {
                            $total_paid_commission_amount += ($hotel_sale['agent_fee'] * $hotel_sale['price_original']) / 100;
                        } else {
                            $total_pending_commission_amount += ($hotel_sale['agent_fee'] * $hotel_sale['price_original']) / 100;
                        }

                        // CURRENT MONTH CALCULATION
                        if ($booking_date >= $current_month_start && $booking_date <= $current_month_end) {
                            $current_total_sales += $hotel_sale['price_markup'];
                            $current_total_commissions += ($hotel_sale['agent_fee'] * $hotel_sale['price_original']) / 100;
                            $current_month_bookings++;
                        }

                        // LAST MONTH CALCULATION
                        if ($booking_date >= $last_month_start && $booking_date <= $last_month_end) {
                            $last_total_sales += $hotel_sale['price_markup'];
                            $last_total_commissions += ($hotel_sale['agent_fee'] * $hotel_sale['price_original']) / 100;
                            $last_month_bookings++;
                        }

                        // TOTAL BOOKINGS
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

        if(!empty($hotel_sales)){
            foreach ($hotel_sales as $hotel_sale) {

                $guest = json_decode($hotel_sale['guest']);

                $checkin = date('M d Y', strtotime($hotel_sale['checkin']));
                $checkout = date('M d Y', strtotime($hotel_sale['checkout']));

                // Convert to DateTime objects
                $checkinDate = new DateTime($hotel_sale['checkin']);
                $checkoutDate = new DateTime($hotel_sale['checkout']);

                // Calculate duration
                $interval = $checkinDate->diff($checkoutDate);
                $duration = $interval->days; 

                $data []= [
                    'id' => $hotel_sale['booking_id'],
                    'guest' => $guest[0]->title .' '. $guest[0]->first_name .' '. $guest[0]->last_name,
                    'hotel' => $hotel_sale['hotel'],
                    'destination' => $hotel_sale['location'],
                    'checkin' => $hotel_sale['checkin'],
                    'nights' => $duration,
                    'value' => $hotel_sale['price_original'],
                    'rate' => $hotel_sale['agent_fee'],
                    'commission' => ($hotel_sale['agent_fee'] * $hotel_sale['price_original']) / 100,
                ];
            }
            $response = array ( "status" => true ,"message" => 'data is retrieved', "data" => $data);
        }else{
            $response = array ( "status" => false, "message"=>"no record found", "data"=> null ); 
        }

    }else{
        $response = array ( "status" => false, "message"=>"no record found", "data"=> null );
    }

    echo json_encode($response);
});

/*==================
AGENT NOTIFICATIONS API
==================*/
$router->post('agent/dashboard/notifications', function () {
    // INCLUDE CONFIG
    include "./config.php";

    $type = $_POST['status'];
    
    //FETCH NOTIFICATIONS BASED ON STATUS IF STATUS IS APPLIED OTHERWISE ALL NOTIFICATIONS
    if(isset($type)){
        $notifications = $db->select("notifications", "*", ["status" => $type]);
    }else{
        $notifications = $db->select("notifications", "*", []);
    }

    if(isset($notifications)){
        $response = array ( "status" => true, "message"=>"data is retrieved", "data"=> $notifications );
    }else{
        $response = array ( "status" => false, "message"=>"no notification found", "data"=> null );
    }

    echo json_encode($response);
});

/*==================
AGENT BOOKING DETAILS API
==================*/
$router->post('agent/dashboard/booking/details', function () {
    // INCLUDE CONFIG
    include "./config.php";

    required('booking_ref_no');
    $booking_ref_no = $_POST["booking_ref_no"];

    $booking_details = $db->select("hotels_bookings", "*", ['booking_ref_no' => $booking_ref_no]);

    //DECODE THE DATA THAT IS SAVED IN JSON
    if(isset($booking_details)){
        $booking_details[0]['room_data'] = json_decode($booking_details[0]['room_data'], true);
        $booking_details[0]['user_data'] = json_decode($booking_details[0]['user_data'], true);
        $booking_details[0]['guest'] = json_decode($booking_details[0]['guest'], true);
    }

    if(isset($booking_details)){
        $response = array ( "status" => true, "message"=>"data is retrieved", "data"=> $booking_details );
    }else{
        $response = array ( "status" => false, "message"=>"no record found", "data"=> null );
    }

    echo json_encode($response);
});

$router->post('agent/dashboard/bookings', function () {

    // INCLUDE CONFIG
    include "./config.php";

    required('user_id');

    $user_id = $_POST["user_id"];
    $status = $_POST["status"];

    // CHECK EMAIL
    $user = $db->select("users", "*", [ "user_id" => $user_id]);

        if(isset($user[0])){
        
            $user_data = (object)$user[0];
            
            if ($user_data->user_type == 'Agent') {
                if ($user_data->status == 1) {

                    // FETCH ALL BOOKINGS FOR THIS AGENT OF PREVIOUS 15 DAYS
                    if(isset($status)){
                        $hotel_sales = $db->select("hotels_bookings", "*", [
                            "agent_id" => $user_id,
                            "booking_date[>=]" => date("Y-m-d", strtotime("-15 days")),
                            "booking_status" => $status
                        ]);
                    }else{
                        $hotel_sales = $db->select("hotels_bookings", "*", [
                            "agent_id" => $user_id,
                        ]);
                    }
                    

                    $data = [];
                    
                    if(isset($hotel_sales)){
                        foreach ($hotel_sales as $key => $hotel_sale) {
                            $guest = $hotel_sale['guest'];
                            $guest = json_decode($guest);

                            $user_data = $hotel_sale['user_data'];
                            $user_data = json_decode($user_data);
                            
                            $checkin = date('M d Y', strtotime($hotel_sale['checkin']));
                            $checkout = date('M d Y', strtotime($hotel_sale['checkout']));

                            // Convert to DateTime objects
                            $checkinDate = new DateTime($hotel_sale['checkin']);
                            $checkoutDate = new DateTime($hotel_sale['checkout']);

                            // Calculate duration
                            $interval = $checkinDate->diff($checkoutDate);
                            $duration = $interval->days; 

                            $room = $hotel_sale['room_data'];
                            $room = json_decode($room);

                            $data []= [
                                'id' => $hotel_sale['booking_id'],
                                'guest' => $guest[0]->title .' '. $guest[0]->first_name .' '. $guest[0]->last_name,
                                'hotel_name' => $hotel_sale['hotel_name'],
                                'room_name' => $room[0]->room_name,
                                'city' => $hotel_sale['location'] ?? '',
                                'date' => date('M d, Y',strtotime($hotel_sale['booking_date'])),
                                'duration' => $duration,
                                'phone' => $user_data->phone,
                                'email' => $user_data->email,
                                'status' => $hotel_sale['booking_status']
                            ];
                        }

                        // FINAL RESPONSE
                        $response = array("status" => true,"message" => "data has be retrieved","data" => $data);
                    } else {
                        //RESPOSE WHEN NO HOTEL BOOKING ARE AVAILABLE
                        $response = array("status" => false,"message" => "no record found","data" => null);
                    }

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

?>