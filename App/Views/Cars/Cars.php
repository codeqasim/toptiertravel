<script src="<?= root ?>assets/js/plugins/ion.rangeSlider.min.js"></script>

<div class="py-4 mb-0">
    <div class="container">
        <div class="modify_search">
            <?php require_once "./App/Views/Cars/Search.php"; ?>
        </div>
    </div>
</div>

<!-- FILTER CONTROLS  -->
<div class="offcanvas offcanvas-end" id="filterContainer">
    <div class="offcanvas-header bg-light">
        <h5 class="offcanvas-title fw-semibold"><?=T::filter_results?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="hotels--filter-wrapper offcanvas-body">
        <div id="hotelsFilter">
            <!-- STAR RATING  -->
            <div class="card mb-2" id="starsRating">
                <div class="card-header">
                    <a class="btn collapsed d-flex justify-content-between align-items-center p-0 text-black" data-bs-toggle="collapse" href="#starsFilter">
                    <?=T::star?> <?=T::rating?>
                        <span class="drop--Icon d-flex justify-content-center align-items-center rounded-pill border border-black p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                        </span>
                    </a>
                </div>

                <?php

                function stars($one,$two){

                    for ($i = 1; $i <= $one; $i++) {
                    echo
                    '<svg class="stars" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>';
                    }

                    for ($i = 1; $i <= $two; $i++) {
                    echo
                    '<svg class="stars_o" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke=""   stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>';
                    }

                }
                ?>

                <div id="starsFilter" class="collapse show" data-bs-parent="#hotelsFilter">
                    <div class="card-body px-3 py-2">
                        <ul class="list-group">
                            <li class="list-group-item border-0 rounded-3 p-1">
                                <div class="form-check d-flex align-items-center gap-2 mb-0">
                                    <input type="radio" class="form-check-input" id="starRating1" name="starRating" value="1">
                                    <label class="form-check-label w-100 fw-semibold" for="starRating1">
                                        1 <?=stars(1,4)?>
                                    </label>
                                </div>
                            </li>
                            <li class="list-group-item border-0 rounded-3 p-1">
                                <div class="form-check d-flex align-items-center gap-2 mb-0">
                                    <input type="radio" class="form-check-input" id="starRating2" name="starRating" value="2">
                                    <label class="form-check-label w-100 fw-semibold" for="starRating2">
                                        2 <?=stars(2,3)?>
                                    </label>
                                </div>
                            </li>
                            <li class="list-group-item border-0 rounded-3 p-1">
                                <div class="form-check d-flex align-items-center gap-2 mb-0">
                                    <input type="radio" class="form-check-input" id="starRating3" name="starRating" value="3">
                                    <label class="form-check-label w-100 fw-semibold" for="starRating3">
                                        3 <?=stars(3,2)?>
                                    </label>
                                </div>
                            </li>
                            <li class="list-group-item border-0 rounded-3 p-1">
                                <div class="form-check d-flex align-items-center gap-2 mb-0">
                                    <input type="radio" class="form-check-input" id="starRating4" name="starRating" value="4">
                                    <label class="form-check-label w-100 fw-semibold" for="starRating4">
                                        4 <?=stars(4,1)?>
                                    </label>
                                </div>
                            </li>
                            <li class="list-group-item border-0 rounded-3 p-1">
                                <div class="form-check d-flex align-items-center gap-2 mb-0">
                                    <input type="radio" class="form-check-input" id="starRating5" name="starRating" value="5">
                                    <label class="form-check-label w-100 fw-semibold" for="starRating5">
                                        5 <?=stars(5,0)?>
                                    </label>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- PRICE RANGE  -->
            <div class="card mb-2" id="priceRange">
                <div class="card-header">
                    <a class="btn collapsed d-flex justify-content-between align-items-center p-0 text-black" data-bs-toggle="collapse" href="#rangeFilter">
                    <?=T::pricerange?>
                        <span class="drop--Icon d-flex justify-content-center align-items-center rounded-pill border border-black p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                        </span>
                    </a>
                </div>
                <div id="rangeFilter" class="collapse show" data-bs-parent="#hotelsFilter">
                    <div class="card-body px-4 py-3">
                        <input type="text" id="PriceRange"  value="" />
                    </div>
                </div>
            </div>

            <!-- SORT ORDER  -->
            <div class="card mb-2" id="sortOrder">
                <div class="card-header">
                    <a class="btn collapsed d-flex justify-content-between align-items-center p-0 text-black" data-bs-toggle="collapse" href="#sortFilter">
                    <?=T::price?> <?=T::sort_by?>
                        <span class="drop--Icon d-flex justify-content-center align-items-center rounded-pill border border-black p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                        </span>
                    </a>
                </div>
                <div id="sortFilter" class="collapse show" data-bs-parent="#Filter">
                    <div class="card-body px-3 py-3">

                        <div class="row g-2">
                            <div class="col-12 mb-2 form-check d-flex align-items-center mb-0 gap-2 border rounded-3 px-5 py-3">
                                    <input type="radio" class="form-check-input" id="desc" name="sortOrder" default="false" value="desc">
                                    <label class="form-check-label w-100 fw-semibold" for="desc">
                                    <?=T::highest_to_lower?>
                                </label>
                                </div>

                                <div class="col-12 mb-2 form-check d-flex align-items-center mb-0 gap-2 border rounded-3 px-5 py-3">
                                    <input type="radio" class="form-check-input" id="asc" name="sortOrder" default="true" value="asc" checked>
                                    <label class="form-check-label w-100 fw-semibold" for="asc">
                                    <?=T::lowest_to_higher?>
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- RESET BTN  -->
            <div class="reset--btn" style="display: none;">
                <button onclick="location.reload()" class="btn btn-primary rounded-3 w-100 py-3"><?=T::reset?></button>
            </div>

        </div>
    </div>

    <div class="offcanvas--footer d-flex gap-3 p-3 bg-light">
        <div class="filter--input">
            <button class="w-100 btn btn-secondary w-100 h-100 text-capitalize" data-bs-dismiss="offcanvas"><?=T::close?></button>
        </div>
        <div class="w-100 filter--input filter-search">
            <button class="btn btn-primary w-100 h-100 text-capitalize" data-bs-dismiss="offcanvas"><?=T::apply_filters?></button>
        </div>
    </div>
