<style>
   .select2-selection__rendered {
      margin-top: 4px;
   }
</style>
<?php
   require_once '_config.php';
   auth_check();

   $title = T::add .' '. T::booking;
   include "_header.php";

   if ($_SERVER['REQUEST_METHOD'] === 'POST') {

      if (!isset($_POST['hotel'])) {
         die("Error: Hotel ID is required.");
      }

      $selected_country = $db->get("countries", "*", ["id" => $_POST['country_id']]);
      $phone_code = $selected_country['phonecode'] ?? "";
      $iso_code = $selected_country['iso'] ?? "";
      $nationality = $selected_country['iso'] ?? "";

      // Remove 0 from starting of phone 
      $phone_number = ltrim($_POST['phone'], '0');
      $clientPhone = $phone_code . $phone_number;

      $hotel_img = $db->select("hotels_images", "*", ["hotel_id" => $_POST['hotel']]);

      // Calculate days between check-in and check-out
      $checkin_date = new DateTime($_POST['checkin']);
      $checkout_date = new DateTime($_POST['checkout']);
      $interval = $checkin_date->diff($checkout_date);
      $days = $interval->days;

      // Get room quantity and per-night prices
      $room_quantity = isset($_POST['room_quantity']) ? floatval($_POST['room_quantity']) : 1;
      $room_price_per_night = isset($_POST['room_price']) ? floatval($_POST['room_price']) : 0.0;
      $actual_room_price_per_night = isset($_POST['actual_room_price']) ? floatval($_POST['actual_room_price']) : 0.0;

      // Get pre-calculated totals from JavaScript (from hidden inputs)
      $total_markup_price = $room_price_per_night * $days * $room_quantity;
      $total_actual_price = $actual_room_price_per_night * $days * $room_quantity;
      $subtotal = isset($_POST['subtotal']) ? floatval($_POST['subtotal']) : 0.0;
      $net_profit = isset($_POST['net_profit']) ? floatval($_POST['net_profit']) : 0.0;
      $agent_commission_amount = isset($_POST['agent_commission_amount']) ? floatval($_POST['agent_commission_amount']) : 0.0;

      // Calculate credit card fee
      $cc_fee = ($total_markup_price * 0.029) + 0.3;

      // Get other values
      $tax_percent = isset($_POST['tax']) ? floatval($_POST['tax']) : 0;
      $supplier_cost = isset($_POST['supplier_cost']) ? floatval($_POST['supplier_cost']) : 0.0;
      $iata = isset($_POST['iata']) ? floatval($_POST['iata']) : 0.0;

      $params = [
         "booking_ref_no" => date('Ymdhis') . rand(),
         "location" => $_POST['location'] ?? "",
         "hotel_id" => $_POST['hotel'],
         "hotel_img" => $hotel_img[0]['img'] ?? "no-image.jpg",
         "price_markup" => $total_markup_price,
         "first_name" => $_POST['adults_data'][0]['firstname'] ?? "",
         "last_name" => $_POST['adults_data'][0]['lastname'] ?? "",
         "email" => $_POST['email'] ?? "",
         "phone_country_code" => $phone_code,
         "phone" => $phone_number, 
         "nationality" => $nationality,
         "country" => $iso_code,
         "supplier" => "hotels",
         "checkin" => $_POST['checkin'] ?? "",
         "checkout" => $_POST['checkout'] ?? "",
         "booking_nights" => $days,
         "agent_id" => $_POST['agent'] ?? null,
         "booking_date" => date('Y-m-d'),
         "agent_fee" => $agent_commission_amount,
         "tax" => $tax_percent,
         "net_profit" => $net_profit,
         "price_original" => $total_actual_price,
         "booking_note" => $_POST['bookingnote'] ?? "",
         "cancellation_terms" => $_POST['cancellation_terms'] ?? "",
         "supplier_cost" => $supplier_cost,
         "supplier_payment_status" => $_POST['supplier_payment_status'] ?? "unpaid",
         "supplier_due_date" => $_POST['supplier_due_date'] ?? "",
         "supplier_id" => $_POST["supplier_id"] ?? "",
         "supplier_payment_type" => $_POST["supplier_payment_type"] ?? "",
         "customer_payment_type" => $_POST["customer_payment_type"] ?? "",
         "iata" => $iata,
         "subtotal" => $subtotal,
         "agent_payment_status" => $_POST['agent_payment_status'] ?? 'pending',
         "agent_payment_type" => $_POST['agent_payment_type'] ?? 'pending',
         "address" => $_POST['address'] ?? "",
      ];

      if ($params['agent_payment_status'] == 'paid') {
         $params['agent_payment_date'] = date('Y-m-d');
      }

      $user_data = [
         "first_name" => $_POST['adults_data'][0]['firstname'] ?? "Unknown",
         "last_name" => $_POST['adults_data'][0]['lastname'] ?? "Unknown",
         "email" => $_POST['email'] ?? "",
         "phone" => $phone_number,
         "address" => $_POST['address'] ?? "",
         "nationality" => $nationality,
         "country_code" => $phone_code,
         "user_id" => $_POST['user_id'] ?? null
      ];

      $params['user_data'] = json_encode($user_data);

      $travelers_data = [];
      if (!empty($_POST['adults_data'])) {
         foreach ($_POST['adults_data'] as $adult) {
               $travelers_data[] = [
                  "traveller_type" => "adults",
                  "title" => $adult['title'] ?? "",
                  "first_name" => $adult['firstname'] ?? "",
                  "last_name" => $adult['lastname'] ?? "",
                  "age" => ""
               ];
         }
      }

      $params['guest'] = json_encode($travelers_data);

      $hotel_id = $_POST['hotel'];
      $hotel_data = $db->select("hotels", ["name"], ["id" => $hotel_id]);
      $params['hotel_name'] = !empty($hotel_data) ? $hotel_data[0]['name'] : "";

      $currency = $db->select("currencies", ["name"], ["default" => 1]);
      $params['currency_markup'] = !empty($currency) ? $currency[0]['name'] : "USD";

      if (isset($_POST['room'])) {
         $room_id = $_POST['room'];

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
                  "room_price_per_night" => $room_price_per_night,
                  "room_quantity" => $room_quantity,
                  "room_extrabed_price" => $room_details[0]['extra_bed_charges'] ?? 0.0,
                  "room_extrabed" => $room_details[0]['extra_bed'] ?? 0,
                  "room_actual_price_per_night" => $actual_room_price_per_night,
                  "total_nights" => $days,
                  "total_markup_price" => $total_markup_price,
                  "total_actual_price" => $total_actual_price,
                  "cc_fee" => $cc_fee
               ];

               $params['room_data'] = json_encode([$room_data]);

               $booking_data_entry = [
                  "id" => $_POST['room'] ?? "0",
                  "currency" => $params['currency_markup'] ?? 'USD',
                  "price" => number_format($total_actual_price, 2, '.', ''), 
                  "per_day" => number_format($actual_room_price_per_night, 2, '.', ''), 
                  "markup_price" => number_format($total_markup_price, 2, '.', ''), 
                  "markup_price_per_night" => number_format($room_price_per_night, 2, '.', ''),
                  "service_fee" => number_format($cc_fee, 2, '.', ''), 
                  "quantity" => $room_quantity,
                  "adults" => 2,
                  "child" => 0,
                  "children_ages" => 0,
                  "bookingurl" => "", 
                  "booking_data" => "", 
                  "extrabeds_quantity" => 0,
                  "extrabed_price" => 0,
                  "cancellation" => "1",
                  "breakfast" => "1", 
                  "room_booked" => false 
               ];

               $params['booking_data'] = json_encode([$booking_data_entry]);
         }
      }

      // For sending messages 
      $sendEmail_agent = isset($_POST['sendEmail_agent']) ? true : false;
      $sendSMS_agent = isset($_POST['sendSMS_agent']) ? true : false;
      $sendWhatsapp_agent = isset($_POST['sendWhatsapp_agent']) ? true : false;

      $sendEmail_client = isset($_POST['sendEmail_client']) ? true : false;
      $sendSMS_client = isset($_POST['sendSMS_client']) ? true : false;
      $sendWhatsapp_client = isset($_POST['sendWhatsapp_client']) ? true : false;

      $agentDetails = $db->get("users", ["phone", "phone_country_code", "first_name", "last_name","email"], ["user_id" => $_POST['agent']]);
      $supplierDetails = $db->get("users", ["phone", "phone_country_code", "first_name", "last_name","email"], ["user_id" => $_POST['supplier_id']]);

      $clientNum = "+" . $clientPhone;
      $agentNum = isset($agentDetails["phone_country_code"]) && isset($agentDetails["phone"]) ? "+" . $agentDetails["phone_country_code"] . $agentDetails["phone"] : "";
      $supplierNum = isset($supplierDetails["phone_country_code"]) && isset($supplierDetails["phone"]) ? "+" . $supplierDetails["phone_country_code"] . $supplierDetails["phone"] : "";

      /////////// For agent ///////////
      // Send email to agent 
      if ($sendEmail_agent && !empty($agentDetails)) {
         $contentArray = [
               'client_name' => $_POST['adults_data'][0]['firstname'] . ' ' . $_POST['adults_data'][0]['lastname'], 
               'total_price' => $total_markup_price, 
               'hotel_name' => $hotel_data[0]['name'],
               'agentComission' => $agent_commission_amount,  
         ];        

         // SEND EMAIL
         $title = "NEW SALE ALERT";
         $template = "agent_new_sale";
         $content = $contentArray;
         $receiver_email = $agentDetails['email'];
         $receiver_name = $agentDetails['first_name'] . ' ' . $agentDetails['last_name'];
         MAILER($template, $title, $content, $receiver_email, $receiver_name);
      }

      // Send SMS to agent 
      if ($sendSMS_agent && !empty($agentDetails) && !empty($agentNum)) {
         require_once 'send_sms.php';
         
         $formattedCheckin = (new DateTime($_POST['checkin']))->format('m-d-Y');
         $formattedCheckout = (new DateTime($_POST['checkout']))->format('m-d-Y');
         
         $message = "
               NEW SALE ALERT

               Great news, " . $agentDetails['first_name'] . ' ' . $agentDetails['last_name'] . "! You've just made a new hotel sale for " . $_POST['adults_data'][0]['firstname'] . ' ' . $_POST['adults_data'][0]['lastname'] . "'s trip to " . $_POST['location'] . ".  

               Hotel: " . $hotel_data[0]['name'] . " 

               Check in: " . $formattedCheckin . "
               Check out: " . $formattedCheckout . "
               Nights: " . $days . "

               Sale amount: $" . number_format($subtotal, 2) . "

               Commission: $" . number_format($agent_commission_amount, 2) . "

               Log into your account to see your sales, commissions and more details about your business! https://toptiertravel.site/admin/login.php  
         ";
         sendSMS($agentNum, $message);
      }

      $db->insert("hotels_bookings", $params);
      $id = $db->id();

      if ($id) {
         $_SESSION['booking_inserted'] = true;
         REDIRECT('./bookings.php');
      } else {
         die("Error inserting booking. Booking ID not generated.");
      }
   }
