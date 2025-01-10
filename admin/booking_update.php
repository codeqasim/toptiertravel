<?php
require_once '_config.php';
auth_check();
$title = T::booking .' '. T::edit;
?>
<div class="page_head">
    <div class="panel-heading">
        <div class="float-start">
            <p class="m-0 page_title">
                <?=T::edit.' '. T::booking?>
            </p>
        </div>
        <div class="float-end">
            <a href="javascript:window.history.back();" data-toggle="tooltip" data-placement="top" title="Previous Page"
                class="loading_effect btn btn-warning">
                <?=T::back?>
            </a>
        </div>
    </div>
</div>

<div class="mt-1">
    <div class="p-3">
        <?=T::booking.' '.T::id?>
        <strong>
            <?php 
        if (isset($_GET['booking'])){ echo $_GET['booking']; }?>
        </strong>
        <hr>

        <?php
if (!empty($_GET['booking_id']) && !empty($_GET['module']) && !empty($_GET['booking_status']) && !empty($_GET['payment_status']) && !empty($_GET['checkin']) && !empty($_GET['checkout'])) {
    // Get the hotel name based on the hotel_id
    $hotel_id = $_GET['hotel_id'];
    $hotel_data = $db->select('hotels', ['name'], ['id' => $hotel_id]);
    $hotel_name = $hotel_data[0]['name'] ?? ''; // Fetch the hotel name

    $table_name = $_GET['module'] . "_bookings";

    $existing_data = $db->select($table_name, "*", ['booking_ref_no' => $_GET['booking_id']]);
    $existing_user_data = json_decode($existing_data[0]['user_data'] ?? '{}', true); 
    $existing_room_data = json_decode($existing_data[0]['room_data'] ?? '{}', true); 

    $updated_user_data = array_merge($existing_user_data, [
        'first_name' => $_GET['first_name'] ?? $existing_user_data['first_name'] ?? null,
        'last_name' => $_GET['last_name'] ?? $existing_user_data['last_name'] ?? null,
        'email' => $_GET['email'] ?? $existing_user_data['email'] ?? null,
        'phone' => $_GET['phone'] ?? $existing_user_data['phone'] ?? null,
    ]);
    

    $user_data_json = json_encode($updated_user_data);

    // Update query to include the new data
    $db->update(
        $table_name,
        [
            'booking_date'  => $_GET['booking_date'],
            'booking_status' => $_GET['booking_status'],
            'payment_status' => $_GET['payment_status'],
            'checkin' => $_GET['checkin'],
            'checkout' => $_GET['checkout'],
            'hotel_id' => $hotel_id, // Updated hotel_id
            'hotel_name' => $hotel_name, // Added hotel_name
            'first_name' => $_GET['first_name'], 
            'last_name' => $_GET['last_name'],
            'email' => $_GET['email'],
            'agent_id' => $_GET['agent_id'],
            'booking_note' => $_GET['bookingnote'],
            'phone' =>  $_GET['phone'],
            'user_data' => $user_data_json, // Updating user_data column

            'price_original' => $_GET['room_price'],
            'platform_comission' => $_GET['platform_comission'], 
            'tax' => $_GET['tax'], 
            'agent_fee' => $_GET['agent_comission'],
            'price_markup' => $_GET['bookingPrice'],
        ],
        ['booking_ref_no' => $_GET['booking_id']]
    );

    REDIRECT('./bookings.php');
}

if (!empty($_GET['booking']) && !empty($_GET['module'])) {
    $table_name = $_GET['module'] . "_bookings";
    $parm = [
        'booking_ref_no' => $_GET['booking'] ?? '',
    ];
    $data = $db->select($table_name, "*", $parm);

    // Decode user_data JSON
    $user_data = json_decode($data[0]['user_data'] ?? '{}', true);

    // Extracting values from user_data JSON
    $email = $user_data['email'] ?? ''; // Extract email from user_data JSON
    $first_name = $user_data['first_name'] ?? ''; // Extract first_name from user_data JSON
    $last_name = $user_data['last_name'] ?? ''; // Extract last_name from user_data JSON
    $phone = $user_data['phone'] ?? ''; // Extract phone from user_data JSON
} else {
    REDIRECT('./bookings.php');
}

