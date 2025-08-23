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

    // reCAPTCHA v3 VERIFICATION - UPDATED FOR v3
    if (!isset($_POST['recaptcha_token']) || empty($_POST['recaptcha_token'])) {
        echo json_encode([
            "status"  => false,
            "message" => "Please complete the reCAPTCHA verification.",
            "data"    => ""
        ]);
        die;
    }

    // VERIFY reCAPTCHA v3 WITH GOOGLE
    $recaptchaSecret = '6Lcwl68rAAAAAML_0FadvMSNW71lz30RoO2Mw94L';
    $recaptchaResponse = $_POST['recaptcha_token'];
    
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => $recaptchaSecret,
        'response' => $recaptchaResponse,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];
    
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $captchaResult = json_decode($result, true);
    
    if (!$captchaResult['success']) {
        echo json_encode([
            "status"  => false,
            "message" => "reCAPTCHA verification failed. Please try again.",
            "data"    => ""
        ]);
        die;
    }

    // reCAPTCHA v3 SCORE CHECK (0.0 = likely bot, 1.0 = likely human)
    if (isset($captchaResult['score']) && $captchaResult['score'] < 0.5) {
        echo json_encode([
            "status"  => false,
            "message" => "Suspicious activity detected. Please try again.",
            "data"    => ""
        ]);
        die;
    }

    // REQUIRED FIELD VALIDATION
    required('first_name');
    required('last_name');
    required('phone_country_code');
    required('country_code');
    required('phone');
    required('address');
    required('agency_city');
    required('agency_name');
    required('agency_license');
    required('email');
    required('password');
    required('confirm_password');

    // PASSWORD CONFIRMATION
    if ($_POST['password'] !== $_POST['confirm_password']) {
        echo json_encode([
            "status"  => false,
            "message" => "Password and Confirm Password do not match.",
            "data"    => ""
        ]);
        die;
    }

    // SANITIZE PHONE
    $mob = str_replace(' ', '', $_POST['phone']);
    $phone = preg_replace('/[^0-9]/', '', $mob); // Keep only digits

    // EMAIL EXIST VALIDATION
    $exist_mail = $db->select('users', ['email'], ['email' => $_POST['email']]);
    if (!empty($exist_mail)) {
        echo json_encode([
            "status"  => false,
            "message" => "Email already exists.",
            "data"    => ""
        ]);
        die;
    }

    // GENERATE RANDOM CODE FOR EMAIL VERIFICATION
    $mail_code = rand(100000, 999999);

    // GENERATE USER ID
    $date_str = date('YmdHis');
    $rand = rand(10, 99);
    $user_id = $date_str . $rand;

    // ENCRYPT PASSWORD
    $password = md5($_POST['password']);
    $date = date('Y-m-d H:i:s');

    // GET DEFAULT CURRENCY NAME
    $currency_id = null;
    $currencies = $db->select("currencies", "*");
    foreach ($currencies as $currency) {
        if ($currency['default'] == 1) {
            $currency_id = $currency['name'];
            break;
        }
    }

    // INSERT USER
    $db->insert("users", [
        "user_id"           => $user_id,
        "first_name"        => $_POST['first_name'],
        "last_name"         => $_POST['last_name'],
        "phone_country_code"=> $_POST['phone_country_code'],
        "country_code"      => $_POST['country_code'],
        "address1"          => $_POST['address'],
        "company_name"      => $_POST['agency_name'],
        "company_license"   => $_POST['agency_license'],
        "email"             => $_POST['email'],
        "city"              => $_POST['agency_city'],
        "phone"             => $phone,
        "email_code"        => $mail_code,
        "currency_id"       => $currency_id,
        "password"          => $password,
        "status"            => $_POST['status'] ?? 0,
        "user_type"         => "Agent",
        "created_at"        => $date,
    ]);

    // GET INSERTED USER
    $user_id = $db->id();

    // if ref_id is available in post then it should be saved 
    if (isset($_POST['ref_id']) && !empty($_POST['ref_id']) && $_POST['ref_id'] != null) {
        $db->update("users", ["ref_id" => $_POST['ref_id']], ["id" => $user_id]);
    }

    // GET UPDATED USER INFO
    $user_info = $db->get("users", "*", ["id" => $user_id]);

    // RESPONSE
    echo json_encode([
        "status"  => true,
        "message" => "Account registered successfully.",
        "data"    => $user_info
    ]);

    // ACTIVATION LINK
    $link = root . '../' . 'account/activation/' . $user_id . '/' . $mail_code;

    // HOOK
    $hook = "user_signup";
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
        if (isset($user_data)) {
            $user_data->profile_photo = !empty($user_data->profile_photo) 
                ? 'https://toptiertravel.site/assets/uploads/' . $user_data->profile_photo
                : null; 
        }
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
                    
                    include "./hooks.php";
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
            
            if ($user_data->user_type == 'Agent') {
                if ($user_data->status == 1) {

                    // GET CURRENT AND LAST MONTH DATE RANGES
                    $current_month_start = date('Y-m-01');
                    $current_month_end = date('Y-m-t');

                    $last_month_start = date('Y-m-01', strtotime('first day of last month'));
                    $last_month_end = date('Y-m-t', strtotime('last day of last month'));

                    // FETCH ALL BOOKINGS FOR THIS AGENT
                    $hotel_sales = $db->select("hotels_bookings", "*", ["agent_id" => $user_id, "payment_status" => "paid"]);
                    
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
                    if(isset($partners) && !empty($partners)){
                        foreach ($partners as $partner) {
                            $hotels_bookings = $db->select("hotels_bookings", "*", ["agent_id" => $partner['user_id'], "payment_status" => "paid"]);
                            if(isset($hotels_bookings) && !empty($hotels_bookings)){
                                foreach ($hotels_bookings as $hotel_booking) {
                                    // FORMAT THE BOOKING DATE TO 'Y-M-D' FOR DATE COMPARISON
                                    $booking_date = date('Y-m-d', strtotime($hotel_booking['booking_date']));
                        
                                    $total_partner_commission += (1 * $hotel_booking['subtotal']) / 100;
                                    
                                    // CURRENT MONTH CALCULATION
                                    if ($booking_date >= $current_month_start && $booking_date <= $current_month_end) {
                                        $current_total_partner_commission += (1 * $hotel_booking['subtotal']) / 100;
                                    }

                                    // LAST MONTH CALCULATION
                                    if ($booking_date >= $last_month_start && $booking_date <= $last_month_end) {
                                        $last_total_partner_commission += (1 * $hotel_booking['subtotal']) / 100;
                                    }
                                }
                            }
                        }
                    }
                    
                    // LOOP THROUGH ALL HOTEL BOOKING
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
                        if (isset($hotel_sale['agent_payment_status']) && $hotel_sale['agent_payment_status'] === 'paid') {
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
            $page = isset($_POST['page']) && is_numeric($_POST['page']) ? (int) $_POST['page'] : 1;
            $limit = isset($_POST['limit']) && is_numeric($_POST['limit']) ? (int) $_POST['limit'] : 5; // Records per page
            $offset = ($page - 1) * $limit;

            // Base condition (without search)
            $conditions = [
                "agent_id" => $user_id,
                "booking_date[>=]" => date("Y-m-d", strtotime("-15 days"))
            ];

            // Optional booking_status filter
            if (isset($status) && !empty($status)) {
                $conditions["booking_status"] = $status;
            }

            // Get all records first (without search)
            $all_records = $db->select("hotels_bookings", "*", $conditions);

            // Apply search filter if provided
            if (!empty($search)) {
                $searched_records = [];
                
                foreach ($all_records as $hotel_sale) {
                    // Decode JSON data
                    $guest = json_decode($hotel_sale['guest']);
                    $user_info = json_decode($hotel_sale['user_data']);
                    $room = json_decode($hotel_sale['room_data']);
                    
                    // Build guest name
                    $guest_name = '';
                    if (!empty($guest[0])) {
                        $guest_name = ($guest[0]->title ?? '') . ' ' . ($guest[0]->first_name ?? '') . ' ' . ($guest[0]->last_name ?? '');
                        $guest_name = trim($guest_name);
                    }
                    
                    // Search in all fields except date and duration
                    if (stripos($hotel_sale['booking_id'] ?? '', $search) !== false ||
                        stripos($hotel_sale['booking_ref_no'] ?? '', $search) !== false ||
                        stripos($guest_name, $search) !== false ||
                        stripos($hotel_sale['hotel_name'] ?? '', $search) !== false ||
                        stripos($room[0]->room_name ?? '', $search) !== false ||
                        stripos($hotel_sale['location'] ?? '', $search) !== false ||
                        stripos($user_info->phone ?? '', $search) !== false ||
                        stripos($user_info->email ?? '', $search) !== false ||
                        stripos($hotel_sale['booking_status'] ?? '', $search) !== false) {
                        
                        $searched_records[] = $hotel_sale;
                    }
                }
                $hotel_sales_to_use = $searched_records;
            } else {
                $hotel_sales_to_use = $all_records;
            }

            // Apply pagination to filtered results
            $total_records = count($hotel_sales_to_use);
            $hotel_sales = array_slice($hotel_sales_to_use, $offset, $limit);

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
                        'booking_ref_no' => $hotel_sale['booking_ref_no'],
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
                    "data" => null,
                    "pagination" => [
                        "current_page" => $page,
                        "total_pages" => 0,
                        "total_records" => 0
                    ]
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
    include "./config.php";
    session_start();

    $response = [
        "status"  => false,
        "message" => "Invalid request",
        "data"    => null
    ];

    // MARK NOTIFICATIONS AS READ
    if (!empty($_POST['action'])) {
        switch ($_POST['action']) {
            case 'mark_all_read':
                $db->update("notifications", ["status" => '0']);
                $response = [
                    "status"  => true,
                    "message" => "All notifications marked as read",
                    "data"    => null
                ];
                echo json_encode($response);
                return;

            case 'mark_one':
                if (!empty($_POST['id'])) {
                    $db->update("notifications", ["status" => '0'], ['id' => $_POST['id']]);
                    $response = [
                        "status"  => true,
                        "message" => "Notification marked as read",
                        "data"    => [
                            "id" => $_POST['id']
                        ]
                    ];
                } else {
                    $response = [
                        "status"  => false,
                        "message" => "Notification ID is required",
                        "data"    => null
                    ];
                }
                echo json_encode($response);
                return;

        }
    }

    // FETCH NOTIFICATIONS
    $limit = isset($_POST['limit']) && is_numeric($_POST['limit']) ? (int)$_POST['limit'] : 10;

    if (!isset($_SESSION['last_offset'])) {
        $_SESSION['last_offset'] = 0;
    }

    $offset = $_SESSION['last_offset'];
    $where = [
        "ORDER" => ["id" => "DESC"], // latest first
        "LIMIT" => [$offset, $limit]
    ];

    if (!empty($_POST['status'])) {
        $where["status"] = $_POST['status'];
    }

    $newRecords = $db->select("notifications", "*", $where);

    // Update session for next scroll
    $_SESSION['last_offset'] += $limit;

    $totalCount = $db->count("notifications", !empty($_POST['status']) ? ["status" => $_POST['status']] : []);

    if (!empty($newRecords)) {
        $response = [
            "status"  => true,
            "message" => "Notifications retrieved successfully",
            "data"    => $newRecords,
            "meta"    => [
                "total"       => $totalCount,
                "fetched"     => count($newRecords),
                "last_offset" => $_SESSION['last_offset'],
                "has_more"    => ($_SESSION['last_offset'] < $totalCount)
            ]
        ];
    } else {
        $response = [
            "status"  => false,
            "message" => "No more notifications found",
            "data"    => [],
            "meta"    => [
                "total"       => $totalCount,
                "fetched"     => 0,
                "last_offset" => $_SESSION['last_offset'],
                "has_more"    => false
            ]
        ];
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

    // FETCH BOOKING DATA BY REFERENCE NUMBER
    $hotel_sale = $db->select("hotels_bookings", "*", [
        "booking_ref_no" => $booking_ref_no
    ]);

    if (!empty($hotel_sale)) {
        
        // GET FIRST RECORD (SHOULD BE ONLY ONE)
        $booking_data = $hotel_sale[0];

        // CALCULATE NIGHTS BETWEEN CHECKIN AND CHECKOUT
        $calculated_nights = 0;
        if (!empty($booking_data['checkin']) && !empty($booking_data['checkout'])) {
            try {
                $checkinDate = new DateTime($booking_data['checkin']);
                $checkoutDate = new DateTime($booking_data['checkout']);
                $calculated_nights = $checkinDate->diff($checkoutDate)->days;
            } catch (Exception $e) {
                $calculated_nights = 0;
            }
        }

        // VALIDATE THAT price_markup, price_original AND agent_fee ARE SET AND NUMERIC
        $price_markup = isset($booking_data['price_markup']) && is_numeric($booking_data['price_markup']) ? $booking_data['price_markup'] : 0;
        $price_original = isset($booking_data['price_original']) && is_numeric($booking_data['price_original']) ? $booking_data['price_original'] : 0;
        $agent_fee = isset($booking_data['agent_fee']) && is_numeric($booking_data['agent_fee']) ? $booking_data['agent_fee'] : 0;

        // CALCULATE REVENUE: price_markup - price_original - agent_fee
        $revenue = round($price_markup - $price_original - $agent_fee, 2);

        // DECODE JSON FIELDS
        $user_data_decoded = null;
        $guest_data_decoded = null;
        $room_data_decoded = null;

        if (!empty($booking_data['user_data'])) {
            $user_data_decoded = json_decode($booking_data['user_data'], true);
        }

        if (!empty($booking_data['guest'])) {
            $guest_data_decoded = json_decode($booking_data['guest'], true);
        }

        if (!empty($booking_data['room_data'])) {
            $room_data_decoded = json_decode($booking_data['room_data'], true);
        }

        // CREATE COMPLETE ARRAY RESPONSE WITH ALL FIELDS
        $complete_booking_data = [
            // DATABASE FIELDS - ALL 67 FIELDS
            'booking_id' => $booking_data['booking_id'] ?? null,
            'booking_ref_no' => $booking_data['booking_ref_no'] ?? null,
            'booking_date' => $booking_data['booking_date'] ?? null,
            'booking_status' => $booking_data['booking_status'] ?? null,
            'price_original' => $booking_data['price_original'] ?? null,
            'price_markup' => $booking_data['price_markup'] ?? null,
            'agent_fee' => $booking_data['agent_fee'] ?? null,
            'vat' => $booking_data['vat'] ?? null,
            'tax' => $booking_data['tax'] ?? null,
            'gst' => $booking_data['gst'] ?? null,
            'first_name' => $booking_data['first_name'] ?? null,
            'last_name' => $booking_data['last_name'] ?? null,
            'email' => $booking_data['email'] ?? null,
            'address' => $booking_data['address'] ?? null,
            'phone_country_code' => $booking_data['phone_country_code'] ?? null,
            'phone' => $booking_data['phone'] ?? null,
            'country' => $booking_data['country'] ?? null,
            'stars' => $booking_data['stars'] ?? null,
            'hotel_id' => $booking_data['hotel_id'] ?? null,
            'hotel_name' => $booking_data['hotel_name'] ?? null,
            'hotel_phone' => $booking_data['hotel_phone'] ?? null,
            'hotel_email' => $booking_data['hotel_email'] ?? null,
            'hotel_website' => $booking_data['hotel_website'] ?? null,
            'hotel_address' => $booking_data['hotel_address'] ?? null,
            'room_data' => $room_data_decoded,
            'location' => $booking_data['location'] ?? null,
            'location_cords' => $booking_data['location_cords'] ?? null,
            'hotel_img' => $booking_data['hotel_img'] ?? null,
            'checkin' => $booking_data['checkin'] ?? null,
            'checkout' => $booking_data['checkout'] ?? null,
            'booking_nights' => $booking_data['booking_nights'] ?? null,
            'adults' => $booking_data['adults'] ?? null,
            'childs' => $booking_data['childs'] ?? null,
            'child_ages' => $booking_data['child_ages'] ?? null,
            'currency_original' => $booking_data['currency_original'] ?? null,
            'currency_markup' => $booking_data['currency_markup'] ?? null,
            'payment_date' => $booking_data['payment_date'] ?? null,
            'cancellation_request' => $booking_data['cancellation_request'] ?? null,
            'cancellation_status' => $booking_data['cancellation_status'] ?? null,
            'booking_data' => $booking_data['booking_data'] ?? null,
            'payment_status' => $booking_data['payment_status'] ?? null,
            'supplier' => $booking_data['supplier'] ?? null,
            'transaction_id' => $booking_data['transaction_id'] ?? null,
            'user_id' => $booking_data['user_id'] ?? null,
            'user_data' => $user_data_decoded,
            'guest' => $guest_data_decoded,
            'nationality' => $booking_data['nationality'] ?? null,
            'payment_gateway' => $booking_data['payment_gateway'] ?? null,
            'module_type' => $booking_data['module_type'] ?? null,
            'pnr' => $booking_data['pnr'] ?? null,
            'booking_response' => $booking_data['booking_response'] ?? null,
            'error_response' => $booking_data['error_response'] ?? null,
            'agent_id' => $booking_data['agent_id'] ?? null,
            'net_profit' => $booking_data['net_profit'] ?? null,
            'booking_note' => $booking_data['booking_note'] ?? null,
            'supplier_payment_status' => $booking_data['supplier_payment_status'] ?? null,
            'supplier_due_date' => $booking_data['supplier_due_date'] ?? null,
            'cancellation_terms' => $booking_data['cancellation_terms'] ?? null,
            'supplier_cost' => $booking_data['supplier_cost'] ?? null,
            'supplier_id' => $booking_data['supplier_id'] ?? null,
            'supplier_payment_type' => $booking_data['supplier_payment_type'] ?? null,
            'customer_payment_type' => $booking_data['customer_payment_type'] ?? null,
            'iata' => $booking_data['iata'] ?? null,
            'agent_commission_status' => $booking_data['agent_commission_status'] ?? null,
            'subtotal' => $booking_data['subtotal'] ?? null,
            'agent_payment_type' => $booking_data['agent_payment_type'] ?? null,
            'agent_payment_status' => $booking_data['agent_payment_status'] ?? null,
            
            // CALCULATED FIELDS
            'revenue' => $revenue,
            'calculated_nights' => $calculated_nights,
            ];

        $response = [
            "status" => true,
            "message" => "booking detail has been retrieved",
            "data" => $complete_booking_data
        ];

    } else {
        $response = [
            "status" => false,
            "message" => "no booking found",
            "data" => null
        ];
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
                $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
                $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
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

/*==================
AGENT SETTINGS API
==================*/
$router->post('agent/dashboard/settings', function () {
    
    // INCLUDE CONFIG
    include "./config.php";

    required('user_id');
    required('type'); // 'general' or 'personal'

    $user_id = $_POST["user_id"];
    $type = $_POST["type"];
    
    $response = [
        "status" => false,
        "message" => "Invalid request",
        "data" => null
    ];

    if ($type === 'general') {

        $settings = $db->get("settings", "*", ["user_id" => $user_id]);
    
        if (!$settings) {
            $response = [
                "status" => false,
                "message" => "No user data found",
                "data" => null
            ];
            echo json_encode($response);
            return;
        }

        // FORM GENERAL SETTINGS DATA - FIXED PATHS
        $response = [
            "status" => true,
            "message" => "General settings retrieved successfully",
            "data" => [
                "business_name" => $settings['business_name'] ?? '',
                "domain_name" => $settings['site_url'] ?? '',
                "website_offline" => $settings['site_offline'] ?? 'No',
                "offline_message" => $settings['offline_message'] ?? '',
                "business_logo" => !empty($settings['header_logo_img']) ? 'https://toptiertravel.site/assets/uploads/' . $settings['header_logo_img'] : null,
                "favicon" => !empty($settings['favicon_img']) ? 'https://toptiertravel.site/assets/uploads/' . $settings['favicon_img'] : null
            ]
        ];
        
    } elseif ($type === 'personal') {
        $user = $db->get("users", "*", ["user_id" => $user_id]);

        if (!$user) {
            $response = [
                "status" => false,
                "message" => "No user found",
                "data" => null
            ];
            echo json_encode($response);
            return;
        }

        $country_name = '';
        if (!empty($user['country_code']) && $user['country_code'] != null) {
            $country = $db->get("countries", "*", [
                "id" => $user['country_code']
            ]);

            $country_name = isset($country['name']) ? $country['name'] : '';
        }

        $countries = $db->select("countries", "*");
        $payment_gateways = $db->select("payment_gateways", "name",['status' => '1']);
        
        // FORM PERSONAL SETTINGS DATA - FIXED PATH
        $response = [
            "status" => true,
            "message" => "Personal settings retrieved successfully",
            "data" => [
                "profile_photo" => !empty($user['profile_photo']) ? 'https://toptiertravel.site/assets/uploads/' . $user['profile_photo'] : null,
                "first_name" => $user['first_name'] ?? '',
                "last_name" => $user['last_name'] ?? '',
                "email" => $user['email'] ?? '',
                "phone_number" => $user['phone'] ?? '',
                "country" => $country_name,
                "address" => $user['address1'] ?? '',
                "bio" => $user['note'] ?? '',
                "linkedin_url" => $user['linkedin_url'] ?? '',
                "twitter_url" => $user['twitter_url'] ?? '',
                "website_url" => $user['website_url'] ?? '',
                "country_code" => $user['country_code'] ?? '',
                "preferred_payment_method" => $user['preferred_payment_method'] ?? '',
                "payment_details" => $user['payment_details'] ?? '',
                "countries" => $countries,
                'payment_gateways' => $payment_gateways
            ]
        ];
        
    } else {
        $response = [
            "status" => false,
            "message" => "Invalid type. Use 'general' or 'personal'",
            "data" => null
        ];
    }

    echo json_encode($response);
});

/*==================
AGENT SETTINGS SAVE API 
==================*/
$router->post('agent/dashboard/settings/save', function () {
    
    include "./config.php";

    required('user_id');
    required('type'); // 'general' or 'personal'

    $user_id = $_POST["user_id"] ?? null;
    $type    = $_POST["type"] ?? null;

    $response = ["status" => false, "message" => "Invalid request", "data" => null];

    // ---------- HELPERS ----------
    function uploadBase64File($base64String, $uploadDir = null) {
        // FIXED: Use absolute path to root/assets/uploads/
        if ($uploadDir === null) {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/assets/uploads/';
        }
        
        if (empty($base64String)) return null;

        // Validate base64 format
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
            return ['error' => 'Invalid base64 image format'];
        }

        $ext = strtolower($type[1]);
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array($ext, $allowedTypes)) {
            return ['error' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.'];
        }

        $base64String = substr($base64String, strpos($base64String, ',') + 1);
        $data = base64_decode($base64String);
        
        if ($data === false) {
            return ['error' => 'Base64 decode failed'];
        }

        // Validate file size (2MB max)
        $maxSize = 2 * 1024 * 1024;
        if (strlen($data) > $maxSize) {
            return ['error' => 'File too large. Maximum size is 2MB.'];
        }

        // Validate actual image content
        $imageInfo = getimagesizefromstring($data);
        if ($imageInfo === false) {
            return ['error' => 'Invalid image data'];
        }

        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                return ['error' => 'Failed to create upload directory'];
            }
        }

        $filename = uniqid().'_'.time().'.'.$ext;
        $filepath = $uploadDir.$filename;

        return file_put_contents($filepath, $data) !== false ? $filename : ['error' => 'Failed to save file'];
    }

    function ensureUserExists($db, $id) {
        return $db->get("users", "user_id", ["user_id" => $id]);
    }

    function handleBase64Field($field, &$data, $dbKey) {
        if (!empty($_POST[$field])) {
            $upload = uploadBase64File($_POST[$field]);
            if (is_array($upload) && isset($upload['error'])) {
                // Return error instead of using exit
                return ['error' => "$field error: " . $upload['error']];
            }
            if ($upload) {
                $data[$dbKey] = $upload;
            }
        }
        return ['success' => true];
    }

    function sendResponse($status, $message, $data = null) {
        echo json_encode(["status" => $status, "message" => $message, "data" => $data]);
    }

    // ---------- MAIN ----------
    if (!ensureUserExists($db, $user_id)) {
        sendResponse(false, "User not found");
        return;
    }

    if ($type === 'general') {
        $updateData = [];

        // Map simple fields
        $simpleFields = [
            'business_name' => 'business_name',
            'domain_name' => 'site_url',
            'website_offline' => 'site_offline',
            'offline_message' => 'offline_message'
        ];

        foreach ($simpleFields as $postKey => $dbKey) {
            if (isset($_POST[$postKey])) {
                $updateData[$dbKey] = $_POST[$postKey];
            }
        }

        // Handle file uploads with error checking
        $fileFields = [
            'business_logo' => 'header_logo_img',
            'favicon' => 'favicon_img'
        ];

        foreach ($fileFields as $postKey => $dbKey) {
            $result = handleBase64Field($postKey, $updateData, $dbKey);
            if (isset($result['error'])) {
                sendResponse(false, $result['error']);
                return;
            }
        }

        if (!empty($updateData)) {
            try {
                $existing = $db->get("settings", "*", ["user_id" => $user_id]);
                
                if ($existing) {
                    $result = $db->update("settings", $updateData, ["user_id" => $user_id]);
                    $msg = "General settings updated successfully";
                } else {
                    $lastId = $db->max("settings", "user_id") ?? 0;
                    $newId  = $lastId + 1;

                    $updateData['d'] = $newId;
                    $updateData['user_id'] = $user_id; 
                    $result = $db->insert("settings", $updateData);
                    $msg = "Settings created successfully";
                }

                // Check if database operation was successful
                if ($result) {
                    // FIXED: Return correct web-accessible paths
                    $responseData = [
                        "business_logo" => isset($updateData['header_logo_img']) ? '/assets/uploads/'.$updateData['header_logo_img'] : null,
                        "favicon" => isset($updateData['favicon_img']) ? '/assets/uploads/'.$updateData['favicon_img'] : null,
                        "settings_created" => !$existing
                    ];
                    sendResponse(true, $msg, $responseData);
                } else {
                    sendResponse(false, "Failed to save general settings");
                }
            } catch (Exception $e) {
                sendResponse(false, "Database error: " . $e->getMessage());
            }
        } else {
            sendResponse(false, "No data provided");
        }

    } elseif ($type === 'personal') {
        $updateData = [];
        
        // Map simple fields
        $simpleFields = [
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'phone_number' => 'phone',
            'address' => 'address1',
            'bio' => 'note',
            'linkedin_url' => 'linkedin_url',
            'twitter_url' => 'twitter_url',
            'website_url' => 'website_url',
            'preferred_payment_method' => 'preferred_payment_method',
            'payment_details' => 'payment_details',
            'country_code' => 'country_code'
        ];

        foreach ($simpleFields as $postKey => $dbKey) {
            if (isset($_POST[$postKey])) {
                $updateData[$dbKey] = $_POST[$postKey];
            }
        }
        
        // Check for email duplicate before updating
        if (isset($updateData['email'])) {
            $emailExists = $db->get("users", "user_id", [
                "email" => $updateData['email'],
                "user_id[!]" => $user_id  // Exclude current user
            ]);
            
            if ($emailExists) {
                sendResponse(false, "Email address is already in use by another user");
                return;
            }
        }

        // Handle profile photo
        $result = handleBase64Field('profile_photo', $updateData, 'profile_photo');
        if (isset($result['error'])) {
            sendResponse(false, $result['error']);
            return;
        }

        if (!empty($updateData)) {
            try {
                $result = $db->update("users", $updateData, ["user_id" => $user_id]);
                
                if ($result) {
                    // FIXED: Return correct web-accessible path
                    $responseData = [
                        "profile_photo" => isset($updateData['profile_photo']) ? '/assets/uploads/'.$updateData['profile_photo'] : null
                    ];
                    sendResponse(true, "Personal settings updated successfully", $responseData);
                } else {
                    sendResponse(false, "Failed to save personal settings");
                }
            } catch (Exception $e) {
                sendResponse(false, "Database error: " . $e->getMessage());
            }
        } else {
            sendResponse(false, "No data provided");
        }

    } else {
        sendResponse(false, "Invalid type (general|personal)");
    }
});

?>