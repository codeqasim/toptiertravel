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
   <ul class="nav nav-tabs p-0 d-none" id="tab" role="tablist">
      <?php
         $keys = array_column($module_status, 'order');
         array_multisort($keys, SORT_ASC, $module_status);

         // echo "<pre>";
         // print_r($module_status);
         // echo "</pre>";

         foreach ($module_status as $module){
         ?>
      <?php if (isset($module->type)){ if ($module->type == "hotels"){ ?>
      <li class="nav-item" role="presentation">
         <button class="nav-link w-100" data-bs-toggle="tab" data-bs-target="#tab-hotels" type="button" role="tab"
            aria-controls="home-tab-pane" aria-selected="true">
            <svg viewBox="0 0 24 24" width="25" height="25" fill="currentColor" class="block" data-v-ee08ca94="">
               <path d="M14.655 3.75a.675.675 0 0 1 .67.59l.005.085h2.595A2.175 2.175 0 0 1 20.1 6.6v12.067a1.425 1.425 0 0 1-1.425 1.425H5.107c-.75 0-1.357-.607-1.357-1.357v-7.966a2.228 2.228 0 0 1 2.047-2.242v-.015a.675.675 0 0 1 1.345-.085l.005.085v.007h2.738v-1.92a2.175 2.175 0 0 1 2.047-2.17v-.004a.675.675 0 0 1 1.345-.085l.006.085h.697a.674.674 0 0 1 .675-.675Zm-4.77 6.12H5.97a.877.877 0 0 0-.545.196l-.073.067a.879.879 0 0 0-.251.63v7.972c0 .003.003.007.007.007h4.778V9.87h-.001Zm2.712-4.096h-.537a.825.825 0 0 0-.825.826v12.142h2.063v-1.305a1.425 1.425 0 0 1 1.313-1.42l.111-.005h.548c.788 0 1.425.638 1.425 1.425v1.304l1.98.001a.07.07 0 0 0 .052-.022l.017-.023.006-.03V6.6a.825.825 0 0 0-.825-.825h-3.27l-.01-.001h-2.048Zm2.673 11.588h-.547a.075.075 0 0 0-.075.075v1.304h.697v-1.304a.075.075 0 0 0-.023-.052l-.023-.017-.029-.006Zm-6.758-.99a.675.675 0 0 1 .085 1.345l-.085.005h-2.04a.676.676 0 0 1-.084-1.345l.084-.005h2.04Zm0-2.76a.675.675 0 0 1 .085 1.345l-.085.005h-2.04a.676.676 0 0 1-.084-1.345l.084-.005h2.04Zm5.46-.322a.675.675 0 0 1 .085 1.345l-.085.005h-1.364a.676.676 0 0 1-.085-1.345l.085-.005h1.364Zm3.406 0a.675.675 0 0 1 .084 1.345l-.084.005h-1.366a.676.676 0 0 1-.084-1.345l.084-.005h1.366Zm-8.866-2.438a.675.675 0 0 1 .085 1.345l-.085.005h-2.04a.676.676 0 0 1-.084-1.345l.084-.005h2.04Zm5.46-.292a.675.675 0 0 1 .085 1.345l-.085.005h-1.364a.676.676 0 0 1-.085-1.345l.085-.005h1.364Zm3.406 0a.675.675 0 0 1 .084 1.345l-.084.005h-1.366a.676.676 0 0 1-.084-1.345l.084-.005h1.366Zm-3.405-2.723a.675.675 0 0 1 .084 1.345l-.085.005h-1.364a.675.675 0 0 1-.085-1.344l.085-.006h1.364Zm3.405 0a.675.675 0 0 1 .084 1.345l-.084.005h-1.366a.675.675 0 0 1-.084-1.344l.084-.006h1.366Z" fill-rule="evenodd"></path>
            </svg>
            <!-- <svg fill="" width="20" height="20" viewBox="0 0 56 56" xmlns="http://www.w3.org/2000/svg">
               <path d="M 5.2892 21.9935 L 10.9031 21.9935 L 10.9031 18.8154 C 10.9031 16.7507 12.0630 15.6372 14.1508 15.6372 L 22.3861 15.6372 C 24.4739 15.6372 25.6338 16.7507 25.6338 18.8154 L 25.6338 21.9935 L 30.6446 21.9935 L 30.6446 18.8154 C 30.6446 16.7507 31.8045 15.6372 34.0084 15.6372 L 41.7333 15.6372 C 43.9373 15.6372 45.0970 16.7507 45.0970 18.8154 L 45.0970 21.9935 L 50.7108 21.9935 L 50.7108 15.6604 C 50.7108 11.5544 48.5305 9.4665 44.5402 9.4665 L 11.4598 9.4665 C 7.4930 9.4665 5.2892 11.5544 5.2892 15.6604 Z M 0 44.8668 C 0 46.0035 .7423 46.7226 1.9022 46.7226 L 3.2013 46.7226 C 4.3380 46.7226 5.0803 46.0035 5.0803 44.8668 L 5.0803 41.5726 C 5.3355 41.6422 6.0779 41.6886 6.6114 41.6886 L 49.4118 41.6886 C 49.9454 41.6886 50.6647 41.6422 50.9198 41.5726 L 50.9198 44.8668 C 50.9198 46.0035 51.6619 46.7226 52.7988 46.7226 L 54.1210 46.7226 C 55.2579 46.7226 56 46.0035 56 44.8668 L 56 31.6670 C 56 27.4682 53.6573 25.1716 49.4118 25.1716 L 6.5883 25.1716 C 2.3430 25.1716 0 27.4682 0 31.6670 Z" />
               </svg> -->
            <span><?=T::hotels?></span>
         </button>
      </li>
      <?php } } ?>
      <?php if (isset($module->type)){ if ($module->type == "flights"){ ?>
      <li class="nav-item" role="presentation">
         <button class="nav-link w-100" data-bs-toggle="tab" data-bs-target="#tab-flights" type="button"
            role="tab" aria-controls="profile-tab-pane" aria-selected="false">
            <svg style="transform: rotate(90deg);" viewBox="0 0 24 24" width="25" height="25" fill="currentColor" class="block" data-v-ee08ca94="">
               <path d="M5.557 5.565c.45-.45.713-.435 1.163-.06l.105.09a.75.75 0 0 1 .112.105l.255.255 3 3.293a.667.667 0 0 0 .675.195l1.988-.555a.682.682 0 0 0 .48-.75l-.045-.165a.376.376 0 0 1 0-.09l.075-.105c.067-.075.135-.158.21-.233l.113-.105c.12-.12.247-.127.33-.052l.682.682a.667.667 0 0 0 .66.173l2.37-.675a1.013 1.013 0 0 1 .982.217l.06.06h-.052l-6.105 2.82a.676.676 0 0 0-.217 1.065l3.217 3.525a.667.667 0 0 0 .75.158l1.5-.698a.188.188 0 0 1 .248.038.173.173 0 0 1 0 .217L15 18.098l-.082.097a.165.165 0 0 1-.233.045.172.172 0 0 1-.068-.195l.075-.135.69-1.5a.668.668 0 0 0-.157-.75l-3.518-3.217a.674.674 0 0 0-1.072.217l-2.85 6.09-.045-.052h-.038a1.012 1.012 0 0 1-.202-.96l.682-2.385a.667.667 0 0 0-.172-.66l-.698-.705a.187.187 0 0 1 0-.263l.12-.127a2.36 2.36 0 0 1 .24-.218l.105-.075h.18a.674.674 0 0 0 .863-.45l.57-2.01a.683.683 0 0 0-.195-.682l-3.293-3-.187-.18a1.92 1.92 0 0 1-.465-.63c-.09-.24 0-.45.3-.788h.007Zm10.373 13.5 3.082-3.075a1.5 1.5 0 0 0 .24-1.965l-.06-.09a1.5 1.5 0 0 0-1.875-.435l-1.035.473-2.25-2.475 5.25-2.438h.06a1.328 1.328 0 0 0 .33-2.205l-.044-.105-.128-.09a2.318 2.318 0 0 0-2.198-.45l-1.95.54-.42-.427a1.56 1.56 0 0 0-2.182.082 3.761 3.761 0 0 0-.75.863v.075a.668.668 0 0 0-.06.24v.165l-1.012.277-2.806-3.052-.18-.188a4.337 4.337 0 0 0-.36-.285 2.002 2.002 0 0 0-3 .15 1.995 1.995 0 0 0-.6 2.25l.045.105c.23.474.563.889.975 1.215l3 2.753-.3 1.035h-.165a.646.646 0 0 0-.307.097 3.54 3.54 0 0 0-.75.585l-.24.248a1.553 1.553 0 0 0 .06 2.047l.435.443-.563 1.987a2.325 2.325 0 0 0 .533 2.25l.052.053A1.327 1.327 0 0 0 9 19.365v-.067l2.43-5.25 2.475 2.25-.473 1.035.068-.083a1.516 1.516 0 1 0 2.453 1.778" fill-rule="evenodd"></path>
            </svg>
            <span><?=T::flights?></span>
         </button>
      </li>
      <?php } } ?>
      <?php if (isset($module->type)){ if ($module->type == "tours"){ ?>
      <li class="nav-item" role="presentation">
         <button class="nav-link w-100" data-bs-toggle="tab" data-bs-target="#tab-tours" type="button" role="tab"
            aria-controls="profile-tab-pane" aria-selected="false">
            <svg viewBox="0 0 24 24" width="25" height="25" fill="currentColor" class="block" data-v-ee08ca94="">
               <path d="M12 3a3.376 3.376 0 0 1 3.351 3H16.5a2.25 2.25 0 0 1 2.25 2.25v3.095A3.001 3.001 0 0 1 21 14.25v2.25a1.5 1.5 0 0 1-1.5 1.5h-.75a3 3 0 0 1-3 3h-7.5a3 3 0 0 1-3-3H4.5a1.5 1.5 0 0 1-1.496-1.388L3 16.5v-2.25a3 3 0 0 1 2.25-2.902V8.25A2.25 2.25 0 0 1 7.5 6h1.146A3.375 3.375 0 0 1 12 3Zm5.25 9-.997.75a3.75 3.75 0 0 1-2.002.742l-.001.758a.75.75 0 0 1-1.495.088l-.005-.088v-.75h-1.5v.75a.75.75 0 0 1-1.495.088l-.005-.088v-.758a3.75 3.75 0 0 1-1.838-.625l-.165-.117L6.75 12v6a1.5 1.5 0 0 0 1.388 1.496l.112.004h7.5a1.5 1.5 0 0 0 1.5-1.5v-6Zm-3 4.5a.75.75 0 0 1 .088 1.495L14.25 18h-4.5a.75.75 0 0 1-.087-1.495l.087-.005h4.5Zm4.5-3.548V16.5h.75v-2.25a1.5 1.5 0 0 0-.683-1.258l-.066-.04Zm-13.5-.001-.056.033a1.5 1.5 0 0 0-.69 1.153l-.004.113v2.25h.75v-3.549ZM16.5 7.5h-9a.75.75 0 0 0-.75.75v1.875l1.898 1.425a2.25 2.25 0 0 0 1.102.436v-.736a.75.75 0 0 1 1.495-.088l.005.088V12h1.5v-.75a.75.75 0 0 1 1.495-.088l.005.088v.736a2.25 2.25 0 0 0 .97-.344l.132-.092 1.898-1.425V8.25a.75.75 0 0 0-.663-.745L16.5 7.5Zm-4.5-3c-.911 0-1.67.65-1.84 1.493L10.158 6h3.68l-.025-.104a1.876 1.876 0 0 0-1.69-1.392L12 4.5Z"></path>
            </svg>
            <span><?=T::tours?></span>
         </button>
      </li>
      <?php } } ?>
      <?php if (isset($module->type)){ if ($module->type == "cars"){ ?>
      <li class="nav-item" role="presentation">
         <button class="nav-link w-100" data-bs-toggle="tab" data-bs-target="#tab-cars" type="button" role="tab"
            aria-controls="profile-tab-pane" aria-selected="false">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
               <path fill-rule="evenodd" clip-rule="evenodd" d="M6.77988 6.77277C6.88549 6.32018 7.28898 6 7.75372 6H16.2463C16.711 6 17.1145 6.32018 17.2201 6.77277L17.7398 9H17H7H6.26019L6.77988 6.77277ZM2 11H2.99963C2.37194 11.8357 2 12.8744 2 14V15C2 16.3062 2.83481 17.4175 4 17.8293V20C4 20.5523 4.44772 21 5 21H6C6.55228 21 7 20.5523 7 20V18H17V20C17 20.5523 17.4477 21 18 21H19C19.5523 21 20 20.5523 20 20V17.8293C21.1652 17.4175 22 16.3062 22 15V14C22 12.8744 21.6281 11.8357 21.0004 11H22C22.5523 11 23 10.5523 23 10C23 9.44772 22.5523 9 22 9H21C20.48 9 20.0527 9.39689 20.0045 9.90427L19.9738 9.77277L19.1678 6.31831C18.851 4.96054 17.6405 4 16.2463 4H7.75372C6.35949 4 5.14901 4.96054 4.8322 6.31831L4.02616 9.77277L3.99548 9.90426C3.94729 9.39689 3.51999 9 3 9H2C1.44772 9 1 9.44772 1 10C1 10.5523 1.44772 11 2 11ZM7 11C5.34315 11 4 12.3431 4 14V15C4 15.5523 4.44772 16 5 16H6H18H19C19.5523 16 20 15.5523 20 15V14C20 12.3431 18.6569 11 17 11H7ZM6 13.5C6 12.6716 6.67157 12 7.5 12C8.32843 12 9 12.6716 9 13.5C9 14.3284 8.32843 15 7.5 15C6.67157 15 6 14.3284 6 13.5ZM16.5 12C15.6716 12 15 12.6716 15 13.5C15 14.3284 15.6716 15 16.5 15C17.3284 15 18 14.3284 18 13.5C18 12.6716 17.3284 12 16.5 12Z" fill=""/>
            </svg>
            <span><?=T::cars?></span>
         </button>
      </li>
      <?php } } ?>
      <?php if (isset($module->type)){ if ($module->type == "visa"){ ?>
      <li class="nav-item" role="presentation">
         <button class="nav-link w-100" data-bs-toggle="tab" data-bs-target="#tab-visa" type="button" role="tab"
            aria-controls="profile-tab-pane" aria-selected="false">
            <svg style="fill:#fff!important;stroke:var(--theme-bg)!important" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
               <path d="M4 6V19C4 20.6569 5.34315 22 7 22H17C18.6569 22 20 20.6569 20 19V9C20 7.34315 18.6569 6 17 6H4ZM4 6V5" stroke-width="2"/>
               <circle cx="12" cy="13" r="3" stroke-width="1.5"/>
               <path d="M18 6.00002V6.75002H18.75V6.00002H18ZM15.7172 2.32614L15.6111 1.58368L15.7172 2.32614ZM4.91959 3.86865L4.81353 3.12619H4.81353L4.91959 3.86865ZM5.07107 6.75002H18V5.25002H5.07107V6.75002ZM18.75 6.00002V4.30604H17.25V6.00002H18.75ZM15.6111 1.58368L4.81353 3.12619L5.02566 4.61111L15.8232 3.0686L15.6111 1.58368ZM4.81353 3.12619C3.91638 3.25435 3.25 4.0227 3.25 4.92895H4.75C4.75 4.76917 4.86749 4.63371 5.02566 4.61111L4.81353 3.12619ZM18.75 4.30604C18.75 2.63253 17.2678 1.34701 15.6111 1.58368L15.8232 3.0686C16.5763 2.96103 17.25 3.54535 17.25 4.30604H18.75ZM5.07107 5.25002C4.89375 5.25002 4.75 5.10627 4.75 4.92895H3.25C3.25 5.9347 4.06532 6.75002 5.07107 6.75002V5.25002Z" />
               <path d="M10 19H14" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <span><?=T::visa?></span>
         </button>
      </li>
      <?php } } ?>
      <?php } ?>
   </ul>
   <div data-anim-child="fade delay-3" class="mainSearch rounded-100 -w-900 bg-white px-20 py-20 lg:px-20 lg:pt-5 lg:pb-20 shadow-2 mt-40">
      <div class="--button-grid items-center">
         <!-- <div class="searchMenu-loc px-30 lg:py-20 lg:px-0 js-form-dd js-liverSearch">
            <div data-x-dd-click="searchMenu-loc">
              <h4 class="text-15 fw-500 ls-2 lh-16">Location</h4>

              <div class="text-15 text-light-1 ls-2 lh-16">
                <input autocomplete="off" type="search" placeholder="Where are you going?" class="js-search js-dd-focus" />
              </div>
            </div>


            <div class="searchMenu-loc__field shadow-2 js-popup-window" data-x-dd="searchMenu-loc" data-x-dd-toggle="-is-active">
              <div class="bg-white px-30 py-30 sm:px-0 sm:py-15 rounded-4">
                <div class="y-gap-5 js-results">

                  <div>
                    <button class="-link d-block col-12 text-left rounded-4 px-20 py-15 js-search-option">
                      <div class="d-flex">
                        <div class="icon-location-2 text-light-1 text-20 pt-4"></div>
                        <div class="ml-10">
                          <div class="text-15 lh-12 fw-500 js-search-option-target">London</div>
                          <div class="text-14 lh-12 text-light-1 mt-5">Greater London, United Kingdom</div>
                        </div>
                      </div>
                    </button>
                  </div>

                  <div>
                    <button class="-link d-block col-12 text-left rounded-4 px-20 py-15 js-search-option">
                      <div class="d-flex">
                        <div class="icon-location-2 text-light-1 text-20 pt-4"></div>
                        <div class="ml-10">
                          <div class="text-15 lh-12 fw-500 js-search-option-target">New York</div>
                          <div class="text-14 lh-12 text-light-1 mt-5">New York State, United States</div>
                        </div>
                      </div>
                    </button>
                  </div>

                  <div>
                    <button class="-link d-block col-12 text-left rounded-4 px-20 py-15 js-search-option">
                      <div class="d-flex">
                        <div class="icon-location-2 text-light-1 text-20 pt-4"></div>
                        <div class="ml-10">
                          <div class="text-15 lh-12 fw-500 js-search-option-target">Paris</div>
                          <div class="text-14 lh-12 text-light-1 mt-5">France</div>
                        </div>
                      </div>
                    </button>
                  </div>

                  <div>
                    <button class="-link d-block col-12 text-left rounded-4 px-20 py-15 js-search-option">
                      <div class="d-flex">
                        <div class="icon-location-2 text-light-1 text-20 pt-4"></div>
                        <div class="ml-10">
                          <div class="text-15 lh-12 fw-500 js-search-option-target">Madrid</div>
                          <div class="text-14 lh-12 text-light-1 mt-5">Spain</div>
                        </div>
                      </div>
                    </button>
                  </div>

                  <div>
                    <button class="-link d-block col-12 text-left rounded-4 px-20 py-15 js-search-option">
                      <div class="d-flex">
                        <div class="icon-location-2 text-light-1 text-20 pt-4"></div>
                        <div class="ml-10">
                          <div class="text-15 lh-12 fw-500 js-search-option-target">Santorini</div>
                          <div class="text-14 lh-12 text-light-1 mt-5">Greece</div>
                        </div>
                      </div>
                    </button>
                  </div>

                </div>
              </div>
            </div>
            </div>





            <div class="searchMenu-date px-30 lg:py-20 lg:px-0 js-form-dd js-calendar js-calendar-el">
            <div data-x-dd-click="searchMenu-date">
              <h4 class="text-15 fw-500 ls-2 lh-16">Check in - Check out</h4>

              <div class="capitalize text-15 text-light-1 ls-2 lh-16">
                <span class="js-first-date">Wed 2 Mar</span>
                -
                <span class="js-last-date">Fri 11 Apr</span>
              </div>
            </div>


            <div class="searchMenu-date__field shadow-2" data-x-dd="searchMenu-date" data-x-dd-toggle="-is-active">
              <div class="bg-white px-30 py-30 rounded-4">
                <div class="elCalendar js-calendar-el-calendar"></div>
              </div>
            </div>
            </div>


            <div class="searchMenu-guests px-30 lg:py-20 lg:px-0 js-form-dd js-form-counters">

            <div data-x-dd-click="searchMenu-guests">
              <h4 class="text-15 fw-500 ls-2 lh-16">Guest</h4>

              <div class="text-15 text-light-1 ls-2 lh-16">
                <span class="js-count-adult">2</span> adults
                -
                <span class="js-count-child">1</span> childeren
                -
                <span class="js-count-room">1</span> room
              </div>
            </div>


            <div class="searchMenu-guests__field shadow-2" data-x-dd="searchMenu-guests" data-x-dd-toggle="-is-active">
              <div class="bg-white px-30 py-30 rounded-4">
                <div class="row y-gap-10 justify-between items-center">
                  <div class="col-auto">
                    <div class="text-15 fw-500">Adults</div>
                  </div>

                  <div class="col-auto">
                    <div class="d-flex items-center js-counter" data-value-change=".js-count-adult">
                      <button class="button -outline-blue-1 text-blue-1 size-38 rounded-4 js-down">
                        <i class="icon-minus text-12"></i>
                      </button>

                      <div class="flex-center size-20 ml-15 mr-15">
                        <div class="text-15 js-count">2</div>
                      </div>

                      <button class="button -outline-blue-1 text-blue-1 size-38 rounded-4 js-up">
                        <i class="icon-plus text-12"></i>
                      </button>
                    </div>
                  </div>
                </div>

                <div class="border-top-light mt-24 mb-24"></div>

                <div class="row y-gap-10 justify-between items-center">
                  <div class="col-auto">
                    <div class="text-15 lh-12 fw-500">Children</div>
                    <div class="text-14 lh-12 text-light-1 mt-5">Ages 0 - 17</div>
                  </div>

                  <div class="col-auto">
                    <div class="d-flex items-center js-counter" data-value-change=".js-count-child">
                      <button class="button -outline-blue-1 text-blue-1 size-38 rounded-4 js-down">
                        <i class="icon-minus text-12"></i>
                      </button>

                      <div class="flex-center size-20 ml-15 mr-15">
                        <div class="text-15 js-count">1</div>
                      </div>

                      <button class="button -outline-blue-1 text-blue-1 size-38 rounded-4 js-up">
                        <i class="icon-plus text-12"></i>
                      </button>
                    </div>
                  </div>
                </div>

                <div class="border-top-light mt-24 mb-24"></div>

                <div class="row y-gap-10 justify-between items-center">
                  <div class="col-auto">
                    <div class="text-15 fw-500">Rooms</div>
                  </div>

                  <div class="col-auto">
                    <div class="d-flex items-center js-counter" data-value-change=".js-count-room">
                      <button class="button -outline-blue-1 text-blue-1 size-38 rounded-4 js-down">
                        <i class="icon-minus text-12"></i>
                      </button>

                      <div class="flex-center size-20 ml-15 mr-15">
                        <div class="text-15 js-count">1</div>
                      </div>

                      <button class="button -outline-blue-1 text-blue-1 size-38 rounded-4 js-up">
                        <i class="icon-plus text-12"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </div>
            <div class="button-item">
            <button class="mainSearch__submit button -blue-1 py-20 px-35 col-12 rounded-100 bg-dark-4 text-white">
              <i class="icon-search text-20 mr-10"></i>
              Search
            </button>
            </div> -->
         <div class="main_search">
            <div class="tab-content">
               <?php
                  $modules = [
                  'hotels' => "./App/Views/Hotels/Search.php",
                  'flights' => "./App/Views/Flights/Search.php",
                  'tours' => "./App/Views/Tours/Search.php",
                  'cars' => "./App/Views/Cars/Search.php",
                  'visa' => "./App/Views/Visa/Search.php"
                  ];

                  $keys = array_column($module_status, 'order');
                  array_multisort($keys, SORT_ASC, $module_status);

                  foreach ($module_status as $m) {
                  if (isset($m->type) && isset($modules[$m->type])) {
                  ?>
               <div class="tab-pane fade" id="tab-<?php echo $m->type; ?>" role="tabpanel" tabindex="0">
                  <?php require_once $modules[$m->type]; ?>
               </div>
               <?php
                  }
                  }
                  ?>
            </div>
         </div>
      </div>
   </div>