?>


        <form class="row g-3" id="search">
            <div class="col-md-2">
                <div class="form-floating">
                    <input type="text" class="form-control" id="booking_id" name="booking_id"
                        value="<?= $data[0]['booking_ref_no'] ?? '' ?>" readonly>
                    <label for="">Booking ID</label>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-floating">
                    <input type="date" class="form-control" id="booking_date" name="booking_date"
                        value="<?= $data[0]['booking_date'] ?? '' ?>">
                    <label for="">Booking Date</label>
                </div>
            </div>


            <div class="col-md-2">
                <div class="form-floating">
                    <select class="form-select booking_status" id="search_type" name="booking_status">
                        <option value="">Select Type</option>
                        <option value="pending" <?=($data[0]['booking_status'] ?? '' )==="pending" ? "selected" : "" ;?>
                            >
                            <?=T::pending?>
                        </option>
                        <option value="confirmed" <?=($data[0]['booking_status'] ?? '' )==="confirmed" ? "selected" : ""
                            ;?>>
                            <?=T::confirmed?>
                        </option>
                        <option value="cancelled" <?=($data[0]['booking_status'] ?? '' )==="cancelled" ? "selected" : ""
                            ;?>>
                            <?=T::cancelled?>
                        </option>
                    </select>
                    <label for="">
                        <?=T::booking?>
                        <?=T::status?>
                    </label>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-floating">
                    <select id="search_type" name="payment_status" class="form-select payment_status">
                        <option value="">Select Type</option>
                        <option value="paid" <?=($data[0]['payment_status'] ?? '' )==="paid" ? "selected" : "" ;?>>
                            <?=T::paid?>
                        </option>
                        <option value="unpaid" <?=($data[0]['payment_status'] ?? '' )==="unpaid" ? "selected" : "" ;?>>
                            <?=T::unpaid?>
                        </option>
                        <option value="refunded" <?=($data[0]['payment_status'] ?? '' )==="refunded" ? "selected" : ""
                            ;?>>
                            <?=T::refunded?>
                        </option>
                    </select>
                    <label for="">
                        <?=T::payment?>
                        <?=T::status?>
                    </label>
                </div>
            </div>
            <?php $hotels = $db->select('hotels', '*', ['status' => 1]); ?>
<div class="col-md-4">
    <div class="form-floating">
        <select class="form-select" id="hotel_select" name="hotel_id">
            <option value="">Select Hotel</option>
            <?php foreach ($hotels as $hotel): ?>
            <option value="<?= $hotel['id'] ?>" <?=($data[0]['hotel_id'] ?? '' ) == $hotel['id'] ? "selected" : ""; ?>>
                <?= $hotel['name'] ?>
            </option>
            <?php endforeach; ?>
        </select>
        <label for="hotel_select">Hotel</label>
    </div>
</div>


<?php
$agents = $db->select('users', '*', [
    'user_type' => 'agent',  
    'status' => 1
]);

$selectedAgentId = $data[0]['agent_id'] ?? null; 
?>

<div class="col-md-4">
    <div class="form-floating">
        <select class="form-select" id="agent_id" name="agent_id">
            <option value="">Select Agent</option>
            <?php foreach ($agents as $agent): ?>
            <option value="<?= $agent['user_id'] ?>" <?= ($selectedAgentId == $agent['user_id']) ? "selected" : ""; ?>>
                <?= htmlspecialchars($agent['first_name'] . ' ' . $agent['last_name']) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <label for="agent_select">Agent</label>
    </div>
</div>

<!-- <div class="col-md-4">
    <div class="form-floating">
        <select class="form-select" id="room_select" name="room_id">
            <option value="">Select Room</option>
        </select>
        <label for="room_select">Room</label>
    </div>
