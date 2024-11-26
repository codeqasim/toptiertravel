<?php
// HEADERS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header("X-Frame-Options: SAMEORIGIN");


function getcarmoduledata($modulename)
{
    $conn = openconn(); // Open a connection to the database.
    // Use the database connection to select data from the 'modules' table where the name matches the input.
    $response = $conn->select("modules", "*", ["name" => $modulename]);
    // Return the results.
    return $response;
}

function currencycarrate($code)
{
    $conn = openconn(); // Open a connection to the database.
    $response = $conn->select("currencies", "*", ["name" => $code]);
    // Return the results.
    return $response[0]['rate'];
}
/*==================
THIS FUNCTION IS USED TO SET THE MARKUP PRICE
==================*/
function price_conn($price, $currency)
{
    $db = openconn();
    //FETCH THE PRICE CONVERSION RATE
    $default_currency = $db->select("currencies", "*", ["status" => 1, "default" => 1]);

    //
    $current_currency_rate = $db->select("currencies", ["rate"], ["name" => $default_currency[0]['name']]);
    if (!empty($price) && !empty($current_currency_price[0]['rate'])) {
        $price = ceil(str_replace(',', '', $price) / $current_currency_rate[0]['rate']);
    } else {
        $price = ceil(str_replace(',', '', $price));
    }
    $con_rate = $db->select("currencies", ['rate'], ["name" => $currency]);
    $con_price = $price * $con_rate[0]['rate'];
    return $con_price;
}
function carsmarkup($module_id, $price, $date, $location)
{
    $conn = openconn();
    $markup = $conn->select('markups', "*", ['type' => 'cars', 'module_id' => $module_id, 'status' => 1]);
    $b2c = '';
    $b2c_markup = '';
    $b2b = '';
    $b2b_markup = '';
    if (!empty($markup)) {
        //THIS CODE CHECKS IF THE DATES ARE PRESENT OR NOT AND MAKES A SAME FORMAT OF DATE
        $city_id = ($location != null) ? $location : "";
        $date = new DateTime($date);
        if (($markup[0]['from_date'] != null && $markup[0]['to_date'] != null)) {
            $from_date = new DateTime($markup[0]['from_date']);
            $to_date = new DateTime($markup[0]['to_date']);
        } else {
            $from_date = "";
            $to_date = "";
        }

        if ((($from_date <= $date && $to_date >= $date) || $markup[0]['location'] == $city_id) && $markup[0]['user_id'] == null) {
            $b2c = $price + ($markup[0]['b2c_markup'] * $price) / 100;
            $b2c_markup = $markup[0]['b2c_markup'];
            $b2b = 0;
            $b2b_markup = 0;
        } else if ($markup[0]['user_id'] != null) {
            $b2b = $price + ($markup[0]['b2b_markup'] * $price) / 100;
            $b2b_markup = $markup[0]['b2b_markup'];
            $b2c = 0;
            $b2c_markup = 0;
        } else {
            $b2c = ($markup[0]['b2c_markup'] != null) ? $price + ($markup[0]['b2c_markup'] * $price) / 100 : $price + ($markup[0]['user_markup'] * $price) / 100;
            $b2c_markup = ($markup[0]['b2c_markup'] != null) ? $markup[0]['b2c_markup'] : $markup[0]['user_markup'];
            $b2b = 0;
            $b2b_markup = 0;
        }
    } else {
        $b2c = $price;
        $b2c_markup = 0;
        $b2b = 0;
        $b2b_markup = 0;
    }
    // return [$b2c,$b2c_markup,$b2b,$b2b_markup];
    return array(
        'b2c' => $b2c,
        'b2c_markup' => $b2c_markup,
        'b2b' => $b2b,
        'b2b_markup' => $b2b_markup,
    );
}