</div>

<!-- FILTER BTN  -->
<div class="enclose z-3" id="bottomtop">
    <button class="filter--btn btn d-flex justify-content-center align-items-center gap-1 bg-dark text-white position-fixed text-capitalize rounded-5" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterContainer">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg>
        <?=T::filter_results?>
    </button>
</div>

<div class="bg-light pb-5">
    <div class="container py-3">
        <div class="row g-2 append_template justify-content-md-center">
            <!-- HERE GOES THE TEMPLATED DATA -->
        </div>
    </div>
</div>

<?php
$mods = array();
foreach ($modules as $i => $m){
    if ($m->type=="cars"){
        $arrs = array( "name"=>$m->name,);
        array_push($mods,$arrs);
    }
}
$api_url = root.api_url.'cars/search';
?>
<?php require_once "./App/Views/Scripts.php"; ?>

<script>
    // GET ALL MODULES NAMES
    var mods = <?= json_encode($mods) ?>;
    // ADD THE "pageCall" PROPERTITY TO EVERY mods, THIS WILL HELP IN PAGINATION AND "NO MORE DATA" MESSAGE
    mods.forEach( _mod => _mod.pageCall = true )
    console.log( mods )
   
    var page_number = 1;
    var isLoading = false;

    var apiFlag = 0;     // THIS WILL HELP TO COUNT< CURRENTLY ONGOING API CALLS 
    var noMoreFlag = true;     // THIS WILL HELP TO PREVENT FROM SHOW "NO MORE DATA" ONLY ONCE 
    var dataSort = $('#sortOrder').find('input[type="radio"][default="true"]').val();     // GET THE SORT ORDER
    var filtrationFlag = true;     // THIS WILL HELP IF USER HAS DONE ANY FILTRATION, SO PAGINATION WILL NOT WORK

    // FILTERS
    var rating_filter = "";
    var price_min_filter = 0;
    var price_max_filter = 10000;
    var price_low_to_high = 0;
    
    // FINAL REQUEST FUNCTION
    function final_request(page_number) {
        $.each(mods, function (key, value) {
            var requestData = {
                from_airport: "<?=$meta['from_airport']?>",
                to_location: "<?=$meta['to_location']?>",
                to_date: "<?=$meta['to_date']?>",
                pick_time: "<?=$meta['pick_time']?>",
                drop_date: "<?=$meta['drop_date']?>",
                drop_time: "<?=$meta['drop_time']?>",
                language: "en",
                adults: <?=$meta['adults']?>,
                childs: <?=$meta['childs']?>,
                currency: "<?=currency?>",
                ip: "0.0.0.0",
                module_name: value.name,
                pagination: page_number,
                rating: rating_filter,
                price_from: price_min_filter,
                price_to: price_max_filter,
                price_low_to_high: price_low_to_high
            };
            sendPostRequest("<?=$api_url?>", requestData, key);
        });
    }

    // ITEMS TEMPLATE
    function template(item) {
        var starsHtml = produceStars(item.rating);
        if(item.redirect != ''){
            var booknow =  `<a href="`+ item.redirect+`" target="_blank" class="w-100 fadeout py-2 d-flex align-items-center justify-content-center btn btn-primary rounded-1 d-block text-center waves-effect">
                <?=T::booknow?>
                 <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1" stroke-linecap="square" stroke-linejoin="arcs">
                        <path d="M9 18l6-6-6-6"></path>
                     </svg>
            </a>`;
        }else{
           var booknow =`<form id="" class="" action="<?=root?>cars/booking"  method="post">
                   <input type="hidden" name="car_id" value="${item.id}">
                   <input type="hidden" name="car_name" value="${item.title}">
                   <input type="hidden" name="car_img" value="${item.img}">
                   <input type="hidden" name="price" value="${item.price}">
                   <input type="hidden" name="actual_price" value="${item.actual_price}">
                   <input type="hidden" name="adults" value="<?=$meta['adults']?>">
                   <input type="hidden" name="childs" value="<?=$meta['childs']?>">
                   <input type="hidden" name="date" value="<?=$meta['to_date']?>">
                   <input type="hidden" name="currency" value="${item.currency}">
                   <input type="hidden" name="supplier" value="${item.supplier_name}">
                   <input type="hidden" name="car_location" value="${item.from_airport}">
                   <input type="hidden" name="car_stars" value="${item.stars}">
                   <input type="hidden" name="booking_data" value="${btoa(JSON.stringify(item.booking_data))}">
                   <input type="hidden" name="module_type" value="cars">
                   <input type="hidden" name="user_data" value="">
                   <input type="hidden" name="cancellation" value="">
                  <button type="submit"
                     class="w-100 fadeout py-2 d-flex align-items-center justify-content-center btn btn-primary rounded-1 d-block text-center waves-effect">
                     <?=T::booknow?>
                     <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1" stroke-linecap="square" stroke-linejoin="arcs">
                        <path d="M9 18l6-6-6-6"></path>
                     </svg>
                  </button>
                  </form>`;
        }
        var rating = '';
        if(item.rating){
            var rating = `<small class="d-flex justify-content-start gap-1 align-items-center mb-2">
                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
                     </svg>
                     <span><?=T::rating?> ${item.rating}<span class="">/5</span></span>
                     <span> ${starsHtml}</span>
                  </small>`
        }
        $('.append_template').prepend(`
            <div class="card--item col-12 col-md-4 col-lg-3">
               <div class="card p-2 rounded-3">
                  <div class="bg-light rounded-3 overflow-hidden" style="height:180px">
                  <img src="${item.img}" class="w-100 h-100 object-fit-cover" alt="${item.title}">
                  <span class="d-inline-block rounded-pill position-absolute" style="top:15px; right:15px; height: 10px;width: 10px;background: `+item.color+`"></span>
                  </div>
                  <div class="card-body p-1 overflow-hidden" style="line-height:25px">
                  <p class="text-nowrap card-title d-flex align-items-center mt-2 mb-0"><strong>${item.title}</strong></p>
                  <p class="card-text text-capitalize mb-0"><small>
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0f294d" stroke-width="2" stroke-linecap="square" stroke-linejoin="arcs"><path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                     ${item.location}
                  </small></p>
                  ${rating}
                  <p class="card-text h5" data-price="${item.actual_price}"><strong><small><?=currency?></small> ${item.actual_price}</strong></p>
                  ${booknow}
                  </div>
               </div>
            </div>
        `);
    }

    // INIT
    loading();
    final_request(page_number);
</script>

<style>
.enclose{height: 40px; width: 40px; position:fixed; margin-right: 20px; margin-bottom: 20px; right:0; bottom:0; pointer-events:none; opacity:0; justify-content: center; align-items: center; color:white; transition:all 0.4s;}
.newsletter-section,.footer-area {display:none}
</style>