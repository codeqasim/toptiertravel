<?php

$booking_data=($meta['data']);
// $rooms=$booking_data->room;
$rooms=($_REQUEST['room_quantity']);

?>
  <main>


<div class="header-margin"></div>
<!-- ================================
    START BREADCRUMB AREA
================================= -->
<section class="bread-bg-booking pt-3 pb-3 bg-primary mb-3" id="">
    <div class="breadcrumb-wrap">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <div class="breadcrumb-content">
                        <div class="section-heading">
                            <p class="mb-0 text-white text-center fw-bold"><?=T::hotel_booking?></p>
                        </div>
                    </div><!-- end breadcrumb-content -->
                </div><!-- end col-lg-6 -->
            </div><!-- end row -->
        </div><!-- end container -->
    </div><!-- end breadcrumb-wrap -->
</section><!-- end breadcrumb-area -->
<!-- ================================
    END BREADCRUMB AREA
================================= -->

<div class="booking_loading" style="display:none">
<div class="rotatingDiv"></div>
</div>

<div class="booking_data">
<!-- ================================
    START BOOKING AREA
================================= -->
<form action="<?=root?>hotels/book" method="POST" class="book">
<section class="booking-area padding-top-50px padding-bottom-70px">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="form-box mb-2">
                    <div class="form-title-wrap">
                        <h3 class="title"> <?=T::personal_information?> </h3>
                    </div><!-- form-title-wrap -->
                    <?php include "App/Views/Accounts/Booking_user.php";?>
                </div>

                <div class="form-box payment-received-wrap mb-2">
                    <div class="form-title-wrap">
                        <h3 class="title"> <?=T::travellers_information?></h3>
                    </div>
                    <div class="card-body">

                    <?php
                    if (isset($_SESSION['hotels_adults'])) {
                    $hotels_adults = $_SESSION['hotels_adults'];
                    } else $hotels_adults =2; for ( $i = 1; $i <= $hotels_adults; $i++ ) { ?>

                    <?php
                    // generate random words
                    $range1 = range('A', 'Z');  $index1 = array_rand($range1);
                    $range2 = range('A', 'Z');  $index2 = array_rand($range2); ?>

                     <div class="card mb-3">
                        <div class="card-header">
                        <strong><?=T::adult .' </strong> '. T::travellers?> <?=$i?>
                        </div>
                        <div class="card-body">
                          <div class="row g-2">
                        <div class="col-md-2">
                        <div class="form-floating">
                         <select name="title_<?=$i?>" class="form-select">
                         <option value="Mr"><?=T::mr?></option>
                        <option value="Miss"><?=T::miss?></option>
                        <option value="Mrs"><?=T::mrs?></option>
                         </select>
                         <label for=""><?=T::title?></label>
                        </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-input">
                            <input type="text" name="firstname_<?=$i?>" class="form-control" value="<?php if (dev == 1){echo "Elan".$range1[$index1];}?>" required/>
                            <label for="">
                            <?=T::first_name?>
                            </label>
                            </div>
                        </div>
                        <div class="col-md-5">
                        <div class="form-input">
                        <input type="text" name="lastname_<?=$i?>" class="form-control" value="<?php if (dev == 1){echo "Mask".$range2[$index2];}?>" required/>
                            <label for="">
                            <?=T::last_name?>
                            </label>
                            </div>
                        </div>

                        </div>
                        </div>
                     </div>
                     <?php } ?>

                     <?php
                    if (isset($_SESSION['hotels_childs'])) {
                    $hotels_childs = $_SESSION['hotels_childs'];
                    } else $hotels_childs =2; for ( $i = 1; $i <= $hotels_childs; $i++ ) { ?>

                    <?php
                    // generate random words
                    $range1 = range('A', 'Z');  $index1 = array_rand($range1);
                    $range2 = range('A', 'Z');  $index2 = array_rand($range2); ?>

                     <div class="card mb-3">
                        <div class="card-header">
                        <strong><?=T::child .' </strong> '. T::travellers?> <?=$i?>
                        </div>
                        <div class="card-body">
                          <div class="row">

                        <div class="col-md-2">
                        <div class="form-floating">
                        <select readonly class="form-select child_age_<?=$i?>" name="child_age_<?=$i?>" required >
                        <?php for ($x = 1; $x <= 16; $x++) { ?>
                        <option value="<?=$x?>"> <?=$x?> </option>
                        <?php } ?>
                        </select>
                        <label class="label-text"> <?=T::age?> </label>

                        </div>
                        </div>

                        <div class="col-md-5">

                        <div class="form-input">
                        <input type="text" name="child_firstname_<?=$i?>" class="form-control" value="<?php if (dev == 1){echo "Elan".$range1[$index1];}?>" required />
                            <label for="">
                            <?=T::first_name?>
                            </label>
                            </div>

                        </div>
                        <div class="col-md-5">

                        <div class="form-input">
                        <input type="text" name="child_lastname_<?=$i?>" class="form-control" value="<?php if (dev == 1){echo "Mask".$range2[$index2];}?>" required />
                            <label for="">
                            <?=T::last_name?>
                            </label>
                            </div>

                        </div>

                        </div>
                        </div>
                     </div>
                     <?php } ?>

                    </div>
                 </div>

                 <?php include "App/Views/Payment_methods.php"; ?>

                <?php // CANCELLATION POLICY
                if (!empty($booking_data->cancellation_policy)) {
                    $cancellation_policy = $booking_data->cancellation_policy
                ?>
                <div class="alert alert-danger p-3 mt-2" style="font-size: 14px; line-height: normal;">
                    <p><strong><?=T::cancellation?> <?=T::policy?></strong></p>
                    <div class="to--be">

                        <p> <?=$booking_data->cancellation_policy?></p>


                        <div class="read--more">
                            <input class="d-none" type="checkbox" name="" id="show--more">
                            <label class="d-block w-100 fw-bold" for="show--more" id="to--be_1">
                            <?=T::read_more?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#b02a37" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                            </label>
                            <label class="d-none w-100 fw-bold" for="show--more" id="to--be_2">
                            <?=T::read_less?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#b02a37" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 15l-6-6-6 6"/></svg>
                            </label>
                        </div>

                    </div>
                </div>
                <?php } else { $cancellation_policy = ""; }?>

                <div class="col-lg-12">
                    <div class="input-box">
                        <div class="">

                            <div class="d-flex gap-3 alert border">
                            <input class="form-check-input" style="height: 1em !important; width: 1em !important;" type="checkbox" id="agreechb" onchange="document.getElementById('booking').disabled = !this.checked;" <?php if (dev == 1){echo "checked";}?>>
            
                            <label for="agreechb"> I agree to all<a target="_blank" href="<?=root?>page/terms-of-use"> &nbsp; Terms & Condition</a></label>

                            </div>

                        </div>
                    </div>
                </div><!-- end col-lg-12 -->

                <div class="col-lg-12 mb-5">
                    <div class="btn-box mt-3">
                     <button style="height:50px" class="btn btn-primary w-100 btn-lg book" type="submit" id="booking" <?php if (dev == 1){} else{echo "disabled";}?>> <?=T::booking_confirm?></button>
                    </div>
                </div><!-- end col-lg-12 -->

            </div><!-- end col-lg-8 -->
            <div class="col-lg-4">

                <div class="form-box booking-detail-form">
                    <div class="form-title-wrap">
                        <h3 class="title"><?=T::booking?> <?=T::details?></h3>
                    </div><!-- end form-title-wrap -->
                    <div class="form-content">
                        <div class="card-item shadow-none radius-none mb-0">
                            <div class="card-img pb-2">
                                <?php
                                if($booking_data->supplier_name=="hotels"){
                                    (isset($booking_data->hotel_img))?$img = root."uploads/".$booking_data->hotel_img:$img = root."assets/img/hotel.jpg";
                                } else {
                                    $img = $booking_data->hotel_img;
                                }
                                ?>

                             <img class="lazyload" src="<?php if (isset( $img)){ echo  $img; } else { echo root."assets/img/hotel.jpg";} ?>" alt="img">
                            </div>

                            <div class="card-body p-0">
                                <div class="d-flex justify-content-between">
                                    <div>
                                     <?php for ($i = 1; $i <= $booking_data->hotel_stars; $i++) { star(); } ?>

                                        <h3 class="card-title fw-bold text-white"><?=$booking_data->hotel_name?></h3>
                                        <p class="card-meta" style="line-height:18px"><?=$booking_data->hotel_address?></p>
                                    </div>
                                    <!--<div>
                                        <a href="#" class="btn ml-1"><i class="la la-edit" data-toggle="tooltip" data-placement="top" title="Edit"></i></a>
                                    </div>-->
                                </div>

                                <!--<div class="card-rating">
                                    <span class="badge text-white">4.4/5</span>
                                    <span class="review__text">Average</span>
                                    <span class="rating__text">(30 Reviews)</span>
                                </div>-->
                                <div class="section-block"></div>
                                <ul class="list-items list-items-2 py-2">
                                    <li><span><?=T::hotels_checkin?>:</span><?=$booking_data->checkin?></li>
                                    <li><span><?=T::hotels_checkout?>:</span><?=$booking_data->checkout?></li>
                                </ul>
                                <div class="section-block"></div>
                                <h3 class="card-title pt-3 pb-2 font-size-15"> <strong><?=T::room.' '.T::type?></strong> </h3>
                                <div class="section-block"></div>

                                <p class="mt-2"><?=$booking_data->room_type?></p>
                                <ul class="list-items list-items-2 py-3">
                                    <li><span><?=T::rooms?> <?=T::quantity?>:</span><?=$rooms?> <?=T::rooms?></li>
                                    <li><span><?=T::room?> <?=T::price?>:</span><?=$booking_data->currency?> <?=number_format($booking_data->room_price,2);?></li>
                                    <li><span><?=T::adults?>:</span><?=$booking_data->adults?></li>
                                    <li><span><?=T::child?>:</span><?=$booking_data->childs?></li>

                                    <?php
                                    $first_date = new DateTime($booking_data->checkin);
                                    $second_date = new DateTime($booking_data->checkout);
                                    $nights = $first_date->diff($second_date);
                                    ?>

                                    <li><span><?=T::total?> <?=T::nights?> :</span><?=($nights->days);?> <?=T::nights?> </li>

                                </ul>

                                <div class="section-block"></div>
                                <ul class="list-items list-items-2 pt-3">
                                    <li><span><?=T::sub_total?>:</span><?=currency?> <?= number_format( $booking_data->room_price,2) ?> </li>
                                    <!-- <li><span><?=T::service_fee?>:</span><?=currency?> <?=number_format( $booking_data->room_price,2) ?></li> -->
                                    <hr>
                                </ul>

                 <ul class="list-items list-items-2 py-0">

                <?php
                // AGENT FEE ONLY FOR AGENTS
                if(isset ($_SESSION['phptravels_client'])){
                    if(($_SESSION['phptravels_client']->user_type)=="Agent"){
                ?>
                <li class="lh-1 mt-3 d-flex align-items-center"><span><?=T::agent?> Service Fee</span> <input id="agent_fee" class="form-control rounded-1" type="number" style="width:100px" value="" name="agent_fee"></li>

                <?php
                        }
                    }
                ?>

                <!-- <li class="lh-1 mt-0"><span><?=T::price?></span><?=currency?> <?=number_format( $booking_data->room_price,2);?></li>
                <li class="lh-1 mt-3"><span><?=T::vat?></span> (0%)</li> -->

                </ul>
                </div>
                </div>

                </div>

                <div class="form-title-wrap">
                <ul class="row">

                 <li class="col-6 d-flex align-items-center">
                  <strong class="text-uppercase"><?=T::total?></strong>
                 </li>

                <strong class="col-6 d-flex align-items-center h4 m-0"><strong><small class="mx-2"><?=currency?></small></strong>
                    <input name="total_price" class="total" readonly type="text" value="<?=number_format( $booking_data->room_price,2);?>" style="background: transparent; border: 0; color: #fff;">
                </strong>


                <script>
                 // Attach the keyup event handler to the input field
                 $("#agent_fee").on('keyup', function(event) {

                    var sanitizedValue = $(this).val().replace(/[^0-9]/g, '');
                    $("#agent_fee").val(sanitizedValue);

                    var agent_fee = parseFloat(sanitizedValue) || 0; // Default to 0 if not a valid number
                    var total_val = parseFloat("<?=number_format( $booking_data->room_price,2);?>") || 0; // Default to 0 if not a valid number
                    var total = agent_fee + total_val;
                    var final = total.toFixed(2);

                    $(".total").val(final);

                });
                </script>




                        </div><!-- end card-item -->
                    </div><!-- end form-content -->
                </div><!-- end form-box -->
            </div><!-- end col-lg-4 -->

        </div><!-- end row -->
    </div><!-- end container -->
