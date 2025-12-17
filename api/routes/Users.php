<?php

use Medoo\Medoo;

// ======================== SIGNUP
$router->post('signup', function() {

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
            $respose = array ( "status"=>false, "message"=>"email already exist.", "data"=> "" );
            echo json_encode($respose);
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
            "status" => $_POST['status']??0,
            "user_type" => "Customer",
            "created_at" => $date,
        ]);

        $user_id_ = $db->id();
        $user_info = $db->select("users","*", [ "id" => $user_id_ ]);

        $respose = array ( "status"=>true, "message"=>"account registered successfully.", "data"=> $user_info );
        echo json_encode($respose);

        // $link = root.'../'.'account/activation/'.$user_id.'/'.$mail_code;
        $link = 'https://toptiertravel.vip/account/activation/'.$user_id.'/'.$mail_code;

        // HOOK
        $hook="user_signup";
        include "./hooks.php";

});
// ======================== SIGNUP

// ======================== ACCOUNT ACTIVATION
$router->post('activation', function() {

    // INCLUDE CONFIG
    include "./config.php";

    required('user_id');
    required('email_code');

    $user = $db->select("users","*", [ "user_id" => $_POST['user_id'], "status" => 0 ]);

    if (isset($user[0]['status'])){
    if ($user[0]['status'] == !1){

        // HOOK
        $hook="account_activated";
        include "./hooks.php";

        $data = $db->update("users", [ "status" => 1, ], [
        "user_id" => $_POST['user_id'],
        "email_code" => $_POST['email_code'],
        ]);

    }}

    $respose = array ( "status"=>true, "message"=>"account activated", "data"=> $user );
    echo json_encode($respose);

});

// ======================== LOGIN
$router->post('login', function() {

    // INCLUDE CONFIG
    include "./config.php";

    // VALIDATION
    required('email');
    required('password');

    // Check if email exists first
    $email_check = $db->select("users","*", [
        "email" => $_POST['email']
    ]);

    if(!isset($email_check[0])) {
        $respose = array ( "status"=>false, "message"=>"email not found", "data"=> null );
        echo json_encode($respose);
        die;
    }

    // Now check email and password together
    $data = $db->select("users","*", [
        "email" => $_POST['email'],
        "password" => md5($_POST['password']),
    ]);

    if(isset($data[0])) {

        if ($data[0]['status'] == 0) {
            $respose = array ( "status"=> false, "message"=>"user account not verified", "data"=> $data[0] );
            echo json_encode($respose);
            die;
        };

        $user_data = (object)$data[0];
        $user_data->profile_photo = !empty($user_data->profile_photo) 
            ? user_upload_url . $user_data->profile_photo
            : null; 
        $user_data->token = null;
        $respose = array ( "status"=> true, "message"=>"user details", "data"=> $user_data );

        if (isset($user_data)){
        if ($user_data->status == 1){

        // HOOK
        $hook="login";
        include "./hooks.php";

        }}

        // INSERT TO LOGS
        $user_id = $user_data->user_id;
        $log_type = "login";
        $datetime = date("Y-m-d h:i:sa");
        $desc = "user logged into account" .get_client_ip();
        logs($user_id,$log_type,$datetime,$desc);

        session_regenerate_id(true);
        $_SESSION['phptravels_client'] = $user_data;

        include "./logs.php";

        echo json_encode($respose);

    } else {
        // Email exists but password is wrong
        $respose = array ( "status"=>false, "message"=>"incorrect password", "data"=> null );
        echo json_encode($respose);
    }
});

// ======================== FORGET PASSWORD
$router->post('forget_password', function() {

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

                    $respose = array ( "status"=>true, "message"=>"password has been sent to email", "data"=> $data );

                    // HOOK
                    $hook="forget_password";
                    include "./hooks.php";

                }

            } else {
                $respose = array ( "status"=>"none", "message"=>"not_activated", "data"=> null );
            }
        } else {
            $respose = array ( "status"=>false, "message"=>"no user found", "data"=> null );
        }

    echo json_encode($respose);

});


