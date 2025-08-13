<?php
// HEADERS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header("X-Frame-Options: SAMEORIGIN");

/*==================
AGENT SIGNUP API
==================*/
$router->post('agent/dashboard/signup', function () {

    // INCLUDE CONFIG
    include "./config.php";

        // VALIDATION
        required('first_name');
        required('last_name');
        required('phone');
        required('phone_country_code');
        required('email');
        required('password');

        $mob = $new_str = str_replace(' ', '', $_POST['phone']);
        $phone = preg_replace('/[^A-Za-z0-9\-]/', '', $mob); // removes special chars.

        // EMAIL EXIST VALIDATION
        $exist_mail = $db->select('users', [ 'email', ], [ 'email' => $_POST['email'], ]);
        if (isset($exist_mail[0]['email'])) {
            $response = array ( "status"=>false, "message"=>"email already exist.", "data"=> "" );
            echo json_encode($response);
            die;
        }

        // GENERATE RANDOM CODE FOR EMAIL
        $mail_code = rand(100000, 999999);

        // UUID
        $rand = rand(100, 99);
        $date = date('Ymdhis');
        $user_id = $date.$rand;

        // GENERATE PASSWORD AND DATETIME
        $password = md5($_POST['password']);
        $date = date('Y-m-d H:i:s');

        // GET DEFAULT CURRENCY NAME
        $currencies = $db->select("currencies","*" );
        foreach ($currencies as $currency){ if($currency['default'] == 1){ $currency_id = $currency['name']; } }

        $db->insert("users", [
            "user_id" => $user_id,
            "first_name" => $_POST['first_name'],
            "last_name" => $_POST['last_name'],
            "phone_country_code" => $_POST['phone_country_code'],
            "email" => $_POST['email'],
            "phone" => $phone,
            "email_code" => $mail_code,
            "currency_id" => $currency_id,
            "password" => $password,
            "status" => $_POST['status'] ?? 0,
            "user_type" => "Customer",
            "created_at" => $date,
        ]);

        $user_id_ = $db->id();
        $user_info = $db->select("users","*", [ "id" => $user_id_ ]);

        $response = array ( "status"=>true, "message"=>"account registered successfully.", "data"=> $user_info );
        echo json_encode($response);

        $link = root.'../'.'account/activation/'.$user_id.'/'.$mail_code;

        // HOOK
        $hook="user_signup";
        include "./hooks.php";
});

/*==================
AGENT LOGIN API
==================*/
$router->post('agent/dashboard/login', function () {

    // INCLUDE CONFIG
    include "./config.php";

    // VALIDATION
    required('email');
    required('password');

    $data = $db->select("users","*", [
        "email" => $_POST['email'],
        "password" => md5($_POST['password']),
    ]);


    if(isset($data[0])) {

        if ($data[0]['status'] == 0) {
            $response = array ( "status"=> false, "message"=>"user account not verified", "data"=> $data[0] );
            echo json_encode($response);
            die;
        };

        $user_data = (object)$data[0];
        $response = array ( "status"=> true, "message"=>"user details", "data"=> $user_data );

        if(isset($user_data) && $user_data->user_type != 'Agent'){
            $response = array ( "status"=>false, "message"=>"this user is not an agent", "data"=> null );    
        }else{
            if (isset($user_data)){
                if ($user_data->status == 1){

                // HOOK
                $hook="login";
                include "./hooks.php";

            }}

            $SESSION_ARRAY = array(
                "backend_user_login" => true,
                "backend_user_id" => $user_data->user_id,
                "backend_user_email" => $user_data->email,
                "backend_user_type" => $user_data->user_type,
                "backend_user_status" => $user_data->status,
                "backend_user_name" => $user_data->first_name. ' ' .$user_data->last_name,
            );
            
            $_SESSION['phptravels_backend_user'] = json_encode($SESSION_ARRAY);

            // INSERT TO LOGS
            $user_id = $user_data->user_id;
            $log_type = "login";
            $datetime = date("Y-m-d h:i:sa");
            $desc = "user logged into account" .get_client_ip();
            logs($user_id,$log_type,$datetime,$desc);

            include "./logs.php";
        }

    } else {
        $response = array ( "status"=>false, "message"=>"no user found", "data"=> null );
    }

    echo json_encode($response);
});

/*==================
AGENT USER PROFILE API
==================*/
$router->post('agent/dashboard/profile', function() {

    // INCLUDE CONFIG
    include "./config.php";
    required('user_id');

    // USERS
    $data = $db->select("users", "*", ["user_id" => $_POST['user_id'],]);
    if (!empty($data)) {
        $response = array("status" => "true", "message" => "profile details", "data" => $data);
    } else {
        $response = array("status" => "false", "message" => "no profile found", "data" => "");
    }
    echo json_encode($response);

});