function sendcarRequest($req_method = 'GET', $service = '', $payload = [], $_headers = [])
{
    // Get the URL from the payload and remove it from the array
    $url = $payload['endpoint'];
    unset($payload['endpoint']);

    // Set up cURL options
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Return the response instead of outputting it
    curl_setopt($curl, CURLOPT_ENCODING, ""); // Handle all encodings
    curl_setopt($curl, CURLOPT_MAXREDIRS, 10); // Follow up to 10 redirects
    curl_setopt($curl, CURLOPT_TIMEOUT, 0); // Time out after 30 seconds
    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1); // Use HTTP/1.1

    // Set the request method and payload

    if ($req_method == 'POST') {
        curl_setopt($curl, CURLOPT_POST, true); // Use POST method
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload); // Include payload in request
    } else {
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET"); // Use GET method
        $url = $url . "?" . http_build_query($payload); // Add payload to URL as query string
    }

    // Set headers
    $headers[] = "cache-control: no-cache"; // Add default cache control header
    if (!empty($headers)) {
        $headers = array_merge($headers, $_headers); // Merge additional headers
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); // Set headers in request
    }
    // Set the URL and execute the request
    curl_setopt($curl, CURLOPT_URL, $url);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    // Handle errors
    if ($err) {
        $response = $err;
    }

    // Return the response or error message
    return $response;
}

/*==================
TOURS SEARCH API
==================*/
$router->post('cars/search', function () {

    // INCLUDE CONFIG FILE
    include "./config.php";

    // VALIDATION
    required('from_airport');
    required('language');
    required('to_date');
    required('adults');
    required('childs');
    required('currency');
    required('ip');

    //PARAMETERS
    $params = array(
        "from_airport" => $_POST['from_airport'],
        "lang" => $_POST['language'],
        "date" => $_POST['to_date'],
        "adults" => $_POST['adults'],
        "childs" => $_POST['childs'],
        "currency" => $_POST['currency'],
        "ip" => $_POST['ip'],
    );

    if($_POST['module_name'] == "cars"){
    $module = $db->select('modules', '*', ['name' => 'cars','type' => 'cars','status' => 1]);
    if (!empty($module[0]['id'])) {
        $page_number = $_POST['pagination']; // You can set this dynamically based on user input
        $items_per_page = 50;
        $offset = ($page_number - 1) * $items_per_page;
        $cars = $db->select('cars', '*', ['airport_code' => $params['from_airport'],'LIMIT' => [$offset, $items_per_page]]);
        $m_cars = [];
        if (!empty($cars)) {
            foreach ($cars as $key => $value) {

                $con_price = price_conn($value['price'],$params['currency']);
                $markup = carsmarkup($module[0]['id'],$value['price'],$params['date'],$value['car_city']);
                $price_markup = ($markup['b2c']) ? $markup['b2c'] : $markup['b2b'] ;

                // Check if hotel has a rating
                if (!empty($_POST['rating'])) {
                    $rating = round($value['stars']);
                    if ($rating != $_POST['rating']) {
                        continue; // Skip this hotel if not rated as specified
                    }
                }

                if(!empty($_POST['price_from']) && !empty($_POST['price_to'])){
                    if ( round($price_markup) < $_POST['price_from'] ||  round($price_markup) > $_POST['price_to']) {
                        continue; // Skip this hotel if not within the price range
                    }
                }


                $m_cars[]= [
                    'id' => $value['id'],
                    'title' => $value['name'],
                    'img' => root."../uploads/".$value['img'],
                    'stars' => $value['stars'],
                    'rating' => $value['stars'],
                    'location' => $value['airport_code'],
                    'from_airport' => $value['airport_code'],
                    'desc' => $value['desc'],
                    'supplier_name' => "cars",
                    'price' => $price_markup,
                    'actual_price' => $value['price'],
                    'b2c_price' =>$markup['b2c'],
                    'b2b_price' => $markup['b2b'],
                    'b2c_markup' => $markup['b2c_markup'],
                    'b2b_markup' => $markup['b2b_markup'] ,
                    'currency' => $params['currency'],
                    'redirect' => "",
                    'color' => $module[0]['module_color']
                ];
            }
        }
    }
    }else {
        $data_car = [];

        $getvalue = getcarmoduledata($_POST['module_name']);
        $module_name = $getvalue[0]['name'];
        $module_id = $getvalue[0]['id'];
        // Determine whether the module is in development or production mode
        if ($getvalue[0]['dev_mode'] == 1) {
            $env = 'production';
        } else {
            $env = 'dev';
        }

        $module_color =  $getvalue[0]['module_color'];

        //Call API's Parameters
        $param = array(
            "endpoint" => api_modules ."/cars/".strtolower($module_name)."/api/v1/search",
            "from_airport" => $_POST['from_airport'],
            "to_location" => $_POST['to_location'],
            "to_date" => $_POST['to_date'],
            "pick_time" => $_POST['pick_time'],
            "drop_date" => $_POST['drop_date'],
            "drop_time" => $_POST['drop_time'],
            "lang" => $_POST['language'],
            "adults" => $_POST['adults'],
            "childs" => $_POST['childs'],
            "currency" => $_POST['currency'],
            "rating" => $_POST['rating'],
            "price_from" => $_POST['price_from'],
            "price_to" => $_POST['price_to'],
            "ip" => $_POST['ip'],
            'c1' => $getvalue[0]['c1'],
            'c2' => $getvalue[0]['c2'],
            'c3' => $getvalue[0]['c3'],
            'c4' => $getvalue[0]['c4'],
            'c5' => $getvalue[0]['c5'],
            "env" => $env,
            "pagination" => $_POST['pagination']
        );

        if(empty($getvalue[0]['c1'])) {
            include "creds.php";
        }

        $response = sendcarRequest('POST', 'search', $param);

        $car_rep = json_decode($response);

        if (!empty($car_rep)) {
            foreach ($car_rep->response as $val) {

                // Get the current exchange rate for the segment's currency
                $current_currency_price = currencycarrate($val->currency);
                // Get the exchange rate for the user's selected currency
                $con_rate = currencycarrate($_POST["currency"]);

                // Convert the  price to the user's selected currency
                if (!empty($val->price) && !empty($current_currency_price)) {
                    // Remove commas from the price string and divide by the current currency rate, then round up to the nearest whole number
                    $price_get = ceil(str_replace(',', '', $val->price) / $current_currency_price);
                } else {
                    // If the  price or currency rate is not available, set the converted price to 0
                    $price_get = 0;
                }

                $price = $price_get * $con_rate; // Total price

                $data_car[] = (object)[
                    'id' => $val->id,
                    'title' => $val->title,
                    'img' => $val->img,
                    'stars' => $val->stars,
                    'rating' => $val->rating,
                    'price' => number_format((float)$price, 2, '.', ''),
                    'actual_price' => number_format((float)$price, 2, '.', ''),
                    'b2c_price' => '',
                    'b2b_price' => '',
                    'b2c_markup' => '',
                    'b2b_markup' => '',
                    'location' => $val->location,
                    'redirect' => $val->redirect,
                    'supplier_name' => $module_name,
                    'from_airport' => $val->from_airport,
                    'desc' => $val->desc,
                    'currency' => $params['currency'],
                    'color' => $module_color,
                    'booking_data' => $val->booking_data,
                ];
            }
        }
    }

    if (!empty($m_cars) && !empty($data_car)) {
        $data = array_merge($m_cars, $data_car);
    } elseif (!empty($m_cars)) {
        $data = ['status'=>true,'response'=>$m_cars,'total'=>count($m_cars)];
    } elseif (!empty($data_car)) {
        $data = ['status'=>true,'response'=>$data_car,'total'=>$car_rep->total];
    } else {
        $data = array("status" => false, "response" => (object)[],'total'=>'');
    }
    echo json_encode($data);
});