</section><!-- end booking-area -->

<?php

// CHECK IF ADULTS AND CHILDS EXIST IN SESSION
if (isset($_SESSION['hotels_adults'])) { $adults = $_SESSION['hotels_adults']; } else { $adults = 2; }
if (isset($_SESSION['hotels_childs'])) { $childs = $_SESSION['hotels_childs']; } else { $childs = 0; }

// $booking['room_price'] =  $booking_data->room_price;
// $booking['room_quantity'] = $rooms_quatity;
// $booking['total_price'] = $grand_total;
// $booking['total_tax'] = $totaltax;

$booking = json_decode(json_encode($booking_data), true);
$booking['nights'] = ($nights->days);
$booking['currency'] = currency;
$booking['adult_travellers'] = $adults;
$booking['child_travellers'] = $childs;
$booking['cancellation_policy'] = $cancellation_policy;

?>

<input type="hidden" name="payload" value="<?= base64_encode(json_encode($booking)) ?>" />
<input type="hidden" name="form_token" value="<?=$_SESSION["form_token"]?>">
</form>

<!-- ================================
    END BOOKING AREA
================================= -->
</div>
<main>
<script>
$(".book").submit(function() {
$("body").scrollTop(0);
$(".booking_loading").css("display", "block");
$(".booking_data").css("display", "none");
});