/*==================
AGENT FORGOT PASSWORD API
==================*/
$router->post('agent/dashboard/forgot-password', function () {
    
    // INCLUDE CONFIG
    include "./config.php";

    required('email');

    // CHECK EMAIL
    $user = $db->select("users", "*", [ "email" => $_POST['email'] ]);
    

        if (isset($user[0]['status'])) {

            if ($user[0]['status'] == 1) {

                // CHANGE PASSWORD
                $newpass = rand(100000, 999999);
                
                $data = $db->update("users", [
                "password" => md5($newpass),
                ], [ "email" => $_POST['email'], ]);
                
                // IF UPDATED SUCCESSFULLY
                if ($data->rowCount() == 1) {

                    $response = array ( "status"=>true, "message"=>"password has been sent to email", "data"=> $data );

                    // HOOK
                    $hook="forget_password";
                    
                    // include "./hooks.php";
                }

            } else {
                $response = array ( "status"=>false, "message"=>"not_activated", "data"=> null );
            }
        } else {
            $response = array ( "status"=>false, "message"=>"no user found", "data"=> null );
        }
    echo json_encode($response);
});

/*==================
AGENT LOGOUT API
==================*/
$router->post('agent/dashboard/logout', function () {

    // INCLUDE CONFIG
    include "./config.php";

    // VALIDATION
    required('email');

    $data = $db->select("users","*", [
        "email" => $_POST['email'],
    ]);

    if(isset($data[0])) {

        if ($data[0]['status'] == 0) {
            $response = array ( "status"=> false, "message"=>"user account not verified", "data"=> $data[0] );
            echo json_encode($response);
            die;
        };

        $user_data = (object)$data[0];
        
        if(isset($user_data) && $user_data->user_type != 'Agent'){
            $response = array ( "status"=>false, "message"=>"this user is not an agent", "data"=> null );    
        }else{
            if (isset($user_data)){
                if ($user_data->status == 1){

                // HOOK
                $hook="logout";
                include "./hooks.php";
    
            }}

            // INSERT TO LOGS
            $user_id = $user_data->user_id;
            $log_type = "logout";
            $datetime = date("Y-m-d h:i:sa");
            $desc = "user logged out of account" .get_client_ip();
            logs($user_id,$log_type,$datetime,$desc);

            include "./logs.php";

            session_start();
            session_unset();
            session_destroy();
            // header("location:Logins");

            $response = array ( "status"=> true, "message"=>"User Logged Out", "data"=> null );

        }
        
    } else {
        $response = array ( "status"=>false, "message"=>"no user found", "data"=> null );
    }

    echo json_encode($response);
});