</section>
<script>
   $(".main_search ul li:first-child button").addClass("active");
   $(".main_search .tab-content div:first-child").addClass("show active");
</script>
<!--
   <svg style="position: relative;bottom: 0;left: 0;width: 100%;height: 50px;fill: #fff;z-index: 100;margin-top: -78px;"
   class="hero-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 10" preserveAspectRatio="none">
   <path d="M0 10 0 0 A 90 59, 0, 0, 0, 100 0 L 100 10 Z"></path>
   </svg>  -->
<?php
   // Define the types of modules and their corresponding view paths
   $module_views = [
       'hotels' => "App/Views/Hotels/Featured.php",
       'flights' => "App/Views/Flights/Featured.php",
       'tours' => "App/Views/Tours/Featured.php",
       'cars' => "App/Views/Cars/Featured.php",
       'extra_Blog' => "App/Views/Blog/Featured.php"
   ];

   // Loop through module status array and include corresponding views
   foreach ($module_status as $m) {
       if (isset($m->type) && isset($module_views[$m->type])) {
           include $module_views[$m->type];
       }
   }
   ?>
<!--
   <div data-aos="fade-up" class="home-body-container mb-3 mt-0 pb-5">
   <div class="container">
      <div class="pb-1 mt-0 info-area info-bg text-center rounded-4">
         <div class="row">
            <div class="col-lg-4">
               <div class="icon-box">
                  <div class="info-icon">
                     <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                     </svg>
                  </div>
                  <div class="mt-4 text-white">
                     <h4 class="future-text mb-0"><strong><?=T::hero_sub1?></strong></h4>
                     <p class="info__desc">
                        <?=T::hero_sub1_?>
                     </p>
                  </div>
               </div>
            </div>
            <div class="col-lg-4">
               <div class="icon-box">
                  <div class="info-icon">
                     <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                        <line x1="4" y1="22" x2="4" y2="15"></line>
                     </svg>
                  </div>
                  <div class="mt-4 text-white">
                     <h4 class="future-text mb-0"><strong><?=T::hero_sub2?></strong></h4>
                     <p class="info__desc">
                        <?=T::hero_sub2_?>
                     </p>
                  </div>
               </div>
            </div>
            <div class="col-lg-4">
               <div class="icon-box">
                  <div class="info-icon">
                     <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                           d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3">
                        </path>
                     </svg>
                  </div>
                  <div class="mt-4 text-white">
                     <h4 class="future-text mb-0"><strong><?=T::hero_sub3?></strong></h4>
                     <p class="info__desc">
                        <?=T::hero_sub3_?>
                     </p>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   </div>
   -->
