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
            $hotel_id = $_GET['hotel_id'];
            $hotel_data = $db->select('hotels', ['name'], ['id' => $hotel_id]);
            $hotel_name = $hotel_data[0]['name'] ?? '';

            $table_name = $_GET['module'] . "_bookings";
            $existing_data = $db->select($table_name, "*", ['booking_ref_no' => $_GET['booking_id']]);
            $existing_user_data = json_decode($existing_data[0]['user_data'] ?? '{}', true);

            $updated_user_data = array_merge($existing_user_data, [
                'first_name' => $_GET['first_name'] ?? $existing_user_data['first_name'] ?? null,
                'last_name' => $_GET['last_name'] ?? $existing_user_data['last_name'] ?? null,
                'email' => $_GET['email'] ?? $existing_user_data['email'] ?? null,
                'phone' => $_GET['phone'] ?? $existing_user_data['phone'] ?? null,
            ]);

            $user_data_json = json_encode($updated_user_data);

            if (isset($_GET['room_select'])) {
                $room_id = $_GET['room_select'];

                $room_details = $db->select("hotels_rooms", [
                    "[>]hotels_settings" => ["room_type_id" => "id"]
                ], [
                    "hotels_rooms.id",
                    "hotels_settings.name",
                    "hotels_rooms.extra_bed_charges",
                    "hotels_rooms.extra_bed",
                ], [
                    "hotels_rooms.id" => $room_id,
                    "hotels_rooms.status" => 1
                ]);

                $room_options = $db->select("hotels_rooms_options", ["price", "quantity"], [
                    "room_id" => $room_id
                ]);

                if (!empty($room_details)) {
                    $room_data = [
                        "room_id" => $room_details[0]['id'],
                        "room_name" => $room_details[0]['name'],
                        "room_price" => $_POST['room_price'] ?? '0.00',
                        "room_quantity" => !empty($room_options) ? $room_options[0]['quantity'] : "1",
                        "room_extrabed_price" => $room_details[0]['extra_bed_charges'],
                        "room_extrabed" => $room_details[0]['extra_bed'],
                        "room_actual_price" => !empty($room_options) ? $room_options[0]['price'] : "0.00"
                    ];

                    $room_data_json = json_encode([$room_data]);
                }
            }

            $hotel_img = $db->select("hotels_images", "*", ["hotel_id" => $_GET['hotel_id']]);

            $db->update(
                $table_name,
                [
                    'booking_date' => $_GET['booking_date'],
                    'booking_status' => $_GET['booking_status'],
                    'payment_status' => $_GET['payment_status'],
                    'checkin' => $_GET['checkin'],
                    'checkout' => $_GET['checkout'],
                    'hotel_id' => $hotel_id,
                    'hotel_name' => $hotel_name,
                    'hotel_img' => $hotel_img[0]["img"],
                    'first_name' => $_GET['first_name'],
                    'last_name' => $_GET['last_name'],
                    'email' => $_GET['email'],
                    'agent_id' => $_GET['agent_id'],
                    'booking_note' => $_GET['bookingnote'],
                    'cancellation_terms' => $_GET['cancellation_terms'],
                    'phone' => $_GET['phone'],
                    'user_data' => $user_data_json,
                    'price_original' => $_GET['room_price'],
                    'platform_comission' => $_GET['platform_comission'],
                    'tax' => $_GET['tax'],
                    'agent_fee' => $_GET['agent_comission'],
                    'price_markup' => $_GET['bookingPrice'],
                    'supplier_id' => $_GET['supplier_id'],
                    'supplier_payment_status' => $_GET['supplier_payment_status'],
                    'supplier_cost' => $_GET['supplier_cost'],
                    'supplier_due_date' => $_GET['supplier_due_date'],
                    'supplier_payment_type' => $_GET['supplier_payment_type'],
                    'iata' => $_GET['iata'],
                    'room_data' => $room_data_json
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

            $user_data = json_decode($data[0]['user_data'] ?? '{}', true);

            $email = $user_data['email'] ?? '';
            $first_name = $user_data['first_name'] ?? '';
            $last_name = $user_data['last_name'] ?? '';
            $phone = $user_data['phone'] ?? '';

            $room_data = json_decode($data[0]['room_data'] ?? '{}', true);

            if (!empty($room_data)) {
                $room_id = $room_data[0]['room_id'];
                $room_name = $room_data[0]['room_name'];
            }
        } else {
            REDIRECT('./bookings.php');
        }
        ?>


        <form class="row g-3" id="search">
            <div class="col-md-3">
                <div class="form-floating">
                    <input type="text" class="form-control" id="booking_id" name="booking_id"
                        value="<?= $data[0]['booking_ref_no'] ?? '' ?>" readonly>
                    <label for="">
                        <?=T::booking?>
                        <?=T::id?>
                    </label>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-floating">
                    <input type="date" class="form-control" id="booking_date" name="booking_date"
                        value="<?= $data[0]['booking_date'] ?? '' ?>">
                    <label for="">
                        <?=T::booking?>
                        <?=T::date?>
                    </label>
                </div>
            </div>


            <div class="col-md-3">
                <div class="form-floating">
                    <select class="form-select booking_status" id="search_type" name="booking_status">
                        <option value="">
                            <?=T::select?>
                            <?=T::type?>
                        </option>
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

            <div class="col-md-3">
                <div class="form-floating">
                    <select id="search_type" name="payment_status" class="form-select payment_status">
                        <option value="">
                            <?=T::select?>
                            <?=T::type?>
                        </option>
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
            <div class="col-md-12">
                <div class="card mb-2">
                    <div class="card-header bg-primary text-dark">
                        <strong class="">
                            <?=T::hotel?>
                        </strong>
                    </div>

                    <div class="card-body p-3">
                        <div class="row g-3">
                            <?php $hotels = $db->select('hotels', '*', ['status' => 1,'location'=>$data[0]['location']]); ?>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="hotel_select" name="hotel_id">
                                        <option value="">
                                            <?=T::select?>
                                            <?=T::hotel?>
                                        </option>
                                        <?php foreach ($hotels as $hotel): ?>
                                        <option value="<?= $hotel['id'] ?>" <?=($data[0]['hotel_id'] ?? ''
                                            )==$hotel['id'] ? "selected" : "" ; ?>>
                                            <?= $hotel['name'] ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="hotel_select">
                                        <?=T::hotel?>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-floating">
                                    <select class="form-select" id="room_select" name="room_id" required>
                                        <option value="">
                                            <?=T::select?>
                                            <?=T::room?>
                                        </option>
                                        <?php if (!empty($room_id)): ?>
                                        <option value="<?= $room_id ?>" selected>
                                            <?= ($room_name ?? ''); ?>
                                        </option>
                                        <?php endif; ?>
                                    </select>
                                    <label for="room_select">
                                        <?=T::room?>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <?php 
                            $curreny = $db->select("currencies", "*", ["default" => 1,]);?>
                                <div class="form-floating">
                                    <div class="input-group">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" id="room_price"
                                                style="border-top-right-radius:0 !important;border-bottom-right-radius:0 !important;"
                                                name="room_price" value="<?= $data[0]['price_original'] ?? '' ?>"
                                                required>
                                            <label for="">
                                                <?=T::room?>
                                                <?=T::price?>
                                            </label>
                                        </div>
                                        <span class="input-group-text text-white bg-primary">
                                            <?= $curreny[0]['name']?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="m-0">
                </div>
            </div>

            <div class="col-md-12">
                <div class="card mb-2">
                    <div class="card-header bg-primary text-dark">
                        <strong class="">
                            <?=T::supplier?>
                        </strong>
                    </div>

                    <div class="card-body p-3">
                        <div class="row g-3">
                            <?php
                            $suppliers = $db->select('users', '*', [
                            'user_type' => 'supplier',  
                            'status' => 1
                        ]);

                        $selectedsupplierId = $data[0]['supplier_id'] ?? null; 
                    ?>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="supplier_id" name="supplier_id">
                                        <option value="">
                                            <?=T::select?>
                                            <?=T::supplier?>
                                        </option>
                                        <?php foreach ($suppliers as $supplier): ?>
                                        <option value="<?= $supplier['user_id'] ?>"
                                            <?=($selectedsupplierId==$supplier['user_id']) ? "selected" : "" ; ?>>
                                            <?= htmlspecialchars($supplier['first_name'] . ' ' . $supplier['last_name']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="agent_select">
                                        <?=T::supplier?>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select id="supplier_payment_status" name="supplier_payment_status"
                                        class="form-select" required>
                                        <option value="" disabled selected>
                                            <?=T::supplier?>
                                            <?=T::payment?>
                                            <?=T::status?>
                                        </option>
                                        <option value="paid" <?=($data[0]['supplier_payment_status'] ?? '' )==="paid"
                                            ? "selected" : "" ;?>>
                                            <?=T::paid?>
                                        </option>
                                        <option value="unpaid" <?=($data[0]['supplier_payment_status'] ?? ''
                                            )==="unpaid" ? "selected" : "" ;?>>
                                            <?=T::unpaid?>
                                        </option>
                                    </select>
                                    <label for="agent_select">
                                        <?=T::payment?>
                                        <?=T::status?>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select id="supplier_payment_type" name="supplier_payment_type"
                                        class="form-select" required>
                                        <option value="" disabled selected>
                                            <?=T::select?>
                                            <?=T::payment?>
                                            <?=T::type?>
                                        </option>
                                        <option value="stripe" <?=($data[0]['supplier_payment_type'] ?? '' )==="stripe"
                                            ? "selected" : "" ;?>>
                                            <?=T::stripe?>
                                        </option>
                                        <option value="wire" <?=($data[0]['supplier_payment_type'] ?? ''
                                            )==="wire" ? "selected" : "" ;?>>
                                            <?=T::wire?>
                                        </option>
                                        <option value="zelle" <?=($data[0]['supplier_payment_type'] ?? ''
                                            )==="zelle" ? "selected" : "" ;?>>
                                            <?=T::zelle?>
                                        </option>
                                        <option value="venmo" <?=($data[0]['supplier_payment_type'] ?? ''
                                            )==="venmo" ? "selected" : "" ;?>>
                                            <?=T::venmo?>
                                        </option>
                                        <option value="paypal" <?=($data[0]['supplier_payment_type'] ?? ''
                                            )==="paypal" ? "selected" : "" ;?>>
                                            <?=T::paypal?>
                                        </option>
                                        <option value="cash" <?=($data[0]['supplier_payment_type'] ?? ''
                                            )==="cash" ? "selected" : "" ;?>>
                                            <?=T::cash?>
                                        </option>
                                    </select>
                                    <label for="agent_select">
                                        <?=T::payment?>
                                        <?=T::type?>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="supplier_due_date"
                                        name="supplier_due_date" autocomplete="off"
                                        value="<?=($data[0]['supplier_due_date'])?>" required>
                                    <label for="supplier_due_date">
                                        <?=T::supplier?>
                                        <?=T::due?>
                                        <?=T::date?>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="iata" name="iata"
                                        value="<?= $data[0]['iata'] ?? '' ?>">
                                    <label for="iata">
                                        <?=T::iata?>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <div class="input-group">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" id="supplier_cost"
                                                name="supplier_cost" value="<?= $data[0]['supplier_cost'] ?? '' ?>"
                                                required
                                                style="border-top-right-radius:0 !important;border-bottom-right-radius:0 !important;">
                                            <label for="">
                                                <?=T::supplier?>
                                                <?=T::cost?>
                                            </label>
                                        </div>
                                        <span class="input-group-text text-white bg-primary">
                                            <?= $curreny[0]['name']?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="m-0">
                </div>
            </div>


            <div class="col-md-12">
                <div class="card mb-2">
                    <div class="card-header bg-primary text-dark">
                        <strong class="">
                            <?=T::agent?>
                        </strong>
                    </div>

                    <div class="card-body p-3">
                        <div class="row g-3">
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
                                        <option value="">
                                            <?=T::select?>
                                            <?=T::agent?>
                                        </option>
                                        <?php foreach ($agents as $agent): ?>
                                        <option value="<?= $agent['user_id'] ?>"
                                            <?=($selectedAgentId==$agent['user_id']) ? "selected" : "" ; ?>>
                                            <?= htmlspecialchars($agent['first_name'] . ' ' . $agent['last_name']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="agent_select">
                                        <?=T::agent?>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-5"></div>
                            <!-- Agent Commission -->
                            <div class="col-md-3">
                                <div class="input-group">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="agent_comission"
                                            name="agent_comission" value="<?= $data[0]['agent_fee'] ?? '' ?>" required
                                            style="border-top-right-radius:0 !important;border-bottom-right-radius:0 !important;">
                                        <label for="">
                                            <?=T::agent?>
                                            <?=T::fee?>
                                        </label>
                                    </div>
                                    <span class="input-group-text text-white bg-primary">
                                        %
                                    </span>

                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="m-0">
                </div>
            </div>





            <!-- Add First Name and Last Name Fields -->

            <div class="col-md-12">
                <div class="card" style="margin-block-end:0px;">
                    <div class="card-header bg-primary text-black">
                        <strong>
                            <?=T::travellers?>
                        </strong>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="first_name" name="first_name"
                                        value="<?= $first_name ?>">
                                    <label for="first_name">
                                        <?=T::first_name?>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="last_name" name="last_name"
                                        value="<?= $last_name ?>">
                                    <label for="last_name">
                                        <?=T::last_name?>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="<?= $email ?>">
                                    <label for="email">
                                        <?=T::email?>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="phone" name="phone"
                                        value="<?= $phone ?>">
                                    <label for="phone">
                                        <?=T::phone?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <hr class=m-0>
                    </div>
                    <div class="row g-3 p-3">
                        <!-- Check-in Date -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="checkin form-control" id="checkin" name="checkin"
                                    autocomplete="off" value="<?= $data[0]['checkin'] ?? '' ?>">
                                <label for="checkin">
                                    <?=T::checkin?>
                                    <?=T::date?>
                                </label>
                            </div>
                        </div>

                        <!-- Check-out Date -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="checkout form-control" id="checkout" name="checkout"
                                    autocomplete="off" value="<?= $data[0]['checkout'] ?? '' ?>">
                                <label for="checkout">
                                    <?=T::checkout?>
                                    <?=T::date?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card" style="margin-block-end:0px;">
                    <div class="card-header bg-primary text-black">
                        <strong>
                            <?=T::booking?>
                            <?=T::note?>
                        </strong>
                    </div>
                    <div class="card-body p-3">
                        <textarea name="bookingnote" class="form-control" id="bookingnote"
                            rows="4"><?= $data[0]['booking_note'] ?? '' ?></textarea>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card" style="margin-block-end:0px;">
                    <div class="card-header bg-primary text-black">
                        <strong>
                            <?=T::cancellation?>
                            <?=T::terms?>
                            <?=T::and?>
                            <?=T::policy?>
                        </strong>
                    </div>
                    <div class="card-body p-3">
                        <textarea name="cancellation_terms" class="form-control" id="cancellation_terms"
                            rows="4"><?= $data[0]['cancellation_terms'] ?? '' ?></textarea>
                    </div>
                </div>
            </div>


            <div class="col-md-2">
                <div class="form-floating">
                    <div class="input-group">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="platform_comission" name="platform_comission"
                                value="<?= $data[0]['platform_comission'] ?? '' ?>" required
                                style="border-top-right-radius:0 !important;border-bottom-right-radius:0 !important;">
                            <label for="">
                                <?=T::net_profit?>
                            </label>
                        </div>
                        <span class="input-group-text text-white bg-primary">
                            <?= $curreny[0]['name']?>
                        </span>

                    </div>
                    <!-- <label for="">Room Price</label> -->
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-floating">
                    <div class="input-group">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="tax" name="tax"
                                value="<?= $data[0]['tax'] ?? '' ?>" required
                                style="border-top-right-radius:0 !important;border-bottom-right-radius:0 !important;">
                            <label for="">
                                <?=T::tax_?>
                            </label>
                        </div>
                        <span class="input-group-text text-white bg-primary">
                            %

                        </span>
                    </div>
                </div>

            </div>


            <div class="col-md-4">
                <div class="form-floating">
                    <div class="input-group">
                        <div class="form-floating">
                            <input type="number" class="form-control text-dark" id="bookingPrice"
                                value="<?= $data[0]['price_markup'] ?? '' ?>" name="price" required
                                style="border-top-right-radius:0 !important;border-bottom-right-radius:0 !important;">
                            <label class="text-dark" for="">
                                <?=T::total?>
                                <?=T::price?>
                            </label>
                        </div>
                        <span class="input-group-text text-white bg-primary">
                            <?= $curreny[0]['name']?>
                        </span>
                    </div>
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
            $(document).ready(function () {
                function updateRooms(hotelId, selectedRoomId) {
                    if (hotelId) {
                        $.ajax({
                            url: 'booking-ajax.php',
                            method: 'POST',
                            data: {
                                action: 'get_rooms_update',
                                hotel_id: hotelId
                            },
                            dataType: 'json',
                            success: function (response) {
                                $('#room_select').html('<option value="">Select Room</option>');

                                if (response.length > 0) {
                                    response.forEach(function (room) {
                                        var selected = room.id == selectedRoomId ? 'selected' : '';
                                        $('#room_select').append('<option value="' + room.id + '" ' + selected + '>' + room.name + '</option>');
                                    });
                                } else {
                                    $('#room_select').append('<option value="">No rooms available</option>');
                                }
                            },
                            error: function () {
                                alert('Error fetching rooms.');
                            }
                        });
                    } else {
                        $('#room_select').html('<option value="">Select Room</option>');
                    }
                }

                var selectedHotelId = $('#hotel_select').val();
                var selectedRoomId = $('#room_select').val();

                updateRooms(selectedHotelId, selectedRoomId);

                $('#hotel_select').change(function () {
                    var hotelId = $(this).val();
                    updateRooms(hotelId, selectedRoomId);
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
                var room_select = $("#room_select").val(); cancellation_terms
                var cancellation_terms = $("#cancellation_terms").val();
                var supplier_id = $("#supplier_id").val();
                var supplier_payment_status = $("#supplier_payment_status").val();
                var supplier_cost = $("#supplier_cost").val();
                var supplier_due_date = $("#supplier_due_date").val();
                var supplier_payment_type = $("#supplier_payment_type").val();
                var iata =  $("#iata").val();

                // Send the updated data back to the server via query parameters or AJAX
                window.location.href = "<?=$root?>/admin/booking_update.php?booking_id=" + booking_id + "&module=" + module + "&booking_date=" + booking_date + "&booking_status=" + booking_status + "&payment_status=" + payment_status + "&checkin=" + checkin + "&checkout=" + checkout + "&hotel_id=" + hotel_id + "&first_name=" + first_name + "&last_name=" + last_name + "&email=" + email + "&phone=" + phone + "&room_price=" + room_price + "&platform_comission=" + platform_comission + "&tax=" + tax + "&agent_comission=" + agent_comission + "&bookingPrice=" + bookingPrice + "&bookingnote=" + bookingnote + "&agent_id=" + agent_id + "&room_select=" + room_select + "&supplier_payment_status=" + supplier_payment_status + "&supplier_due_date=" + supplier_due_date + "&cancellation_terms=" + cancellation_terms + "&supplier_cost=" + supplier_cost + "&supplier_id=" + supplier_id + "&supplier_payment_type=" + supplier_payment_type + "&iata=" + iata;

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