?>

<!-- <div class="page_head bg-transparent">
   <div class="panel-heading px-5">
      <div class="float-start">
         <p class="m-0 page_title">
            <?=T::add.' '.T::booking?>
         </p>
      </div>
      <div class="float-end">
         <a href="javascript:window.history.back();" data-toggle="tooltip" data-placement="top" title="Previous Page"
            class="loading_effect btn btn-warning">
            <?=T::back?>
         </a>
      </div>
   </div>
</div> -->
<div class="container">
</div>
<div class="mt-1">
   <div class="p-3">
      <div class="container px-5">

         <form method="post" action="<?=root?>booking_add.php" onsubmit="loading()">
            <!-- Select Hotel -->
            <div class="card mb-2">
               <div class="card-header bg-primary text-dark py-3">
                  <strong class="">
                     <?=T::hotel?>
                  </strong>
               </div>

               <div class="card-body p-4">
                  <div class="row g-3">
                     <div class="col-md-4">
                        <label for="">Location</label>
                        <hr>
                        <?php
                     $locations = $db->select("hotels", "location", ["status" => 1, "GROUP" => "location"]);
                     ?>
                        <div class="">
                           <select class="select2 form-select" id="locationSelect" name="location" required>
                              <option value="" disabled selected>
                                 <?=T::select_location?>
                              </option>
                              <?php foreach($locations as $location) { ?>
                              <option value="<?= $location ?>">
                                 <?= $location ?>
                              </option>
                              <?php } ?>
                           </select>
                           <!-- <label for="locationSelect">Select Location</label> -->
                        </div>
                     </div>
                     <div class="col-md-4">
                        <label for="">Hotel</label>
                        <hr>
                        <div class=" ">
                           <select class="select2" id="hotelSelect" name="hotel" required>
                              <option value="" disabled selected>
                                 <?=T::select_hotel?>
                              </option>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <label for="">Room</label>
                        <hr>
                        <div class=" ">
                           <select class="select2" id="roomSelect" name="room" required>
                              <option value="" disabled selected>
                                 <?=T::select_room?>
                              </option>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <label for="">
                           <?=T::customer?>
                           <?=T::payment?>
                           <?=T::type?>
                        </label>
                        <div class=" ">
                           <div class="form-floating mt-2">
                              <div class="input-group">
                                 <!-- <div class="form-floating"> -->
                                 <input type="text" class="form-control" id="customer_payment_type" name="customer_payment_type" value="stripe" required readonly>

                              </div>
                              <!-- <label for="">Room Price</label> -->
                           </div>
                           <!-- <select class="select2" id="customer_payment_type" name="customer_payment_type" required>
                              <option disabled selected value="">
                                 <?=T::select?>
                                 <?=T::payment?>
                                 <?=T::type?>
                              </option>
                              <option value="stripe" selected>
                                 <?=T::stripe?>
                              </option>
                              <option value="wire">
                                 <?=T::wire?>
                              </option>
                              <option value="zelle">
                                 <?=T::zelle?>
                              </option>
                              <option value="venmo">
                                 <?=T::venmo?>
                              </option>
                              <option value="paypal">
                                 <?=T::paypal?>
                              </option>
                              <option value="cash">
                                 <?=T::cash?>
                              </option>
                           </select> -->
                        </div>
                     </div>
                     <div class="col-md-3">

                        <label for="">Room Quantity</label>

                        <!-- <small for="">Room Price</small> -->
                        <div class="form-floating mt-2">
                           <div class="input-group">
                              <!-- <div class="form-floating"> -->
                              <input type="number" class="form-control" id="" name="room_quantity" value="1" step="any"
                                 min="0" required>

                           </div>
                           <!-- <label for="">Room Price</label> -->
                        </div>
                     </div>
                     <div class="col-md-3">

                        <label for="">Actual Room Price</label>

                        <?php $curreny = $db->select("currencies", "*", ["default" => 1,]);?>
                        <!-- <small for="">Room Price</small> -->
                        <div class="form-floating mt-2">
                           <div class="input-group">
                              <!-- <div class="form-floating"> -->
                              <input type="number" class="form-control" id="" name="actual_room_price" value="0" step="any"
                                 min="0" required
                                 style="border-top-right-radius:0 !important;border-bottom-right-radius:0 !important;">

                              <!-- </div> -->
                              <span class="input-group-text text-white bg-primary">
                                 <?= $curreny[0]['name']?>
                              </span>
                           </div>
                           <!-- <label for="">Room Price</label> -->
                        </div>
                     </div>
                     <div class="col-md-3">

                        <label for="">Markup Room Price</label>

                        <?php $curreny = $db->select("currencies", "*", ["default" => 1,]);?>
                        <!-- <small for="">Room Price</small> -->
                        <div class="form-floating mt-2">
                           <div class="input-group">
                              <!-- <div class="form-floating"> -->
                              <input type="number" class="form-control" id="" name="room_price" value="0" step="any"
                                 min="0" required
                                 style="border-top-right-radius:0 !important;border-bottom-right-radius:0 !important;">

                              <!-- </div> -->
                              <span class="input-group-text text-white bg-primary">
                                 <?= $curreny[0]['name']?>
                              </span>
                           </div>
                           <!-- <label for="">Room Price</label> -->
                        </div>
                     </div>
                  </div>
               </div>
               <hr class="m-0">
            </div>

            <div class="card mb-2">
               <div class="card-header bg-primary text-dark py-3">
                  <strong class="">
                     <?=T::travelers?>
                  </strong>
               </div>
               <div class="card-body p-3">
                  <p class="mb-2"><strong>
                        <?=T::adults?>
                     </strong></p>
                  <div class="adults-container text-center">
                     <div class="row adults_clone mt-3">
                        <div class="col-md-2">
                           <div class="form-floating">
                              <select name="adults_data[0][title]" class="form-select">
                                 <option value="Mr">
                                    <?=T::mr?>
                                 </option>
                                 <option value="Miss">
                                    <?=T::miss?>
                                 </option>
                                 <option value="Mrs">
                                    <?=T::mrs?>
                                 </option>
                              </select>
                              <label for="">
                                 <?=T::title?>
                              </label>
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="form-floating">
                              <input type="text" name="adults_data[0][firstname]" class="form-control"
                                 placeholder="First Name" value="" required />
                              <label for="">
                                 <?=T::first_name?>
                              </label>
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="form-floating">
                              <input type="text" name="adults_data[0][lastname]" class="form-control"
                                 placeholder="Last Name" value="" required />
                              <label for="">
                                 <?=T::last_name?>
                              </label>
                           </div>
                        </div>
                        <div class="col-md-2">
                           <button type="button"
                              class="btn btn-primary align-items-center float-end w-100 h-100 add_adults">
                              <?=T::add_more?>
                           </button>
                           <button type="button"
                              class="btn btn-danger mt-2 align-items-center float-end remove-adult-btn remove_adults"
                              style="display:none;">
                              <?=T::remove?>
                           </button>
                        </div>
                     </div>
                  </div>
                  <div>
                     <hr>
                  </div>
                  <div class="row g-3">
                     <div class="col-md-6">
                        <div class="form-floating">
                           <input type="email" class="form-control" id="clientEmail" name="email"
                              placeholder="Enter email address">
                           <label for="clientEmail">
                              <?=T::email?>(
                              <?=T::optional?>)
                           </label>
                        </div>
                     </div>

                     <div class="col-3">
                        <div class="form-floating">
                           <select name="country_id" class="form-select w-100 rounded-3" data-live-search="true"
                              id="country_id" required>
                              <option value="">Select Country</option>
                              <?php $countries = $db->select("countries", "*");

                              foreach ($countries as $c) { ?>
                              <option value="<?= $c['id'] ?>" data-country="<?= $c['iso'] ?>"
                                 data-country-phonecode="<?= $c['phonecode'] ?>" <?php
                                 if($c['nicename']=='United States' ) echo 'selected' ; ?>>
                                 <!-- United States by name -->
                                 <?= $c['nicename'] ?> <strong>+
                                    <?= $c['phonecode'] ?>
                                 </strong>
                              </option>
                              <?php } ?>
                           </select>
                           <label for="">Select Country</label>
                        </div>
                     </div>

                     <div class="col-3">
                        <div class="form-floating">
                           <input type="number" class="form-control rounded-3 whatsapp" id="clientPhone" name="phone"
                              placeholder="Enter Phone Number">
                           <label for="clientPhone">
                              <?=T::phone?>(
                              <?=T::optional?>)
                           </label>
                        </div>
                     </div>


                     <!-- <div class="col-md-6">
                        <div class="form-floating">
                           <input type="number" class="form-control" id="clientPhone" name="phone"
                              placeholder="Enter Phone Number">
                           <label for="clientPhone"><?=T::phone?>(<?=T::optional?>)</label>
                        </div>
                     </div> -->
                  </div>
               </div>
               <hr class="m-0">
               <div class="p-3">
                  <div class="row g-3">
                     <div class="col-md-6">
                        <div class="form-floating">
                        <input type="text" class="form-control" id="checkin" name="checkin" autocomplete="off" required
                           value="<?php $d=strtotime('+3 Days'); echo date('Y-m-d', $d); ?>">
                        <label for="checkin"><?=T::checkin?> <?=T::date?></label>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-floating">
                        <input type="text" class="form-control" id="checkout" name="checkout" autocomplete="off" required
                           value="<?php $d=strtotime('+4 Days'); echo date('Y-m-d', $d); ?>">
                        <label for="checkout"><?=T::checkout?> <?=T::date?></label>
                        </div>
                     </div>
                  </div>
               </div>
            </div>

            <div class="card mb-2">
               <div class="card-header bg-primary text-dark py-3">
                  <strong>
                     <?=T::supplier?>
                     <?=T::payment?>
                     <?=T::details?>
                  </strong>
               </div>
               <div class="card-body p-4">
                  <div class="row g-3">
                     <div class="col-md-4">
                        <label for="">
                           <?=T::supplier?>
                        </label>
                        <div class="form-floating mt-3 rounded-2 h-100">
                           <select class="form-select select2 pt-2" id="supplier_id" name="supplier_id" required>
                              <option value="" disabled selected>
                                 <?=T::select_supplier?>
                              </option>
                              <?php 
                                    $agents = $db->select("users", "*", ["user_type" => "supplier"]);
                                    foreach ($agents as $agent) {
                                    ?>

                              <option value="<?= $agent['user_id']?>">
                                 <?= $agent['first_name'] . ' ' . $agent['last_name'] ?>
                              </option>
                              <?php } ?>
                           </select>
                        </div>
                     </div>

                     <!-- Supplier Payment Status (With select2) -->
                     <div class="col-md-4">
                        <label for="">
                           <?=T::payment?>
                           <?=T::status?>
                        </label>
                        <div class="form-floating mt-3 rounded-2 h-100">
                           <select class="form-select select2 pt-2" id="payment_status" name="supplier_payment_status">
                              <option value="" disabled selected>
                                 <?=T::select?>
                                 <?=T::payment?>
                                 <?=T::status?>
                              </option>
                              <option value="paid">
                                 <?=T::paid?>
                              </option>
                              <option value="unpaid">
                                 <?=T::unpaid?>
                              </option>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <label for="">
                           <?=T::payment?>
                           <?=T::type?>
                        </label>
                        <div class="form-floating mt-3 rounded-2 h-100">
                           <select class="form-select select2 pt-2" id="supplier_payment_type"
                              name="supplier_payment_type">
                              <option value="" disabled selected>
                                 <?=T::select?>
                                 <?=T::payment?>
                                 <?=T::type?>
                              </option>
                              <option value="credit card">
                                 <?=T::credit?>
                                 <?=T::card?>
                              </option>
                              <option value="wire">
                                 <?=T::wire?>
                              </option>
                           </select>
                        </div>
                     </div>


                     <div class="col-md-4">
                        <label for="">
                           <?=T::supplier?>
                           <?=T::due?>
                           <?=T::date?>
                        </label>
                        <div class="">
                           <input type="date" class="form-control mt-2" id="supplier_due_date" name="supplier_due_date"
                              autocomplete="off" required>
                           <!-- <label for="supplier_due_date">Due Date</label> -->
                        </div>
                     </div>
                     <!-- for IATA -->

                     <div class="col-md-4">
                        <label for="">
                           <?=T::iata?>
                        </label>
                        <div class="form-floating">
                           <div class="input-group mt-2">
                              <input type="number" step="any" min="0" class="form-control rounded-0" id="iata"
                                 name="iata" value="0" required>
                              <span class="input-group-text text-white bg-primary">
                                 <?= $curreny[0]['name']?>
                              </span>
                           </div>
                           <!-- <label for="supplierCost">Supplier Cost</label> -->
                        </div>
                     </div>

                     <!-- Supplier Cost Input -->
                     <div class="col-md-4">
                        <label for="">
                           <?=T::supplier?>
                           <?=T::cost?>
                        </label>
                        <div class="form-floating">
                           <div class="input-group mt-2">
                              <input type="number" step="any" min="0" class="form-control rounded-0" id="supplierCost"
                                 name="supplier_cost" value="0" required>
                              <span class="input-group-text text-white bg-primary">
                                 <?= $curreny[0]['name']?>
                              </span>
                           </div>
                           <!-- <label for="supplierCost">Supplier Cost</label> -->
                        </div>
                     </div>
                  </div>
               </div>
               <hr class="m-0">
            </div>

            <div class="card mb-2">
               <div class="card-header bg-primary text-dark py-3">
                  <strong class="">
                     <?=T::agent?>
                  </strong>
               </div>
               <div class="card-body p-4">
                  <div class="row g-3">
                     <div class="col-md-3">
                        <label for="">
                           <?=T::agent?>
                        </label>
                        <div class="form-floating mt-3 rounded-2 h-100">
                           <select class="select2 pt-2" id="agentSelect" name="agent">
                              <option value="" selected>
                                 <?=T::select?>
                                 <?=T::agent?>
                              </option>
                              <?php
                           // Fetch agents from users table where user_type is 'agent'
                           $agents = $db->select("users", "*", ["user_type" => "agent"]);
                           foreach ($agents as $agent) {
                           ?>
                              <option value="<?= $agent['user_id']?>">
                                 <?= $agent['first_name'] . ' ' . $agent['last_name'] ?>
                              </option>
                              <?php } ?>
                           </select>
                           <!-- <label for="agentSelect">Select an Agent</label> -->
                        </div>
                     </div>

                     <div class="col-md-3">
                        <label for="agentPaymentType">
                        <?= T::payment?> <?= T::type?>
                        </label>
                        <div class="form-floating mt-3 rounded-2 h-100">
                           <select class="select2 pt-2" id="agentPaymentType" name="agent_payment_type">
                                 <option value="" selected>
                                    <?= T::select ?> <?= T::payment?> <?= T::type?>
                                 </option>
                                 <option value="pending"><?= T::pending ?></option>
                                 <option value="wire"><?= T::wire ?></option>
                                 <option value="zelle"><?= T::zelle ?></option>
                                 <option value="paypal"><?= T::paypal ?></option>
                                 <option value="venmo"><?= T::venmo ?></option>
                           </select>
                        </div>
                     </div>

                     <div class="col-md-2">
                        <label for="agentPaymentStatus">
                           <?= T::payment?> <?= T::status?>
                        </label>
                        <div class="form-floating mt-3 rounded-2 h-100">
                           <select class="select2 pt-2" id="agentPaymentStatus" name="agent_payment_status">
                                 <option selected value="pending"><?= T::pending ?></option>
                                 <option value="paid"><?= T::paid ?></option>
                                 <option value="cancelled"><?= T::cancelled ?></option>
                           </select>
                        </div>
                     </div>

                     <div class="col-md-2">
                        <label for="">
                           <?=T::amount?>
                        </label>
                        <div class="form-floating mt-2">
                           <div class="input-group">
                              <input type="number" step="any" min="0" class="form-control rounded-0" id="agentCommissionAmount"
                                 name="agent_commission_amount" value="0">
                              <span class="input-group-text text-white bg-primary"><?= $curreny[0]['name']?></span>
                           </div>
                        </div>
                     </div>

                     <!-- Agent Commission -->
                     <div class="col-md-2">
                        <label for="">
                           <?=T::agent?>
                           <?=T::fee?>
                        </label>
                        <div class="form-floating mt-2">
                           <div class="input-group">
                              <input type="number" step="any" min="0" class="form-control rounded-0" id=""
                                 name="agent_comission" value="0">
                              <span class="input-group-text text-white bg-primary">%</span>
                           </div>
                           <!-- <label for="">Agent Commission</label> -->
                        </div>
                     </div>

                  </div>
               </div>
               <hr class="m-0">
            </div>

            <script>
               document.addEventListener("DOMContentLoaded", function () {
                  const paymentStatus = document.getElementById("agentPaymentStatus");
                  const agentCommission = document.querySelector("input[name='agent_comission']");
                  const agentCommissionAmount = document.getElementById("agentCommissionAmount");

                  if (paymentStatus && agentCommission && agentCommissionAmount) {
                     paymentStatus.addEventListener("change", function () {
                        updateCommission(this.value);
                     });

                     if (jQuery && $.fn.select2) {
                        $('#agentPaymentStatus').on('select2:select', function (e) {
                           updateCommission(e.params.data.id);
                        });
                     }
                  }

                  function updateCommission(value) {
                     if (value === "cancelled") {
                        agentCommission.value = 0;
                        agentCommissionAmount.value = 0;

                        agentCommission.setAttribute("readonly", true);
                        agentCommissionAmount.setAttribute("readonly", true);

                        $(agentCommission).trigger("input");
                        $(agentCommissionAmount).trigger("input");
                     } else {
                        agentCommission.removeAttribute("readonly");
                        agentCommissionAmount.removeAttribute("readonly");
                     }

                     calculateTotalPrice(); 
                  }
               });
            </script>

            <!-- Number of Travelers -->

            <!-- Client Details -->
            <!-- <div class="row mb-3 g-3">
               <div class="col-md-3">
                  <div class="form-floating">
                     <input type="text" class="form-control" id="firstName" name="first_name"
                        placeholder="Enter first name" required>
                     <label for="firstName">First Name</label>
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="form-floating">
                     <input type="text" class="form-control" id="lastName" name="last_name"
                        placeholder="Enter last name" required>
                     <label for="lastName">Last Name</label>
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="form-floating">
                     <input type="email" class="form-control" id="clientEmail" name="email"
                        placeholder="Enter email address" required>
                     <label for="clientEmail">Email</label>
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="form-floating">
                     <input type="number" class="form-control" id="clientPhone" name="phone"
                        placeholder="Enter Phone Number" required>
                     <label for="clientPhone">Phone</label>
                  </div>
               </div>
            </div> -->

            <script>
               $(document).ready(function () {
                  let adultIndex = 1; // Start index for adults

                  $(".adults-container").on("click", ".add_adults", function () {
                     const clonedRow = $(".adults_clone:first").clone();

                     // Clear input values
                     clonedRow.find("input").val("");
                     clonedRow.find("select").val("Mr");

                     // Update the `name` attributes with unique index
                     clonedRow.find("[name^='adults_data']").each(function () {
                        const nameAttr = $(this).attr("name");
                        $(this).attr("name", nameAttr.replace(/\[0\]/, `[${adultIndex}]`));
                     });

                     clonedRow.find(".add_adults").hide();
                     clonedRow.find(".remove_adults").show();

                     $(".adults-container").append(clonedRow);
                     adultIndex++;
                  });

                  $(".adults-container").on("click", ".remove_adults", function () {
                     $(this).closest(".adults_clone").remove();
                  });

               });
            </script>

            <div class="card mb-2">
               <div class="card-header bg-primary text-dark py-3">
                  <strong class="">
                     <?=T::booking?>
                     <?=T::note?>
               </div>
               <div class="card-body p-3">
                  <textarea name="bookingnote" class="form-control" id="bookingnote" rows="4"
                     placeholder="Add booking note here..."></textarea>
               </div>
               <hr class="m-0">
            </div>

            <div class="card mb-2">
               <div class="card-header bg-primary text-dark py-3">
                  <strong class="">
                     <?=T::cancellation?>
                     <?=T::terms?>
                     <?=T::and?>
                     <?=T::policy?>
               </div>
               <div class="card-body p-3">
                  <textarea name="cancellation_terms" class="form-control" id="cancellation_terms" rows="4"
                     placeholder="Add Cancellation terms & policy here..."></textarea>
               </div>
               <hr class="m-0">
            </div>

            <!-- for agent -->
            <div class="card mb-2">
               <div class="card-header bg-primary text-dark py-3">
                  <strong class="">
                     <?=T::agent?>
                     <?=T::notifications?>
               </div>
               <div class="card-body p-3">
                  <div class="row">
                     <div class="col-md-3">
                        <div class="form-check d-flex gap-3 align-items-center">
                           <input class="form-check-input" type="checkbox" value="" name="sendEmail_agent"
                              id="sendEmail_agent">
                           <label class="form-check-label" for="sendEmail">
                              Email Sale Alert
                           </label>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-check d-flex gap-3 align-items-center">
                           <input class="form-check-input" type="checkbox" value="" name="sendSMS_agent"
                              id="sendSMS_agent">
                           <label class="form-check-label" for="sendSMS">
                              SMS Sale Alert
                           </label>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-check d-flex gap-3 align-items-center">
                           <input class="form-check-input" type="checkbox" value="" name="sendWhatsapp_agent"
                              id="sendWhatsapp_agent">
                           <label class="form-check-label" for="sendWhatsapp">
                              WhatsApp Sale Alert
                           </label>
                        </div>
                     </div>
                  </div>
               </div>
               <hr class="m-0">
            </div>
            <!-- for agent -->

            <!-- for client -->
            <div class="card mb-2">
               <div class="card-header bg-primary text-dark py-3">
                  <strong class="">
                     <?=T::client?>
                     <?=T::notifications?>
               </div>
               <div class="card-body p-3">
                  <div class="row ">
                     <div class="col-md-3">
                        <div class="form-check d-flex gap-3 align-items-center">
                           <input class="form-check-input" type="checkbox" value="" name="sendEmail_client"
                              id="sendEmail_client">
                           <label class="form-check-label" for="sendEmail">
                              Email Confirmation
                           </label>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-check d-flex gap-3 align-items-center">
                           <input class="form-check-input" type="checkbox" value="" name="sendSMS" id="sendSMS_client">
                           <label class="form-check-label" for="sendSMS">
                              SMS Confirmation
                           </label>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-check d-flex gap-3 align-items-center">
                           <input class="form-check-input" type="checkbox" value="" name="sendWhatsapp"
                              id="sendWhatsapp_client">
                           <label class="form-check-label" for="sendWhatsapp">
                              WhatsApp Confirmation
                           </label>
                        </div>
                     </div>
                  </div>
               </div>
               <hr class="m-0">
            </div>
            <!-- for client -->

            <div class="d-block"></div>
            <?php
            $curreny = $db->select("currencies", "*", ["default" => 1,]);?>
            <div class="d-block"></div>
            <div class="row mb-3 g-3">

               <div class="col-md-2">
                  <small for="">
                        <?=T::tax_?>
                  </small>
                  <div class="form-floating">
                        <div class="input-group">
                           <input type="number" step="any" min="0" class="form-control rounded-0" id="" name="tax"
                              value="14" required>
                           <span class="input-group-text text-white bg-primary">%</span>
                        </div>
                        <!-- <label for="">Tax</label> -->
                  </div>
               </div>

               <div class="col-md-3">
                  <small for="">
                     <?=T::sub?>
                     <?=T::total?>
                  </small>
                  <div class="form-floating">
                     <div class="input-group">
                        <input type="number" step="any" min="0" class="form-control fw-semibold text-dark rounded-0"
                           id="subtotal" name="subtotal" readonly>
                        <span class="input-group-text text-white bg-primary">
                           <?= $curreny[0]['name']?>
                        </span>
                     </div>
                     <!-- <label for="">Tax</label> -->
                  </div>
               </div>

               <div class="col-md-3">
                  <small for="">
                     <?=T::net_profit?>
                  </small>
                  <div class="form-floating">
                     <div class="input-group">
                        <input type="number" step="any" min="0" class="form-control fw-semibold text-dark rounded-0" id=""
                           name="net_profit" value="0" required readonly>
                        <span class="input-group-text text-white bg-primary">
                           <?= $curreny[0]['name']?>
                        </span>
                     </div>
                     <!-- <label for="">Platform Commission</label> -->
                  </div>
               </div>

               <div class="col-md-4">
                  <small for="">
                     <?=T::total?>
                     <?=T::price?>
                  </small>
                  <div class="form-floating">
                     <div class="input-group">
                        <input type="number" step="any" min="0" class="form-control fw-semibold text-dark rounded-0"
                           id="bookingPrice" name="price" readonly>
                        <input type="hidden" step="any" min="0" class="form-control fw-semibold text-dark rounded-0"
                           id="actualbookingPrice" name="actual_price" readonly>
                        <span class="input-group-text text-white bg-primary">
                           <?= $curreny[0]['name']?>
                        </span>
                     </div>
                     <!-- <label for="">Tax</label> -->
                  </div>
               </div>
            </div>

            <div class="d-block "></div>

            <hr>
            <!-- Submit Button -->
            <div class="text-start">
               <button type="submit" class="btn btn-primary">Submit Booking</button>
            </div>
         </form>
      </div>
   </div>
