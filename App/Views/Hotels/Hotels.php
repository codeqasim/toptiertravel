<script src="//cdnjs.cloudflare.com/ajax/libs/list.js/2.3.1/list.min.js"></script>
<script src="<?= root ?>assets/js/plugins/ion.rangeSlider.min.js"></script>

<div class="py-4 mb-0 search_page">
    <div class="container">
        <div class="modify_search">
            <?php require_once "./App/Views/Hotels/Search.php"; ?>
        </div>
    </div>
</div>

<!-- FILTER CONTROLS  -->
<!-- <div class="offcanvas offcanvas-end" id="filterContainer">
    <div class="offcanvas-header bg-light">
        <h5 class="offcanvas-title fw-semibold"><?=T::filter_results?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas--footer d-flex gap-3 p-3 bg-light">
        <div class="filter--input">
            <button class="w-100 btn btn-secondary w-100 h-100 text-capitalize" data-bs-dismiss="offcanvas"><?=T::close?></button>
        </div>

    </div>
</div> -->

<!-- FILTER BTN  -->
<!-- <div class="enclose z-3" id="bottomtop">
    <button class="filter--btn btn d-flex justify-content-center align-items-center gap-1 bg-dark text-white position-fixed text-capitalize rounded-5" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterContainer">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg>
        <?=T::filter_results?>
    </button>
</div> -->

<div class="pb-5">
    <div class="container py-3">
        <div class="row g-3">
            <div class="col-md-3">
                <?php include "HotelsFilter.php" ?>
            </div>
            <div class="col-md-9">
            <div class="row g-3 append_template justify-content-md-center">
            <!-- HERE GOES THE TEMPLATED DATA -->
            </div>
            </div>
        </div>
    </div>
</div>

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
        $('.append_template').prepend(`
            <div class="card--item col-12">
               <div class="card rounded-2">
                <div class="row">
                <div class="col-md-4">
                    <div class="bg-light rounded-0 overflow-hidden" style="height:200px">
                    <img src="${item.img}" class="w-100 h-100 object-fit-cover" alt="${item.name}">
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

                  <div class="col-md-5">
                  <div class="card-body p-2 overflow-hidden" style="line-height:25px">
                  <h5 class="card-title d-flex align-items-center mt-2 mb-1"> <strong>${item.name}</strong></h5>

                  <p class="card-text text-capitalize mb-0"><small>
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0f294d" stroke-width="2" stroke-linecap="square" stroke-linejoin="arcs"><path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                     ${item.location}
                  </small>
                  </p>

                  <span class="d-block"> ${starsHtml}</span>

                  <small class="d-flex justify-content-start gap-2 align-items-center mb-1 py-2">
                     <span class="btn btn-outline-primary rounded-4 p-2 d-flex gap-2 justify-content-between align-items-center px-3">
                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="" style="stroke:var(--theme-bg)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
                     </svg>
                     <?=T::rating?> ${item.rating}/5
                     </span>
                  </small>


                  </div>
                  </div>

                  <div class="col-md-3 d-flex align-content-end flex-wrap mb-3 px-4">
                  <p class="card-text h5 w-100 text-end" data-price="${item.actual_price}">
                  <strong class="w-100">
                  <small style="font-size: 14px; font-weight: 100; color: #5b5b5b;"><?=currency?></small>
                  ${item.actual_price}
                  </strong>
                  </p>


                  <a href="`+url+`" target="`+target_blank+`" class="w-100 fadeout py-2 d-flex align-items-center justify-content-center btn btn-primary d-block text-center waves-effect">
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

<style>
.enclose{height: 40px; width: 40px; position:fixed; margin-right: 20px; margin-bottom: 20px; right:0; bottom:0; pointer-events:none; opacity:0; justify-content: center; align-items: center; color:white; transition:all 0.4s;}
.newsletter-section,.footer-area {display:none}
</style>