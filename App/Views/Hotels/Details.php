<!-- slick  -->
<link rel="stylesheet" type="text/css" href="<?=root?>assets/plugins/slick/slick.css"/>
<script src="<?=root?>assets/plugins/slick/slick.js"></script>
<?php $gMapKey = "AIzaSyBvPooGV84U2zlu--JO8IQQvKDakc_VJ6k" ?>

<!-- fancybox -->
<script src="<?=root?>assets/plugins/fancybox/fancybox.umd.js"></script>
<link rel="stylesheet" href="<?=root?>assets/plugins/fancybox/fancybox.css"/>
<?php (isset($meta['data']))?$hotel=$meta['data']:$hotel=""; ?>

<div class="bg-light pt-4">
   <div class="container">
      <nav aria-label="breadcrumb">
         <ol class="breadcrumb d-none d-lg-flex d-md-flex">
            <li class="breadcrumb-item"><a href="<?=root?>"><?=T::home?></a></li>
            <li class="breadcrumb-item"><a href="<?=root?>hotels"><?=T::hotels?></a></li>
            <li class="breadcrumb-item"><a href="#"><?=$hotel->city?></a></li>
            <li class="breadcrumb-item active"><?=$hotel->name?></li>
         </ol>
      </nav>
      <div class="rounded-2 overflow-hidden bg-white mt-2 px-3 py-4">
         <div class="row">
            <!-- left  -->
            <div class="col-xl-8 col-md-8 col-sm-12 mb-3">
               <div class="d-md-flex d-sm-block gap-1 align-items-center justify-content-lg-start justify-content-md-center">
                  <div class="h4 fw-bold mb-0"><strong><?=$hotel->name?></strong></div>
                  <!-- star and thumb  -->
                  <div class="d-flex gap-1 align-items-center mb-1">
                     <div style="margin-top:5px;" class="d-flex">
                        <?php for ($i = 1; $i <= $hotel->stars; $i++) { ?>
                        <?= star() ?>
                        <?php } ?>
                     </div>
                  </div>
               </div>
               <!-- location and map  -->
               <div class="d-flex gap-1 align-items-center">
                  <span class="mb-1">
                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="10" r="3" />
                        <path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 1 0-16 0c0 3 2.7 6.9 8 11.7z" />
                     </svg>
                  </span>
                  <span class="text--overflow">
                  <?=$hotel->address?>, <?=$hotel->city?>, <?=$hotel->country?>
                  </span>
               </div>
            </div>
            <div class="col-xl-4 col-md-4">
               <div class="d-flex justify-content-end align-items-start h-100">
                  <div class="d-flex gap-1 align-items-center">
                     <span class="h3 fw-bold"></span>
                  </div>
                  <a class="d-none d-lg-flex d-md-flex w-50 btn btn-primary rounded-2 py-3 fw-bold d-flex align-items-center justify-content-center gap-2" href="#rooms">
                     <?=T::select_room?>
                     <svg class="" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 9l6 6 6-6"></path>
                     </svg>
                  </a>
               </div>
            </div>
         </div>
         <!-- carasoul -->
         <div class="col-md-12">
            <ul class="carasoul px-0 m-0">
              <?php
              foreach ($hotel->img as $img){
                  $imgUrl = (@getimagesize($img)) ? $img : root . "uploads/" . $img;
                  $imgSrc = (@getimagesize($imgUrl)) ? $imgUrl : root . "uploads/" . $img;
              ?>

              <li data-src="<?=$imgUrl?>" data-fancybox="gallery" class="carasoul--img">
                  <img src="<?=$imgSrc?>" alt="">
              </li>

              <?php } ?>

               <div class="carasoul--icon imgvalue py-1 text-white">
                  <span class="current"></span>
                  <span class="actual"></span>
               </div>
               <button class="carasoul--btn btn--left" type="button" onclick="showImage(-1)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                     fill="none" stroke="white" stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round">
                     <path d="M15 18l-6-6 6-6" />
                  </svg>
               </button>
               <button class="carasoul--btn btn--right" type="button" onclick="showImage(1)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                     fill="none" stroke="white" stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round">
                     <path d="M9 18l6-6-6-6" />
                  </svg>
               </button>
            </ul>
         </div>
         <div class="col-md-12">
            <div class="row mt-3 g-0">
               <div class="col-md-12 bg-light rounded-2 overflow-hidden p-3 shadow-sm" style="background: url(<?=root?>assets/img/map.png) no-repeat right; background-size: 100%;">
                  <div class="d-block gap-2 align-items-center">
                     <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                           fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round"
                           stroke-linejoin="round">
                           <circle cx="12" cy="10" r="3" />
                           <path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 1 0-16 0c0 3 2.7 6.9 8 11.7z" />
                        </svg>
                     </span>
                     <span class=""><?=$hotel->address?>, <?=$hotel->city?>, <?=$hotel->country?> </span>
                     <a class="text-decoration-none fw-bold px-2" href="#map">
                       <strong> <?=T::show_on_map?></strong>
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
      <!-- rooms  -->

      <?php include "rooms.php" ?>

      <!-- property Description  -->
      <div class="row gx-0 gy-3 bg-white rounded-2 overflow-hidden mt-3 px-3 pb-3">
         <div class="col-xl-12 h5 fw-bold"><?=T::property_description?></div>
         <hr>

         <div>
            <?=htmlspecialchars_decode($hotel->desc)?>
         </div>
      </div>
      
      <div class="row gx-0 gy-3 bg-white rounded-2 overflow-hidden mt-3 px-3 pb-3">
         <div class="h5 fw-bold mb-0 mb-sm-1"><?=T::hotel_policy?></div>
         <div class="fw-bold col-md-3 text-sm-start"><?=T::Check_in_and_check_out?></div>
         <div class="col-md-3">
            <span class=""><?=T::from?> <strong><?=$hotel->checkin?></strong> <?=T::to?> <strong><?=$hotel->checkout?></strong> </span>
          </div>
         <hr />
         <div class="col-md-3 fw-bold"><?=T::age_requirements?></div>

         <?php
         if (empty($hotel->booking_age_requirement)) {
            $age = 12;
         } else {
            $age = $hotel->booking_age_requirement;
         }
         ?>
         <div class="col-md-9">The guest checking in must be at least <strong><?=$age?></strong> years old.</div>
         <hr />

         <?php
         if (empty($hotel->policy)) {
            $policy_ = "Our hotel is dedicated to ensuring a comfortable and enjoyable experience for all guests. We maintain a strict no-smoking policy throughout the premises, and pets are not permitted. We value the safety of our guests and have 24/7 security and surveillance in place. To promote a serene environment, we request all guests to keep noise levels to a minimum, especially during nighttime hours. We appreciate your cooperation in adhering to these policies and look forward to providing you with a pleasant stay";
         } else {
            $policy_ = $hotel->policy;
         }
         ?>

         <div class="col-md-3 fw-bold"><?=T::policy?></div>
         <div class="col-md-9 mt-0 mt-sm-1">
            <?=htmlspecialchars_decode($policy_)?>
         </div>
         <hr />

         <?php
         if (empty($hotel->cancellation)) {
            $cancellation_ = "Our hotel cancellation policy aims to provide flexibility and convenience to our valued guests. Reservations can be canceled up to 2 days prior to the check-in date without incurring any charges. For cancellations made within 2 days of the check-in date or in case of a no-show, a fee equivalent to 5% of the total reservation amount will be charged. We understand that plans can change, and we strive to accommodate your needs while maintaining the quality of our services. Thank you for considering for your stay.";
         } else {
            $cancellation_ = $hotel->cancellation;
         }
         ?>

         <div class="col-md-3 fw-bold"><?=T::cancellation?></div>
         <div class="col-md-9 mt-0 mt-sm-1">
            <?=htmlspecialchars_decode($cancellation_)?>
         </div>
      </div>

      <!-- Services & Amenities  -->
      <div class="row gx-0 gy-3 bg-white rounded-2 overflow-hidden mt-3 px-3 pb-3">
         <div class="h5 fw-bold"><?=T::services_amenities?></div>
         <hr>

         <?php
            // AMENITIES
            foreach($hotel->amenities as $i) {
                if(!empty($i)){
            ?>
         <div class="col-xl-6 mt-0">
            <div class="d-flex gap-2 align-items-center">
               <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                  <polyline points="22 4 12 14.01 9 11.01"></polyline>
               </svg>
               <span><?=$i?></span>
            </div>
         </div>
         <?php } } ?>
      </div>
      <!-- properities nearby  -->
      <?php  if (isset($_SESSION['related_hotels'])){ ?>
      <div class="bg-white rounded-2 overflow-hidden mt-3 px-4 pb-3">
         <div class="pt-3 ps-2 h5 fw-bold"><?=T::properties_nearby?></div>
         <ul class="touch_car row g-0 gy-4 list-group list-group-horizontal mt-3">
            <?php
               foreach ($_SESSION['related_hotels']->data as $item) {
                   (isset($_SESSION['hotels_nationality']))?$nationality=$_SESSION['hotels_nationality']:$nationality="US";
                   $link = root.'hotel/'.$item->hotel_id.'/'.
                   clean_var($item->name).'/'.
                   $meta['checkin'].'/'.$meta['checkout'].'/'.$meta['rooms'].'/'.$meta['adults'].'/'.$meta['childs'].'/'.$nationality;
//                   (isset($item->img))?$img = root."uploads/".$item->img:$img = root."assets/img/hotel.jpg";
                   ?>
            <li class="col-lg-3 col-md-6 col-12 list-group-item border-0 px-2 py-0">
               <div class="border rounded-2 overflow-hidden">
                  <a class="d-inline-block w-100 text-decoration-none" href="<?=$link?>" >
                     <div>
                        <!-- img  -->
                        <div class="w-100 rounded-top overflow-hidden" style="height: 186px;">
                            <?php
                            if($item->supplier_name=="hotels"){
                                (isset($item->img))?$img = root."uploads/".$item->img:$img = root."assets/img/hotel.jpg";
                            } else {
                                $img = $item->img;
                            }
                            ?>
                           <img class="w-100 h-100" src="<?=$img?>" alt=""
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
                           <?php for ($i = 1; $i <= $item->stars; $i++) { ?>
                           <?= star() ?>
                           <?php } ?>
                           <div class="d-flex flex-column align-items-start mt-3 mb-2">
                              <span class="h5 fw-bold m-0 text-dark">
                              <small>
                              <?=$_SESSION['phptravels_client_currency']?>
                              </small>
                              <?=$item->markup_price?>
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
      <!-- Recently Viewed -->
      <div class="row gx-1 gy-0 bg-white rounded-2 overflow-hidden mt-3 px-3 pb-3">
         <div class="h5 fw-bold mt-4 mb-3"><?=T::recently_viewed?></div>
         <hr>

         <?php
            $max = 12;
            $max_print = count(array_unique($_SESSION['HOTEL_DETAILS'], SORT_REGULAR));
            krsort($_SESSION['HOTEL_DETAILS']);
            $data=(array_unique($_SESSION['HOTEL_DETAILS'], SORT_REGULAR));
            foreach($data as $d){
            (isset($_SESSION['hotels_nationality']))?$nationality=$_SESSION['hotels_nationality']:$nationality="US";
            ?>
         <div class="col-md-4 mb-1">
            <div class="bg-light p-3 rounded-2 overflow-hidden">
               <div class="d-flex gap-2">
                  <div class="rounded-2 overflow-hidden" style="height: 70px; width: 104px;">
                      <?php
                      if($d->supplier_name=="hotels"){
                          (isset($d->img[0]))?$img = root."uploads/".$d->img[0]:$img = root."assets/img/hotel.jpg";
                      } else {
                          $img = $d->img[0];
                      }
                      ?>
                     <img class="w-100" style="height:100%" src="<?=$img?>" alt="" style="object-fit: cover;">
                  </div>
                  <a class="w-100" href="<?=root?>hotel/<?=$d->id?>/<?=clean_var($d->name)?>/<?=date('d-m-Y',strtotime('+3 day')).'/'.date('d-m-Y',strtotime('+4 day')).'/1/2/0/'.$nationality.'/'.$d->supplier_name;?>">
                     <div class="h6 mb-1 text--overflow"><strong><?=$d->name?></strong></div>
                     <div class="h6 mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                           <circle cx="12" cy="10" r="3"></circle>
                           <path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 1 0-16 0c0 3 2.7 6.9 8 11.7z"></path>
                        </svg>
                        <?=$d->city?>, <?=$d->country?>
                     </div>
                     <?php

                     $stars = isset($d->stars) ? $d->stars : "";

                     for ($i = 1; $i <= $stars; $i++) { ?>
                     <?= star() ?>
                     <?php } ?>
                  </a>
               </div>
            </div>
         </div>
         <?php } ?>
      </div>

      <div class="row gx-1 gy-0 bg-white rounded-2 overflow-hidden mt-3 px-3 pt-3 p-5">

      <div class="h5 fw-bold mt-0 mb-3"><?=T::adress_on_map?></div>
      <div id='map'></div>
      </div>

      <?php
      $cords = explode (",", $hotel->longitude);
      ?>

      <script>

function initMap() {
    var mapOpts = {
        center: {
         lat: <?=$cords[0]?>,
         lng: <?=$cords[1]?>
        },
        zoom: 13,
        mapTypeId: google.maps.MapTypeId.TERRAIN,
        styles: [{
            "featureType": "road.local",
            "stylers": [{
                "weight": 4.5
            }]
        }]
    };
    var map = new google.maps.Map(document.getElementById('map'), mapOpts);
    var bicyclayer = new google.maps.BicyclingLayer();
    bicyclayer.setMap(map);
    var infowincontent = '<div style="width:200px">CONTENT</div>';
    var marker0 = new google.maps.Marker({
        position: {
         lat: <?=$cords[0]?>,
         lng: <?=$cords[1]?>
        },
        map: map,
        title: 'Old Highway 80 Overpass',
        animation: google.maps.Animation.DROP
    });
    var infowindow0 = new google.maps.InfoWindow({
        content: infowincontent.replace('CONTENT', 'Be careful of traffic on 80. Visibility is poor around the bends, but there are good shoulders further on. Clinton has gas stations, restaurants, and hotels. Loads of people commute on the Trace from Clinton to Ridgeland, so don\'t bike this during rush hour.')
    });
    marker0.addListener('click', function() {
        infowindow0.open(map, marker0)
    });

}

   </script>

      <style>
         #map{height:400px !important;width:100%}
      </style>

      <script async defer src="https://maps.googleapis.com/maps/api/js?callback=initMap&key=<?=$gMapKey?>"></script>

      <!-- Haven't found the right property -->
      <div class="row gx-0 gy-3 bg-white rounded-2 overflow-hidden mt-3 px-3 py-5 text-center">
         <svg class="text-center" xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
         </svg>
         <div class="col-xl-12 h4 fw-bold"><?=T::haven_t_found_the_right_property_yet?></div>
         <div class="col-xl-12 mt-3">
         <a href="<?=root?>page/contact" class="btn btn-primary rounded-1"><?=T::contact_us_now?></a>
         </div>
      </div>
      <!-- simple container end   -->

      <div class="pb-5"></div>
   </div>
</div>

<script>
   const totalImages = document.querySelectorAll(".carasoul > .carasoul--img");
   const imagesLength = totalImages.length;
   let index = 0;

   let imgCountAct = document.querySelector(".imgvalue > .actual");
   let imgCountCurr = document.querySelector(".imgvalue > .current");

   imgCountCurr.textContent = "1";
   imgCountAct.textContent = `/${imagesLength}`;

   function showImage(imgVal) {
   totalImages.forEach((li) => {
       li.style.display = "none";
   });

   index += imgVal;

   if (index === imagesLength) {
       index = 0;
   }
   if (index === -1) {
       index += imagesLength;
   }

   totalImages[index].style.display = "block";
   imgCountCurr.textContent = `${index + 1} `;
   }

   // top slider
   Fancybox.bind(document.querySelector('.carasoul'), '[data-fancybox="gallery"]', {
   Toolbar: {
       display: {
       left: ["infobar"],
       middle: [
           "zoomIn",
           "zoomOut",
           "toggle1to1",
       ],
       right: ["close"],
       },
   }
   });

   // rooms images slider

   // classic twin
   Fancybox.bind(document.getElementById('cltrm') ,'[data-fancybox="gallery"]', {
   Toolbar: {
       display: {
       left: ["infobar"],
       middle: [
           "zoomIn",
           "zoomOut",
           "toggle1to1",
       ],
       right: ["close"],
       },
   }
   });

   // classic king room
   Fancybox.bind(document.getElementById('clkr') ,'[data-fancybox="gallery"]', {
   Toolbar: {
       display: {
       left: ["infobar"],
       middle: [
           "zoomIn",
           "zoomOut",
           "toggle1to1",
       ],
       right: ["close"],
       },
   }
   });

   // twin club room
   Fancybox.bind(document.getElementById('tcr') ,'[data-fancybox="gallery"]', {
   Toolbar: {
       display: {
       left: ["infobar"],
       middle: [
           "zoomIn",
           "zoomOut",
           "toggle1to1",
       ],
       right: ["close"],
       },
   }
   });

   // premium room

   Fancybox.bind(document.getElementById('prmumr') ,'[data-fancybox="gallery"]', {
   Toolbar: {
       display: {
       left: ["infobar"],
       middle: [
           "zoomIn",
           "zoomOut",
           "toggle1to1",
       ],
       right: ["close"],
       },
   }
   });

   // near by propertity
   $(".touch_car").slick({
       infinite: true,
       speed: 300,
       slidesToShow: 4,
       slidesToScroll: 1,
       responsive: [
           {
           breakpoint: 1025,
           settings: {
               autoplay: false,
               slidesToShow: 4,
               slidesToScroll: 1,
           },
           },
           {
           breakpoint: 769,
           settings: {
               autoplay: false,
               arrows: false,
               slidesToShow: 2,
               slidesToScroll: 1,
           },
           },
           {
           breakpoint: 480,
           settings: {
               arrows: false,
               slidesToShow: 1,
               slidesToScroll: 1,
           },
           },
       ],
   });

</script>