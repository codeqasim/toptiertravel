<?php
$data=($meta['data']);
include "App/Views/Invoice_header.php";

// Check if booking_response is set and not null
if (isset($data->booking_response) && !empty($data->booking_response)) {
    $booking_data = json_decode($data->booking_response);
} else {
    $booking_data = null; // Or set it to a default value
    // You can log an error or handle the case where booking_response is missing
    error_log("Warning: booking_response is null or not set.");
}

// print_r($data->booking_response);

?>
<?php
if(!empty($booking_data->Prn)){
    ?>
    <div class="mb-3">
        <div class="card">
            <ul class="list-group list-group-flush"><li class="list-group-item"><span>Payable through <?=$booking_data->response->booking->hotel->supplier->name?>, acting as agent for the service operating company, details of which can be provided upon request. VAT: <?=$booking_data->response->booking->hotel->supplier->vatNumber?> Reference: <?=$booking_data->Prn?>;</span></li></ul>
        </div>
    </div>
<?php } ?>
    <table class="table table-bordered">
        <thead class="bg-light">
            <tr>
                <th class="text-center"><?=T::booking?> <?=T::id?></th>
                <th class="text-center"><?=T::booking?> <?=T::reference?></th>
                 <th class="text-center"><?=T::booking?> <?=T::pnr?></th>
                 <th class="text-center"><?=T::booking?> <?=T::date?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th class="text-center"><?=$data->booking_id?></th>
                <th class="text-center"><?=$data->booking_ref_no?></th>
                <th class="text-center"><?=$data->pnr?></th>
                 <th class="text-center"><?=$data->booking_date?></th>
            </tr>
        </tbody>
    </table>

    <p class="border mb-0 p-2 px-3 bg-light"><strong class="text-uppercase"><small><?=T::travellers?></small></strong></p>
    <table class="table table-bordered">
        <thead class="">
            <tr>
                <th class="text-center"><?=T::no?></th>
                <th class="text-center"><?=T::sr?></th>
                <th class="text-center"><?=T::name?></th>
             </tr>
        </thead>
        <tbody>

        <?php

//        $travellers=(json_decode($data->guest));
        $guest=(json_decode($data->guest));

        foreach($guest as $i => $t){
        ?>
            <tr>
                <th class="text-center"><?=$i+1?></th>

                <?php if(!empty($t->age)){?>
                <th class="text-center"><?=T::child?> <?=T::age?> <?=$t->age?></th>
                <?php }else{ ?>
                <th class="text-center"><?=$t->title?></th>
                <?php } ?>

                <th class="text-center"><?=$t->first_name?> <?=$t->last_name?></th>
             </tr>
            <?php } ?>

        </tbody>
    </table>

    <div class="card mb-3">
        <div class="row g-0">
            <div class="col-md-4">
                <?php
                if($data->supplier=="hotels"){
                    (isset($data->hotel_img))?$img = root."uploads/".$data->hotel_img:$img = root."assets/img/hotel.jpg";
                } else {
                    $img = $data->hotel_img;
                }
                ?>
                <img src="<?=$img?>" class="img-fluid">
            </div>
            <div class="col-md-8">
                <div class="card-body p-3 pb-0 px-3">
                <h5 class="card-title m-0"><strong><?=$data->hotel_name?></strong></h5>
                <span class="d-flex mt-1">
                <?php for ($i = 1; $i <= $data->stars; $i++) { ?>
                    <svg class="stars" style="margin-right:-3px" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                <?php } ?>
                </span>
                <p class="m-0 card-text ttc"><small class="text-muted"><?=$data->hotel_address?></small></p>

                <div style="font-size:13px;line-height:20px;">

                    <?php if ($data->booking_status == "confirmed") { ?>
                    <?php if (!empty($data->location_cords)) {?>
                    <p class="m-0 py-0 card-text"><a target="_target" href="https://www.google.com/maps/?q=<?=$data->location_cords.','.$data->location_cords?>" class="text-color">
                    <strong class="text-black mr-1"><?=T::hotel?>:</strong>
                    <i class="la la-map-marker"></i> <?=T::location?> </a></p>
                    <?php } ?>
                    <?php if (!empty($data->hotel_phone)) {?>
                    <a href="tel:<?=$data->hotel_phone?>">
                    <p class="m-0 py-0 card-text">
                    <strong class="text-black mr-1"><?=T::phone?>:</strong> +<?=$data->hotel_phone?></a></p>
                    <?php } ?>
                    <?php if (!empty($data->hotel_email)) {?>
                    <p class="m-0 py-0 card-text"><a target="_target" href="mailto:<?=$data->hotel_email?>" class="text-color"><strong class="text-black mr-1"><?=T::hotel?> <?=T::email?>:</strong> <i class="la la-envelope"></i> <?=$data->hotel_email?> </a></p>
                    <?php } ?>
                    <?php if (!empty($data->hotel_website)) {?>
                    <p class="m-0 py-0 card-text"><a target="_target" href="http://<?=$data->hotel_website?>" class="text-color"><strong class="text-black mr-1"><?=T::hotel?> <?=T::website?>:</strong> <i class="la la-globe"></i> <?=$data->hotel_website?> </a></p>
                    <?php } ?>
                    <?php } ?>

                </div>

                </div>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <div class="card">
        <div class="card-title px-3 pt-2 strong">
        <?=T::room?> <?=T::details?>
        </div>
        <ul class="list-group list-group-flush">
            <?php foreach(json_decode($data->room_data) as $room){ ?>
            <li class="list-group-item"><span><strong><?=T::hotels_checkin?> </strong>:</span> <?=$data->checkin?> <strong><?=T::hotels_checkout?> </strong>:</span> <?=$data->checkout?> <strong><?=T::total?> <?=T::nights?> </strong> :

            <?php
            // CANCLULATE NIGHTS FROM 2 DATES
            $earlier = new DateTime($data->checkin);
            $later = new DateTime($data->checkout);
            $nights = $later->diff($earlier)->format("%a");
            echo $nights;

            ?>

            </li>
            <li class="list-group-item"><span><strong><?=T::room?> <?=T::type?></strong>:</span> <?=$room->room_name?></li>
            <li class="list-group-item"><span><strong><?=T::room?> <?=T::quantity?></strong>:</span> <?=$room->room_qaunitity?></li>
            <?php } ?>

        </ul>
        </div>
    </div>
        <p><strong><?=T::agent?> <?=T::details?></strong></p>
        <table class="table table-bordered">
        <tbody class="">
            <tr class="">
                <th class="text-start"><?=T::name?>: <?=$data->first_name . ' ' . $data->last_name;?></th>
                <th class="text-start"><?=T::email?>: <?=$data->email?></th>
                <th class="text-start"><?=T::fee?>: <?= $data->agent_fee ?: 0?>%</th>
            </tr>
        </tbody>
    </table>

