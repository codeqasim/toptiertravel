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
                            if(isset($hotel_bookings) && !empty($hotels_bookings)){
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

        // FORM GENERAL SETTINGS DATA ARRAY
        $response = [
            "status" => true,
            "message" => "General settings retrieved successfully",
            "data" => [
                "business_name" => $settings['business_name'] ?? '',
                "domain_name" => $settings['site_url'] ?? '',
                "website_offline" => $settings['site_offline'] ?? 'No',
                "offline_message" => $settings['offline_message'] ?? '',
                "business_logo" => $settings['header_logo_img'] ?? null,
                "favicon" => $settings['favicon_img'] ?? null
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
        // FORM PERSONAL SETTINGS DATA ARRAY
        $response = [
            "status" => true,
            "message" => "Personal settings retrieved successfully",
            "data" => [
                "profile_photo" => $user['profile_photo'] ?? null,
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
                "preferred_payment_method" => $user['preferred_payment_method'] ?? '',
                "payment_details" => $user['payment_details'] ?? ''
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
AGENT SETTINGS API
==================*/
$router->post('agent/dashboard/settings/save', function () {
    
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

    // FUNCTION TO HANDLE FILE UPLOADS
    function uploadFile($fileInputName, $uploadDir = 'assets/uploads/') {
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES[$fileInputName];
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        // VALIDATE FILE TYPE
        if (!in_array($file['type'], $allowedTypes)) {
            return ['error' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.'];
        }

        // VALIDATE FILE SIZE
        if ($file['size'] > $maxSize) {
            return ['error' => 'File too large. Maximum size is 2MB.'];
        }

        // CREATE UPLOAD DIRECTORY IF NOT EXISTS
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // GENERATE UNIQUE FILENAME
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        // MOVE UPLOADED FILE
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filename;
        } else {
            return ['error' => 'Failed to upload file.'];
        }
    }

    if ($type === 'general') {

        // VERIFY USER EXISTS IN USERS TABLE
        $userExists = $db->get("users", "user_id", ["user_id" => $user_id]);
        
        if (!$userExists) {
            $response = [
                "status" => false,
                "message" => "User not found",
                "data" => null
            ];
            echo json_encode($response);
            return;
        }

        // PREPARE GENERAL SETTINGS DATA FOR UPDATE
        $updateData = [];
        
        if (isset($_POST['business_name'])) {
            $updateData['business_name'] = $_POST['business_name'];
        }
        if (isset($_POST['domain_name'])) {
            $updateData['site_url'] = $_POST['domain_name'];
        }
        if (isset($_POST['website_offline'])) {
            $updateData['site_offline'] = $_POST['website_offline'];
        }
        if (isset($_POST['offline_message'])) {
            $updateData['offline_message'] = $_POST['offline_message'];
        }

        // HANDLE BUSINESS LOGO UPLOAD
        if (isset($_FILES['business_logo'])) {
            $logoUpload = uploadFile('business_logo');
            if (is_array($logoUpload) && isset($logoUpload['error'])) {
                $response = [
                    "status" => false,
                    "message" => "Business logo upload error: " . $logoUpload['error'],
                    "data" => null
                ];
                echo json_encode($response);
                return;
            } elseif ($logoUpload) {
                $updateData['header_logo_img'] = $logoUpload;
            }
        } elseif (isset($_POST['business_logo'])) {
            $updateData['header_logo_img'] = $_POST['business_logo'];
        }

        // HANDLE FAVICON UPLOAD
        if (isset($_FILES['favicon'])) {
            $faviconUpload = uploadFile('favicon');
            if (is_array($faviconUpload) && isset($faviconUpload['error'])) {
                $response = [
                    "status" => false,
                    "message" => "Favicon upload error: " . $faviconUpload['error'],
                    "data" => null
                ];
                echo json_encode($response);
                return;
            } elseif ($faviconUpload) {
                $updateData['favicon_img'] = $faviconUpload;
            }
        } elseif (isset($_POST['favicon'])) {
            $updateData['favicon_img'] = $_POST['favicon'];
        }

        if (!empty($updateData)) {
            // CHECK IF SETTINGS RECORD EXISTS FOR THIS USER
            $existingSettings = $db->get("settings", "*", ["user_id" => $user_id]);
            
            if ($existingSettings) {
                // UPDATE EXISTING SETTINGS RECORD
                $result = $db->update("settings", $updateData, ["user_id" => $user_id]);
                $message = "General settings updated successfully";
            } else {
                // CREATE NEW SETTINGS RECORD FOR THIS USER
                $updateData['user_id'] = $user_id;
                $updateData['created_at'] = date('Y-m-d H:i:s');
                $result = $db->insert("settings", $updateData);
                $message = "New settings record created and general settings saved successfully";
            }

            if ($result) {
                $response = [
                    "status" => true,
                    "message" => $message,
                    "data" => [
                        "business_logo" => isset($updateData['header_logo_img']) ? 'assets/uploads/' . $updateData['header_logo_img'] : null,
                        "favicon" => isset($updateData['favicon_img']) ? 'assets/uploads/' . $updateData['favicon_img'] : null,
                        "settings_created" => !$existingSettings
                    ]
                ];
            } else {
                $response = [
                    "status" => false,
                    "message" => "Failed to save general settings",
                    "data" => null
                ];
            }
        } else {
            $response = [
                "status" => false,
                "message" => "No data provided to update",
                "data" => null
            ];
        }
        
    } elseif ($type === 'personal') {
        
        // VERIFY USER EXISTS IN USERS TABLE
        $userExists = $db->get("users", "user_id", ["user_id" => $user_id]);
        
        if (!$userExists) {
            $response = [
                "status" => false,
                "message" => "User not found",
                "data" => null
            ];
            echo json_encode($response);
            return;
        }
        
        // PREPARE PERSONAL SETTINGS DATA FOR UPDATE
        $updateData = [];
        
        // HANDLE PROFILE PHOTO UPLOAD
        if (isset($_FILES['profile_photo'])) {
            $photoUpload = uploadFile('profile_photo');
            if (is_array($photoUpload) && isset($photoUpload['error'])) {
                $response = [
                    "status" => false,
                    "message" => "Profile photo upload error: " . $photoUpload['error'],
                    "data" => null
                ];
                echo json_encode($response);
                return;
            } elseif ($photoUpload) {
                $updateData['profile_photo'] = $photoUpload;
            }
        } elseif (isset($_POST['profile_photo'])) {
            $updateData['profile_photo'] = $_POST['profile_photo'];
        }

        if (isset($_POST['first_name'])) {
            $updateData['first_name'] = $_POST['first_name'];
        }
        if (isset($_POST['last_name'])) {
            $updateData['last_name'] = $_POST['last_name'];
        }
        if (isset($_POST['email'])) {
            $updateData['email'] = $_POST['email'];
        }
        if (isset($_POST['phone_number'])) {
            $updateData['phone'] = $_POST['phone_number'];
        }
        if (isset($_POST['country'])) {
            // CONVERT COUNTRY NAME TO ID IF NEEDED
            if (!is_numeric($_POST['country'])) {
                $country = $db->get("countries", "id", ["name" => $_POST['country']]);
                $updateData['country_code'] = $country ? $country : null;
            } else {
                $updateData['country_code'] = $_POST['country'];
            }
        }
        if (isset($_POST['address'])) {
            $updateData['address1'] = $_POST['address'];
        }
        if (isset($_POST['bio'])) {
            $updateData['note'] = $_POST['bio'];
        }
        if (isset($_POST['linkedin_url'])) {
            $updateData['linkedin_url'] = $_POST['linkedin_url'];
        }
        if (isset($_POST['twitter_url'])) {
            $updateData['twitter_url'] = $_POST['twitter_url'];
        }
        if (isset($_POST['website_url'])) {
            $updateData['website_url'] = $_POST['website_url'];
        }
        if (isset($_POST['preferred_payment_method'])) {
            $updateData['preferred_payment_method'] = $_POST['preferred_payment_method'];
        }
        if (isset($_POST['payment_details'])) {
            $updateData['payment_details'] = $_POST['payment_details'];
        }

        if (!empty($updateData)) {
            // UPDATE USER RECORD (USER ALWAYS EXISTS)
            $result = $db->update("users", $updateData, ["user_id" => $user_id]);

            if ($result) {
                $response = [
                    "status" => true,
                    "message" => "Personal settings updated successfully",
                    "data" => [
                        "profile_photo" => isset($updateData['profile_photo']) ? 'assets/uploads/' . $updateData['profile_photo'] : null
                    ]
                ];
            } else {
                $response = [
                    "status" => false,
                    "message" => "Failed to save personal settings",
                    "data" => null
                ];
            }
        } else {
            $response = [
                "status" => false,
                "message" => "No data provided to update",
                "data" => null
            ];
        }
        
    } else {
        $response = [
            "status" => false,
            "message" => "Invalid type. Use 'general' or 'personal'",
            "data" => null
        ];
    }

    echo json_encode($response);
});

?>