</div>
<script>
   $(document).ready(function () {
      const flatpickrInstance = {
         checkin: null,
         checkout: null
      };

      // Initialize checkin datepicker
      flatpickrInstance.checkin = flatpickr("#checkin", {
         dateFormat: "Y-m-d",
         minDate: "today",
         onChange: function(selectedDates, dateStr, instance) {
            // Automatically set checkout to +1 day
            const checkout = flatpickrInstance.checkout;
            if (checkout) {
               const nextDay = new Date(selectedDates[0]);
               nextDay.setDate(nextDay.getDate() + 1);
               checkout.set("minDate", nextDay);
            }
            calculateTotalPrice();
            calculateDays();
         }
      });

      // Initialize checkout datepicker
      flatpickrInstance.checkout = flatpickr("#checkout", {
         dateFormat: "Y-m-d",
         minDate: new Date().fp_incr(1), // tomorrow
         onChange: function(selectedDates, dateStr, instance) {
            calculateTotalPrice();
            calculateDays();
         }
      });

      // Function to calculate days between check-in and check-out
      function calculateDays() {
         const checkinDate = flatpickrInstance.checkin.selectedDates[0];
         const checkoutDate = flatpickrInstance.checkout.selectedDates[0];
         
         if (checkinDate && checkoutDate) {
            // Calculate difference in milliseconds
            const diffTime = checkoutDate - checkinDate;
            // Convert to days
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            console.log(`Number of nights: ${diffDays}`);
            
            // Display it somewhere on your page
            document.getElementById("nights-display").textContent = `${diffDays} night${diffDays !== 1 ? 's' : ''}`;
         }
      }
      const hotelSelect = $('#hotelSelect');
      const roomSelect = $('#roomSelect');
      
      /* 
      EXPLANATION OF CALCULATIONS:
      ================================

      1. TOTAL MARKUP PRICE (Customer pays):
         roomPricePerNight × days × roomQuantity
         Example: $100/night × 3 nights × 2 rooms = $600

      2. TOTAL ACTUAL PRICE (Original cost):
         actualRoomPricePerNight × days × roomQuantity
         Example: $80/night × 3 nights × 2 rooms = $480

      3. CREDIT CARD FEE:
         (totalMarkupPrice × 2.9%) + $0.30
         Example: ($600 × 0.029) + $0.30 = $17.70

      4. SUBTOTAL (Price before tax):
         Per Night: roomPricePerNight / (1 + tax%)
         Total: subtotalPerNight × days × roomQuantity
         Example: $100 / 1.14 = $87.72 per night
               $87.72 × 3 nights × 2 rooms = $526.32 total

      5. AGENT COMMISSION:
         totalSubtotal × commission%
         Example: $526.32 × 10% = $52.63

      6. NET PROFIT:
         totalMarkupPrice - supplierCost - agentCommission + iata - ccFee
         Example: $600 - $480 - $52.63 + $5 - $17.70 = $54.67

      ================================
      */

      function calculateTotalPrice() {
         const getInputValue = (name) => parseFloat($(`input[name="${name}"]`).val()) || 0;

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
            $('#actualbookingPrice').val("0.00");
            $('#subtotal').val("0.00");
            $('input[name="net_profit"]').val("0.00");
            $('input[name="agent_commission_amount"]').val("0.00");
            return;
         }

         // Get input values
         const roomPricePerNight = getInputValue("room_price"); // Markup price per night
         const actualRoomPricePerNight = getInputValue("actual_room_price"); // Actual price per night
         const roomQuantity = getInputValue("room_quantity") || 1;
         const agentCommissionPercent = getInputValue("agent_comission");
         const taxPercent = getInputValue("tax");
         const iata = getInputValue("iata");

         if (roomPricePerNight === 0) {
            $('#bookingPrice').val("0.00");
            $('#actualbookingPrice').val("0.00");
            $('#subtotal').val("0.00");
            $('input[name="net_profit"]').val("0.00");
            $('input[name="agent_commission_amount"]').val("0.00");
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
         const agentCommissionAmount = (totalSubtotal * agentCommissionPercent) / 100;

         // Net Profit Calculation:
         // Total revenue - supplier cost - agent commission + IATA benefit - CC fee
         const netProfit = totalMarkupPrice - totalActualPrice - agentCommissionAmount + iata - ccFee;

         // Set calculated values in respective input fields
         $('#bookingPrice').val(totalMarkupPrice.toFixed(2));
         $('#actualbookingPrice').val(totalActualPrice.toFixed(2));
         $('#subtotal').val(totalSubtotal.toFixed(2));
         $('input[name="net_profit"]').val(netProfit.toFixed(2));
         $('input[name="agent_commission_amount"]').val(agentCommissionAmount.toFixed(2));
         $('input[name="supplier_cost"]').val(totalActualPrice.toFixed(2));
         $('input[name="supplier_cost"]').text(totalActualPrice.toFixed(2));
      }

      // Event listeners for input fields
      $('input[name="room_price"], input[name="agent_comission"], input[name="supplier_cost"], input[name="tax"], input[name="supplier_cost"], input[name="iata"], input[name="room_quantity"]').on('input', calculateTotalPrice);

      // Initial calculation
      calculateTotalPrice();


      // Handle location selection change
      $('#locationSelect').on('change', function () {
         const location = $(this).val();
         if (location) {
            hotelSelect.prop('disabled', false);
            $.ajax({
               url: 'booking-ajax.php',
               type: 'POST',
               data: {
                  action: 'get_hotels',
                  location: location
               },
               success: function (response) {
                  hotelSelect.html('<option value="" disabled selected>Select a Hotel</option>');
                  if (response.status === 'success') {
                     response.hotels.forEach(function (hotel) {
                        hotelSelect.append(`<option value="${hotel.id}">${hotel.name}</option>`);
                     });
                  } else {
                     console.error('Error fetching hotels:', response.message);
                  }
               },
               error: function (xhr, status, error) {
                  console.error('Ajax error:', error);
               }
            });
         } else {
            hotelSelect.prop('disabled', true).html('<option value="" disabled selected>Select a Hotel</option>');
         }
      });

      // Handle hotel selection change
      hotelSelect.on('change', function () {
         const hotelId = $(this).val();
         if (hotelId) {
            roomSelect.prop('disabled', false);
            $.ajax({
               url: 'booking-ajax.php',
               type: 'POST',
               data: {
                  action: 'get_rooms',
                  hotel_id: hotelId
               },
               success: function (response) {
                  roomSelect.html('<option value="" disabled selected>Select a Room</option>');
                  if (response.status === 'success') {
                     response.rooms.forEach(function (room) {
                        roomSelect.append(`<option value="${room.id}">${room.name}</option>`);
                     });
                  } else {
                     console.error('Error fetching rooms:', response.message);
                  }
               },
               error: function (xhr, status, error) {
                  console.error('Ajax error:', error);
               }
            });
         } else {
            roomSelect.prop('disabled', true).html('<option value="" disabled selected>Select a Room</option>');
         }
      });

      // Handle agent selection change
      $('#agentSelect').on('change', function () {
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
                     $('input[name="agent_comission"]').val(response.markup);
                     calculateTotalPrice(); // Recalculate total price with new commission
                  } else {
                     console.error('Error fetching agent commission:', response.message);
                  }
               },
               error: function (xhr, status, error) {
                  console.error('Ajax error:', error);
               }
            });
         } else {
            $('input[name="agent_comission"]').val('0'); // Reset agent commission if no agent is selected
            calculateTotalPrice(); // Recalculate total price
         }
      });
   });

   $(document).ready(function () {
   const agentFeeInput = $('input[name="agent_comission"]');
   const agentAmountInput = $('input[name="agent_commission_amount"]'); 
   const subtotalInput = $('#subtotal'); 

   function updateAmountFromFee() {
      let feePercent = parseFloat(agentFeeInput.val()) || 0;
      let subtotal = parseFloat(subtotalInput.val()) || 0;
      let commissionAmount = (subtotal * feePercent) / 100;
      agentAmountInput.val(commissionAmount.toFixed(2));
   }

   function updateFeeFromAmount() {
      let amount = parseFloat(agentAmountInput.val()) || 0;
      let subtotal = parseFloat(subtotalInput.val()) || 0;
      let feePercent = subtotal > 0 ? (amount / subtotal) * 100 : 0;
      agentFeeInput.val(feePercent.toFixed(2));
   }

   // Event Listeners
   agentFeeInput.on("input", updateAmountFromFee);
   agentAmountInput.on("input", updateFeeFromAmount);
});

</script>

<script>
   $(document).ready(function () {
      $('.select2').select2();
   });
</script>
<script>
   function loading() {
      $('.bodyload').show()
   }
</script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<?php include "_footer.php" ?>