<?php
   if (!empty(base()->featured_flights)){?>
<div data-aos="fade-up" class="py-5 pb-0">
<div class="container">
   <section class="round-trip-flight mb-4">
      <div class="row">
         <div class="col-lg-12">
            <div class="section-heading text-end">
               <div class="text-start">
               <h4 class="mt-1 mb-0"><strong><?=T::flights_featured_flights?></strong></h4>
               <p><?=T::these_alluring_destinations_are_picked_just_for_you?></p>
               </div>
               <div class="mb-4"></div>
            </div>
            <!-- end section-heading -->
         </div>
         <!-- end col-lg-12 -->
      </div>
      <!-- end row -->
      <div class="row padding-top-0px">
         <div class="col-lg-12">
            <div class="popular-round-trip-wrap padding-top-10px">
               <div class="tab-content" id="myTabContent4">
                  <div class="tab-pane fade show active" id="" role="" aria-labelledby="">
                     <div class="row g-3">
                        <!-- <div class="col-md-3">
                           <div class="shadow-sm rounded card-item p-2">
                              <div class="card-img">
                                 <a href="<?=root?>flights" class="d-block" tabindex="0">
                                    <img src="<?=root?>assets/img/featured_flights.png" class="w-100" alt="hotel-img">
                                    <div class="pt-5" style="border-bottom-right-radius: 6px; border-bottom-left-radius: 6px; position: absolute; width: 100%; z-index: 9; display: block; padding: 25px 20px 5px; color: #fff; left: 0; bottom: 0; height: 164px; background: transparent; background: linear-gradient(to bottom,transparent,#005cff); box-sizing: border-box;">
                                       <h6 class="strong text-center"><strong><?=T::find_the_next_flight_for_your_trip?></strong></h6>
                                       <span class="btn btn-block btn-outline-light w-100">
                                       <?=T::view_more?>
                                       </span>
                                    </div>
                                 </a>
                              </div>
                           </div>
                        </div> -->
                        <div class="">
                           <div class="row g-3">
                              <?php
                                 foreach (base()->featured_flights as $flights){

                                 // $from = explode(" ", $flights->from);
                                 // $froms = end($from);

                                 // $to = explode(" ", $flights->to);
                                 // $tos = end($to);

                                 // // get flights codes
                                 // $from_code = explode(' ',trim($flights->from));
                                 // $to_code = explode(' ',trim($flights->to));

                                 ?>
                              <!-- <script>
                                 $.ajax({
                                 type: "GET",
                                 url: "https://www.kayak.com/mvm/smartyv2/search?f=j&s=airportonly&where=<?=strtolower($flights->origin)?>",
                                 cache: false,
                                 success: function(data){
                                 if (typeof data[0].destination_images !== 'undefined') {
                                 var flight_bg = data[0].destination_images.image_jpeg;
                                 } else {
                                     var flight_bg = "./uploads/none.jpg";
                                 }
                                     $('.featured_flight_<?=$flights->id?>').append('<img style="object-fit: cover;" class="w-100 h-100" src='+flight_bg+' />').hide().fadeIn(500);
                                 }
                                 });

                              </script> -->

                              <div class="col-xl-4 col-12">
                              <a class=" hover-primary rounded-3 d-flex p-3 px-4 fadeout" href="<?=root?>flights/<?=strtolower($flights->origin_code)?>/<?=strtolower($flights->destination_code)?>/oneway/economy/<?php $d=strtotime("+5 Days"); echo date("d-m-Y", $d);?>/1/0/0">

                              <div class="col-5 d-flex flex-column">
                                 <p class="m-0 text-hover"><strong><?=$flights->origin?></strong></p>
                                 <p class="m-0 text-muted fw-lighter text-truncate"><small> <?=$flights->airline_name?> <? // =T::from?></strong> </small></p>
                                 </div>
                                 <div class="col-2 d-flex flex-column justify-content-center align-items-center">
                                 <div style="background: linear-gradient(to top, #2980b9, #D4E8FF);" class="vr mx-auto my-auto"></div>

                                 <svg class="plane-svg rounded-5 my-1" width="22" height="22" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 -7 12 24"><path d="m.991 6.037-.824.825 2.887 1.237 1.237 2.888.825-.825-.412-2.063 1.932-1.932 2.106 4.494.781-.781-.694-5.906 1.65-1.65a1.167 1.167 0 1 0-1.65-1.65L7.136 2.369 1.23 1.673l-.738.739 4.459 2.14L3.054 6.45.991 6.037Z"></path></svg>

                                 <div style="background: linear-gradient(to top, #D4E8FF, #2980b9);" class="vr mx-auto my-auto"></div>
                                 </div>
                                 <div class="col-5 d-flex flex-column align-items-end">
                                 <p class="m-0 text-hover"><strong><?=$flights->destination?></strong></p>
                                 <p class="m-0 text-muted fw-lighter"><small><?=currency?> <?=$flights->price?></small></p>
                                 </div>
                                 </a>
                                 </div>

                                 <!-- <?=$flights->id?> -->
                                 <!-- <img src="<?=airline_logo($flights->airline)?>" alt="air-line-img" class="lazyload px-3" style="width: 60px;height: 40px;"> -->
                                 <!-- <h6 class="text--overflow"><strong><?=$flights->airline_name?></strong></h6> -->



                              <?php } ?>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- end tab-pane -->
               </div>
               <!-- end tab-content -->
               <div class="tab-content-info d-flex justify-content-between align-items-center">
               </div>
               <!-- end tab-content-info -->
            </div>
         </div>
         <!-- end col-lg-12 -->
      </div>
      <!-- end row -->
   </section>
</div>
</div>
<!-- end container -->
<?php } ?>