<?php
require_once '_config.php';
auth_check();
$title = T::booking .' '. T::edit;
include "_header.php";

?>

<style>
  .modal.fade {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 1050;
    background-color: rgba(0, 0, 0, 0.5);
    align-items: center;
    justify-content: center;
    transition: opacity 0.2s ease-in-out;
    }

    .modal.show {
    display: flex;
    }

    /* Modal Dialog */
    .modal-dialog {
    position: relative;
    width: 100%;
    max-width: 500px;
    margin: auto;
    }

    /* Modal Content */
    .modal-content {
    background-color: #fff;
    border-radius: 0.5rem;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
    overflow: hidden;
    animation: fadeInScale 0.25s ease;
    }

    @keyframes fadeInScale {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
    }

    /* Header */
    .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #dee2e6;
    background-color: #f8f9fa;
    }

    .modal-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #333;
    }

    .btn-close {
    background: transparent;
    border: none;
    font-size: 1.25rem;
    color: #555;
    cursor: pointer;
    }

    .btn-close:hover {
    color: #000;
    }

    /* Body */
    .modal-body {
    padding: 1.5rem;
    text-align: center;
    }

    .input-group {
    display: flex;
    align-items: stretch;
    }

    .input-group input {
    flex: 1;
    padding: 0.5rem 0.75rem;
    border: 1px solid #ced4da;
    border-right: none;
    border-radius: 0.375rem 0 0 0.375rem;
    font-size: 0.95rem;
    }

    .input-group button {
    border: 1px solid #ced4da;
    background-color: #f8f9fa;
    cursor: pointer;
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
    border-radius: 0 0.375rem 0.375rem 0;
    transition: background 0.2s ease;
    }

    .input-group button:hover {
    background-color: #e2e6ea;
    }

    /* Footer */
    .modal-footer {
    display: flex;
    justify-content: flex-end;
    padding: 1rem 1.25rem;
    border-top: 1px solid #dee2e6;
    background-color: #f8f9fa;
    gap: 0.5rem;
    }

    /* Buttons */
    .btn {
    display: inline-block;
    font-weight: 500;
    text-align: center;
    vertical-align: middle;
    cursor: pointer;
    border: 1px solid transparent;
    padding: 0.45rem 1rem;
    font-size: 0.95rem;
    border-radius: 0.375rem;
    transition: background-color 0.2s, border-color 0.2s;
    }

    .btn-primary {
    color: #fff;
    background-color: #0d6efd;
    border-color: #0d6efd;
    }

    .btn-primary:hover {
    background-color: #0b5ed7;
    }

    .btn-secondary {
    color: #fff;
    background-color: #6c757d;
    border-color: #6c757d;
    }

    .btn-secondary:hover {
    background-color: #5c636a;
    }

    .btn-outline-secondary {
    color: #6c757d;
    border-color: #6c757d;
    }

    .btn-outline-secondary:hover {
    background-color: #6c757d;
    color: #fff;
    }