<!-- <script>
   AOS.init();
   </script> -->
<section class="layout-pt-md layout-pb-md">
   <div class="container">
      <div class="row justify-center text-center">
         <div class="col-auto">
            <div class="sectionTitle -md">
               <h2 class="sectionTitle__title">Why Choose Us</h2>
               <p class=" sectionTitle__text mt-5 sm:mt-0">These popular destinations have a lot to offer</p>
            </div>
         </div>
      </div>
      <div class="row y-gap-40 justify-between pt-50">
         <div class="col-lg-3 col-sm-6">
            <div class="featureIcon -type-1 ">
               <div class="d-flex justify-center">
                  <img src="#" data-src="<?=root?>assets/img/featureIcons/1/1.svg" alt="image" class="js-lazy">
               </div>
               <div class="text-center mt-30">
                  <h4 class="text-18 fw-500">Best Price Guarantee</h4>
                  <p class="text-15 mt-10">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
               </div>
            </div>
         </div>
         <div class="col-lg-3 col-sm-6">
            <div class="featureIcon -type-1 ">
               <div class="d-flex justify-center">
                  <img src="#" data-src="<?=root?>assets/img/featureIcons/1/2.svg" alt="image" class="js-lazy">
               </div>
               <div class="text-center mt-30">
                  <h4 class="text-18 fw-500">Easy & Quick Booking</h4>
                  <p class="text-15 mt-10">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
               </div>
            </div>
         </div>
         <div class="col-lg-3 col-sm-6">
            <div class="featureIcon -type-1 ">
               <div class="d-flex justify-center">
                  <img src="#" data-src="<?=root?>assets/img/featureIcons/1/3.svg" alt="image" class="js-lazy">
               </div>
               <div class="text-center mt-30">
                  <h4 class="text-18 fw-500">Customer Care 24/7</h4>
                  <p class="text-15 mt-10">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
