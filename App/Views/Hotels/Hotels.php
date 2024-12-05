<script src="//cdnjs.cloudflare.com/ajax/libs/list.js/2.3.1/list.min.js"></script>
<script src="<?= root ?>assets/js/plugins/ion.rangeSlider.min.js"></script>
<style>
.enclose{height: 40px; width: 40px; position:fixed; margin-right: 20px; margin-bottom: 20px; right:0; bottom:0; pointer-events:none; opacity:0; justify-content: center; align-items: center; color:white; transition:all 0.4s;}
/* .newsletter-section,.footer-area {display:none} */
</style>
<div class="header-margin"></div>

<section class="pt-40 pb-40 bg-light-2">
    <div class="container ">
        <div class="row">
        <div class="text-center my-2">
              <h1 class="text-30 fw-600">Find Your Dream Luxury Hotel</h1>
            </div>
            <div class="col-12 bg-white mt-2 py-2">
                <?php require_once "./App/Views/Hotels/Search.php"; ?>
            </div>
        </div>
    </div>
</section>

<section class="layout-pt-md layout-pb-lg">
    <div class="container">
        <div class="row y-gap-30">
            <!-- FILTER SIDEBAR -->
            <div class="col-xl-3 col-lg-4">
                <aside class="sidebar y-gap-40">
                    <?php include "HotelsFilter.php"; ?>
                </aside>
            </div>

            <!-- HOTEL RESULTS -->
            <div class="col-xl-9 col-lg-8 ">
                <div class="row y-gap-30 append_template ">
                    
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$mods = array();
foreach ($modules as $i => $m){
    if ($m->type=="hotels"){
        $arrs = array( "name"=>$m->name,);
        array_push($mods,$arrs);
    }
};
$api_url = root.api_url.'hotel_search';
?>
<?php require_once "./App/Views/Scripts.php"; ?>

<script>
    // GET ALL MODULES NAMES
    var mods = <?= json_encode($mods) ?>;
    // ADD THE "pageCall" PROPERTITY TO EVERY mods, THIS WILL HELP IN PAGINATION AND "NO MORE DATA" MESSAGE
    mods.forEach( _mod => _mod.pageCall = true )

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
                city: "<?=$meta['city']?>",
                checkin: "<?=$meta['checkin']?>",
                checkout: "<?=$meta['checkout']?>",
                nationality: "<?=$meta['nationality']?>",
                adults: "<?=$meta['adults']?>",
                childs: "<?=$meta['childs']?>",
                rooms: "<?=$meta['rooms']?>",
                language: "en",
                ip: "0.0.0.0",
                currency: "<?=currency?>",
                child_age: JSON.stringify(<?=json_encode($meta['child_age'])?>),
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
            var url  =  item.redirect;
            var target_blank = "_blank";
        }else{
            var url  =  "<?=root?>hotel/"+item.hotel_id+"/"+item.name.toLowerCase().replace(/ /g, '-')+"/<?=$meta['checkin']?>/<?=$meta['checkout']?>/<?=$meta['rooms']?>/<?=$meta['adults']?>/<?=$meta['childs']?>/<?=$meta['nationality']?>/"+item.supplier_name+"";
            var target_blank = "_self";
        }
    var randomReviews = Math.floor(Math.random() * (5000 - 2000 + 1)) + 2000;
        // TEMPLATE
        $('.append_template').prepend(`
    <div class="card--item col-12">

                <div class="border-top-light pt-30">
                  <div class="row x-gap-20 y-gap-20">
                    <div class="col-md-auto">

                      <div class="cardImage ratio ratio-1:1 w-250 md:w-1/1 rounded">
                        <div class="cardImage__content">
                          <img class="rounded col-12" src="${item.img}" alt="${item.name}">
                          <span class="d-inline-block rounded-pill position-absolute" style="top:15px; right:15px; height: 10px;width: 10px;background: `+item.color+`"></span>

                    <div class="images-bottom-banner multi">
                    <div class="indicator">
                    <div class="indicator-area">
                    <span class="indicator-item active">
                    </span><span class="indicator-item ">
                    </span><span class="indicator-item ">
                    </span><span class="indicator-item ">
                    </span><span class="indicator-item ">
                    </span>
                    </div>
                    </div>
                    </div>
                        </div>
                      </div>

                    </div>

                    <div class="col-md">
                      <h3 class="text-18 lh-16 fw-500">
                        ${item.name}

                        <div class="d-inline-block ml-10">
                           ${starsHtml}
                        </div>
                      </h3>

                      <div class="row x-gap-10 y-gap-10 items-center pt-10">
                        <div class="col-auto">
                          <p class="text-14">${item.location}</p>
                        </div>

                        <div class="col-auto">
                          <button data-x-click="mapFilter" class="d-block text-14 text-blue-1 underline">Show on map</button>
                        </div>

                        <div class="col-auto">
                          <div class="size-3 rounded-full bg-light-1"></div>
                        </div>

                        <div class="col-auto">
                          <p class="text-14">2 km to city center</p>
                        </div>
                      </div>

                      <div class="text-14 lh-15 mt-20">
                        <div class="fw-500">King Room</div>
                        <div class="text-light-1">1 extra-large double bed</div>
                      </div>

                      <div class="text-14 text-green-2 lh-15 mt-10">
                        <div class="fw-500">Free cancellation</div>
                        <div class="">You can cancel later, so lock in this great price.</div>
                      </div>

                      <div class="row x-gap-10 y-gap-10 pt-20">

                        <div class="col-auto">
                          <div class="border-light rounded-100 py-5 px-20 text-14 lh-14">Breakfast</div>
                        </div>

                        <div class="col-auto">
                          <div class="border-light rounded-100 py-5 px-20 text-14 lh-14">WiFi</div>
                        </div>

                        <div class="col-auto">
                          <div class="border-light rounded-100 py-5 px-20 text-14 lh-14">Spa</div>
                        </div>

                      </div>
                    </div>

                    <div class="col-md-auto text-right md:text-left">
                      <div class="row x-gap-10 y-gap-10 justify-end items-center md:justify-start">
                        <div class="col-auto">
                          <div class="text-14 lh-14 fw-500">Exceptional</div>
                          <div class="text-14 lh-14 text-light-1">${randomReviews} reviews</div>
                        </div>
                        <div class="col-auto">
                          <div class="flex-center text-white fw-600 text-14 size-40 rounded bg-blue-1">${item.rating}/5</div>
                        </div>
                      </div>

                      <div class="">
                        <div class="text-14 text-light-1 mt-50 md:mt-20"><?=$meta['rooms']?> room, <?=$meta['adults']?> adult</div>
                        <div class="text-22 lh-12 fw-600 mt-5" data-price="${item.actual_price}" ><?=currency?>$${item.actual_price}</div>
                        


                        <a href="`+url+`" target="`+target_blank+`" class="button -md -dark-1 bg-blue-1 text-white mt-24">
                          See Availability <div class="icon-arrow-top-right ml-15"></div>
                        </a>

                      </div>
                    </div>
                  </div>
                </div>

              </div>
`);



    }
    // INIT
    loading();
    final_request(page_number);
</script>