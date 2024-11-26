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
$router->post('payment/worldpay', function() {
//echo $_POST['payload'];
//echo base64_decode($_POST['payload']);
$payload = json_decode(base64_decode($_POST['payload']));
if($payload->type == 'wallet'){
$payload->price = $_POST['price'];
}

    $rand =date('Ymdhis').rand();
    $_SESSION['bookingkey'] = $rand;
// dd($payload);
$success_url = (root).'payment/success/?token='.$_POST['payload']."&key=".$rand."&type=0";
$gateway = array_column(base()->payment_gateways, null, 'name')['Worldpay'];


?>

<form action="https://secure-test.worldpay.com/wcc/purchase" method="POST"> <!-- Specifies the URL for our test environment -->
<input type="hidden" name="testMode" value="100"> <!-- 100 instructs our test system -->
<input type="hidden" name="instId" value="1474397"> <!-- A mandatory parameter - installationID -->
<input type="hidden" name="cartId" value="<?=$payload->booking_ref_no?>"> <!-- A mandatory parameter - reference for the item purchased -->
<input type="hidden" name="amount" value="<?=$payload->price?>"> <!-- A mandatory parameter -->
<input type="hidden" name="currency" value="<?=($payload->currency)?>"> <!-- A mandatory parameter. ISO currency code -->
<input type="hidden" name="MC_callback" id="MC_callback" value="<?=$success_url?>">
<input type="submit" value="Pay Now" style="width: 100%; height: 50px; font-weight: 600; cursor: pointer;">
</form>

<script>
// Function to update the value of the hidden input field
function updateMC_callback() {
// Get the current URL
// var currentURL = window.location.href;
var currentURL = '<?=$success_url?>';
// Get the hidden input field element
var MC_callbackInput = document.getElementById('MC_callback');
// Set the value of the hidden input field to the current URL
MC_callbackInput.value = currentURL;
}
// Call the function to update the value when the page loads
updateMC_callback();
</script>


<?php }); ?>