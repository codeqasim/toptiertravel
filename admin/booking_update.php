<?php
require_once '_config.php';
auth_check();
$title = T::booking .' '. T::edit;
?>
<div class="page_head">
    <div class="panel-heading">
        <div class="float-start">
            <p class="m-0 page_title"><?=T::edit.' '. T::booking?></p>
        </div>
        <div class="float-end">
            <a href="javascript:window.history.back();" data-toggle="tooltip" data-placement="top" title="Previous Page" class="loading_effect btn btn-warning"><?=T::back?></a>
        </div>
    </div>
</div>

<div class="mt-1">
    <div class="p-3">
        <?=T::booking.' '.T::id?>
        <strong><?php 
        if (isset($_GET['booking'])){ echo $_GET['booking']; }?></strong>
        <hr>

        <?php
        if(!empty($_GET['booking_id']) && !empty($_GET['module']) && !empty($_GET['booking_status']) && !empty($_GET['payment_status']) && !empty($_GET['checkin']) && !empty($_GET['checkout'])) {
            // Get the hotel name based on the hotel_id
            $hotel_id = $_GET['hotel_id'];
            $hotel_data = $db->select('hotels', ['name'], ['id' => $hotel_id]);
            $hotel_name = $hotel_data[0]['name'] ?? ''; // Fetch the hotel name

            // Get the existing booking data
            $table_name = $_GET['module']."_bookings";
            $booking_data = $db->select($table_name, "*", ['booking_ref_no' => $_GET['booking_id']]);


            // Decode the user_data JSON
            $user_data = json_decode($booking_data[0]['user_data'], true);

            // Update the email in user_data
            $user_data['email'] = $_GET['email'];

            // Update query to include the modified user_data
            $data = $db->update(
                $table_name,
                [
                    'booking_status' => $_GET['booking_status'],
                    'payment_status' => $_GET['payment_status'],
                    'checkin' => $_GET['checkin'],
                    'checkout' => $_GET['checkout'],
                    'hotel_id' => $_GET['hotel_id'], // Updated hotel_id
                    'hotel_name' => $hotel_name, // Added hotel_name
                    'user_data' => json_encode($user_data) // Updated user_data with new email
                ],
                ['booking_ref_no' => $_GET['booking_id']]
            );
            
            REDIRECT('./bookings.php');
        }

        if(!empty($_GET['booking']) && !empty($_GET['module'])){
            $table_name = $_GET['module']."_bookings";
            $parm = [
                'booking_ref_no' => $_GET['booking'] ?? '',
            ];
            $data = $db->select($table_name, "*", $parm);
        } else {
            REDIRECT('./bookings.php');
        }

        // Extracting email, first_name, and last_name from user_data JSON column
        $user_data = json_decode($data[0]['user_data'], true); // Decoding the JSON
        $email = $user_data['email'] ?? ''; // Extracting email from the user_data JSON
        $first_name = $user_data['first_name'] ?? ''; // Extracting first_name from user_data
        $last_name = $user_data['last_name'] ?? ''; // Extracting last_name from user_data
        ?>
        
        <form class="row g-3" id="search">
            <div class="col-md-2">
                <div class="form-floating">
                    <input type="text" class="form-control" id="booking_id" name="booking_id" value="<?= $data[0]['booking_ref_no'] ?? '' ?>" readonly>
                    <label for="">Booking ID</label>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-floating">
                    <input type="text" class="form-control" id="booking_date" name="booking_date" value="<?= $data[0]['booking_date'] ?? '' ?>">
                    <label for="">Booking Date</label>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-floating">
                    <input type="text" class="form-control" id="email" name="email" value="<?= $email ?>">
                    <label for="email">Email</label>
                </div>
            </div>
            
            <!-- Add First Name and Last Name Fields -->
            <div class="col-md-2">
                <div class="form-floating">
                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?= $first_name ?>">
                    <label for="first_name">First Name</label>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-floating">
                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?= $last_name ?>">
                    <label for="last_name">Last Name</label>
                </div>
            </div>

            <?php $hotels = $db->select('hotels', '*', ['status' => 1]); ?>
            <div class="col-md-2">
                <div class="form-floating">
                    <select class="form-select" id="hotel_select" name="hotel_id">
                        <?php foreach ($hotels as $hotel): ?>
                            <option value="<?= $hotel['id'] ?>" <?= ($data[0]['hotel_id'] ?? '') == $hotel['id'] ? "selected" : ""; ?>><?= $hotel['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="hotel_select">Listing</label>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-floating">
                    <select class="form-select booking_status" id="search_type" name="booking_status">
                        <option value="">Select Type</option>
                        <option value="pending" <?= ($data[0]['booking_status'] ?? '') === "pending" ? "selected" : "";?>><?=T::pending?></option>
                        <option value="confirmed" <?= ($data[0]['booking_status'] ?? '') === "confirmed" ? "selected" : "";?>><?=T::confirmed?></option>
                        <option value="cancelled" <?= ($data[0]['booking_status'] ?? '') === "cancelled" ? "selected" : "";?>><?=T::cancelled?></option>
                    </select>
                    <label for=""><?=T::booking?> <?=T::status?></label>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-floating">
                    <select id="search_type" name="payment_status" class="form-select payment_status">
                        <option value="">Select Type</option>
                        <option value="paid" <?= ($data[0]['payment_status'] ?? '') === "paid" ? "selected" : "";?>><?=T::paid?></option>
                        <option value="unpaid" <?= ($data[0]['payment_status'] ?? '') === "unpaid" ? "selected" : "";?>><?=T::unpaid?></option>
                        <option value="refunded" <?= ($data[0]['payment_status'] ?? '') === "refunded" ? "selected" : "";?>><?=T::refunded?></option>
                    </select>
                    <label for=""><?=T::payment?> <?=T::status?></label>
                </div>
            </div>

            <!-- Check-in Date -->
            <div class="col-md-2">
                <div class="form-floating">
                    <input type="text" class="checkin form-control" id="checkin" name="checkin" autocomplete="off"
                    value="<?= $data[0]['checkin'] ?? '' ?>">
                    <label for="checkin">Check-in Date</label>
                </div>
            </div>

            <!-- Check-out Date -->
            <div class="col-md-2">
                <div class="form-floating">
                    <input type="text" class="checkout form-control" id="checkout" name="checkout" autocomplete="off"
                    value="<?= $data[0]['checkout'] ?? '' ?>">
                    <label for="checkout">Check-out Date</label>
                </div>
            </div>
            <input type="hidden" id="booking_id" name="booking_id" value="<?=$_GET['booking']?>">
            <input type="hidden" id="module" name="module" value="<?=$_GET['module']?>">
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 h-100 rounded-4" style="border-radius: 8px !important;"><?=T::submit?></button>
            </div>
        </form>

        <script>
            $("#search").submit(function(event) {
                event.preventDefault();
                
                var booking_id = $("#booking_id").val();
                var module = $("#module").val();
                var booking_status = $(".booking_status").val();
                var payment_status = $(".payment_status").val();
                var checkin = $("#checkin").val();
                var checkout = $("#checkout").val();
                var hotel_id = $("#hotel_select").val();
                var email = $("#email").val();
                var first_name = $("#first_name").val();  // Get first_name
                var last_name = $("#last_name").val();    // Get last_name
                
                // Prepare the user_data JSON with the new values
                var user_data = <?php echo json_encode($user_data); ?>;
                user_data.email = email;
                user_data.first_name = first_name;  // Update first_name
                user_data.last_name = last_name;    // Update last_name
                
                // Send the updated data back to the server via query parameters or AJAX
                window.location.href = "<?=$root?>/admin/booking_update.php?booking_id="+booking_id+"&module="+module+"&booking_status="+booking_status+"&payment_status="+payment_status+"&checkin="+checkin+"&checkout="+checkout+"&hotel_id="+hotel_id+"&user_data="+encodeURIComponent(JSON.stringify(user_data));
            });
        </script>
<script>
    //function booking_status(data)
    //{
    //    var booking_id = $("#booking_id").val();
    //    var module = $("#module").val();
    //    alert(data.value);
    //    window.location.href = "<?//=$root?>//booking_update.php?booking="+booking_id+"&module="+module+"&booking_status="+data.value;
    //}

</script>
</div>
<?php include "_footer.php" ?>