</style>
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

            $booking_ref_no = $_GET['booking_id'];
            $module = $_GET['module'];
            $table_name = $module . "_bookings";

            // Fetch existing full booking
            $existing = $db->get($table_name, "*", ["booking_ref_no" => $booking_ref_no]);
            if (!$existing) {
                die("Booking not found.");
            }

            // --- 1. Recalculate nights ---
            $checkin = $_GET['checkin'];
            $checkout = $_GET['checkout'];
            $checkin_date = new DateTime($checkin);
            $checkout_date = new DateTime($checkout);
            $days = max(1, $checkin_date->diff($checkout_date)->days);

            // --- 2. Get UPDATED pricing inputs from $_GET ---
            $room_price_per_night = floatval($_GET['room_price'] ?? 0);          // markup price per night
            $actual_room_price_per_night = floatval($_GET['actual_room_price'] ?? 0); // actual/supplier price per night
            $room_quantity = floatval($_GET['room_quantity'] ?? ($existing['booking_nights'] > 0 ? 1 : 1)); // fallback to 1

            // Recalculate totals
            $total_markup_price = $room_price_per_night * $days * $room_quantity;
            $total_actual_price = $actual_room_price_per_night * $days * $room_quantity;
            $cc_fee = ($total_markup_price * 0.029) + 0.3;

            $tax_percent = floatval($_GET['tax'] ?? $existing['tax']);
            $agent_commission = floatval($_GET['agent_comission'] ?? $existing['agent_fee']);
            $supplier_cost = floatval($_GET['supplier_cost'] ?? $existing['supplier_cost']);
            $iata = floatval($_GET['iata'] ?? $existing['iata']);

            // Recalculate dependent fields
            $subtotal_per_night = $tax_percent > 0 ? ($room_price_per_night / (1 + $tax_percent / 100)) : $room_price_per_night;
            $total_subtotal = $subtotal_per_night * $days * $room_quantity;
            $net_profit = $total_markup_price - $supplier_cost - $agent_commission + $iata - $cc_fee;

            // --- 3. UPDATE room_data (only prices & totals, keep rest as-is) ---
            $room_data_json = $existing['room_data'];
            $room_data = json_decode($existing['room_data'], true);
            if (!empty($room_data) && is_array($room_data)) {
                // Update only price-related fields
                $room_data[0]['room_price_per_night'] = $room_price_per_night;
                $room_data[0]['room_actual_price_per_night'] = $actual_room_price_per_night;
                $room_data[0]['room_quantity'] = $room_quantity;
                $room_data[0]['total_nights'] = $days;
                $room_data[0]['total_markup_price'] = $total_markup_price;
                $room_data[0]['total_actual_price'] = $total_actual_price;
                $room_data[0]['cc_fee'] = $cc_fee;
                $room_data_json = json_encode($room_data);
            }

            // --- 4. UPDATE booking_data (only prices & totals) ---
            $booking_data_json = $existing['booking_data'];
            $booking_data = json_decode($existing['booking_data'], true);
            if (!empty($booking_data) && is_array($booking_data)) {
                $booking_data['price'] = number_format($total_actual_price, 2, '.', '');
                $booking_data['per_day'] = number_format($actual_room_price_per_night, 2, '.', '');
                $booking_data['markup_price'] = number_format($total_markup_price, 2, '.', '');
                $booking_data['markup_price_per_night'] = number_format($room_price_per_night, 2, '.', '');
                $booking_data['service_fee'] = number_format($cc_fee, 2, '.', '');
                $booking_data['quantity'] = $room_quantity;
                $booking_data_json = json_encode($booking_data);
            }

            // --- 5. Prepare update data (only change price-related + statuses) ---
            $update_data = [
                'booking_status' => $_GET['booking_status'],
                'payment_status' => $_GET['payment_status'],
                'checkin' => $checkin,
                'checkout' => $checkout,
                'booking_nights' => $days,

                // Pricing fields
                'price_original' => $total_actual_price,
                'price_markup' => $total_markup_price,
                'net_profit' => $net_profit,
                'tax' => $tax_percent,
                'agent_fee' => $agent_commission,
                'supplier_cost' => $supplier_cost,
                'iata' => $iata,
                'subtotal' => number_format($total_subtotal,2),

                // Status & payment fields
                'agent_id' => $_GET['agent_id'] ?? $existing['agent_id'],
                'supplier_id' => $_GET['supplier_id'] ?? $existing['supplier_id'],
                'supplier_payment_status' => $_GET['supplier_payment_status'] ?? $existing['supplier_payment_status'],
                'supplier_due_date' => $_GET['supplier_due_date'] ?? $existing['supplier_due_date'],
                'supplier_payment_type' => $_GET['supplier_payment_type'] ?? $existing['supplier_payment_type'],
                'customer_payment_type' => $_GET['customer_payment_type'] ?? $existing['customer_payment_type'],
                'agent_payment_type' => $_GET['agent_payment_type'] ?? $existing['agent_payment_type'],
                'agent_payment_status' => $_GET['agent_payment_status'] ?? $existing['agent_payment_status'],
                'booking_note' => $_GET['bookingnote'] ?? $existing['booking_note'],
                'cancellation_terms' => $_GET['cancellation_terms'] ?? $existing['cancellation_terms'],

                // Keep user/hotel/room structure intact
                'room_data' => $room_data_json,
                'booking_data' => $booking_data_json,
            ];

            // Update agent payment date if paid
            if (($_GET['agent_payment_status'] ?? $existing['agent_payment_status']) == 'paid') {
                $update_data['agent_payment_date'] = date('Y-m-d');
            }

            // Perform update
            $db->update($table_name, $update_data, ['booking_ref_no' => $booking_ref_no]);

            $update_success = true;
            $booking_ref = $booking_ref_no;
            $module = $module;
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
                $room_quantity = $room_data[0]['room_quantity'];
                $price_actual_per_day = $room_data[0]['room_actual_price_per_night'];
                $price_markup_per_day = $room_data[0]['room_price_per_night'];
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

            <div class="col-md-2">
                <div class="form-floating">
                    <input type="date" class="form-control" id="booking_date" name="booking_date"
                        value="<?= $data[0]['booking_date'] ?? '' ?>">
                    <label for="">
                        <?=T::booking?>
                        <?=T::date?>
                    </label>
                </div>
            </div>


            <div class="col-md-2">
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

            <div class="col-md-2">
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

            <div class="col-md-3">
                <div class="form-floating">
                    <input type="text" class="form-control" name="customer_payment_type" value="stripe" readonly>
                    <!-- <select class="form-select select2" id="customer_payment_type" name="customer_payment_type" required>
                        <option disabled selected value=""><?=T::select?> <?=T::payment?> <?=T::type?></option>
                        <option value="stripe" <?=($data[0]['customer_payment_type'] ?? '' )==="stripe"
                                            ? "selected" : "" ;?>>
                                            <?=T::stripe?>
                        </option>
                        <option value="wire" <?=($data[0]['customer_payment_type'] ?? '' )==="wire"
                                            ? "selected" : "" ;?>>
                                            <?=T::wire?>
                        </option>
                        <option value="zelle" <?=($data[0]['customer_payment_type'] ?? '' )==="zelle"
                                            ? "selected" : "" ;?>>
                                            <?=T::zelle?>
                        </option>
                        <option value="venmo" <?=($data[0]['customer_payment_type'] ?? '' )==="venmo"
                                            ? "selected" : "" ;?>>
                                            <?=T::venmo?>
                        </option>
                        <option value="paypal" <?=($data[0]['customer_payment_type'] ?? '' )==="paypal"
                                            ? "selected" : "" ;?>>
                                            <?=T::paypal?>
                        </option>
                        <option value="cash" <?=($data[0]['customer_payment_type'] ?? '' )==="cash"
                                            ? "selected" : "" ;?>>
                                            <?=T::cash?>
                        </option>
                    </select> -->
                    <label for="customer_payment_type"><?=T::customer?> <?=T::payment?> <?=T::type?></label>
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
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="hidden" class="form-control" name="hotel_select" value="<?= htmlspecialchars($data[0]['hotel_id']) ?>" readonly>
                                    <input type="text" class="form-control" name="hotel_name" value="<?= htmlspecialchars($data[0]['hotel_name']) ?>" readonly>
                                    <label><?= T::hotel ?></label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="hidden" class="form-control" name="room_select" value="<?= htmlspecialchars($room_id) ?>" readonly>
                                    <input type="text" class="form-control" name="room_name" value="<?= htmlspecialchars($room_name) ?>" readonly>
                                    <label><?= T::room ?></label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <?php 
                            $curreny = $db->select("currencies", "*", ["default" => 1,]);?>
                                <div class="form-floating">
                                    <div class="input-group">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" id="room_quantity" step="any" min="0"
                                                name="room_quantity" value="<?= $room_quantity ?? '' ?>"
                                                required>
                                            <label for="">
                                                <?=T::room?>
                                                <?=T::quantity?>
                                            </label>
                                        </div>
                                        <span class="input-group-text text-white bg-primary">
                                            <?= $curreny[0]['name']?>
                                        </span>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-4">
                                <?php 
                            $curreny = $db->select("currencies", "*", ["default" => 1,]);?>
                                <div class="form-floating">
                                    <div class="input-group">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" id="actual_room_price" step="any" min="0"
                                                style="border-top-right-radius:0 !important;border-bottom-right-radius:0 !important;"
                                                name="actual_room_price" value="<?= $price_actual_per_day ?? '' ?>"
                                                required>
                                            <label for="">
                                                <?=T::actual?>
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


                            <div class="col-md-4">
                                <?php 
                            $curreny = $db->select("currencies", "*", ["default" => 1,]);?>
                                <div class="form-floating">
                                    <div class="input-group">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" id="room_price" step="any" min="0"
                                                style="border-top-right-radius:0 !important;border-bottom-right-radius:0 !important;"
                                                name="room_price" value="<?= $price_markup_per_day ?? '' ?>"
                                                required>
                                            <label for="">
                                                <?=T::markup?>
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
                            $suppliers = $db->select('modules', ['name','id'], [
                                'type' => 'hotels',  
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
                                        <option value="<?= $supplier['id'] ?>"
                                            <?=($selectedsupplierId==$supplier['id']) ? "selected" : "" ; ?>>
                                            <?= ucfirst(htmlspecialchars($supplier['name'] . ' Supplier')) ?>
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
                                        class="form-select">
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
                                        class="form-select">
                                        <option value="" disabled selected>
                                            <?=T::select?>
                                            <?=T::payment?>
                                            <?=T::type?>
                                        </option>
                                        <option value="credit card" <?=($data[0]['supplier_payment_type'] ?? '' )==="credit card"
                                            ? "selected" : "" ;?>><?=T::credit?> <?=T::card?>
                                        </option>
                                        <option value="wire" <?=($data[0]['supplier_payment_type'] ?? '' )==="wire"
                                            ? "selected" : "" ;?>>
                                            <?=T::wire?>
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
                                    <input type="number" class="form-control" id="iata" name="iata" step="any" min="0"
                                        value="<?= $data[0]['iata'] ?? 0 ?>">
                                    <label for="iata">
                                        <?=T::iata?>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <div class="input-group">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" id="supplier_cost" step="any" min="0"
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

            <?php
            if(!empty($data[0]['agent_id']) && $data[0]['agent_id'] != null && $data[0]['agent_id'] != "0"){
            ?>
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


                            <div class="col-md-3">
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
                            
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select id="agent_payment_type" name="agent_payment_type" class="form-select">
                                        <option value="" disabled selected>
                                            <?=T::select?> <?=T::payment?> <?=T::type?>
                                        </option>
                                        <option value="pending" <?=($data[0]['agent_payment_type'] ?? '' )==="pending" ? "selected" : "" ;?>><?=T::pending?></option>
                                        <option value="wire" <?=($data[0]['agent_payment_type'] ?? '' )==="wire" ? "selected" : "" ;?>><?=T::wire?></option>
                                        <option value="zelle" <?=($data[0]['agent_payment_type'] ?? '' )==="zelle" ? "selected" : "" ;?>><?=T::zelle?></option>
                                        <option value="paypal" <?=($data[0]['agent_payment_type'] ?? '' )==="paypal" ? "selected" : "" ;?>><?=T::paypal?></option>
                                        <option value="venmo" <?=($data[0]['agent_payment_type'] ?? '' )==="venmo" ? "selected" : "" ;?>><?=T::venmo?></option>
                                    </select>
                                    <label for="agent_payment_type">
                                        <?=T::payment?> <?=T::type?>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select id="agent_payment_status" name="agent_payment_status" class="form-select">
                                        <option value="" disabled selected>
                                            <?=T::select?> <?=T::payment?> <?=T::status?>
                                        </option>
                                        <option value="paid" <?=($data[0]['agent_payment_status'] ?? '' )==="paid" ? "selected" : "" ;?>><?=T::paid?></option>
                                        <option value="pending" <?=($data[0]['agent_payment_status'] ?? '' )==="pending" ? "selected" : "" ;?>><?=T::pending?></option>
                                        <option value="cancelled" <?=($data[0]['agent_payment_status'] ?? '' )==="cancelled" ? "selected" : "" ;?>><?=T::cancelled?></option>
                                    </select>
                                    <label for="agent_payment_status">
                                        <?=T::payment?> <?=T::status?>
                                    </label>
                                </div>
                            </div>

                            
                            <!-- <div class="col-md-5"></div> -->
                            <!-- Agent Commission -->
                            <div class="col-md-3">
                                <div class="input-group">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="agent_comission" step="any" min="0"
                                            name="agent_comission" value="<?= $data[0]['agent_fee'] ?? '' ?>"
                                            style="border-top-right-radius:0 !important;border-bottom-right-radius:0 !important;">
                                        <label for="">
                                            <?=T::agent?>
                                            <?=T::commission?>
                                        </label>
                                    </div>
                                    <span class="input-group-text text-white bg-primary">
                                     <?= $curreny[0]['name']?>
                                    </span>

                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="m-0">
                </div>
            </div>
            <?php } ?>

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
                            <input type="number" class="form-control" id="tax" name="tax" step="any" min="0"
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

            <div class="col-md-2">
                <div class="form-floating">
                    <div class="input-group">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="subtotal" name="subtotal" step="any" min="0"
                                value="<?= $data[0]['subtotal'] ?? '' ?>" required
                                style="border-top-right-radius:0 !important;border-bottom-right-radius:0 !important;">
                            <label for="">
                                <?=T::sub?> <?=T::total?>
                            </label>
                        </div>
                        <span class="input-group-text text-white bg-primary">
                            %

                        </span>
                    </div>
                </div>

            </div>

            <div class="col-md-2">
                <div class="form-floating">
                    <div class="input-group">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="net_profit" name="net_profit" step="any"
                                value="<?= $data[0]['net_profit'] ?? '' ?>" required
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



            <div class="col-md-4">
                <div class="form-floating">
                    <div class="input-group">
                        <div class="form-floating">
                            <input type="number" class="form-control text-dark" id="bookingPrice" step="any" min="0"
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
                <button type="submit" data-ref="<?=$data[0]['booking_ref_no']?>" data_status="<?=$data[0]['payment_status']?>" class="btn btn-primary w-100 h-100 rounded-4"
                    style="border-radius: 8px !important;">
                    <?=T::submit?>
                </button>
            </div>
        </form>

    </div>
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

                /* updateRooms(selectedHotelId, selectedRoomId); */

                $('#hotel_select').change(function () {
                    var hotelId = $(this).val();
                    updateRooms(hotelId, selectedRoomId);
                });

                const flatpickrInstance = {
                    checkin: null,
                    checkout: null
                };

                // Initialize checkin datepicker
                flatpickrInstance.checkin = flatpickr("#checkin", {
                    dateFormat: "Y-m-d",
                    minDate: "today",
                    onChange: function(selectedDates, dateStr, instance) {
                        const checkout = flatpickrInstance.checkout;
                        if (checkout) {
                            const nextDay = new Date(selectedDates[0]);
                            nextDay.setDate(nextDay.getDate() + 1);
                            checkout.set("minDate", nextDay);
                        }
                        calculateTotalPrice();
                    }
                });

                // Initialize checkout datepicker
                flatpickrInstance.checkout = flatpickr("#checkout", {
                    dateFormat: "Y-m-d",
                    minDate: new Date().fp_incr(1),
                    onChange: function(selectedDates, dateStr, instance) {
                        calculateTotalPrice();
                    }
                });

                function calculateTotalPrice() {
                    const getInputValue = (id) => parseFloat($(`#${id}`).val()) || 0;

                    // Get total days from flatpickr instances
                    const checkinDate = flatpickrInstance.checkin?.selectedDates[0];
                    const checkoutDate = flatpickrInstance.checkout?.selectedDates[0];
                    
                    let days = 0;
                    if (checkinDate && checkoutDate) {
                        const diffTime = checkoutDate - checkinDate;
                        days = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    }

                    // If no days, don't calculate
                    if (days === 0) {
                        $('#bookingPrice').val("0.00");
                        $('#subtotal').val("0.00");
                        $('#net_profit').val("0.00");
                        $('#agent_comission').val("0.00");
                        return;
                    }

                    // Get input values
                    const roomPricePerNight = getInputValue("room_price"); // Markup price per night
                    const actualRoomPricePerNight = getInputValue("actual_room_price"); // Actual price per night
                    const roomQuantity = getInputValue("room_quantity") || 1;
                    const agentCommissionPercent = getInputValue("agent_comission");
                    const taxPercent = getInputValue("tax");
                    const supplierCost = getInputValue("supplier_cost"); // Total supplier cost
                    const iata = getInputValue("iata");

                    if (roomPricePerNight === 0) {
                        $('#bookingPrice').val("0.00");
                        $('#subtotal').val("0.00");
                        $('#net_profit').val("0.00");
                        $('#agent_comission').val("0.00");
                        return;
                    }

                    // Calculate total prices (per night × days × quantity)
                    const totalMarkupPrice = roomPricePerNight * days * roomQuantity;
                    const totalActualPrice = actualRoomPricePerNight * days * roomQuantity;

                    // Calculate Credit Card Fee (based on total markup price)
                    const ccFee = (totalMarkupPrice * 0.029) + 0.3;

                    // Subtotal calculation: PER-NIGHT price without tax
                    const subtotalPerNight = roomPricePerNight / (1 + taxPercent / 100);
                    
                    // Total subtotal (subtotal per night × days × quantity)
                    const totalSubtotal = subtotalPerNight * days * roomQuantity;

                    // Agent commission calculation (based on total subtotal)
                    const agentCommissionAmount = agentCommissionPercent;

                    // Net Profit Calculation:
                    // Total revenue - supplier cost - agent commission + IATA benefit - CC fee
                    const netProfit = totalMarkupPrice - supplierCost - agentCommissionAmount + iata - ccFee;

                    // Set calculated values in respective input fields
                    $('#bookingPrice').val(totalMarkupPrice.toFixed(2));
                    $('#subtotal').val(totalSubtotal.toFixed(2));
                    $('#net_profit').val(netProfit.toFixed(2));
                    $('#supplier_cost').val(totalActualPrice.toFixed(2));
                    // Note: agent_comission field shows percentage, not amount
                }

                // Event listeners for input fields that should trigger recalculation
                $('#room_price, #actual_room_price, #room_quantity, #agent_comission, #tax, #supplier_cost, #iata').on('input', calculateTotalPrice);

                // Handle agent selection change to auto-fill commission percentage
                $('#agent_id').on('change', function () {
                    const agentId = $(this).val();
                    if (agentId) {
                        $.ajax({
                            url: 'booking-ajax.php',
                            type: 'POST',
                            data: {
                                action: 'get_agent_markup',
                                agent_id: agentId
                            },
                            success: function (response) {
                                if (response.status === 'success') {
                                    $('#agent_comission').val(response.markup);
                                    calculateTotalPrice(); // Recalculate with new commission
                                } else {
                                    console.error('Error fetching agent commission:', response.message);
                                }
                            },
                            error: function (xhr, status, error) {
                                console.error('Ajax error:', error);
                            }
                        });
                    } else {
                        $('#agent_comission').val('0');
                        calculateTotalPrice();
                    }
                });

                // Handle agent payment status change
                $('#agent_payment_status').on('change', function () {
                    if ($(this).val() === 'cancelled') {
                        $('#agent_comission').val(0);
                        $('#agent_comission').prop('readonly', true);
                        calculateTotalPrice();
                    } else {
                        $('#agent_comission').prop('readonly', false);
                    }
                });

                // Initial calculation on page load
                calculateTotalPrice();
            });


        </script>
        <script>
            $(document).ready(function() {
                $("#search").submit(function(event) {
                    event.preventDefault();

                    // Get the button that triggered the submit
                    const submitBtn = $(this).find("button[type='submit']");
                    const bookingRef = submitBtn.data("ref");
                    const paymentStatus = submitBtn.attr("data_status"); // notice underscore attribute

                    console.log("Booking Ref:", bookingRef);
                    console.log("Payment Status:", paymentStatus);

                    // If paid → directly submit
                    if (paymentStatus && paymentStatus.toLowerCase() === "paid") {
                        console.log("Payment already done — submitting directly.");
                        submitForm();
                        return;
                    }

                    // If not paid → show modal
                    const linkToShow = `https://toptier-tr-ef19.vercel.app/bookings/payment/${bookingRef}`;
                    console.log("Form intercepted — showing modal");
                    showModal(linkToShow);
                });

                function showModal(link) {
                    $("#customModal").remove();

                    const modalHtml = `
                                    <div class="modal fade" id="customModal" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content shadow-lg border-0 rounded-3">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="customModalLabel">Share Link</h5>
                                            <button type="button" class="btn-close" id="modalClose"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <p>Here is your payment link:</p>
                                            <div class="input-group mb-3">
                                            <input type="text" class="form-control" id="copyLink" value="${link}" readonly style="border-top-right-radius: 0px !important;border-bottom-right-radius: 0px !important;">
                                            <button class="btn btn-outline-secondary" type="button" id="copyBtn" title="Copy">
                                                <!-- Copy Icon -->
                                                <svg xmlns="http://www.w3.org/2000/svg" id="copyIcon" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M10 1.5A1.5 1.5 0 0 1 11.5 3v1h1A1.5 1.5 0 0 1 14 5.5v8A1.5 1.5 0 0 1 12.5 15h-8A1.5 1.5 0 0 1 3 13.5v-1h1v1A.5.5 0 0 0 4.5 14h8a.5.5 0 0 0 .5-.5v-8a.5.5 0 0 0-.5-.5h-1v-1A.5.5 0 0 0 11.5 3h-8a.5.5 0 0 0-.5.5v1H2v-1A1.5 1.5 0 0 1 3.5 2h6.5z"/>
                                                <path d="M4.5 5A1.5 1.5 0 0 1 6 6.5v8A1.5 1.5 0 0 1 4.5 16h-3A1.5 1.5 0 0 1 0 14.5v-8A1.5 1.5 0 0 1 1.5 5h3z"/>
                                                </svg>
                                            </button>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" id="modalCancel">Cancel</button>
                                            <button type="button" class="btn btn-primary" id="modalOk">OK</button>
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                `;

                    $("body").append(modalHtml);
                    const modal = $("#customModal");
                    modal.addClass("show").css("display", "flex");

                    // Copy button logic
                    $(document).off("click", "#copyBtn").on("click", "#copyBtn", function() {
                        const linkInput = document.getElementById("copyLink");
                        linkInput.select();
                        document.execCommand("copy");

                        const btn = $(this);
                        btn.removeClass("btn-outline-secondary").addClass("btn-success").html(`
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="white" viewBox="0 0 16 16">
                                        <path d="M13.485 1.929a.75.75 0 0 1 1.06 1.06l-8 8a.75.75 0 0 1-1.06 0l-4-4a.75.75 0 0 1 1.06-1.06L6 9.439l7.485-7.51z"/>
                                    </svg>
                                    `);

                        setTimeout(() => {
                            btn.removeClass("btn-success").addClass("btn-outline-secondary").html(`
                                        <svg xmlns="http://www.w3.org/2000/svg" id="copyIcon" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M10 1.5A1.5 1.5 0 0 1 11.5 3v1h1A1.5 1.5 0 0 1 14 5.5v8A1.5 1.5 0 0 1 12.5 15h-8A1.5 1.5 0 0 1 3 13.5v-1h1v1A.5.5 0 0 0 4.5 14h8a.5.5 0 0 0 .5-.5v-8a.5.5 0 0 0-.5-.5h-1v-1A.5.5 0 0 0 11.5 3h-8a.5.5 0 0 0-.5.5v1H2v-1A1.5 1.5 0 0 1 3.5 2h6.5z"/>
                                        <path d="M4.5 5A1.5 1.5 0 0 1 6 6.5v8A1.5 1.5 0 0 1 4.5 16h-3A1.5 1.5 0 0 1 0 14.5v-8A1.5 1.5 0 0 1 1.5 5h3z"/>
                                        </svg>
                                    `);
                        }, 2000);
                    });

                    // OK button logic
                    $(document).off("click", "#modalOk").on("click", "#modalOk", function() {
                        hideCustomModal();
                        submitForm();
                    });

                    // Cancel or close button
                    $(document).off("click", "#modalCancel, #modalClose").on("click", "#modalCancel, #modalClose", hideCustomModal);
                }

                function hideCustomModal() {
                    const modal = $("#customModal");
                    modal.removeClass("show").fadeOut(200, function() {
                        $(this).remove();
                    });
                }

                function submitForm() {
                    var booking_id = $("#booking_id").val();
                    var module = $("#module").val();
                    var booking_date = $("#booking_date").val();
                    var booking_status = $(".booking_status").val();
                    var payment_status = $(".payment_status").val();
                    var checkin = $("#checkin").val();
                    var checkout = $("#checkout").val();
                    var hotel_id = $("#hotel_select").val();
                    var first_name = $("#first_name").val();
                    var last_name = $("#last_name").val();
                    var email = $("#email").val();
                    var phone = $("#phone").val();
                    var bookingnote = $("#bookingnote").val();
                    var agent_id = $("#agent_id").val() ?? '';
                    var room_price = $("#room_price").val();
                    var actual_room_price = $("#actual_room_price").val(); // ADD THIS
                    var room_quantity = $("#room_quantity").val(); // ADD THIS
                    var net_profit = $("#net_profit").val();
                    var tax = $("#tax").val();
                    var agent_comission = $("#agent_comission").val();
                    var bookingPrice = $("#bookingPrice").val();
                    var room_select = $("#room_select").val();
                    var cancellation_terms = $("#cancellation_terms").val();
                    var supplier_id = $("#supplier_id").val();
                    var supplier_payment_status = $("#supplier_payment_status").val();
                    var supplier_cost = $("#supplier_cost").val();
                    var supplier_due_date = $("#supplier_due_date").val();
                    var supplier_payment_type = $("#supplier_payment_type").val();
                    var customer_payment_type = $("#customer_payment_type").val();
                    var iata = $("#iata").val();
                    var subtotal = $("#subtotal").val();
                    var agent_payment_type = $("#agent_payment_type").val();
                    var agent_payment_status = $("#agent_payment_status").val();

                    // Build URL with all parameters
                    var url = "<?=$root?>booking_update.php?" +
                        "booking_id=" + booking_id +
                        "&module=" + module +
                        "&booking_date=" + booking_date +
                        "&booking_status=" + booking_status +
                        "&payment_status=" + payment_status +
                        "&checkin=" + checkin +
                        "&checkout=" + checkout +
                        "&hotel_id=" + hotel_id +
                        "&first_name=" + first_name +
                        "&last_name=" + last_name +
                        "&email=" + email +
                        "&phone=" + phone +
                        "&room_price=" + room_price +
                        "&actual_room_price=" + actual_room_price + // ADD THIS
                        "&room_quantity=" + room_quantity + // ADD THIS
                        "&net_profit=" + net_profit +
                        "&tax=" + tax +
                        "&agent_comission=" + agent_comission +
                        "&bookingPrice=" + bookingPrice +
                        "&bookingnote=" + bookingnote +
                        "&agent_id=" + agent_id +
                        "&room_select=" + room_select +
                        "&supplier_payment_status=" + supplier_payment_status +
                        "&supplier_due_date=" + supplier_due_date +
                        "&cancellation_terms=" + cancellation_terms +
                        "&supplier_cost=" + supplier_cost +
                        "&supplier_id=" + supplier_id +
                        "&supplier_payment_type=" + supplier_payment_type +
                        "&customer_payment_type=" + customer_payment_type +
                        "&iata=" + iata +
                        "&subtotal=" + subtotal +
                        "&agent_payment_type=" + agent_payment_type +
                        "&agent_payment_status=" + agent_payment_status;

                    window.location.href = url;
                }
            });
        </script>
    <?php include "_footer.php" ?>