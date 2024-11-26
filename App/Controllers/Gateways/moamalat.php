<?php
$router->post('payment/moamalat', function() {

    $payload = json_decode(base64_decode($_POST['payload']));
    if($payload->type == 'wallet'){
        $payload->price = $_POST['price'];
    }

    $rand =date('Ymdhis').rand();
    $_SESSION['bookingkey'] = $rand;
    // SUCCESS URL
    $success_url = (root).'payment/success/?token='.$_POST['payload']."&key=".$rand."&type=0";
    $gateway = array_column(base()->payment_gateways, null, 'name')['moamalat'];



    $creds = ' <div id="target"></div>';
    $body = $creds;
    include "App/Views/Pay_view.php";
    ?>
    <script src="https://tnpg.moamalat.net:6006/js/lightbox.js"> </script>

    <script>
        Lightbox.Checkout.showLightbox();
        Lightbox.Checkout.configure = {
            MID: '10081014649',
            TID: '99179395',
            AmountTrxn: 250,
            MerchantReference: "test-demo",
            TrxDateTime: "<?php echo date('YmdHis'); ?>",
            SecureHash: "39636630633731362D663963322D346362642D386531662D633963303432353936373431",
            completeCallback: function (data) {
                // Handle payment success here
                console.log("Payment completed", data);

            },
            errorCallback: function (error) {
                // Handle payment error here
                console.log("Payment error", error);
            },
            cancelCallback: function () {
                // Handle payment cancellation here
                console.log("Payment cancelled");
            },
        };
    </script>
    <?php

});