<section class="section-bg rounded-4 overflow-hidden">
   <div class="section-bg__item -left-100 -right-100 bg-blue-2"></div>
   <div class="section-bg__item col-4 -right-100 lg:d-none">
      <img src="<?=root?>assets/img/hb.jpg" alt="image">
   </div>
   <div class="container">
      <div class="row">
         <div class="col-xl-6 col-lg-8 p-5">
            <div class="pt-120 pb-120 lg:pt-80 lg:pb-80">
               <h2 class="text-30 fw-600">Our Vission</h2>
               <p class="mt-5">IF YOU CAN DREAM IT, WE CAN MAKE IT HAPPEN</p>
               <div class="overflow-hidden pt-60 lg:pt-40 js-section-slider" data-slider-cols="base-1">
                  <div class="swiper-wrapper">
                     <div class="swiper-slide">
                        <div class="testimonials -type-2">
                           <img src="<?=root?>assets/img/misc/quote.svg" alt="quote" class="mb-35">
                           <div class="text-22 md:text-18 fw-600 text-dark-1">"Trust in our expertise to turn your travel dreams into a seamless reality. With meticulous planning & attention to detail, we ensure that every aspect of your journey exceeds your wildest expectations.
                              "
                           </div>
                           <!-- <div class="d-flex items-center mt-35">
                              <img src="<?=root?>assets/img/happy.jpg" alt="image" class="size-80">
                              <div class="ml-20">
                                <h5 class="text-15 lh-11 fw-500">Ali Tufan</h5>
                                <div class="text-14 lh-11 mt-5">Product Manager, Apple Inc</div>
                              </div>
                              </div> -->
                        </div>
                     </div>
                     <div class="swiper-slide">
                        <div class="testimonials -type-2">
                           <img src="img/misc/quote.svg" alt="quote" class="mb-35">
                           <div class="text-22 md:text-18 fw-600 text-dark-1">"Our family was traveling via bullet train between cities in Japan with our luggage - the location for this hotel made that so easy. Agoda price was fantastic."</div>
                           <div class="d-flex items-center mt-35">
                              <img src="<?=root?>assets/img/avatars/testimonials/1.png" alt="image" class="size-70">
                              <div class="ml-20">
                                 <h5 class="text-15 lh-11 fw-500">Ali Tufan</h5>
                                 <div class="text-14 lh-11 mt-5">Product Manager, Apple Inc</div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
