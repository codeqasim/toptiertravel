<?php
$router->get('(payment)/success', function() {

    if(!empty($_SESSION['bookingkey'])){
        $bookingkey = $_SESSION['bookingkey'];
    }else{
        $bookingkey = $_GET['bookingref'];
    }

    if($_GET['key'] == !empty($bookingkey)) {

    unset($_SESSION['bookingkey']);
      // GET TOKEN PAYLOAD
      $token = json_decode(base64_decode($_GET['token']));

      // TRX ID || GATEWAY
      !empty($_GET['trx_id']) ? $trx_id = ($_GET['trx_id']) : $trx_id = "";
      !empty($_GET['type']) ? $type = ($_GET['type']) : $type = "";
      !empty($_GET['pit_id']) ? $pit_id = ($_GET['pit_id']) : $pit_id = "";
      !empty($_GET['gateway']) ? $gateway = ($_GET['gateway']) : $gateway = "";


      // dd($token->module_type);

      if (isset($token->booking_ref_no)) {

          // PARAMS
          $params = array(
              "booking_ref_no" => ($token->booking_ref_no),
              "booking_status" => "confirmed",
              "payment_status" => "paid",
              "payment_gateway" => $gateway,
              "booking_type" => $_GET['type'],
              "transaction_id" => $trx_id,
              "pit_id" => $pit_id,
              "transaction_user_id" => ($token->user_id),
              "transaction_desc" => "Payment for Invoice " . ($token->booking_ref_no),
              "transaction_type" => "purchase",
              "transaction_email" => ($token->client_email),
              "transaction_date" => "",
              "transaction_payment_gateway" => $gateway,
              "transaction_amount" => ($token->price),
              "transaction_currency" => ($token->currency),
          );

          if(!empty($token->card_details->card_number) && !empty($token->card_details->card_expiry) && !empty($token->card_details->card_cvc)){
              $params['card_number'] = $token->card_details->card_number;
              $params['card_expiry'] = $token->card_details->card_expiry;
              $params['card_cvc'] = $token->card_details->card_cvc;
          }
          // CALL TO BOOKING API
            $RESPONSE = POST(api_url . ($token->module_type) . '/booking_update', $params);

          //print_r($RESPONSE);die;

          // REDIRECT TO INVOICE AFTER SUCCESS
          $_SESSION['animate'] = true;
          REDIRECT($token->invoice_url);

      } else {
          echo "Something went wrong contact support for your request";
      }
  }else{
      echo "Something went wrong contact support";
  }
});

$router->post('payment', function() {

    echo "<script> window.history.back(); </script>";
    // $routes = json_decode(base64_decode($_POST['payload']));
    // dd($routes);

});

?>