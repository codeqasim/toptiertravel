<?php

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['card_number']) && isset($_GET['card_expiry']) && isset($_GET['card_cvc'])) {
    $rand = date('Ymdhis') . rand();
    $_SESSION['bookingkey'] = $rand;

    //$link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $url = explode('/', $_GET['url']);

    if (!empty(json_decode($data->user_data)->user_id)) {
        $user_id = (json_decode($data->user_data)->user_id);
    } else {
        $user_id = "";
    }
    $payload = [
        'booking_ref_no' => $data->booking_ref_no,
        'currency' => $data->currency_markup,
        'price' => $data->price_markup,
        'client_email' => (json_decode($data->user_data)->email),
        'invoice_url' => root.$url[0]."/".$url[1]."/".$url[2],
        'type' => 'invoice',
        'user_id' => $user_id,
        'module_type' => $data->module_type,
        'card_details' => ['card_number'=>$_GET['card_number'],'card_expiry'=>$_GET['card_expiry'],'card_cvc'=>$_GET['card_cvc']],
    ];

    $success_url = (root).'payment/success/?token='.base64_encode(json_encode($payload))."&trx_id=000&gateway=cartrawler&key=".$rand."&type=0";
    REDIRECT($success_url);
}
?>

<form method="GET" class="border p-3 rounded mb-3 bg-light no_print" onsubmit="return validate_form()">
    <div class="row g-2">
        <div class="col-md-3">
            <div class="form-group">
                <input type="tel" class="form-control" value="4263971921001307" id="card_number" name="card_number" placeholder="Valid Card Number" autocomplete="cc-number" maxlength="16" required autofocus style="height: 38px;" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <input type="tel" class="form-control"  value="11/27" id="card_expiry" name="card_expiry" placeholder="MM / YY" autocomplete="cc-exp" maxlength="5" required style="height: 38px;" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <input type="tel" class="form-control"  value="123" id="card_cvc" name="card_cvc" placeholder="CVC" autocomplete="cc-csc" maxlength="3" required style="height: 38px;" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <input type="submit"  class="btn btn-success w-100" value="Proceed" />
            </div>
        </div>

        <div id="response"></div>
    </div>

</form>

<script>
    function validate_form() {
        let card_number = document.getElementById("card_number").value.trim();
        let card_expiry = document.getElementById("card_expiry").value.trim();
        let card_cvc = document.getElementById("card_cvc").value.trim();

        if (!/^\d{16}$/.test(card_number)) {
            alert("Please enter a valid 16-digit card number.");
            return false;
        }

        if (!/^(0[1-9]|1[0-2])\/?([0-9]{2})$/.test(card_expiry)) {
            alert("Please enter a valid expiry date (MM/YY).");
            return false;
        }

        let expiry = card_expiry.split("/");
        let month = parseInt(expiry[0], 10);
        let year = parseInt("20" + expiry[1], 10); // Convert YY to YYYY

        // Get the current month and year
        let today = new Date();
        let current_month = today.getMonth() + 1;
        let current_year = today.getFullYear();

        // Check if the card expiry date is in the past
        if (year < current_year || (year === current_year && month < current_month)) {
            alert("The card expiry date cannot be in the past.");
            return false;
        }

        if (!/^\d{3}$/.test(card_cvc)) {
            alert("Please enter a valid 3-digit CVC.");
            return false;
        }
        return true;
    }
</script>
