<?php

/* ---------------------------------------------- */
// CARS INDEX PAGE
/* ---------------------------------------------- */
$router->get('(cars)', function ($nav_menu) {

    // META DETAILS
    $meta = array(
        "title" => "Cars",
        "meta_title" => "Cars",
        "meta_desc" => "",
        "meta_img" => "",
        "meta_url" => "",
        "meta_author" => "",
        "nav_menu" => $nav_menu,
    );

    views($meta,"Cars/Index");

});

// ========================================================  INVOCE

$router->get('/(cars)/invoice/(\d+)', function($nav_menu,$id) {

    // SEARCH PARAMS
    $params = array( "booking_ref_no"=>$id,);
    $RESPONSE=POST(api_url.'cars/invoice',$params);
    // dd($RESPONSE);

    if(empty($RESPONSE->response)){
        REDIRECT(root);
    } else {
        $data = $RESPONSE;
    }

    $meta = array(
        "title" => "Cars Invoice",
        "meta_title" => "Cars invoice",
        "meta_desc" => "",
        "meta_img" => "",
        "meta_url" => "",
        "meta_author" => "",
        "nav_menu" => $nav_menu,
        "data" => $data->response[0]
    );

    views($meta,"Cars/Invoice");

});

// ========================================================  INVOICE

// ======================================================== CARS SEARCH RESULTS

$router->get('/(cars)/(.*)', function($nav_menu,$uri) {

    $url = explode('/', $uri);
    $count = count($url);

    // REDIRECT HOME IF URI PARAMS ARE LESS THEN 7
    if ($count > 8 ) { REDIRECT(root); }

    $from_airport = $url[0];
    $to_location = $url[1];
    $to_date = $url[2];
    $pick_time = $url[3];
    $drop_date = $url[4];
    $drop_time = $url[5];
    $adults = $url[6];
    $childs = $url[7];


    // META DETAILS
    $meta = array(
        "title" => T::cars.' - '.ucfirst($from_airport),
        "meta_title" => T::cars.' - '.ucfirst($from_airport),
        "meta_desc" => "",
        "meta_img" => "",
        "meta_url" => "",
        "meta_author" => "",
        "from_airport" => $from_airport,
        "to_location" => $to_location,
        "to_date" => $to_date,
        "pick_time" => $pick_time,
        "drop_date" => $drop_date,
        "drop_time" => $drop_time,
        "adults" => $adults,
        "childs" => $childs,
        "nav_menu" => $nav_menu,
    );

    // ADD SEARCH CRITERIA TO SESSION
    $_SESSION['from_airport'] = $from_airport;
    $_SESSION['to_location'] = $to_location;
    $_SESSION['to_date'] = $to_date;
    $_SESSION['pick_time'] = $pick_time;
    $_SESSION['drop_date'] = $drop_date;
    $_SESSION['drop_time'] = $drop_time;
    $_SESSION['cars_adults'] = $adults;
    $_SESSION['cars_childs'] = $childs;

    views($meta,"Cars/Cars");

});

// ========================================================  BOOKING

$router->post('(cars)/booking', function($nav_menu) {

    CSRF();
    $decoded = base64_decode($_POST['booking_data']);
    $hashed = [
        "car_id" => $_POST['car_id'],
        "car_name" => $_POST['car_name'],
        "car_img" => $_POST['car_img'],
        "price" => $_POST['price'],
        "actual_price" => $_POST['actual_price'],
        "adults" => $_POST['adults'],
        "childs" => $_POST['childs'],
        "date" => $_POST['date'],
        "currency" => $_POST['currency'],
        "supplier" => $_POST['supplier'],
        "car_location" => $_POST['car_location'],
        "car_stars" => $_POST['car_stars'],
        "booking_data" =>$decoded,
        "user_data" => $_POST['user_data'],
        "module_type" => $_POST['module_type'],
        "cancellation" => $_POST['cancellation']
    ];

    // META DETAILS
    $meta = array(
    "title" => T::bookings,
    "meta_title" => T::bookings,
    "meta_desc" => "",
    "meta_img" => "",
    "meta_url" => "",
    "meta_author" => "",
    "nationality" => "",
    "data"=> $hashed,
    "nav_menu" => $nav_menu,
    );
    views($meta, "Cars/Booking");

});

// ======================================================== HOTELS BOOKING

// ======================================================== HOTELS BOOK

$router->post('(cars)/book', function() {

    CSRF();

    $payload = json_decode(base64_decode($_POST['payload']));

    if (!empty($_POST['user']['address'])){ $address = $_POST['user']['address']; } else { $address = ""; }
    if (!empty($_POST['agent_fee'])){ $agent_fee = $_POST['agent_fee']; } else { $agent_fee = ""; }

    $total_price_markup = floatval($agent_fee) + floatval($payload->price);

    $adult_travellers = $payload->adult_travellers;
    $child_travellers = $payload->child_travellers;

    $data = [];

     for ($i = 1; $i <= $adult_travellers; $i++) {
        array_push($data, (object) array(
            'title'=>$_POST["title_".$i],
            'first_name'=>$_POST["firstname_".$i],
            'last_name'=>$_POST["lastname_".$i],
            'age'=>'',
            ));
      }

      for ($x = 1; $x <= $child_travellers; $x++) {
        array_push($data, (object) array(
            'title'=>'mr',
            'first_name'=>$_POST["firstname_".$x],
            'last_name'=>$_POST["lastname_".$x],
            'age'=>$_POST["child_age_".$x],
            ));
      }

    $guest = json_encode($data);

    // CHECK USER SESSION
    function user_id()
    { if (isset($_SESSION['phptravels_client']->user_id)) {
    return $_SESSION['phptravels_client']->user_id;} else { return "0";}
    } $user_id = user_id();

    if(!empty($_POST['payment_gateway'])){
        $payment_gateway = $_POST['payment_gateway'];
    }else{
        $payment_gateway = "";
    }

    $params = array(
        'first_name' => $_POST['user']['first_name'],
        'last_name' => $_POST['user']['last_name'],
        'email' => $_POST['user']['email'],
        'address' => $address,
        'phone_country_code' => $_POST['user']['country_code'],
        'phone' => $_POST['user']['country_code'],
        "cars_name" => $payload->car_name,
        "car_img" => $payload->car_img,
        "car_location" => $payload->car_location,
        "car_stars" => $payload->car_stars,
        "car_id" => $payload->car_id,
        "booking_data" => $payload->booking_data,
        "cancellation" => $payload->cancellation,
        "booking_adults" => $payload->adults,
        "booking_childs" => $payload->childs,
        "price_markup" => $total_price_markup,
        "actual_price" => $payload->actual_price,
        "booking_curr_code" => $payload->currency,
        "module_type" => $payload->module_type,
        "booking_payment_gateway" =>$payment_gateway,
        "booking_supplier" => $payload->supplier,
        "user_id" => $user_id,
        "booking_guest_info" => $guest,
        "user_data" => json_encode($_POST['user'], JSON_PRETTY_PRINT),
        "agent_fee" => $agent_fee,

    );

    // FINAL BOOKING REQUEST
    $REQUEST=POST(api_url.'cars/booking', $params);
    if ($REQUEST->status == true) {
    $invoice_url = root.'cars/invoice/' . $REQUEST->booking_ref_no;
    $_SESSION['booking_celebration'] = true;
    REDIRECT($invoice_url);
    }

});

// ======================================================== CARS BOOK

?>