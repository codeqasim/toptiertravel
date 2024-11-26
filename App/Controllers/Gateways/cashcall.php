<?php
$router->post('payment/cashcall', function() {

    $payload = json_decode(base64_decode($_POST['payload']));

    $url = root."payment/cashcall_create_invoice";
    $creds = '<form style="width: 100%;" action="'.$url.'" method="post">';
    $creds .= '<input type="hidden" name="payload" value="' . $_POST['payload'] . '">';
    $creds .= '<input style="background: #5469d4;" class="pay" type="submit" value="'.T::paynow.' '.($payload->currency).' '.(number_format($payload->price,2)).'">
        </form>';
    $body = $creds;
    include "App/Views/Pay_view.php";
});

$router->post('payment/cashcall_create_invoice', function() {

    $payload = json_decode(base64_decode($_POST['payload']));
    if($payload->type == 'wallet'){
        $payload->price = $_POST['price'];
    }

    $rand =date('Ymdhis').rand();
    $_SESSION['bookingkey'] = $rand;
    // SUCCESS URL
    $success_url = (root).'payment/success/?token='.$_POST['payload']."&key=".$rand."&type=0&gateway=cashcall";
    $gateway = array_column(base()->payment_gateways, null, 'name')['cashcall'];

// Get the current exchange rate for the segment's currency
    $currency_name = $payload->currency;
    $current_currency = array_values(array_filter(App()->currencies, function($currency) use ($currency_name) {
        return $currency->name == $currency_name;
    }))[0] ?? null;
    $current_currency_price = $current_currency->rate;

    // Get the exchange rate for the user's selected currency

    $gateway_name = $gateway->currency;
    $currency_rate = array_values(array_filter(App()->currencies, function($currency) use ($gateway_name) {
        return $currency->name == $gateway_name;
    }))[0] ?? null;
    $con_rate = $currency_rate->rate;

    $price_get = ceil(str_replace(',', '', $payload->price) / $current_currency_price);

    $price = $price_get * $con_rate; // Total price


    $amount =  number_format((float)$price, 2, '.', '');
    $merchant_id = $payload->booking_ref_no;


    file_put_contents($payload->booking_ref_no."_cashcall_logs.json", json_encode(["data"=>$success_url,"invoice_url"=>$payload->invoice_url,"bookingref"=>$rand]));


    // Concatenate the fields
    $data = $amount  . $merchant_id;

    // Merchant secured hash (secret key)
    $merchant_secret = $gateway->c2;

    // Create HMAC hash using SHA256
    $hmac_hash = hash_hmac('sha256', $data, $merchant_secret);

    if($gateway->dev_mode==1){
        $base = "https://paygatestg.cashcall.com.eg/stg/atcashcall/payment/";
    }else{
        $base = "https://paygate.cashcall.com.eg/atcashcall/payment/";
    }

    $data = [
        "currency"=>$gateway->currency,
        "amount"=>$amount,
        "language"=>"ar-eg",
        "order"=>[
            "merchantOrderId"=>$payload->booking_ref_no,
            "orderSummary"=>ucfirst($payload->module_type)." Booking no ".$payload->booking_ref_no,
        ],
        "customProperties"=>[[
            "serviceCode"=>"E-Commerce",
            "accountId"=>"11111111111111",
        ]],
        "signature"=>$hmac_hash
    ];

    $base64_url_encoded = base64url_encode(json_encode($data));

    $url = $base.$gateway->c4."/".$gateway->c3."/".$base64_url_encoded;
//print_r($url);die;
    header('Location: ' .$url);
});

$router->get('success/payment', function() {

    if($_GET['success'] == true){
        $booking_data = file_get_contents($_GET['merchantOrderId']."_cashcall_logs.json");
        $get_data = json_decode($booking_data);

        $link = $get_data->data;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $link."&bookingref=".$get_data->bookingref,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        unlink($_GET['merchantOrderId']."_cashcall_logs.json");
        header('Location: ' .$get_data->invoice_url);
    }else{

    }
});
function base64url_encode($data) {
    $base64 = base64_encode($data);
    return str_replace(['+', '/', '='], ['-', '_', ''], $base64);
}