// ======================== GET PROFILE DATA
$router->post('profile', function() {

    // INCLUDE CONFIG
    include "./config.php";
    required('user_id');

    // USERS
    $data = $db->select("users", "*", ["user_id" => $_POST['user_id']]);
    
    if(!empty($data)){
        $user_id = $_POST['user_id'];
        
        // Initialize counters
        $total_bookings = 0;
        $pending_bookings = 0;
        
        // Define all booking tables
        $booking_tables = [
            'hotels_bookings',
            'flights_bookings',
            'tours_bookings',
            'visa_bookings',
            'cars_bookings'
        ];
        
        // Count bookings from each table
        foreach($booking_tables as $table) {
            // Total bookings count
            $total_count = $db->count($table, ["user_id" => $user_id]);
            $total_bookings += $total_count;
            
            // Pending bookings count (assuming status field exists)
            $pending_count = $db->count($table, [
                "user_id" => $user_id,
                "booking_status" => "pending"
            ]);
            $pending_bookings += $pending_count;
        }

        $data[0]['profile_photo'] = (isset($data[0]['profile_photo']) && $data[0]['profile_photo'] != null) ? user_upload_url . $data[0]['profile_photo'] : null;
        
        // Add booking counts to user data
        $data[0]['total_bookings'] = $total_bookings;
        $data[0]['pending_bookings'] = $pending_bookings;
        
        $respose = array(
            "status" => "true", 
            "message" => "profile details", 
            "data" => $data
        );
    } else {
        $respose = array(
            "status" => "false", 
            "message" => "no profile found", 
            "data" => ""
        );
    }
    
    echo json_encode($respose);

});

// ======================== PROFILE UPDATE
$router->post('profile_update', function() {

    include "./config.php";

    required('user_id');
    // required('first_name');
    // required('last_name');
    // required('email');
    // required('phone');
    // required('phone_country_code');
    // required('country_code');

    // Prepare data for update
    $updateData = [
        "first_name" => $_POST['first_name'] ?? '',
        "last_name" => $_POST['last_name'] ?? '',
        "email" => $_POST['email'] ?? '',
        "phone" => $_POST['phone'] ?? '',
        "phone_country_code" => $_POST['phone_country_code'] ?? '',
        "country_code" => $_POST['country_code'] ?? '',
        "state" => $_POST['state'] ?? null,
        "city" => $_POST['city'] ?? null,
        "address1" => $_POST['address1'] ?? null,
        "address2" => $_POST['address2'] ?? null,
    ];

    // Only update password if provided
    if (!empty($_POST['password'])) {
        $updateData["password"] = md5($_POST['password']);
    }

    // Perform update
    $data = $db->update("users", $updateData, [
        "user_id" => $_POST['user_id'],
    ]);

    $user = $db->select("users", "*", [ "user_id" => $_POST['user_id'] ]);
    if(isset($user)){
        $user[0]['token'] = null;
        $user[0]['profile_photo'] = (isset($user[0]['profile_photo']) && $user[0]['profile_photo'] != null) ? user_upload_url . $user[0]['profile_photo'] : null;
    }
    echo json_encode([
        "status" => true,
        "message" => "Profile updated successfully",
        "data" => $user
    ]);
});

// ======================== PROFILE UPDATE
$router->post('wallet_booking', function() {

    // INCLUDE CONFIG
    include "./config.php";

    required('user_id');
    required('price');
    required('currency');

    // USER UPDATE
    $data = $db->update("users", [
        "balance[-]" => $_POST['price'],
     ], [
        "user_id" => $_POST['user_id'],
    ]);

    // USER TRX INSERT
    $data = $db->insert("transactions", [
        "description" => "purchased with balance",
        "type" => "purchase",
        "amount" => $_POST['price'],
        "user_id" => $_POST['user_id'],
        "currency" => $_POST['currency'],
        "payment_gateway" => "wallet_balance",
        "date" =>date('Ymdhis'),
    ]);

    $respose = array ( "status"=>"true", "message"=>"balance updated", "data"=> $data );
    echo json_encode($respose);

});

