<link href="https://dimsemenov.com/plugins/royal-slider/royalslider/royalslider.css" rel="stylesheet">
<script src="https://dimsemenov.com/plugins/royal-slider/royalslider/jquery.royalslider.min.js?v=9.3.6"></script>
<link href="https://dimsemenov.com/plugins/royal-slider/royalslider/skins/universal/rs-universal.css?v=1.0.4" rel="stylesheet">

<?php
   (isset($meta['data']))?$tour=$meta['data']:$tour="";
?>

<div class="bg-light">
   <div class="container">

         <nav aria-label="breadcrumb mt-5">
            <ol class="breadcrumb m-0 pt-4 pb-2">
               <li class="breadcrumb-item"><a href="<?=root?>"><?=T::home?></a></li>
               <li class="breadcrumb-item"><a href="<?=root?>tours"><?=T::tours?></a></li>
               <li class="breadcrumb-item"><a href="javascript:void(0)"><?=$tour->location ?? ""?></a></li>
               <li class="breadcrumb-item active"><?=$tour->name ?? ""?></li>
            </ol>
         </nav>

         <div class="h3 fw-bold mb-0 py-1"><?=$tour->name ?? ""?></div>

         <div class="d-md-flex d-sm-block gap-2 align-items-center justify-content-lg-start justify-content-md-center">
            <div class="d-flex gap-1 align-items-center mb-1">
               <div style="margin-top:5px;" class="d-flex">
                  <?php
                     $rating = $tour->rating ?? "";
                     for ($i = 1; $i <= $rating; $i++) { ?>
                  <?= star() ?>
                  <?php } ?>
               </div>
            </div>
            <p class="m-0"><?=T::stars?> |
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="10" r="3"></circle>
            <path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 1 0-16 0c0 3 2.7 6.9 8 11.7z"></path>
            </svg>
            <?=$tour->location?></p>
         </div>

         <div class="row">
         <div class="col-md-8 my-3 order-2 order-lg-1">

         <div id="gallery-2" class="royalSlider rsUni rounded-2 overflow-hidden">
            <?php $imges = $tour->img ?? "";
               foreach ($imges as $img){?>
                  <a class="rsImg" data-rsBigImg="<?=$img?>" href="<?=$img?>" data-rsw="" data-rsh="200">
                  <img width="96" height="72" class="rsTmb" src="<?=$img?>" />
                  </a>
            <?php } ?>
         </div>


      <script>
         jQuery(document).ready(function() {
         $('#gallery-2').royalSlider({
         fullscreen: {
         enabled: true,
         nativeFS: true
         },
         controlNavigation: 'thumbnails',
         thumbs: {
         orientation: 'vertical',
         paddingBottom: 4,
         appendSpan: true
         },
         transitionType:'fade',
         autoScaleSlider: true,
         autoScaleSliderWidth: 960,
         autoScaleSliderHeight: 600,
         loop: true,
         arrowsNav: true,
         keyboardNavEnabled: true

         });
         });

      </script>
      <style>
         #gallery-2 {
         width: 100%;
         -webkit-user-select: none;
         -moz-user-select: none;
         user-select: none;
         }
         #gallery-2 * {
         -webkit-backface-visibility: initial;
         }
      </style>
      <div class="row g-3 d-block mt-4">

         <div class="rounded-2 overflow-hidden bg-white mt-2 px-3 py-4 d-none">

            <div class="row g-0">
               <div class="col-md-12 rounded-2 overflow-hidden p-3 shadow-sm" style="background: url(<?=root?>assets/img/map.png) no-repeat right; background-size: 100%;">
                  <div class="d-block gap-2 align-items-center">
                     <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                           fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round"
                           stroke-linejoin="round">
                           <circle cx="12" cy="10" r="3" />
                           <path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 1 0-16 0c0 3 2.7 6.9 8 11.7z" />
                        </svg>
                     </span>
                     <span class=""><?=$tour->location ?? ""?></span>
                     <a class="text-decoration-none fw-bold px-2" href="#">
                        <?=T::show_on_map?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="16" viewBox="0 0 24 24"
                           fill="none" stroke="#3264ff" stroke-width="2" stroke-linecap="round"
                           stroke-linejoin="round">
                           <path d="M9 18l6-6-6-6" />
                        </svg>
                     </a>
                  </div>
               </div>
            </div>

         </div>

      </div>
      <?php
         $itinerary = $tour->tours_itinerary ?? "";
         if(!empty($itinerary)){ ?>
      <div class="py-4">
         <h4 class="fw-bold"><?=T::tour?> <?=T::itinerary?></h4>
      </div>
      <div class="border rounded-bottom-4 overflow-hidden py-0 tour_itinerary">
         <ul class="list-group list-group-numbered">
            <?php
               $tours_itinerary=($tour->tours_itinerary ?? "");
               foreach($tours_itinerary as $key => $data) { ?>
            <li class="list-group-item d-flex rounded-0 p-0 user-select-none" data-before="<?=$key +1?>">
               <div class="position-relative w-100 ps-4 ps-sm-5 des--label-w">
                  <div class="circle--timeline"></div>
                  <div class="d-flex justify-content-between align-items-center dropdown-toggle des--label-h pe-4" data-bs-toggle="collapse" data-bs-target="#day__<?=$key+1?>">
                     <?php $string=preg_replace('/<(.+?)[\s]*\/?[\s]*>/si', '', $data)?>
                     <span><?=mb_strimwidth($string, 0, 53, "...")?></span>
                  </div>
                  <div class="collapse pe-4" id="day__<?=$key +1?>">
                     <div class="text-muted">
                        <span><?=$data?></span>
                     </div>
                     <div class="rounded-1 overflow-hidden my-3 col--img">
                        <img src="./images/1.jpg" alt="">
                     </div>
                  </div>
               </div>
            </li>
            <?php  } ?>
            </li>
         </ul>
      </div>
      <?php  } ?>
      <!-- Fine Print -->
      <div class="row gx-0 gy-3 bg-white rounded-2 overflow-hidden mt-3 px-3 pb-3">
         <div class="h5 fw-bold mb-0 mb-sm-1"><?=T::policy?></div>
         <hr>
         <div class="col-md-12 mt-0 mt-sm-1">
            <?=$tour->policy ?? ""?>
         </div>
      </div>
      <!-- property Description  -->
      <div class="row gx-0 gy-3 bg-white rounded-2 overflow-hidden mt-3 px-3 pb-3">
         <div class="col-xl-12 h5 fw-bold"><?=T::description?></div>
         <div>
            <?=$tour->desc ?? ""?>
         </div>
      </div>
      <!-- Services & Amenities  -->
      <div class="row gx-0 gy-3 bg-white rounded-2 overflow-hidden mt-3 px-3 pb-3">
      <div class="h5 fw-bold"><?=T::tour ." ". T::inclusions?></div>
         <?php
            // AMENITIES
            $inclusions = $tour->inclusions ?? "";
            if (is_array($inclusions) || is_object($inclusions)) {
            foreach($inclusions as $i) {
                if(!empty($i)){
            ?>
         <div class="col-xl-4">
            <div class="d-flex gap-2 align-items-center">
               <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#00c36c" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                  <polyline points="22 4 12 14.01 9 11.01"></polyline>
               </svg>
               <span><?=$i ?? ""?></span>
            </div>
         </div>
         <?php } } } ?>
      </div>
      <div class="row gx-0 gy-3 bg-white rounded-2 overflow-hidden mt-3 px-3 pb-3">
         <div class="h5 fw-bold"><?=T::tour ." ". T::exclusions?></div>
         <?php
            // AMENITIES
            $exclusions = $tour->exclusions ?? "";
            if (is_array($exclusions) || is_object($exclusions)) {
            foreach($exclusions as $i) {
                if(!empty($i)){
            ?>
         <div class="col-xl-4">
            <div class="d-flex gap-2 align-items-center">
               <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#ff0857" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
               </svg>
               <span><?=$i ?? ""?></span>
            </div>
         </div>
         <?php } } } ?>
      </div>
      <!-- properities nearby  -->
      <?php
         if (isset($_SESSION['related_tours'])){ ?>
      <div class="bg-white rounded-2 overflow-hidden mt-3 px-4 pb-3">
         <div class="pt-3 ps-2 h5 fw-bold"><?php echo "OTHER TOURS" ?></div>
         <ul class="touch_car row g-0 gy-4 list-group list-group-horizontal mt-3">
            <?php
               foreach ($_SESSION['related_tours'] as $item) {
                   (isset($_SESSION['tours_nationality']))?$nationality=$_SESSION['tours_nationality']:$nationality="US";
                   $link = root.'tour/'.$item->tour_id.'/'.
                   $meta['date'].'/'.$meta['adults'].'/'.$meta['childs'].'/'.base64_encode(json_encode($item->supplier));
                   (isset($item->img))?$img = root."uploads/".$item->img:$img = root."assets/img/tour.jpg";
                   ?>
            <li class="col-lg-3 col-md-6 col-12 list-group-item border-0 px-2 py-0">
               <div class="border rounded-2 overflow-hidden">
                  <a class="d-inline-block w-100 text-decoration-none" href="<?=$link?>" >
                     <div>
                        <!-- img  -->
                        <div class="w-100 rounded-top overflow-hidden" style="height: 186px;">
                           <img class="w-100 h-100" src="<?=root?>uploads/<?=$item->img?>" alt=""
                              style="object-fit: cover;">
                        </div>
                        <div class="position-relative px-3">
                           <!-- review  -->
                           <!-- <div class="position-absolute bg-white border border-primary rounded-pill overflow-hidden pe-2"
                              style="top: -14px; left: 8px;">
                              <span
                                  class="d-inline-block bg-primary rounded-pill px-2 h-100 text-white fw-bold">3.6<span
                                      class="text-muted">/5</span></span>
                              <span class="text-primary">23 reviews</span>
                              </div> -->
                           <div class="pt-3">
                              <span class="h6 overflow-hidden" style="white-space: nowrap; text-overflow: ellipsis;"><strong><?=$item->name?></strong></span>
                              <div class="d-block"></div>
                              <div class="text-muted">
                                 <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="10" r="3"/>
                                    <path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 1 0-16 0c0 3 2.7 6.9 8 11.7z"/>
                                 </svg>
                                 <span><?=$item->location?></span>
                              </div>
                           </div>
                           <?php for ($i = 1; $i <= $item->rating; $i++) { ?>
                           <?= star() ?>
                           <?php } ?>
                           <div class="d-flex flex-column align-items-start mt-3 mb-2">
                              <span class="h5 fw-bold m-0 text-dark">
                              <small>
                              <?=$_SESSION['phptravels_client_currency']?>
                              </small>
                              <?=$item->price?>
                              </span>
                           </div>
                        </div>
                     </div>
                  </a>
               </div>
            </li>
            <?php }?>
         </ul>
      </div>
      <?php } ?>

       <!-- <div class="row gx-0 gy-3 bg-white rounded-2 overflow-hidden mt-3 px-3 py-5 text-center">
         <svg class="text-center" xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
         </svg>
         <div class="col-xl-12 h4 fw-bold"><?=T::haven_t_found_the_right_property_yet?></div>
         <div class="col-xl-12 mt-3">
            <a href="<?=root?>page/contact" class="btn btn-primary rounded-1"><?=T::contact_us_now?></a>
         </div>
      </div> -->
       <div class="pb-5"></div>
   </div>