/*=======================
CARS_BOOKING REQUEST API
=======================*/
$router->post('cars/booking', function () {

    if (!empty($_POST['agent_fee'])){ $agent_fee = $_POST['agent_fee']; } else { $agent_fee = ""; }

    //CONFIG FILE
    include "./config.php";
    //VALIDATION
    $user_data=json_decode($_POST['user_data']);
    $param = array(
        "booking_ref_no" => date('Ymdhis'),
        "car_id" => $_POST['car_id'],
        "booking_status" => 'pending',
        "price_markup" => $_POST['price_markup'],
        "actual_price" => $_POST['actual_price'],
        "amount_paid"=>0,
        "payment_status" =>'unpaid',
        "payment_gateway" => $_POST['booking_payment_gateway'],
        "first_name"=>$_POST['first_name'],
        "last_name"=>$_POST['last_name'],
        "email"=>$_POST['email'],
        "address"=>$_POST['address'],
        "phone_country_code"=>$_POST['phone_country_code'],
        "phone"=>$_POST['phone'],
        "country"=>$user_data->country_code,
        "booking_date" => date("Y-m-d"),
        "user_id" => $_POST['user_id'],
        "booking_additional_notes"=>"",
        "infants" =>0,
        "adults" => $_POST['booking_adults'],
        "childs" => $_POST['booking_childs'],
        "currency_original" => $_POST['booking_curr_code'],
        "currency_markup" => $_POST['booking_curr_code'],
        "transaction_id" => "",
        "guest" => $_POST['booking_guest_info'],
        "user_data" => $_POST['user_data'],
        "booking_data" => $_POST['booking_data'],
        "booking_response" => "",
        "payment_desc" => "",
        "supplier" => $_POST['booking_supplier'],
        "payment_date" => "",
        "redirect" => "",
        "cars_name" => $_POST['cars_name'],
        "car_img" => $_POST['car_img'],
        "car_location" => $_POST['car_location'],
        "car_stars" => $_POST['car_stars'],
        "cancellation_request" => $_POST['cancellation'],
        "cancellation_status" => 0,
        "module_type" => $_POST['module_type'],
        "agent_fee" => $agent_fee,
        "pnr" => ""
    );
    $db->insert("cars_bookings", $param); //INSERTION OF BOOKING DATA INTO DATABASE
    $data = (json_decode($_POST["user_data"]));
    $data = (object) array_merge((array) $data, array('booking_ref_no' => $param['booking_ref_no']));
    // HOOK
    $hook = "cars_booking";
    include "./hooks.php";
    echo json_encode(array('status' => true, 'id' => $db->id(), 'booking_ref_no' => $param['booking_ref_no']));
});