<section data-anim="slide-up delay-1" class="layout-pt-md layout-pb-md">
   <div class="container p-0">
      <div class="row ml-0 mr-0 items-center justify-between">
         <div class="col-xl-5 px-0">
            <img class="col-12 h-400" src="<?=root?>assets/img/dep.jpg" alt="image">
         </div>
         <div class="col px-0">
            <div class="d-flex justify-center flex-column h-400 px-80 py-40 md:px-30 bg-green-1">
               <div class="icon-newsletter text-60 sm:text-40 text-dark-1"></div>
               <h2 class="text-30 sm:text-24 lh-15 mt-20">Your Travel Journey Starts Here</h2>
               <p class="text-dark-1 mt-5">Sign up and we'll send the best deals to you</p>
               <div class="row my-3">
                  <div class="col-6">
                     <div class="form-floating">
                        <input type="text" placeholder=" " name="name" value="" class=" bg-white h-60 newsletter_name form-control">
                        <label for=""><?=T::name?></label>
                     </div>
                  </div>
                  <div class="col-6">
                     <div class="form-floating">
                        <input type="bg-white h-60 text" placeholder=" " name="email" value="" class=" bg-white h-60 newsletter_email form-control">
                        <label for=""><?=T::email?></label>
                     </div>
                  </div>
                  <div class="col-12 col-md-6">
                     <button class="mt-3 subscribe button -md h-60 -blue-1 bg-yellow-1 text-dark-1"><?=T::signup?> <?=T::newsletter?></button>
                     <div class="loading_button" style="display:none">
                        <button style="height:58px !important"
                           class="loading_button btn btn-outline-primary w-100 h-100"
                           type="button" disabled>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </button>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