// child ages
<?php

// loop for child ages
if(isset($_SESSION['ages'])) {
$ages_ = json_decode($_SESSION['ages']);
foreach ($ages_ as $i => $ages) { ?>

// disable selection of values
$('.child_age_<?=$i+1?>').css('pointer-events','none');
$('.child_age_<?=$i+1?>').css('background','#e9eef2');

// js change select option to this valu
$('.child_age_<?=$i+1?> option[value=<?=$ages->ages?>]')
.attr('selected', 'selected');
<?php } } ?>

</script>

<style>
.form-check{cursor:pointer}
.header-top-bar,.main-menu-content,.info-area,.cta-area{display:none}
.menu-wrapper{display: flex; justify-content: center; padding: 12px;}
.nav-link:focus, .nav-link:hover { color: var(--theme-bg) !important; }

/* cancellation read more  */
.to--be > p { max-height: calc(3.5em + 2px); overflow: hidden; }
.to--be > .read--more { display: none; }
.to--be > .read--more > label { cursor: pointer; }
.to--be:has(:checked) > p { max-height: unset !important; }
.to--be:has(:checked) > .read--more > #to--be_1 { display: none !important; }
.to--be:has(:checked) > .read--more > #to--be_2 { display: block !important; }
header #navbarSupportedContent { display: none !important }
header { height: 80px; }
header .container{ justify-content: center !important; }
.newsletter-section{ display:none}
</style>

<script>
    if( (document.querySelector('.to--be > p').scrollHeight) > (document.querySelector('.to--be > p').offsetHeight) ) {
        document.querySelector('.to--be > .read--more').style.display = "block";
    }
</script>