// ======================== PROFILE UPDATE
$router->post('user_bookings', function () {
    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    include "./config.php";

    // Basic input validation
    $user_id = isset($_POST['user_id']) ? trim($_POST['user_id']) : null;
    if ($user_id === null || $user_id === '') {
        header('Content-Type: application/json', true, 400);
        echo json_encode([
            "status" => "false",
            "message" => "user_id is required"
        ]);
        return;
    }

    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 6;
    $search = isset($_POST['search']) ? trim($_POST['search']) : '';
    $payment_status = isset($_POST['payment_status']) ? trim($_POST['payment_status']) : '';
    $booking_status = isset($_POST['booking_status']) ? trim($_POST['booking_status']) : '';
    $type = isset($_POST['type']) ? trim($_POST['type']) : 'agent'; // Default to agent

    if ($page <= 0) {
        header('Content-Type: application/json', true, 400);
        echo json_encode([
            "status" => "false",
            "message" => "page must be a positive integer"
        ]);
        return;
    }

    if ($limit <= 0 || $limit > 500) {
        header('Content-Type: application/json', true, 400);
        echo json_encode([
            "status" => "false",
            "message" => "limit must be a positive integer (max 500)"
        ]);
        return;
    }

    $offset = ($page - 1) * $limit;

    // Tables to merge
    $tables = [
        'flights_bookings' => 'flight',
        'hotels_bookings'  => 'hotel',
        'tours_bookings'   => 'tour',
        'cars_bookings'    => 'car',
        'visa_bookings'    => 'visa'
    ];
   
    $allBookings = [];
    $allBookingsUnfiltered = []; // For calculating counts

    try {
        // STEP 1: Get ALL bookings for counts (no filters)
        foreach ($tables as $table => $serviceType) {
            try {
                $unfilteredRows = $db->select($table, "*", ["user_id" => $user_id]);
                
                if (!is_array($unfilteredRows)) {
                    $unfilteredRows = [];
                }
                
                foreach ($unfilteredRows as $r) {
                    $allBookingsUnfiltered[] = $r;
                }
                
            } catch (Exception $tableError) {
                error_log("Error querying table $table for counts: " . $tableError->getMessage());
                continue;
            }
        }
        
        // STEP 2: Get filtered bookings for display
        foreach ($tables as $table => $serviceType) {
            try {
                $where = ["user_id" => $user_id];
                
                if ($search !== '' && $payment_status !== '' && $booking_status !== '') {
                    $where = [
                        "user_id" => $user_id,
                        "payment_status" => $payment_status,
                        "booking_status" => $booking_status,
                        "OR" => [
                            "booking_ref_no[~]"  => $search,
                            "first_name[~]"      => $search,
                            "hotel_name[~]"      => $search,
                            "last_name[~]"       => $search,
                            "pnr[~]"             => $search,
                            "price_markup[~]"    => $search,
                            "booking_date[~]"    => $search,
                            "checkin[~]"    => $search,
                            "checkout[~]"    => $search,
                        ]
                    ];
                } elseif ($search !== '' && $booking_status !== '') {
                    $where = [
                        "user_id" => $user_id,
                        "booking_status" => $booking_status,
                        "OR" => [
                            "booking_ref_no[~]"  => $search,
                            "first_name[~]"      => $search,
                            "hotel_name[~]"      => $search,
                            "last_name[~]"       => $search,
                            "pnr[~]"             => $search,
                            "price_markup[~]"    => $search,
                            "booking_date[~]"    => $search,
                            "checkin[~]"    => $search,
                            "checkout[~]"    => $search,
                        ]
                    ];
                    
                }  elseif ($search !== '' && $payment_status !== '') {
                    $where = [
                        "user_id" => $user_id,
                        "payment_status" => $payment_status,
                        "OR" => [
                            "booking_ref_no[~]"  => $search,
                            "first_name[~]"      => $search,
                            "hotel_name[~]"      => $search,
                            "last_name[~]"       => $search,
                            "pnr[~]"             => $search,
                            "price_markup[~]"    => $search,
                            "booking_date[~]"    => $search,
                            "checkin[~]"    => $search,
                            "checkout[~]"    => $search,
                        ]
                    ];
                    
                } elseif ($search !== '') {
                    $where = [
                        "user_id" => $user_id,
                        "OR" => [
                            "booking_ref_no[~]"  => $search,
                            "first_name[~]"      => $search,
                            "hotel_name[~]"      => $search,
                            "last_name[~]"       => $search,
                            "pnr[~]"             => $search,
                            "price_markup[~]"    => $search,
                            "booking_date[~]"    => $search,
                            "checkin[~]"    => $search,
                            "checkout[~]"    => $search,
                        ]
                    ];
                    
                } elseif ($payment_status !== '') {
                    $where = [
                        "user_id" => $user_id,
                        "payment_status" => $payment_status
                    ];
                } elseif ($booking_status !== '') {
                    $where = [
                        "user_id" => $user_id,
                        "booking_status" => $booking_status
                    ];
                }
                
                $rows = $db->select($table, "*", $where);
                
                if (!is_array($rows)) {
                    $rows = [];
                }
                
                // Normalize filtered data
                foreach ($rows as $r) {
                    $first = isset($r['first_name']) ? trim($r['first_name']) : '';
                    $last  = isset($r['last_name'])  ? trim($r['last_name'])  : '';
                    $customer = trim($first . ' ' . $last);
                    if ($customer === '') $customer = null;

                    if ($type === 'customer') {
                        $r['customer'] = $customer;
                        $r['service'] = isset($r['module_type']) ? (string)$r['module_type'] : $serviceType;
                        $allBookings[] = $r;
                    } else {
                        $allBookings[] = [
                            "reference"   => isset($r['booking_ref_no']) ? (string)$r['booking_ref_no'] : null,
                            "service"     => isset($r['module_type']) ? (string)$r['module_type'] : $serviceType,
                            "customer"    => $customer,
                            "pnr"         => isset($r['pnr']) ? (string)$r['pnr'] : null,
                            "total_price" => isset($r['price_markup']) ? (string)$r['price_markup'] : null,
                            "payment"     => isset($r['payment_status']) ? (string)$r['payment_status'] : null,
                            "status"      => isset($r['booking_status']) ? (string)$r['booking_status'] : null,
                            "date"        => isset($r['booking_date']) ? (string)$r['booking_date'] : null
                        ];
                    }
                }
                
            } catch (Exception $tableError) {
                error_log("Error querying table $table for filtered data: " . $tableError->getMessage());
                continue;
            }
        }
        
        // STEP 3: Calculate counts from UNFILTERED data
        $counts = [
            "total"    => count($allBookingsUnfiltered),
            "unpaid"  => 0,
            "paid"     => 0,
            "refunded" => 0,
            "canceled" => 0
        ];

        foreach ($allBookingsUnfiltered as $booking) {
            $payment = strtolower(trim($booking['payment_status'] ?? ''));
            $status = strtolower(trim($booking['booking_status'] ?? ''));

            // Check canceled first (both spellings, both fields)
            if ($payment === 'canceled' || $payment === 'cancelled' || 
                $status === 'canceled' || $status === 'cancelled') {
                $counts['canceled']++;
            } elseif ($payment === 'unpaid' || $status === 'unpaid') {
                $counts['unpaid']++;
            } elseif ($payment === 'paid' || $status === 'paid') {
                $counts['paid']++;
            } elseif ($payment === 'refunded' || $status === 'refunded') {
                $counts['refunded']++;
            }
        }
        
        // Sort FILTERED bookings by booking_date DESC
        usort($allBookings, function ($a, $b) {
            $aid = isset($a['booking_id']) ? (int)$a['booking_id'] : 0;
            $bid = isset($b['booking_id']) ? (int)$b['booking_id'] : 0;
        
            if ($aid === $bid) return 0;
            return ($aid > $bid) ? -1 : 1;   // Descending (latest first)
        });

        $total_records = count($allBookings);

        // Pagination: slice filtered array
        $paged = array_slice($allBookings, $offset, $limit);

        // Response
        $response = [
            "status" => "true",
            "message" => "User bookings loaded",
            "total_records" => $total_records,
            "page" => $page,
            "limit" => $limit,
            "data" => $paged,
            "counts" => $counts
        ];

        header('Content-Type: application/json');
        echo json_encode($response);
        return;

    } catch (Exception $e) {
        error_log("Fatal error in user_bookings: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        header('Content-Type: application/json', true, 500);
        echo json_encode([
            "status" => "false",
            "message" => "Server error: " . $e->getMessage(),
            "trace" => $e->getTraceAsString()
        ]);
        return;
    }
});

// ======================== User Delete
$router->post('user_delete', function() {
    // INCLUDE CONFIG
    include "./config.php";

    $check_user = $db->select("users","*",[ "email"=>$_POST['email']]);
    if(!empty($check_user[0]['email'])){
        $db->update("users", ["status" => 0,"note"=>"Deleted by User"],["email" => $_POST['email']]);
        $respose = array ( "status"=>true, "message"=>"This user has been deleted");
    }else{
        $respose = array ( "status"=>false, "message"=>"This email does not exist");
    }

    echo json_encode($respose);

});

//LOGOUT API
$router->post('logout', function() {
    include "./config.php";
    header('Content-Type: application/json');

    // VALIDATION
    if (!isset($_POST['user_id']) || empty($_POST['user_id'])) {
        http_response_code(400);
        echo json_encode(["status" => false,"message" => "user_id is required","data" => null]);
        exit;
    }

    $user_id = filter_var($_POST['user_id']);
    $token_to_remove = isset($_POST['token']) ? $_POST['token'] : null;
    
    // Check if user exists
    $user = $db->get("users", "*", ["user_id" => $user_id]);
    if (!$user) {
        http_response_code(404);
        echo json_encode(["status" => false,"message" => "User not found","data" => null]);
        exit;
    }
    
    // Decode stored tokens (array of objects: {token, created_at, last_used})
    $tokens = json_decode($user['token'], true);
    if (!is_array($tokens)) {
        $tokens = [];
    }
    
    $updated_tokens = [];
    $token_found = false;
    $inactive_removed = 0;
    $three_hours_ago = date('Y-m-d H:i:s', strtotime('-3 hours'));

    if (!empty($token_to_remove)) {
        // TOKEN PROVIDED: Remove specific token
        foreach ($tokens as $entry) {
            if (isset($entry['token']) && $entry['token'] === $token_to_remove) {
                $token_found = true;
                // Skip this entry (remove it)
                continue;
            }
            // Keep all other tokens
            $updated_tokens[] = $entry;
        }
        
        if (!$token_found) {
            http_response_code(401);
            echo json_encode([
                "status" => false,
                "message" => "Invalid token",
                "data" => null
            ]);
            exit;
        }
        
        $logout_message = "Logout successful";
        $log_desc = "User logged out with token: " . substr($token_to_remove, 0, 10) . "...";
        
    } else {
        // NO TOKEN PROVIDED: Remove tokens inactive for 3+ hours
        foreach ($tokens as $entry) {
            $last_used = isset($entry['last_used']) ? $entry['last_used'] : (isset($entry['created_at']) ? $entry['created_at'] : null);
            
            // If no timestamp or token is older than 3 hours, remove it
            if (empty($last_used) || $last_used < $three_hours_ago) {
                $inactive_removed++;
                continue; // Skip this entry (remove it)
            }
            
            // Keep active tokens
            $updated_tokens[] = $entry;
        }
        
        if ($inactive_removed === 0) {
            http_response_code(200);
            echo json_encode([
                "status" => true,
                "message" => "No inactive tokens found to remove",
                "data" => ["removed_count" => 0]
            ]);
            exit;
        }
        
        $logout_message = "Inactive tokens removed successfully";
        $log_desc = "Removed $inactive_removed inactive token(s) (not used for 3+ hours)";
    }

    // Update DB with cleaned token list
    $updated_json = json_encode($updated_tokens);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(500);
        echo json_encode([
            "status" => false,
            "message" => "Failed to encode tokens",
            "data" => null
        ]);
        exit;
    }
    
    // Update database
    if (!empty($updated_tokens)) {
        $db->update("users", [
            "token" => $updated_json
        ], [
            "user_id" => $user_id
        ]);
    } else {
        $db->update("users", [
            "token" => null
        ], [
            "user_id" => $user_id
        ]);
    }

    // Log the event
    $log_type = "logout";
    $datetime = date("Y-m-d H:i:s");
    logs($user_id, $log_type, $datetime, $log_desc);

    // Optional: Run logout hook
    $hook = "logout";
    include "./hooks.php";

    // Clear local session if active
    if (isset($_SESSION['phptravels_client']) && 
        isset($_SESSION['phptravels_client']['user_id']) && 
        $_SESSION['phptravels_client']['user_id'] == $user_id) {
        unset($_SESSION['phptravels_client']);
        session_regenerate_id(true);
    }

    http_response_code(200);
    echo json_encode([
        "status" => true,
        "message" => $logout_message,
        "data" => [
            "removed_count" => !empty($token_to_remove) ? 1 : $inactive_removed,
            "remaining_tokens" => count($updated_tokens)
        ]
    ]);
});

