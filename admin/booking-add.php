<style>
   .select2-selection__rendered{
      margin-top:4px;
   }
</style>
<?php
   require_once '_config.php';
   auth_check();

   $title = T::add .' '. T::booking;
   include "_header.php";

///////// for send sms using twillio
   use Twilio\Rest\Client;

// Send SMS Function
function sendSMS($to_number, $message) {
   global $account_sid, $auth_token, $twilio_number;

   $client = new Client($account_sid, $auth_token);
   $message = $client->messages->create(
       $to_number,
       [
           'from' => $twilio_number,
           'body' => $message,
       ]
   );
   return "Message sent to $to_number: {$message->sid}";
}


///////// for send sms using twillio
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

      if (!isset($_POST['hotel'])) {
         die("Error: Hotel ID is required.");
      }

      $selected_country = $db->get("countries", "*", ["id" => $_POST['country_id']]);
      $phone_code = $selected_country['phonecode'] ?? "";
      $iso_code = $selected_country['iso'] ?? "";
      $nationality = $selected_country['iso'] ?? "";

      // for remvoing 0 form starting of phone 
      $phone_number = ltrim($_POST['phone'], '0');

      $clientPhone = $phone_code .$phone_number;

      $hotel_img = $db->select("hotels_images", "*", ["hotel_id" => $_POST['hotel']]);

      $params = [
         "booking_ref_no" => date('Ymdhis') . rand(),
         "location" => $_POST['location'] ?? "",
         "hotel_id" => $_POST['hotel'],
         "hotel_img" => $hotel_img[0]['img'] ?? "no-image.jpg",
         "price_markup" => $_POST['price'] ?? 0.0,
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
         "agent_id" => $_POST['agent'] ?? null,
         "booking_date" => date('Y-m-d'),
         "agent_fee" => $_POST['agent_comission'] ?? 0,
         "tax" => $_POST['tax'] ?? 0,
         "platform_comission" => $_POST['platform_comission'] ?? 0,
         "price_original" => $_POST['room_price'] ?? 0.0,
         "booking_note" => $_POST['bookingnote'] ?? "",
         "cancellation_terms" => $_POST['cancellation_terms'] ?? "",
         "supplier_cost" => $_POST['supplier_cost'] ?? 0.0,
         "supplier_payment_status" => $_POST['supplier_payment_status'] ?? "unpaid",
         "supplier_due_date" => $_POST['supplier_due_date'] ?? "",
         "supplier_id" => $_POST["supplier_id"] ?? null
      ];

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
               "room_price" => $_POST['room_price'] ?? 0.0,
               "room_quantity" => !empty($room_options) ? $room_options[0]['quantity'] : "1",
               "room_extrabed_price" => $room_details[0]['extra_bed_charges'] ?? 0.0,
               "room_extrabed" => $room_details[0]['extra_bed'] ?? 0,
               "room_actual_price" => !empty($room_options) ? $room_options[0]['price'] : "0.00"
            ];

            $params['room_data'] = json_encode([$room_data]);
         }
      }

      // for sending messages 
      $sendEmail = isset($_POST['sendEmail']) ? true : false;
      $sendSMS = isset($_POST['sendSMS']) ? true : false;
      $sendWhatsapp = isset($_POST['sendWhatsapp']) ? true : false;


      ///// twillio credentials
      $account_sid = "______AC7d8e150______4e88a2adf5231____178e1876b85e";
      $auth_token = "_____69cdf72___865007de74__d05f9c8191__69d67";
      $twilio_number = "+19477777293";
      ///// twillio credentials

      if ($sendSMS) {
         $agentDetails = $db->get("users", ["phone", "phone_country_code", "first_name", "last_name"], ["user_id" => $_POST['agent']]);
         $supplierDetails = $db->get("users", ["phone", "phone_country_code", "first_name", "last_name"], ["user_id" => $_POST['supplier_id']]);
     
         $clientNum = "+".$clientPhone;
         $agentNum = isset($agentDetails["phone_country_code"]) && isset($agentDetails["phone"]) ? "+".$agentDetails["phone_country_code"] . $agentDetails["phone"] : "";
         $supplierNum = isset($supplierDetails["phone_country_code"]) && isset($supplierDetails["phone"]) ? "+".$supplierDetails["phone_country_code"] . $supplierDetails["phone"] : "";
         

         // print_r($clientNum);
         // print_r($agentNum);
         // print_r($supplierNum);
         // exit;
         if (!empty($clientNum)) {
             // sendSMS($clientNum, "Hello! This is a client message.", $account_sid, $auth_token, $twilio_number);
             // echo $response;
         }
     
         if (!empty($agentNum)) {
            try {
               $room_price = $_POST['room_price'] ?? 0.0; 
               $agent_comission = $_POST['agent_comission'] ?? 0.0;  
               
               $commission_amount = ($room_price * $agent_comission) / 100;
               $subtotal = $room_price + $commission_amount;

                $commissionAmount = ($_POST['price'] ?? 0.0) * ($_POST['agent_comission'] ?? 0) / 100;
        
                sendSMS($agentNum, "
NEW SALE ALERT
                            
Great news, " . $agentDetails['first_name'] . ' ' . $agentDetails['last_name'] . "! You’ve just made a new hotel sale for " . $_POST['adults_data'][0]['firstname'] . ' ' . $_POST['adults_data'][0]['lastname'] . "’s trip to " . $_POST['location'] . ".  
                            
Hotel: " . $hotel_data[0]['name'] . " 
                            
Check in & out dates: " . $_POST['checkin'] . " - " . $_POST['checkout'] . "
                            
Sale amount: " . $subtotal . " " . $currency[0]['name'] . "
                            
Commission: " . number_format($commissionAmount, 2) . " " . $currency[0]['name'] . "
                            
Log into your account to see your sales, commissions and more details about your business! www.TopTierTravel.Site/partners
               ", $account_sid, $auth_token, $twilio_number);
                
            } catch (Exception $e) {
                echo "Error: Failed to send SMS. Please try again later.";
                exit;
            }
        }
        
     
         if (!empty($supplierNum)) {
            // sendSMS($supplierNum, "Hello! This is a client message.", $account_sid, $auth_token, $twilio_number);
             // echo $response;
         }
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

         <form method="post" action="<?=root?>booking-add.php" onsubmit="loading()">
            <!-- Select Hotel -->
            <div class="card mb-2">
               <div class="card-header bg-primary text-dark py-3">
                  <strong class="">
                     <?=T::hotel?>
                  </strong>
               </div>

               <div class="card-body p-4">
                  <div class="row g-3">
                     <div class="col-md-3">
                     <label for="" >Location</label>
                      <hr>

                        <?php
                     $locations = $db->select("hotels", "location", ["status" => 1, "GROUP" => "location"]);
                     ?>
                        <div class="">
                           <select class="select2 form-select" id="locationSelect" name="location" required>
                              <option value="" disabled selected><?=T::select_location?></option>
                              <?php foreach($locations as $location) { ?>
                              <option value="<?= $location ?>">
                                 <?= $location ?>
                              </option>
                              <?php } ?>
                           </select>
                           <!-- <label for="locationSelect">Select Location</label> -->
                        </div>
                     </div>
                     <div class="col-md-3">
                     <label for="" >Hotel</label>
                     <hr>
                        <div class=" ">
                           <select class="select2" id="hotelSelect" name="hotel" required>
                              <option value="" disabled selected><?=T::select_hotel?></option>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-3">
                     <label for="" >Room</label>
                     <hr>
                        <div class=" ">
                           <select class="select2" id="roomSelect" name="room" required>
                              <option value="" disabled selected><?=T::select_room?></option>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-3">

                     <label for="" >Room Price</label>
 
                        <?php $curreny = $db->select("currencies", "*", ["default" => 1,]);?>
                        <!-- <small for="">Room Price</small> -->
                        <div class="form-floating mt-2">
                           <div class="input-group">
                              <!-- <div class="form-floating"> -->
                                 <input type="number" class="form-control" id="" name="room_price" value="0" required
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
                     <?=T::travellers?>
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
                                 <option value="Mr"><?=T::mr?></option>
                                 <option value="Miss"><?=T::miss?></option>
                                 <option value="Mrs"><?=T::mrs?></option>
                              </select>
                              <label for=""><?=T::title?></label>
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="form-floating">
                              <input type="text" name="adults_data[0][firstname]" class="form-control"
                                 placeholder="First Name" value="" required />
                              <label for=""><?=T::first_name?></label>
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="form-floating">
                              <input type="text" name="adults_data[0][lastname]" class="form-control"
                                 placeholder="Last Name" value="" required />
                              <label for=""><?=T::last_name?></label>
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
                           <label for="clientEmail"><?=T::email?>(<?=T::optional?>)</label>
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
                                 data-country-phonecode="<?= $c['phonecode'] ?>">
                                 <?= $c['nicename'] ?> <strong>+
                                    <?= $c['phonecode'] ?>
                                 </strong>
                              </option>
                              <?php } ?>
                           </select>
                           <label for="">Select Country</label>

                           <script>
                              var requestUrl = "https://ipwhois.app/json/";
                              fetch(requestUrl)
                                 .then(function (response) { return response.json(); })
                                 .then(function (c) {
                                    var user_country = c['country_phone'];
                                    user_country = user_country.replace('+', '');
                                    $("[data-country-phonecode='" + user_country + "']").prop("selected", true);
                                    console.log(user_country);
                                 });
                           </script>
                        </div>
                     </div>
                     <div class="col-3">
                        <div class="form-floating">
                           <input required type="number" class="form-control rounded-3 whatsapp" id="clientPhone" name="phone" placeholder="Enter Phone Number">
                           <label for="clientPhone"><?=T::phone?>(<?=T::optional?>)</label>
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
                           <input type="text" class="checkin form-control" id="" name="checkin" autocomplete="off"
                              required value="<?php $d=strtotime(" +3 Days"); echo date("d-m-Y", $d); ?>">
                           <label for="checkinDate"><?=T::checkin?> <?=T::date?></label>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-floating">
                           <input type="text" class="checkout form-control" id="" name="checkout" required
                              autocomplete="off" value="<?php $d=strtotime(" +4 Days"); echo date("d-m-Y", $d); ?>">
                           <label for="checkoutDate"><?=T::checkout?> <?=T::date?></label>
                        </div>
                     </div>
                  </div>
               </div>
            </div>


            <div class="card mb-2">
               <div class="card-header bg-primary text-dark py-3">
                  <strong>
                     <?=T::supplier?>
                  </strong>
               </div>
               <div class="card-body p-4">
                  <div class="row g-3">
                     <div class="col-md-4">
                        <label for=""><?=T::supplier?></label>
                        <div class="form-floating mt-3 rounded-2 h-100">
                           <select class="form-select select2 pt-2" id="supplier_id" name="supplier_id" required>
                              <option value="" disabled selected><?=T::select_supplier?></option>
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
                     <div class="col-md-3">
                        <label for=""><?=T::payment?> <?=T::status?></label>
                        <div class="form-floating mt-3 rounded-2 h-100">
                           <select class="form-select select2 pt-2" id="search_type" name="supplier_payment_status"
                              required>
                              <option value="" disabled selected><?=T::select?> <?=T::payment?> <?=T::status?></option>
                              <option value="paid">
                                 <?=T::paid?>
                              </option>
                              <option value="unpaid">
                                 <?=T::unpaid?>
                              </option>
                           </select>
                           <!-- <label for="search_type">Supplier Payment Status</label> -->
                        </div>
                     </div>
                     <div class="col-md-3">
                        <label for=""><?=T::supplier?> <?=T::due?> <?=T::date?></label>
                        <div class="">
                           <input type="date" class="form-control mt-2" id="supplier_due_date" name="supplier_due_date"
                              autocomplete="off" required>
                           <!-- <label for="supplier_due_date">Due Date</label> -->
                        </div>
                     </div>
                     <!-- Supplier Cost Input -->
                     <div class="col-md-2">
                        <label for=""><?=T::supplier?> <?=T::cost?></label>
                        <div class="form-floating">
                           <div class="input-group">
                              <input type="number" class="form-control rounded-0 mt-2" id="supplierCost" name="supplier_cost"
                                 value="0" required>
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
                     <div class="col-md-4">
                        <label for=""><?=T::agent?></label>
                        <div class="form-floating mt-3 rounded-2 h-100">
                           <select class="select2 pt-2" id="agentSelect" name="agent" required>
                              <option value="" selected><?=T::select?> <?=T::agent?></option>
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
                     <div class="col-md-6">

                     </div>

                     <!-- Agent Commission -->
                     <div class="col-md-2">
                        <label for=""><?=T::agent?> <?=T::fee?></label>
                        <div class="form-floating mt-2">
                           <div class="input-group">
                              <input type="number" class="form-control rounded-0" id="" name="agent_comission" value="0"
                                 required>
                              <span class="input-group-text text-white bg-primary">%</span>
                           </div>
                           <!-- <label for="">Agent Commission</label> -->
                        </div>
                     </div>

                  </div>
               </div>
               <hr class="m-0">
            </div>



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
                  <?=T::booking?> <?=T::note?>
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
                     <?=T::cancellation?> <?=T::terms?> <?=T::and?> <?=T::policy?>
               </div>
               <div class="card-body p-3">
                  <textarea name="cancellation_terms" class="form-control" id="cancellation_terms" rows="4"
                     placeholder="Add Cancellation terms & policy here..."></textarea>
               </div>
               <hr class="m-0">
            </div>


            <div class="d-block border-top pt-3"></div>

            <?php
            $curreny = $db->select("currencies", "*", ["default" => 1,]);?>
            <div class="d-block"></div>
            <div class="row mb-3 g-3">



               <div class="col-md-2">
                  <small for=""><?=T::net_profit?></small>
                  <div class="form-floating">
                     <div class="input-group">
                        <input type="number" class="form-control rounded-0" id="" name="platform_comission" value="0"
                           required>
                        <span class="input-group-text text-white bg-primary">
                           <?= $curreny[0]['name']?>
                        </span>
                     </div>
                     <!-- <label for="">Platform Commission</label> -->
                  </div>
               </div>

               <div class="col-md-2">
                  <small for=""><?=T::tax_?></small>
                  <div class="form-floating">
                     <div class="input-group">
                        <input type="number" class="form-control rounded-0" id="" name="tax" value="14" required>
                        <span class="input-group-text text-white bg-primary">%</span>
                     </div>
                     <!-- <label for="">Tax</label> -->
                  </div>
               </div>

               <div class="col-md-4">
                  <small for=""><?=T::total?> <?=T::price?></small>
                  <div class="form-floating">
                     <div class="input-group">
                        <input type="number" class="form-control fw-semibold text-dark rounded-0" id="bookingPrice"
                           name="price" readonly>
                        <span class="input-group-text text-white bg-primary">
                           <?= $curreny[0]['name']?>
                        </span>
                     </div>
                     <!-- <label for="">Tax</label> -->
                  </div>
               </div>
            </div>

            <div class="d-block border-top pt-3"></div>

            <div class="row">
               <div class="col-md-2">
                  <div class="form-check d-flex gap-3 align-items-center">
                     <input class="form-check-input" type="checkbox" value="" name="sendEmail" id="sendEmail">
                     <label class="form-check-label" for="sendEmail">
                        Send Email
                     </label>
                  </div>
               </div>
               <div class="col-md-2">
                  <div class="form-check d-flex gap-3 align-items-center">
                     <input class="form-check-input" type="checkbox" value="" name="sendSMS" id="sendSMS">
                     <label class="form-check-label" for="sendSMS">
                        Send SMS
                     </label>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-check d-flex gap-3 align-items-center">
                     <input class="form-check-input" type="checkbox" value="" name="sendWhatsapp" id="sendWhatsapp">
                     <label class="form-check-label" for="sendWhatsapp">
                        Send Whatsapp
                     </label>
                  </div>
               </div>
            </div>
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
      const hotelSelect = $('#hotelSelect');
      const roomSelect = $('#roomSelect');

      // Function to calculate the total price
      function calculateTotalPrice() {
         // Get values from input fields
         const roomPrice = parseFloat($('input[name="room_price"]').val()) || 0;
         const platformCommission = parseFloat($('input[name="platform_comission"]').val()) || 0;
         const agentCommissionPercent = parseFloat($('input[name="agent_comission"]').val()) || 0;
         const taxPercent = parseFloat($('input[name="tax"]').val()) || 0;
         const supplierCost = parseFloat($('input[name="supplier_cost"]').val()) || 0; // Add supplier cost

         // Calculate the agent commission based on room price and platform commission
         const agentCommission = (roomPrice + platformCommission) * (agentCommissionPercent / 100);

         // Calculate total before tax (room price + platform commission + agent commission + supplier cost)
         const totalBeforeTax = roomPrice + platformCommission + agentCommission + supplierCost;

         // Calculate tax amount
         const taxAmount = totalBeforeTax * (taxPercent / 100);

         // Calculate total price (including tax)
         const totalPrice = totalBeforeTax + taxAmount;

         // Update the total price field
         $('#bookingPrice').val(totalPrice.toFixed(2));
      }

      // Bind change events to fields to recalculate the total price
      $('input[name="room_price"], input[name="platform_comission"], input[name="agent_comission"], input[name="tax"], input[name="supplier_cost"]').on('input', function () {
         calculateTotalPrice();
      });

      // Initial calculation when the page loads
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