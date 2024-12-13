<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<script src="
https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js
"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<script src="
https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js
"></script>
<section data-anim-wrap class="masthead -type-7 pt-0">
   <div class="masthead-slider js-masthead-slider-7">
      <div class="swiper-wrapper">
         <?php
            // Array containing the data for each slide
            $slides = [
               [
                  'image' => root.'assets/img/slider/1.jpg',
                  'subtitle' => 'Discover amazing places at exclusive deals',
                  'title' => 'Unique Houses Are Waiting<br class="lg:d-none"> For You'
               ],
               [
                  'image' => root.'assets/img/slider/2.jpg',
                  'subtitle' => 'Explore luxurious destinations effortlessly',
                  'title' => 'Find Your Perfect<br class="lg:d-none"> Getaway'
               ],
               [
                  'image' => root.'assets/img/slider/3.jpg',
                  'subtitle' => 'Uncover hidden gems with exclusive offers',
                  'title' => 'Start Your Journey<br class="lg:d-none"> Today'
               ]
            ];

            // Loop through the slides and generate the HTML
            foreach ($slides as $slide) {
               echo '
               <div class="swiper-slide">
                  <div class="row justify-center text-center">
                        <div class="col-auto">
                           <div class="masthead__content">
                              <div class="masthead__bg">
                                    <img src="' . $slide['image'] . '" alt="image">
                              </div>
                              <div data-anim-child="slide-up delay-1" class="text-white">
                                    ' . $slide['subtitle'] . '
                              </div>
                              <h1 data-anim-child="slide-up delay-2" class="text-60 lg:text-40 md:text-30 text-white">
                                    ' . $slide['title'] . '
                              </h1>
                           </div>
                        </div>
                  </div>
               </div>';
            }
            ?>
      </div>
      <div class="masthead-slider__nav -prev js-prev">
         <button class="button -outline-white text-white size-50 rounded-full">
         <i class="icon-arrow-left"></i>
         </button>
      </div>
      <div class="masthead-slider__nav -next js-next">
         <button class="button -outline-white text-white size-50 rounded-full">
         <i class="icon-arrow-right"></i>
         </button>
      </div>
   </div>
   <div data-anim-child="fade delay-3" class="mainSearch -w-900 bg-white rounded-100 px-20 py-20 lg:px-20 lg:pt-5 lg:pb-20 shadow-2 mt-40">
      <div class="--button-grid items-center">
         <div class="main_search">
            <div class="tab-content">
             <?php include "Search.php"; ?>
            </div>
         </div>
      </div>
   </div>
</section>
<?php include "Featured.php"; ?>
<style>
.select2-container--default .select2-selection--single .select2-selection__rendered {
/* padding: 10px 7px !important; */
}
</style>