</div> -->



            <!-- Add First Name and Last Name Fields -->

            <div class="col-md-12">
                <div class="card" style="margin-block-end:0px;">
                    <div class="card-header bg-primary text-black">
                        <strong>Travellers</strong>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="first_name" name="first_name"
                                        value="<?= $first_name ?>">
                                    <label for="first_name">First Name</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="last_name" name="last_name"
                                        value="<?= $last_name ?>">
                                    <label for="last_name">Last Name</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="<?= $email ?>">
                                    <label for="email">Email</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="phone" name="phone"
                                        value="<?= $phone ?>">
                                    <label for="phone">Phone</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card" style="margin-block-end:0px;">
                    <div class="card-header bg-primary text-black">
                        <strong>Booking Note                        </strong>
                    </div>
                    <div class="card-body p-3">
                    <textarea name="bookingnote" class="form-control" id="bookingnote" rows="4"><?= $data[0]['booking_note'] ?? '' ?></textarea>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
            <?php 
            $curreny = $db->select("currencies", "*", ["default" => 1,]);?>
               <small for="">Room Price</small>
               <div class="form-floating">
                  <div class="input-group">
                     <input type="number" class="form-control rounded-0" id="room_price" name="room_price" value="<?= $data[0]['price_original'] ?? '' ?>" required>
                     <span class="input-group-text text-white bg-primary"><?= $curreny[0]['name']?></span>
                  </div>
                  <!-- <label for="">Room Price</label> -->
               </div>
            </div>

            <div class="col-md-2">
               <small for="">Platform Commission</small>
               <div class="form-floating">
                  <div class="input-group">
                     <input type="number" class="form-control rounded-0" id="platform_comission" name="platform_comission" value="<?= $data[0]['platform_comission'] ?? '' ?>" required>
                     <span class="input-group-text text-white bg-primary"><?= $curreny[0]['name']?></span>
                  </div>
                  <!-- <label for="">Platform Commission</label> -->
               </div>
            </div>

            <div class="col-md-2">
               <small for="">Tax</small>
               <div class="form-floating">
                  <div class="input-group">
                     <input type="number" class="form-control rounded-0" id="tax" name="tax" value="<?= $data[0]['tax'] ?? '' ?>" required>
                     <span class="input-group-text text-white bg-primary">%</span>
                  </div>
                  <!-- <label for="">Tax</label> -->
               </div>
            </div>

            <!-- Agent Commission -->
            <div class="col-md-2">
               <div class="form-floating">
                  <small for="">Agent Commission</small>
                  <div class="input-group">
                     <input type="number" class="form-control rounded-0" id="agent_comission" name="agent_comission" value="<?= $data[0]['agent_fee'] ?? '' ?>" required>
                     <span class="input-group-text text-white bg-primary">%</span>
                  </div>
                  <!-- <label for="">Agent Commission</label> -->
               </div>
            </div>

            <div class="col-md-4">
               <div class="form-floating">
                  <small for="">Total Price</small>
                  <div class="input-group">
                  <input type="text" class="form-control fw-semibold text-dark" id="bookingPrice" value="<?= $data[0]['price_markup'] ?? '' ?>" name="price" >
                  </div>
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
                <button type="submit" class="btn btn-primary w-100 h-100 rounded-4"
                    style="border-radius: 8px !important;">
                    <?=T::submit?>
                </button>
            </div>
        </form>
        <script>
        $(document).ready(function() {
    $('#hotel_select').change(function() {
        var hotelId = $(this).val();

        if (hotelId) {
            $.ajax({
                url: 'book-update-ajax.php',
                method: 'POST', 
                data: { hotel_id: hotelId },
                dataType: 'json',
                success: function(response) {
                    $('#room_select').html('<option value="">Select Room</option>');

                    if (response.length > 0) {
                        response.forEach(function(room) {
                            $('#room_select').append('<option value="' + room.id + '">' + room.name + '</option>');
                        });
                    } else {
                        $('#room_select').append('<option value="">No rooms available</option>');
                    }
                },
                error: function() {
                    alert('Error fetching rooms.');
                }
            });
        } else {
            $('#room_select').html('<option value="">Select Room</option>');
        }
    });
});
</script>


        <script>
            $("#search").submit(function (event) {
                event.preventDefault();

                var booking_id = $("#booking_id").val();
                var module = $("#module").val();
                var booking_date = $("#booking_date").val();
                var booking_status = $(".booking_status").val();
                var payment_status = $(".payment_status").val();
                var checkin = $("#checkin").val();
                var checkout = $("#checkout").val();
                var hotel_id = $("#hotel_select").val();
                var first_name = $("#first_name").val();  // Get first_name
                var last_name = $("#last_name").val();    // Get last_name
                var email = $("#email").val();
                var phone = $("#phone").val();
                var bookingnote = $("#bookingnote").val();
                var agent_id = $("#agent_id").val();

                var room_price = $("#room_price").val();
                var platform_comission = $("#platform_comission").val();
                var tax = $("#tax").val();
                var agent_comission = $("#agent_comission").val();
                var bookingPrice = $("#bookingPrice").val();

                // Send the updated data back to the server via query parameters or AJAX
                window.location.href = "<?=$root?>/admin/booking_update.php?booking_id=" + booking_id + "&module=" + module + "&booking_date=" + booking_date + "&booking_status=" + booking_status + "&payment_status=" + payment_status + "&checkin=" + checkin + "&checkout=" + checkout + "&hotel_id=" + hotel_id + "&first_name=" + first_name + "&last_name=" + last_name + "&email=" + email + "&phone=" + phone + "&room_price=" + room_price + "&platform_comission=" + platform_comission + "&tax=" + tax + "&agent_comission=" + agent_comission + "&bookingPrice=" + bookingPrice + "&bookingnote=" + bookingnote + "&agent_id=" + agent_id;

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