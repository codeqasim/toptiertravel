<div id="rooms"></div>
<h4 class="mt-4"><strong><?=T::hotel?> <?=T::rooms?></strong></h4>
<div class="row g-3">
   <?php foreach ($hotel->rooms as $room) {
      // CONDITION TO CHECK IF IMAGE EXIST
      (@getimagesize(root."uploads/".$room->img))?$img = root."uploads/".$room->img:$img = root."assets/img/hotel.jpg";
      ?>
   <?php foreach($room->options as $opt => $options){ ?>
   <div class="col-md-4">
      <div class="bg-white rounded-2 overflow-hidden p-3 h-100">
         <!-- images  -->
         <div class="col-12 col-sm-12">
            <!-- classic room images  -->
            <div class="position-relative rounded-2 h-100 overflow-hidden">
               <div class="row g-0 h-100" id="cltrm">
                  <!-- big iamge  -->
                  <div class="col-md-12">
                     <div class="rounded overflow-hidden h-100">
                        <img data-fancybox="gallery"
                           data-src="<?=$img?>"
                           class="w-100 h-100"
                           src="<?=$img?>" alt="room"
                           style="object-fit: cover;">
                     </div>
                  </div>
               </div>
               <!-- images counter  -->
            </div>
         </div>
         <div class="d-flex align-items-center justify-content-between">
            <p class="text-lg-start text-md-center fw-bold my-3"><strong><?=$room->name?></strong></p>
            <span class="d-inline-block btn btn-outline-primary rounded-1 text-white fw-bold px-3"><?=T::option?> <?=$opt+1?></span>
         </div>
         <hr class="my-1">
         <div class="row my-2">
            <div class="col-md-12 px-3">
               <svg height="20px" width="20px" version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                  viewBox="0 0 512 512"  xml:space="preserve">
                  <style type="text/css">
                     .st0{fill:#000000;}
                  </style>
                  <g>
                     <path class="st0" d="M223.092,102.384c36.408,0,43.7-28.298,43.7-43.732V43.724c0-15.434-7.292-43.724-43.7-43.724
                        c-36.408,0-43.699,28.29-43.699,43.724v14.928C179.393,74.086,186.684,102.384,223.092,102.384z"/>
                     <path class="st0" d="M332.816,204.792l-33.502-54.597c-9.812-16.012-22.516-28.507-40.535-28.507h-27.463h-39.106
                        c-29.406,0-53.24,25.255-53.24,56.404v162.192h35.992V512h34.834l21.521-156.443L252.836,512h34.834V263.901v-40.857l29.47,34.048
                        l55.89-38.978v-32.932L332.816,204.792z"/>
                  </g>
               </svg>
               <?=$options->adults?>
               <svg fill="#000000" width="15px" height="15px" viewBox="-64 0 512 512" xmlns="http://www.w3.org/2000/svg">
                  <path d="M120 72c0-39.765 32.235-72 72-72s72 32.235 72 72c0 39.764-32.235 72-72 72s-72-32.236-72-72zm254.627 1.373c-12.496-12.497-32.758-12.497-45.254 0L242.745 160H141.254L54.627 73.373c-12.496-12.497-32.758-12.497-45.254 0-12.497 12.497-12.497 32.758 0 45.255L104 213.254V480c0 17.673 14.327 32 32 32h16c17.673 0 32-14.327 32-32V368h16v112c0 17.673 14.327 32 32 32h16c17.673 0 32-14.327 32-32V213.254l94.627-94.627c12.497-12.497 12.497-32.757 0-45.254z"/>
               </svg>
               <?=$options->child?>
            </div>
         </div>
         <!-- Modal -->
         <div class="modal fade" id="roomamenities_<?=$room->id?>" data-bs-backdrop="static" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="false">
            <div class="modal-dialog">
               <div class="modal-content">
                  <div class="modal-header">
                     <h1 class="modal-title fs-5" id="staticBackdropLabel"><?=T::amenities?></h1>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                     <?php foreach($room->amenities as $i) {?>
                     <div class="d-flex align-items-center">
                        <span class="fw-bold"><?=$i?></span>
                     </div>
                     <?php } ?>
                  </div>
                  <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=T::close?></button>
                  </div>
               </div>
            </div>
         </div>
         <form action="<?=root?>hotels/booking" method="POST">
            <!-- 1  -->
            <?php if ($options->breakfast==1){?>
            <div class="text-primary">
               <span class="text-decoration-underline fw-bold gap-2 d-flex align-items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                     <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                     <polyline points="22 4 12 14.01 9 11.01"></polyline>
                  </svg>
                  <?=T::free_breakfast?>
               </span>
            </div>
            <?php } ?>
            <?php if ($options->cancellation==1){?>
            <div class="text-primary">
               <span class="text-decoration-underline fw-bold gap-2 d-flex align-items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                     <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                     <polyline points="22 4 12 14.01 9 11.01"></polyline>
                  </svg>
                  <?=T::free_cancellation?>
               </span>
            </div>
            <?php } ?>
            <div style="cursor:pointer" class="text-primary" data-bs-toggle="modal" data-bs-target="#roomamenities_<?=$room->id?>">
               <span class="text-decoration-underline fw-bold gap-2 d-flex align-items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                     <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                     <polyline points="22 4 12 14.01 9 11.01"></polyline>
                  </svg>
                  <?=T::amenities?>
               </span>
            </div>
            <!-- show more  -->
            <hr class="my-1 mt-3">
            <div class="row g-2">
               <div class="col-md-6">
                  <select name="room_quantity" class="form-select px-4 py-2 mt-2 h-100 border" style="height:52px">
                     <?php
                        (isset($options->quantity))?$quantity=$options->quantity:$quantity=1;
                        for (
                        $i = 1;
                        $i <= $quantity;
                        $i++){
                           
                        ?>
                     <option class="" value="<?=$i?>"><?=$i?> - <?=currency?> <?=$i * $options->price ?></option>
                     <?php } ?>
                  </select>
               </div>
               <div class="col-md-6">
                  <button type="submit" class="btn btn-primary fw-bold w-100 mt-2 h-100" type="button">
                     <!-- <?=currency?> <?=$options->price?>
                        <small class="d-block"><?=T::booknow?></small> -->
                     <?=T::booknow?>
                  </button>
               </div>
            </div>
            <?php
               $payload = [
               "supplier_name" => $hotel->supplier_name,
               "hotel_id" => $hotel->id,
               "hotel_name" => $hotel->name,
               "hotel_img" => $hotel->img[0],
               "hotel_address" => $hotel->city . "&nbsp;" . $hotel->address,
               "hotel_stars" => $hotel->stars,
               "room_id" => $room->id,
               "room_type" => $room->name,
               "currency" => currency,
               "room_price" => $options->price,
               "real_price" => $options->price,
               "checkin" => $meta['checkin'],
               "checkout" => $meta['checkout'],
               "adults" => $meta['adults'],
               "childs" => $meta['childs'],
               "supplier" => $hotel->supplier_name,
               "nationality" => $meta['nationality'],
               "city_name" => $hotel->city,
               "latitude" => $hotel->latitude,
               "longitude" => $hotel->longitude,
               "booking_data" => $options,
               "adult_travellers" => $hotel->hotel_phone,
               "child_travellers" => $hotel->hotel_phone,
               "hotel_phone" => $hotel->hotel_phone,
               "hotel_email" => $hotel->hotel_email,
               "hotel_website" => $hotel->hotel_website,
               "children_ages" => "",
               "cancellation_policy" => $hotel->cancellation,
               "room_data" => $room->options[0],
               ];

               ?>
            <input name="payload" type="hidden" value="<?php echo base64_encode(json_encode($payload)) ?>">
         </form>
      </div>
   </div>
   <!-- ROOM ENDING -->
   <?php } ?>
   <?php } ?>
</div>