<script>
   $('.subscribe').on('click', function() {

      // GETTING VALUES
      var newsletter_name = $('.newsletter_name').val();
      var newsletter_email = $('.newsletter_email').val();

      // VALIDATION
      if (newsletter_name == ""){
         alert("<?=T::please_add_your?> <?=T::name?>");

         } else {

         // VALIDATION
         if (newsletter_email == ""){
               alert("<?=T::please_add_your?> <?=T::email?>");

               } else {

               // LOADING ANIMATION
               $('.subscribe').hide();
               $('.loading_button').show();

               var form = new FormData();

               form.append("name", newsletter_name);
               form.append("email", newsletter_email);

               var settings = {
                  "url": "<?=api_url?>newsletter-subscribe",
                  "method": "POST",
                  "timeout": 0,
                  "processData": false,
                  "mimeType": "multipart/form-data",
                  "contentType": false,
                  "data": form
               };

               $.ajax(settings).done(function(response) {
                  var data = JSON.parse(response);
                  console.log(data.status);

                  // FAILED
                  if (data.status==false){
                     alert("<?=T::email?> <?=T::exist_please_use_different?>");

                     // LOADING ANIMATION
                     $('.subscribe').show();
                     $('.loading_button').hide();
                  }

                  // SUCCESS
                  if (data.status==true){
                     alert('<?=T::successfully_subscribed_newsletter?>');

                     // LOADING ANIMATION
                     $('.subscribe').show();
                     $('.loading_button').hide();

                  }
               });
         }
      }
   });
</script>