<?php
if(!empty($booking_data->response->booking->hotel->rooms[0]->rates[0]->rateComments)){
?>
<div class="mb-3">
    <div class="card">
        <div class="card-title px-3 pt-2 strong">
            <?=T::rate?> <?=T::comment?>
        </div>
        <ul class="list-group list-group-flush"><li class="list-group-item"><span><?=$booking_data->response->booking->hotel->rooms[0]->rates[0]->rateComments?></span></li></ul>
    </div>
</div>
<?php } ?>


    <p><strong><?=T::fare_details?></strong></p>
    <table class="table table-bordered">
        <thead class="">
            <!-- <tr>
                <th class="text-start"><?=T::total?></th>
                <th class="text-end"><?=($data->currency_markup)?> <?=($data->price_markup)?></th>
            </tr> -->
            <tr>
                <th class="text-start"><?=T::room?> <?=T::price?></th>
                <th class="text-end"><?=$data->currency_markup?> <?= $data->price_original?></th>
            </tr>
            <tr>
    <th class="text-start"><?= T::agent ?> <?= T::fee ?></th>
    <th class="text-end">% <?= $data->agent_fee ?: 0 ?></th>
</tr>
<tr>
    <th class="text-start"><?= T::tax ?></th>
    <th class="text-end">% <?= $data->tax ?: 0 ?></th>
</tr>
<tr>
    <th class="text-start"><?= T::net_profit ?></th>
    <th class="text-end"><?= $data->currency_markup ?> <?= $data->net_profit ?: 0 ?></th>
</tr>

            <!-- <tr>
                <th class="text-start"><?=T::gst?></th>
                <th class="text-end">% 0</th>
            </tr>
            <tr>
                <th class="text-start"><?=T::vat?></th>
                <th class="text-end">% 0</th>
            </tr> -->

            <tr class="bg-light">
                <th class="text-start"><strong><?=T::total?></strong></th>
                <th class="text-end"><strong><?=($data->currency_markup)?> <?=($data->price_markup)?></strong></th>
            </tr>
        </thead>
    </table>

<?php

 include "App/Views/Invoice_footer.php"; ?>
