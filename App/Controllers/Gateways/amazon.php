<?php
// PARAMS TO USE FOR GATEWAY
// ===================================>
// Booking REF  -> ($payload->booking_ref_no)
// Invoice URL  -> ($payload->invoice_url)
// Client Email -> ($payload->client_email)
// Price        -> ($payload->price);
// Currency     -> ($payload->currency)
// ===================================>

// GATEWAY NAME CONTROLLER ( IF SPACES IN NAME FOLLOW BY UNDERSCOPE INSTEAD DASH )

$router->post('payment/amazon', function() {

    $payload = json_decode(base64_decode($_POST['payload']));

    if($payload->type == 'wallet'){
        $payload->price = $_POST['price'];
    }

    $rand =date('Ymdhis').rand();
    $_SESSION['bookingkey'] = $rand;
    file_put_contents("session_logs.json", json_encode(["bookig_key"=>$_SESSION['bookingkey']]));
    // SUCCESS URL
    $success_url = (root).'success/payment?token='.$_POST['payload']."&key=".$rand."&type=0";

    $gateway = array_column(base()->payment_gateways, null, 'name')['amazon'];


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

    if($gateway->dev_mode == 1){
        $status = true;
    }else{
        $status = false;
    }
    $payfortConfig = [
        'merchant_identifier' => $gateway->c1 ,
        'access_code' => $gateway->c2,
        'SHA_request_phrase' => $gateway->c3,
        'sandbox' => $status // set to false for production
    ];

    function generateSignature($data, $phrase, $algorithm = 'sha256') {
        ksort($data);
        $queryString = '';
        foreach ($data as $key => $value) {
            $queryString .= "$key=$value";
        }
        return hash($algorithm, $phrase . $queryString . $phrase);
    }

    $orderAmount = round($price * 100);
    $orderCurrency =  $gateway->currency;
    $orderReference = ($payload->booking_ref_no);

    $requestData = [
        'merchant_identifier' => $payfortConfig['merchant_identifier'],
        'access_code' => $payfortConfig['access_code'],
        'merchant_reference' => $orderReference,
        'amount' => $orderAmount,
        'currency' => $orderCurrency,
        'command' => 'AUTHORIZATION',
        'language' => 'en',
        'customer_email' => ($payload->client_email),
        'order_description' => 'Booking for Invoice ' . ($payload->booking_ref_no),
        'return_url' => $success_url
    ];

    $requestData['signature'] = generateSignature($requestData, $payfortConfig['SHA_request_phrase']);

    $payfortUrl = $payfortConfig['sandbox'] ? 'https://sbcheckout.payfort.com/FortAPI/paymentPage' : 'https://checkout.payfort.com/FortAPI/paymentPage';
 ?>

<?php
    $creds = '<form style="width: 100%;" action="' . $payfortUrl . '" method="post">';
    foreach ($requestData as $key => $value):
        $creds .= '<input type="hidden" name="' . $key . '" value="' . $value . '">';
    endforeach;
    $creds .= '<input style="background: #5469d4;" class="pay" type="submit" value="'.T::paynow.' '.($orderCurrency).' '.(round($price)).'">
        </form>';
    $body = $creds;
    include "App/Views/Pay_view.php";
});

$router->post('success/payment', function() {
    $get_seesion = json_decode(file_get_contents("session_logs.json"));
    $rand =  $get_seesion->bookig_key;
    header('Location: ' . (root).'payment/success?token='.$_GET['token']."&key=".$_GET['key']."&type=".$_GET['type']."&bookingkey=".$rand."&gateway=amazon");
});

