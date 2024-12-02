<script src="//cdnjs.cloudflare.com/ajax/libs/list.js/2.3.1/list.min.js"></script>
<script src="<?= root ?>assets/js/plugins/ion.rangeSlider.min.js"></script>
<style>
.enclose{height: 40px; width: 40px; position:fixed; margin-right: 20px; margin-bottom: 20px; right:0; bottom:0; pointer-events:none; opacity:0; justify-content: center; align-items: center; color:white; transition:all 0.4s;}
/* .newsletter-section,.footer-area {display:none} */
</style>
<div class="header-margin"></div>

<section class="pt-40 pb-40 bg-blue-2">
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

        // TEMPLATE
        $('.append_template').append(`
    <div class="col-lg-4 col-sm-6">
        <div class="hotelsCard -type-1 d-flex flex-column" style="min-height: 450px;">
            <div class="hotelsCard__image">
                <div class="cardImage ratio ratio-1x1 rounded">
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
            <div class="hotelsCard__content mt-10 flex-grow-1">
                <h4 class="hotelsCard__title text-dark-1 text-18 lh-16 fw-500">
                    <span>${item.name}</span>
                </h4>     
            </div>

            <!-- Moved the rating and price sections to the bottom -->
            <p class="text-light-1 text-14 mt-5">${item.location}</p>
            <div class="d-flex justify-content-between align-items-center mt-auto">
            
                <div class="item">
                    <span class="d-block"> ${starsHtml}</span>
                </div>
                <div class="item">
                    <small class="d-flex justify-content-start gap-2 align-items-center mb-1 py-2">
                        <span class="btn btn-outline-primary rounded-4 p-2 d-flex gap-2 justify-content-between align-items-center px-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" style="stroke:var(--theme-bg)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
                            </svg>
                            <?=T::rating?> ${item.rating}/5
                        </span>
                    </small>
                </div>
            </div>
            <div class="mt-5">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="content">
                        <p><?=currency?>${item.actual_price}</p>
                    </div>
                    <div class="content"></div>
                    <div class="content">
                        <a href="${url}" target="${target_blank}" class="w-100 fadeout py-2 d-flex align-items-center justify-content-center btn btn-primary d-block text-center waves-effect">
                            <?=T::view_more?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1" stroke-linecap="square" stroke-linejoin="arcs">
                                <path d="M9 18l6-6-6-6"></path>
                            </svg>
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