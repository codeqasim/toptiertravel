<?php

// PARAMS TO SEND IN API
$params = array(
"api_key" => api_key,
"user_id" => $_SESSION['phptravels_client']->user_id,
);

// REQUEST TO API 
$account = POST(api_url.'profile',$params);
// pre($account->data[0]->balance);

$details = array(
    "reviews"=>"0",
    "pending_nvoices"=>"0",
    "totel_booking"=>"0",
    "balance"=>($account->data[0]->currency_id),
    "currency"=>($account->data[0]->balance),
);

$da = json_encode($details);
$dashboard_details = json_decode($da);

// CALL API GET DATA
$params = array( "api_key" => api_key, "user_id" => $_SESSION['phptravels_client']->user_id, );
$bookings = POST(api_url.'user_bookings', $params)->data;
if (isset($bookings->flights)){ $flights_bookings = $bookings->flights; } else { $flights_bookings = 0; }
if (isset($bookings->hotels)){ $hotels_bookings = $bookings->hotels; } else { $hotels_bookings = 0; }
if (isset($bookings->tours)){ $tours_bookings = $bookings->tours; } else { $tours_bookings = 0; }
if (isset($bookings->cars)){ $cars_bookings = $bookings->cars; } else { $cars_bookings = 0; }
if (isset($bookings->visa)){ $visa_bookings = $bookings->visa; } else { $visa_bookings = 0; }
$total_bookings = count($flights_bookings) + count($hotels_bookings) + count($tours_bookings) + count($cars_bookings) + count($visa_bookings);

// PENDING BOOKINGS FOR PAYMENT

// FLIGHTS
$pending_flights=array();
foreach($flights_bookings as $flights){
    if ($flights->payment_status=="unpaid"){ array_push($pending_flights,$flights); }
}

// HOTELS
$pending_hotels=array();
foreach($hotels_bookings as $hotels){
    if ($hotels->payment_status=="unpaid"){ array_push($pending_hotels,$hotels); }
}

// TOURS
$pending_tours=array();
foreach($tours_bookings as $tours){
    if ($tours->payment_status=="unpaid"){ array_push($pending_tours,$tours); }
}

// CARS
$pending_cars=array();
foreach($cars_bookings as $cars){
    if ($cars->payment_status=="unpaid"){ array_push($pending_cars,$cars); }
}

// CARS
$pending_visa=array();
foreach($visa_bookings as $visa){
    if ($visa->payment_status=="unpaid"){ array_push($pending_visa,$visa); }
}

$pending_bookings = count($pending_flights) + count($pending_hotels) + count($pending_tours) + count($pending_cars) + count($pending_visa);

?>
<div class="col-xl-4 col-md-6">
            <div class="py-30 px-30 rounded-1 bg-white shadow-3">
              <div class="row y-gap-20 justify-between items-center">
                <div class="col-auto">
                  <div class="fw-500 lh-14">Wallet Balance</div>
                  <h1 class=""><small><?=$dashboard_details->balance?></small> <strong><?=$dashboard_details->currency?></strong> </h1>
                  <p class="mb-0"><small>Total <?=T::walletbalance?></small></p>
                </div>
                <div class="col-auto">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect>
                    <rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect>
                    <line x1="6" y1="6" x2="6.01" y2="6"></line>
                    <line x1="6" y1="18" x2="6.01" y2="18"></line>
                </svg>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-4 col-md-6">
            <div class="py-30 px-30 rounded-1 bg-white shadow-3">
              <div class="row y-gap-20 justify-between items-center">
                <div class="col-auto">
                  <div class="fw-500 lh-14">Bookings</div>
                  <h1 class=""><strong><?=$total_bookings?></strong> </h1>
                  <p class="mb-0"><small><?=T::totalbookings?></small></p>
                </div>

                <div class="col-auto">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2V6l-3-4H6zM3.8 6h16.4M16 10a4 4 0 1 1-8 0"/></svg>
            
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-4 col-md-6">
            <div class="py-30 px-30 rounded-1 bg-white shadow-3">
              <div class="row y-gap-20 justify-between items-center">
                <div class="col-auto">
                  <div class="fw-500 lh-14">Pending Invoices</div>
                  <h1 class=""><strong><?=$pending_bookings?></strong> </h1>
                  <p class="mb-0"><small><?=T::pendinginvoices?></small></p>
                </div>
                <div class="col-auto">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            
                </div>
              </div>
            </div>
          </div>
        </div>

<style>
.icon-box .info-icon::before { background-color: rgb(230 230 230); }
.icon-box .info-icon { color: #fff; border-radius: 50%; background: rgb(255 255 255 / 91%); }
.info-icon svg { stroke: #000 !important; height: 26px; width: 26px; }
</style>

