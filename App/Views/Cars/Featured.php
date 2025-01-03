<!-- ================================
START CAR FEATURED AREA
================================= -->
<?php
if(!empty(base()->featured_cars)){
?>
<div data-aos="fade-up" class="py-5 bg-white">
<div class="container">
   <section class="hotel-area section-bg section-padding pb-0">
      <div class="row">
         <div class="col-lg-12">
            <div class="section-heading text-start">
               <h5 class="mb-4"><strong><?=base()->featured_cars?T::cars_rentalbestcarstoday:''?></strong></h5>
            </div>
         </div>
      </div>
      <div class="row padding-top-10px">
         <div class="col-lg-12">
            <div class="hotel-card-wrap">
               <div class="row">
                  <div class="col-lg-3 col-12 col-md-12">
                     <div class="shadow-sm rounded card-item p-2">
                        <div class="card-img">
                           <a href="<?=root?>cars" class="d-block" tabindex="0">
                              <img src="<?=root?>assets/img/featured_cars.png" class="w-100" alt="hotel-img" style="min-height:330px">
                              <div class="pt-5" style="border-bottom-right-radius: 6px; border-bottom-left-radius: 6px; position: absolute; width: 100%; z-index: 9; display: block; padding: 25px 20px 5px; color: #fff; left: 0; bottom: 0; height: 164px; background: transparent; background: linear-gradient(to bottom,transparent,var(--theme-bg)); box-sizing: border-box;">
                                 <p class="strong text-center" style="line-height:20px"><strong><?=T::discover_great_deals_transfers?></strong></p>
                                 <span class="btn btn-block btn-outline-light w-100">
                                 <?=T::view_more?>
                                 </span>
                              </div>
                           </a>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-9">
                     <div class="row featured--cars-slick">
                        <?php foreach (base()->featured_cars as $cars){ {
                           $date=date_create("4 days");
                           $name = str_replace(' ', '-', $cars->name);
                           ?>
                        <div class="col-md-4 mb-3 px-1">
                           <form class="" style="display:inline;" action="<?=root?>cars/booking" method="post">
                              <!--<input name="payload" type="hidden" value="<?php //echo base64_encode(json_encode($payload))?>">-->
                              <div class="">
                                 <div class="card-img">
                                    <div class="d-block">
                                    <img src="<?=root?>uploads/<?=$cars->img?>" class="w-100 p-3" alt="car-img" style="height:200px">
                                    </div>
                                 </div>
                                 <div class="card-body p-4 pt-0">
                                    <p class="m-0 lenght-cover">
                                       <div class="text-dark"><strong><?=$cars->name?></strong></div>
                                    </p>
                                    <div class="stars">
                                       <?php for ($i = 1; $i <= $cars->stars; $i++) { ?>
                                       <svg class="stars" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                          <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                       </svg>
                                       <?php } ?>
                                    </div>
                                    <p class="card-meta m-0">
                                       <!-- <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                          <circle cx="12" cy="10" r="3"></circle>
                                          <path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 1 0-16 0c0 3 2.7 6.9 8 11.7z"></path>
                                          </svg>     -->
                                       <?=$cars->location?>
                                    </p>
                                    <div class="#card-rating">
                                       <span class="text-white">
                                       </span>
                                    </div>
                                    <div class="card-price d-flex align-items-center justify-content-between">
                                       <span class="price__num"><strong><?=currency?> <?=$cars->price?></strong></span>
                                       <!-- <span class="price__text"><?=T::price?> </span> -->
                                        <input type="hidden" name="car_id" value="<?=$cars->id?>">
                                        <input type="hidden" name="car_name" value="<?=$cars->name?>">
                                        <input type="hidden" name="car_img" value="<?=root?>uploads/<?=$cars->img?>">
                                        <input type="hidden" name="price" value="<?=$cars->price?>">
                                        <input type="hidden" name="actual_price" value="<?=$cars->price?>">
                                        <input type="hidden" name="adults" value="<?=$cars->adult?>">
                                        <input type="hidden" name="childs" value="<?=$cars->child?>">
                                        <input type="hidden" name="date" value="<?=date_format($date,"d-m-Y")?>">
                                        <input type="hidden" name="currency" value="<?=$cars->currency?>">
                                        <input type="hidden" name="supplier" value="<?=$cars->supplier?>">
                                        <input type="hidden" name="car_location" value="<?=$cars->location?>">
                                        <input type="hidden" name="car_stars" value="<?=$cars->stars?>">
                                        <input type="hidden" name="booking_data" value="">
                                        <input type="hidden" name="module_type" value="cars">
                                        <input type="hidden" name="user_data" value="">
                                        <input type="hidden" name="cancellation" value="">
                                       <button type="submit" class="btn btn-outline-dark fadeout"><?=T::booknow?>  </button>
                                    </div>
                                 </div>
                              </div>
                           </form>
                        </div>
                        <?php } } ?>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>
</div>
</div>
<?php } ?>
<!-- ================================
   END CAR FEATURED AREA
   ================================= -->
<style>
   @media screen and (max-width: 425px) {
   .hotel-area > .container > :first-child { padding: 20px !important }
   }
</style>