/*==================
AGENT SALES AND COMMISSIONS API
==================*/
$router->post('agent/dashboard', function () {
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
                    $hotel_sales = $db->select("hotels_bookings", "*", ["agent_id" => $user_id]);
                    
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

                    $last_total_sales = 0;
                    $last_total_commissions = 0;
                    $last_total_partner_commission = 0;

                    //GET THE PARTNER AGENTS
                    $partners = $db->select('users' , '*' , ['ref_id' => $user_id]);

                    //LOOP THROUGH ALL THE PARTNERS BOOKINGS TO CALACULATE THE PARTNER COMMISSION
                    if(isset($partners)){
                        foreach ($partners as $partner) {
                            $hotels_bookings = $db->select("hotels_bookings", "*", ["agent_id" => $partner->user_id]);
                            if(isset($hotel_bookings)){
                                foreach ($hotel_bookings as $hotel_booking) {
                                    $total_partner_commission += (1 * $hotel_sale['subtotal']) / 100;
                                    
                                    // CURRENT MONTH CALCULATION
                                    if ($booking_date >= $current_month_start && $booking_date <= $current_month_end) {
                                        $current_total_partner_commission += (1 * $hotel_sale['subtotal']) / 100;
                                    }

                                    // LAST MONTH CALCULATION
                                    if ($booking_date >= $last_month_start && $booking_date <= $last_month_end) {
                                        $last_total_partner_commission += (1 * $hotel_sale['subtotal']) / 100;
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

                        // CHECK IF BOOKING IS IN CURRENT MONTH AND ADD TO CURRENT TOTALS
                        if ($booking_date >= $current_month_start && $booking_date <= $current_month_end) {
                            $current_total_sales += isset($hotel_sale['price_markup']) && is_numeric($hotel_sale['price_markup']) ? $hotel_sale['price_markup'] : 0;
                            $current_total_commissions += $commission;
                        }

                        // CHECK IF BOOKING IS IN LAST MONTH AND ADD TO LAST MONTH TOTALS
                        if ($booking_date >= $last_month_start && $booking_date <= $last_month_end) {
                            $last_total_sales += isset($hotel_sale['price_markup']) && is_numeric($hotel_sale['price_markup']) ? $hotel_sale['price_markup'] : 0;
                            $last_total_commissions += $commission;
                        }

                        // INCREMENT TOTAL BOOKINGS COUNT
                        $total_bookings++;
                    }
                    
                    // PERCENT CHANGE FUNCTION
                    function percentChange($current, $last) {
                        if ($last == 0) return $current > 0 ? 100 : 0;
                        return round((($current - $last) / $last) * 100);
                    }

                    // PERCENT CHANGE CALCULATIONS
                    $sales_change = percentChange($current_total_sales, $last_total_sales);
                    $commissions_change = percentChange($current_total_commissions, $last_total_commissions);
                    $partner_commissions_change = percentChange($current_total_partner_commission, $last_total_partner_commission);

                    // AVERAGE SALE AMOUNT CALCULATION
                    $average_sale_amount = $total_bookings > 0 ? ($total_sales / $total_bookings) : 0; // PREVENT DIVISION BY ZERO

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
                        'average_sale_amount' => $average_sale_amount,
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
$router->post('agent/dashboard/bookings/recent', function () {

    // INCLUDE CONFIG
    include "./config.php";

    required('user_id');

    $user_id = $_POST["user_id"];
    $status = isset($_POST['status']) ? $_POST["status"] : '';
    $search = isset($_POST['search']) ? trim($_POST['search']) : '';

    // CHECK EMAIL
    $user = $db->select("users", "*", [ "user_id" => $user_id]);

        if(isset($user[0])){
        
            $user_data = (object)$user[0];
            
            if ($user_data->status == 1) {

                // Pagination variables
                $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
                $limit = 5; // Records per page
                $offset = ($page - 1) * $limit;

                // Base condition
                $conditions = [
                    "agent_id" => $user_id,
                    "booking_date[>=]" => date("Y-m-d", strtotime("-15 days")),
                    "LIMIT" => [$offset, $limit]
                ];

                // Optional booking_status filter
                if (isset($status) && !empty($status)) {
                    $conditions["booking_status"] = $status;
                }

                // Optional search filter
                if (!empty($search)) {
                    $conditions["OR"] = [
                        "hotel_name[~]" => $search,
                        "location[~]" => $search,
                        "booking_id[~]" => $search
                    ];
                }

                // Fetch paginated results
                $hotel_sales = $db->select("hotels_bookings", "*", $conditions);

                // Fetch total count (without LIMIT)
                $total_records = $db->count("hotels_bookings", [
                    "agent_id" => $user_id,
                    "booking_date[>=]" => date("Y-m-d", strtotime("-15 days"))
                ]);

                $data = [];

                if (!empty($hotel_sales)) {
                    foreach ($hotel_sales as $hotel_sale) {
                        $guest = json_decode($hotel_sale['guest']);
                        $user_info = json_decode($hotel_sale['user_data']);
                        $room = json_decode($hotel_sale['room_data']);

                        $checkinDate = new DateTime($hotel_sale['checkin']);
                        $checkoutDate = new DateTime($hotel_sale['checkout']);
                        $duration = $checkinDate->diff($checkoutDate)->days;

                        $data[] = [
                            'id' => $hotel_sale['booking_id'],
                            'guest' => $guest[0]->title .' '. $guest[0]->first_name .' '. $guest[0]->last_name,
                            'hotel_name' => $hotel_sale['hotel_name'],
                            'room_name' => $room[0]->room_name,
                            'city' => $hotel_sale['location'] ?? '',
                            'date' => date('M d, Y', strtotime($hotel_sale['booking_date'])),
                            'duration' => $duration,
                            'phone' => $user_info->phone,
                            'email' => $user_info->email,
                            'status' => $hotel_sale['booking_status']
                        ];
                    }

                    // Pagination info
                    $total_pages = ceil($total_records / $limit);

                    $response = [
                        "status" => true,
                        "message" => "data has been retrieved",
                        "data" => $data,
                        "pagination" => [
                            "current_page" => $page,
                            "total_pages" => $total_pages,
                            "total_records" => $total_records
                        ]
                    ];
                } else {
                    $response = [
                        "status" => false,
                        "message" => "no record found",
                        "data" => null
                    ];
                }

            } else {
                $response = [
                    "status" => false,
                    "message" => "not_activated",
                    "data" => null
                ];
            }
        } else {
            $response = array ( "status"=>false, "message"=>"no user found", "data"=> null );
        }
    echo json_encode($response);
});

/*==================
AGENT NOTIFICATIONS API
==================*/
$router->post('agent/dashboard/notifications', function () {
    // INCLUDE CONFIG
    include "./config.php";

    //FETCH NOTIFICATIONS BASED ON STATUS IF STATUS IS APPLIED OTHERWISE ALL NOTIFICATIONS
    if(isset($_POST['status']) ){
        $notifications = $db->select("notifications", "*", ["status" => $_POST['status']]);
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
    if(isset($booking_details) && !empty($booking_details)){
        $booking_details[0]['room_data'] = json_decode($booking_details[0]['room_data'], true);
        $booking_details[0]['user_data'] = json_decode($booking_details[0]['user_data'], true);
        $booking_details[0]['guest'] = json_decode($booking_details[0]['guest'], true);

        // FINAL RESPONSE
        $response = array ( "status" => true, "message"=>"data is retrieved", "data"=> $booking_details );
    } else {
        $response = array ( "status" => false, "message"=>"no record found", "data"=> null );
    }

    echo json_encode($response);
});

/*==================
AGENT ALL BOOKING API
==================*/
$router->post('agent/dashboard/bookings', function () {

    // INCLUDE CONFIG
    include "./config.php";

    required('user_id');

    $user_id = $_POST["user_id"];
    $status = isset($_POST["status"]) ? $_POST["status"] : '';
    $search = isset($_POST["search"]) ? trim($_POST["search"]) : ''; // <-- Added search input

    // CHECK EMAIL
    $user = $db->select("users", "*", [ "user_id" => $user_id ]);

    if (isset($user[0])) {

        $user_data = (object)$user[0];

        if ($user_data->user_type == 'Agent') {
            if ($user_data->status == 1) {

                // PAGINATION VARIABLES
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
                $offset = ($page - 1) * $limit;

                // Base conditions
                $conditions = [
                    "agent_id" => $user_id
                ];

                // Status filter
                if (!empty($status)) {
                    $conditions["booking_status"] = $status;
                }

                // Limit to last 15 days
                $conditions["booking_date[>=]"] = date("Y-m-d", strtotime("-15 days"));

                // Search filter
                if (!empty($search)) {
                    $conditions["OR"] = [
                        "hotel_name[~]" => $search,
                        "location[~]" => $search,
                        "booking_id[~]" => $search
                    ];
                }

                // Fetch bookings
                $hotel_sales = $db->select("hotels_bookings", "*", array_merge($conditions, [
                    "LIMIT" => [$offset, $limit]
                ]));

                // Total records (without LIMIT)
                $total_records = $db->count("hotels_bookings", $conditions);

                $data = [];

                if (!empty($hotel_sales)) {
                    foreach ($hotel_sales as $hotel_sale) {
                        $guest = json_decode($hotel_sale['guest']);
                        $user_data_decoded = json_decode($hotel_sale['user_data']);
                        $room = json_decode($hotel_sale['room_data']);

                        $checkinDate = new DateTime($hotel_sale['checkin']);
                        $checkoutDate = new DateTime($hotel_sale['checkout']);
                        $duration = $checkinDate->diff($checkoutDate)->days;

                        $data[] = [
                            'id' => $hotel_sale['booking_id'],
                            'guest' => $guest[0]->title . ' ' . $guest[0]->first_name . ' ' . $guest[0]->last_name,
                            'hotel_name' => $hotel_sale['hotel_name'],
                            'room_name' => $room[0]->room_name,
                            'city' => $hotel_sale['location'] ?? '',
                            'date' => date('M d, Y', strtotime($hotel_sale['booking_date'])),
                            'duration' => $duration,
                            'phone' => $user_data_decoded->phone,
                            'email' => $user_data_decoded->email,
                            'status' => $hotel_sale['booking_status']
                        ];
                    }

                    $total_pages = ceil($total_records / $limit);

                    $response = [
                        "status" => true,
                        "message" => "data has been retrieved",
                        "data" => $data,
                        "pagination" => [
                            "current_page" => $page,
                            "total_pages" => $total_pages,
                            "total_records" => $total_records,
                            "limit" => $limit
                        ]
                    ];
                } else {
                    $response = [
                        "status" => false,
                        "message" => "no record found",
                        "data" => null
                    ];
                }

            } else {
                $response = [
                    "status" => false,
                    "message" => "not_activated",
                    "data" => null
                ];
            }
        } else {
            $response = [
                "status" => false,
                "message" => "This user is not an agent",
                "data" => null
            ];
        }
    } else {
        $response = [
            "status" => false,
            "message" => "no user found",
            "data" => null
        ];
    }

    echo json_encode($response);
});

?>