/*=======================
CARS_BOOKING INVOICE API
=======================*/
$router->post('cars/invoice', function () {

    // CONFIG FILE
    include "./config.php";

    // VALIDATION
    required('booking_ref_no');
    $booking_ref_no = $_POST["booking_ref_no"];

    $response = $db->select("cars_bookings", "*", ['booking_ref_no' => $booking_ref_no]); // SELECT THE BOOKING DATA FROM DATABASE ACCORDING TO BOOKING REFERENCE NUMBER
    if (!empty($response)) {
        echo json_encode(array('status' => true, 'response' => $response)); // RETURN INVOICE IF BOOKING REFERENCE NUMBER IS CORRECT
    } else {
        echo json_encode(array('status' => false, 'response' => 'The booking reference number in invalid')); // RETURN IF BOOKING REFERENCE NUMBER IS CORRECT
    }
});

/*=======================
CARS BOOKING PAYMENT UPDATE API
=======================*/
$router->post('cars/booking_update', function () {
    include "./config.php";


    // VALIDATION
    required('booking_ref_no');
    required('transaction_id');
    required('transaction_type');

    $booking_ref_no = $_POST['booking_ref_no'];
    $query = $db->select("cars_bookings", "*", ['booking_ref_no' => $booking_ref_no]); // SELECT THE BOOKING DATA FROM DATABASE ACCORDING TO BOOKING REFERENCE NUMBER

    if (!empty($query)) {

        $getvalue = getmoduledata($query[0]['supplier']);
        $_POST['module_name'] = $getvalue[0]['name'];
        // Determine whether the module is in development or production mode
        if ($getvalue[0]['dev_mode'] == 1) {
            $env = 'pro';
        } else {
            $env = 'dev';
        }

        if($getvalue[0]['prn_type'] == $_POST['booking_type']) {
            $param = array(
                'c1' => $getvalue[0]['c1'],
                'c2' => $getvalue[0]['c2'],
                'c3' => $getvalue[0]['c3'],
                'c4' => $getvalue[0]['c4'],
                'c5' => $getvalue[0]['c5'],
                'env' => $env,
                'endpoint' =>  api_modules . "/cars/".strtolower($query[0]['supplier'])."/api/v1/booking",
                "booking_ref_no" => $query[0]['booking_ref_no'],
                "booking_data" => $query[0]['booking_data'],
                "guest" => $query[0]['guest'],
                "nationality" => $query[0]['nationality'],
                "user_data" => $query[0]['user_data'],
            );
            if(!empty($_POST['card_number']) && !empty($_POST['card_expiry']) && !empty($_POST['card_cvc'])){
                $param['card_number'] =  $_POST['card_number'];
                $param['card_expiry'] = $_POST['card_expiry'];
                $param['card_cvc'] = $_POST['card_cvc'];
            }
            if(empty($getvalue[0]['c1'])) {
                include "creds.php";
            }

            $response = sendRequest('POST', 'search', $param);
            $savebooking = json_decode($response);
        }else{
            $savebooking = (object)['Prn'=>'Your booking on Hold'];
        }

    $params = array(
        "booking_ref_no" => $query[0]['booking_ref_no'],
        "booking_status" => "confirmed",
        "payment_status" => "paid",
        "payment_gateway" => $query[0]['payment_gateway'],
        "user_id" => $query[0]['user_id'],
        "transaction_id" => $_POST['transaction_id'],
        "transaction_desc" => "Payment for Invoice " . $query[0]['booking_ref_no'],
        "transaction_type" => $_POST['transaction_type'],
        "transaction_date" => date('Y-m-d'),
        "transaction_payment_gateway" => $query[0]['payment_gateway'],
        "transaction_amount" => $query[0]['price_markup'],
        "transaction_currency" => $query[0]['currency_markup'],
    );
    if (!empty($query)) {
        //UPDATE THE DATA IN CARS_BOOKING TABLE IN DATABASE
        $update = $db->update("cars_bookings", [
            "pnr" => $savebooking->booking_pnr,
            "booking_response" => $response,
            'user_id' => $params['user_id'],
            'booking_date' => date('Y-m-d'),
            'booking_status' => $params['booking_status'],
            'transaction_id' => $params['transaction_id'],
            'payment_status' => $params['payment_status'],
            'payment_gateway' => $params['payment_gateway'],
            'payment_date' => date('Y-m-d'),
        ], [
                "booking_ref_no" => $booking_ref_no
            ]);

        $data = $db->select("cars_bookings", '*', ["booking_ref_no" => $booking_ref_no]); // SELECT THE UPDATED BOOKING DATA FROM DATABASE ACCORDING TO BOOKING REFERENCE NUMBER

        //INSERT THE TRANSACTION INFO IN TRANSACTION TABLE IN DATABASE
        if ($data[0]['payment_status'] == 'paid') {
            $transaction_entry = $db->insert("transactions", [
                'description' => $params['transaction_desc'],
                'user_id' => $data[0]['user_id'],
                'trx_id' => $data[0]['transaction_id'],
                'type' => $params['transaction_type'],
                'date' => date('Y-m-d'),
                'amount' => $data[0]['price_markup'],
                'payment_gateway' => $data[0]['payment_gateway'],
                'currency' => $data[0]['currency_markup']
            ]);
        }

        // HOOK
        $user = (json_decode($data[0]['user_data']));
        $hook = "cars_update_booking";
        include "./hooks.php";

        $response = array('status' => true, 'data' =>  $booking_ref_no,'Prn' => $savebooking->Prn);
    } else {
        $response = array('status' => false, 'data' => 'Please enter valid booking ref no','Prn' => '');
    }
    echo json_encode($response);
} else {
    echo json_encode(array('status' => false, 'response' => 'Please valid booking ref no'));
}
});

/*=======================
CARS BOOKING CANCELLATION API
=======================*/
$router->post('cars/cancellation', function () {
    //CONFIG FILE
    include "./config.php";

    // VALIDATION
    required('booking_ref_no');
    $booking_ref_no = $_POST["booking_ref_no"];

    $params = array('cancellation_request' => 1, );
    $data = $db->update("cars_bookings", $params, ["booking_ref_no" => $booking_ref_no]); //UPDATE THE CANCELATION STATUS IN DATABASE IF REQUEST IS MADE

    $booking = $db->select("cars_bookings", '*', ["booking_ref_no" => $booking_ref_no]);
    $user = (json_decode($booking[0]['user_data']));

    // HOOK
    $hook = "cars_cancellation_request";
    include "./hooks.php";

    echo json_encode(array('status' => true, 'message' => 'request received successfully'));

});

?>