$router->post('save_token', function() {
    include "./config.php";

    required('user_id');
    required('token');

    $user_id = $_POST['user_id'];
    $token = $_POST['token'];

    // Check if user exists
    $user = $db->get("users", "*", ["user_id" => $user_id]);
    if (!$user) {
        echo json_encode(["status" => false, "message" => "User not found", "data" => null]);
        die;
    }

    // Decode existing tokens
    $tokenList = [];
    if (!empty($user['token'])) {
        $decoded = json_decode($user['token'], true);
        if (is_array($decoded)) {
            $tokenList = $decoded;
        }
    }

    // Check if token already exists (prevent duplicates)
    $found = false;
    foreach ($tokenList as $entry) {
        if (isset($entry['token']) && $entry['token'] === $token) {
            $found = true;
            break;
        }
    }

    // If token doesn't exist, add it
    if (!$found) {
        $tokenList[] = [
            'token' => $token,
            'created_at' => date('Y-m-d H:i:s'),
            'last_used' => date('Y-m-d H:i:s')
        ];
    }

    // Save back to DB
    $updatedJson = json_encode($tokenList);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["status" => false, "message" => "Failed to encode tokens", "data" => null]);
        die;
    }

    $update = $db->update("users", [
        "token" => $updatedJson
    ], [
        "user_id" => $user_id
    ]);

    if ($update && $update->rowCount() > 0) {
        $response = ["status" => true, "message" => "Token saved successfully", "data" => ["token" => $token]];
    } else {
        // Check if it was already there (no update needed)
        if ($found) {
            $response = ["status" => true, "message" => "Token already exists", "data" => ["token" => $token]];
        } else {
            $response = ["status" => false, "message" => "Failed to save token", "data" => null];
        }
    }

    logs($user_id, "token_save", date("Y-m-d h:i:sa"), "Token saved: $token");
    echo json_encode($response);
});