<div class="col-md-4 order-1">
<div class="sticky-top" style="top:100px">
      <?php
         $adult = $tour->b2c_price_adult ?? "";
         if ($adult != 0) {
         $adult_price = $tour->b2c_price_adult ?? "";
         $child_price = $tour->b2c_price_child ?? "";
         } else {
         $adult_price = $tour->b2b_price_adult ?? "";
         $child_price = $tour->b2b_price_child ?? "";
         }
         ?>
      <div class="bg-dark text-white card p-4 h-100 pb-0" style="margin-top: 15px;">
         <div class="mt-2">
            <div class="sidebar-book-title-wrap mb-3 d-flex align-items-end gap-3">
               <p class="m-0"><?=T::from?></p>
               <h3 class="m-0">
                  <span class="text-value ml-2 d-flex align-items-end gap-1"> <small><?=currency?></small>
                  <span class="total">
                  <strong><?=$tour->price ?? ""?></strong>
               </span>
               </h3>
            </div>
         </div>

         <!-- end sidebar-widget-item -->
         <form action="<?=root?>tours/booking" method="post" autocomplete="off">
            <div class="sidebar-widget-item">
               <div class="contact-form-action">
                  <div class="input-box">
                     <div class="form-floating show active text-dark">
                        <input class="dp_tour form-control date_change" type="text" name="date" value=<?=$tour->date ?? ""?>>
                        <label for="checkout">
                           <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                              <line x1="16" y1="2" x2="16" y2="6"></line>
                              <line x1="8" y1="2" x2="8" y2="6"></line>
                              <line x1="3" y1="10" x2="21" y2="10"></line>
                           </svg>
                           <?=T::date?>
                        </label>
                     </div>
                  </div>
               </div>
            </div>
            <!-- end sidebar-widget-item -->
            <?php
               // print_r($tour);
               ?>
            <div class="sidebar-widget-item mt-3 text-white">
               <div class="qty-box mb-3 d-flex align-items-center justify-content-between adult">
                  <label class="text-white mb-0"><strong><?=T::adults?></strong> <span class="m-0 text-white"><?=T::age?> 12+</span></label>
                  <label class="text-white mb-0"><?=T::price?> <span class="m-0 text-white"><?=currency?> <?=$adult_price ?? ""?></span></label>
                  <div class="d-flex align-items-center">
                     <select name="adults" id="adults" class="adults form-select">
                        <?php
                           if (property_exists($tour, 'max_Adults')) {
                               $max_adult = (int) $tour->max_Adults;
                           for ($i=0; $i < $max_adult+1  ; $i++) {
                           ?>
                        <?php
                           $select="";
                           if ($i == $meta['adults']) { $select = "selected"; }?>
                        <option value="<?=$i?>"<?=$select?>><?=$i?></option>
                        <?php } }?>
                     </select>
                  </div>
               </div>
               <!-- end qty-box -->
               <div class="qty-box mb-2 d-flex align-items-center justify-content-between child">
                  <label class="text-white mb-0"><strong><?=T::childs?></strong> <span class="m-0 text-white"><?=T::age?> 12-</span></label>
                  <label class="text-white"><?=T::price?> <span class="m-0 text-white"><?=$_SESSION['phptravels_client_currency']?><?=$child_price ?? ""?></span></label>
                  <div class="d-flex align-items-center">
                     <select name="childs" id="childs" class="childs form-select">
                        <?php
                           if (property_exists($tour, 'max_Child')) {
                           $max_chlid = (int) $tour->max_Child;
                           for ($i=0; $i < $max_chlid+1 ; $i++) { ?>
                        <?php
                           $select="";
                           if ($i == $meta['childs']) {
                              $select = "selected";
                           }?>
                        <option value="<?=$i?>"<?=$select?>><?=$i?></option>
                        <?php } }?>
                     </select>
                  </div>
               </div>
               <!-- end qty-box -->
               <!-- <div class="qty-box mb-2 d-flex align-items-center justify-content-between infant">
                  <label class="font-size-16">Infant <span>Age 4-</span></label>
                  <label class="font-size-16">Price <span>USD 50</span></label>
                  <div class="d-flex align-items-center">
                    <select name="infants" id="infants" class="infants form-select">
                      <option value="">0</option>
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                      <option value="5">5</option>
                      <option value="6">6</option>
                      <option value="7">7</option>
                      <option value="8">8</option>
                      <option value="9">9</option>
                      <option value="10">10</option>
                    </select>
                  </div>
                  </div> -->
               <!-- end qty-box -->
            </div>
            <!-- end sidebar-widget-item -->
             <?php
             if(!empty($tour->redirect)){
             ?>
            <div class="btn-box pt-2">
               <a href="<?=$tour->redirect?>" target="_blank" style="height:50px" class="btn-lg btn btn-primary w-100 ladda effect ladda-button waves-effect d-flex align-items-center justify-content-center" data-style="zoom-in">
               <span class="ladda-label"><?=T::booknow ?></span>
               <span class="ladda-spinner"></span></a>
            </div>

             <?php }else{ ?>
                 <div class="btn-box pt-2">
                     <button type="submit" style="height:50px" class="btn-lg btn btn-primary w-100 ladda effect ladda-button waves-effect" data-style="zoom-in">
                         <span class="ladda-label"><?=T::booknow ?></span>
                         <span class="ladda-spinner"></span></button>
                 </div>
             <?php } ?>
            <hr>

            <p class="m-0"><strong>Reserve Now & Pay Later</strong></p>
            <p>Secure your spot while staying flexible</p>

            <script>
               // create clean cars
               var adult_price = <?= $adult_price ?? ""?>;
               var child_price = <?= $child_price ?? ""?>;
               //   var infant_price = 50;

               // jquery check on change selectbox
               $('#adults,#childs,#infants').on('change', function() {
               var adults = $( "#adults option:selected" ).text();
               var childs = $( "#childs option:selected" ).text();
               var date = $( ".dp_tour.date_change" ).val();
               //   var infants = $( "#infants option:selected" ).text();

               console.log('adults '+ adults);
               console.log('childs '+ childs);
               //   console.log('infants '+ infants);

               // create clean cars and calculate
               a_price = adult_price * adults;
               c_price = child_price * childs;
               //   i_price = infant_price * infants;

               // get all travelers with their pricing
               //   var cost = a_price + c_price + i_price;
               var cost = a_price + c_price;
               $(".total").html(cost);

               const price = [cost,adults,childs,date];
               // add price to hidden input
               document.getElementById("price").value =btoa(JSON.stringify(price));

               // console.log(document.getElementById("price").value);
               });

            </script>
            <?php
               $tour_id = $tour->tour_id ?? "";
               $name = $tour->name ?? "";
               $img = $tour->img[0]?? "";
               $tour_type = $tour->tour_type ?? "";
               $price = $tour->price ?? "";
               $actual_price = $tour->actual_price ?? "";
               $adults = $meta['adults'] ?? "";
               $childs = $meta['childs'] ?? "";
               $date = $meta['date'] ?? "";
               $supplier = $tour->supplier ?? "";
               $location = $tour->location ?? "";
               $latitude = $tour->latitude ?? "";
               $longitude = $tour->longitude ?? "";
               $rating = $tour->rating ?? "";
               $currencycode = $tour->currencycode ?? "";
                  $payload = [
                     "tours_id" => $tour_id,
                     "tours_name" => $name,
                     "tour_img" => $img,
                     "tour_type" => $tour_type,
                     "price" => $price,
                     "actual_price" => $actual_price,
                     "adults" => $adults,
                     "childs" => $childs,
                     "date" => $date,
                     "currency_original" =>$currencycode,
                     "currency_markup" => $_SESSION['phptravels_client_currency'],
                     "supplier" => $supplier,
                     "tour_location" => $location,
                     "tour_latitude" => $latitude,
                     "tour_longitude" => $longitude,
                     "tour_stars" => $rating,
                     "booking_data" => "",
                     "module_type" => 'tours',
                     "cancellation" => ""
                  ];
                  ?>
            <input name="payload" type="hidden" value="<?php echo base64_encode(json_encode($payload)) ?>">
            <input type="hidden" name="price" value=cost id="price">
         </form>
      </div>
      </div>
   </div>
   </div>



</div>
</div>


<style>
   .rsUni, .rsUni .rsOverflow, .rsUni .rsSlide, .rsUni .rsVideoFrameHolder, .rsUni .rsThumbs, #gallery-2 {
   background: #e8ecf0 !important;
   }
   .rsOverflow {float:right;padding-left: 4px;}
   .rsUni .rsThumbsVer {right:none;left:0}
   .rsUni .rsThumb.rsNavSelected .thumbIco {border: 2px solid rgb(9 9 9 / 90%);}
   img.rsImg {width: 100% !important; height: 100% !important; margin: 0 !important; margin-top: 0px !important;}
</style>