$router->post('verify_token', function() {
    include "./config.php";

    // Ensure required fields are present
    if (!isset($_POST['user_id']) || empty($_POST['user_id'])) {
        echo json_encode(["status" => false, "message" => "user_id is required", "data" => null]);
        die;
    }
    if (!isset($_POST['token']) || empty($_POST['token'])) {
        echo json_encode(["status" => false, "message" => "token is required", "data" => null]);
        die;
    }

    $user_id = $_POST['user_id'];
    $token = $_POST['token'];

    // Fetch user from database
    $user = $db->get("users", "*", ["user_id" => $user_id]);
    if (!$user) {
        echo json_encode(["status" => false, "message" => "User not found", "data" => null]);
        die;
    }

    // Decode stored tokens
    $storedTokens = json_decode($user['token'], true);
    if (!is_array($storedTokens)) {
        $storedTokens = [];
    }

    // Verify: does this token exist?
    $authenticated = false;
    $tokenIndex = -1;
    foreach ($storedTokens as $index => $entry) {
        if (isset($entry['token']) && $entry['token'] === $token) {
            $authenticated = true;
            $tokenIndex = $index;
            break;
        }
    }

    if ($authenticated) {
        // Update last_used timestamp for this token
        if ($tokenIndex !== -1 && isset($storedTokens[$tokenIndex])) {
            $storedTokens[$tokenIndex]['last_used'] = date('Y-m-d H:i:s');
            
            // Save updated token list back to database
            $db->update("users", [
                "token" => json_encode($storedTokens)
            ], [
                "user_id" => $user_id
            ]);
        }

        // Remove sensitive fields before sending user data
        unset($user['password']); // Don't expose password
        unset($user['token']); // Don't expose tokens in response

        $response = [
            "status" => true,
            "message" => "Authenticated",
            "data" => (object)$user
        ];
        
        logs($user_id, "token_verify", date("Y-m-d h:i:sa"), "Token verified successfully");
    } else {
        $response = [
            "status" => false,
            "message" => "Invalid token or unauthorized access",
            "data" => null
        ];
        
        logs($user_id, "token_verify_failed", date("Y-m-d h:i:sa"), "Token verification failed");
    }

    // Set JSON header
    header('Content-Type: application/json');
    echo json_encode($response);
});


?>