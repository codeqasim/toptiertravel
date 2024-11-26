<script>

    // LOADING TEMPLATE
    function loading() {
        $(".append_template").append(`
            <div id="loaderAnimation" class="loaders col-12 col-md-4 col-lg-3">
                <div class="d-flex my-5 py-5 justify-content-center">
                    <span class="shadow bg-dark rounded-5 p-4 py-5 my-5" role="status" style="border-radius: 60px !important; display: flex; align-items: center; justify-content: center; height: 100px; width: 100px; box-shadow: 4px 1px 12px 2px rgba(0, 0, 0, 0.3)">
                        <svg width="44" height="44" viewBox="0 0 44 44" xmlns="http://www.w3.org/2000/svg" stroke="#fff">
                            <g fill="none" fill-rule="evenodd" stroke-width="2">
                                <circle cx="22" cy="22" r="1">
                                    <animate attributeName="r" begin="0s" dur="1.8s" values="1; 20" calcMode="spline" keyTimes="0; 1" keySplines="0.165, 0.84, 0.44, 1" repeatCount="indefinite" />
                                    <animate attributeName="stroke-opacity" begin="0s" dur="1.8s" values="1; 0" calcMode="spline" keyTimes="0; 1" keySplines="0.3, 0.61, 0.355, 1" repeatCount="indefinite" />
                                </circle>
                                <circle cx="22" cy="22" r="1">
                                    <animate attributeName="r" begin="-0.9s" dur="1.8s" values="1; 20" calcMode="spline" keyTimes="0; 1" keySplines="0.165, 0.84, 0.44, 1" repeatCount="indefinite" />
                                    <animate attributeName="stroke-opacity" begin="-0.9s" dur="1.8s" values="1; 0" calcMode="spline" keyTimes="0; 1" keySplines="0.3, 0.61, 0.355, 1" repeatCount="indefinite" />
                                </circle>
                            </g>
                        </svg>
                    </span>
                </div>
            </div>
        `);
    }

    // CALCULATE STARS ON LISTING
    function produceStars(numberOfStars) {
        return Array.from({ length: Math.floor(numberOfStars) }, () => `
        <svg class="stars" style="margin-right:-3px" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
        </svg>
        `).join('');
    }

    // AJAX REQUEST FUNCTION
    function sendPostRequest(url, params, modValue) {
        $.ajax({
            url: url,
            type: 'POST',
            data: params,
            dataType: 'json',
            success: function (res) {
                console.log(res);
                if (res.status === false) {
                    // MAKE THG "pageCall" propertity to false, IF there is no data
                    mods[modValue].pageCall = false;
                    // NO MORE DATA MESSAGE
                    if(mods.every(_module => _module.pageCall === false) && noMoreFlag) {
                        noMoreFlag = false;
                        $('.loaders').remove();
                        vt.error('No more data to load', { title:"No More Data", position: "bottom-center" });
                    }
                    // SHOW FILTER BTN, IF THERE IS NO DATA
                    $("#bottomtop").css("pointerEvents", "all");
                    $("#bottomtop").css("opacity", 1);
                }
                $.each(res.response, function (key, value) {
                    template(value);
                });
            },
            complete: function () {
                apiFlag++;
                dataOrder();
                if(apiFlag === mods.length) { isLoading = true;
                    $('.loaders').remove();
                }
            }
        });
    }

    // RANGE SLIDER
    let rangeSlider =  $("#PriceRange").ionRangeSlider({
        skin: "round",
        type: "double",
        min: 0,
        max: 10000,
        from: 0,
        to: 10000,
        step: 1,
        prettify_enabled: false,
        onFinish: function(data) {
            price_min_filter = data.from;
            price_max_filter = data.to
        }
    });

    // SCROLL THE PAGE TO THE TOP ON PAGE LOAD
    $(window).on('load', () => $("body,html").animate({ scrollTop: 0 }, 10) );

    // FUNCTION RECALL THE DATA WITH
    $(window).scroll(function () {
        // Check if not already loading and near the bottom of the page
        if ( $(window).scrollTop() + $(window).height() >= ( $(document).height() - 100) && mods.some(_mod => _mod.pageCall === true)
            && filtrationFlag ) {
            // SHOW TEH LAODER
            if(isLoading) { isLoading = false;
                loading();
            }

            if(apiFlag === mods.length) { apiFlag = 0; page_number++;
                final_request(page_number);}
        }
    });

    // SHOW FITERS BUTTON
    window.onscroll = function() {
        var appear = 20
        if (window.pageYOffset >= appear) {
        $("#bottomtop").css("pointerEvents", "all");
        $("#bottomtop").css("opacity", 1);
        } else {
        $("#bottomtop").css("pointerEvents", "none");
        $("#bottomtop").css("opacity", 0);
        }
    }

    // GET THE SORT ORDER
    $('#sortOrder').on( 'change', function() { dataSort = $(this).find('input[type="radio"]:checked').val() } );

    // SORT ORDER FUNCTION
    function dataOrder() {
        var _sortedElements;
        _sortedElements = (dataSort === 'asc') ? $('.card--item').sort( (a, b) => $(a).find('[data-price]').attr('data-price').replace(',', '') - $(b).find('[data-price]').attr('data-price').replace(',', '') )
        : $('.card--item').sort( (a, b) => $(b).find('[data-price]').attr('data-price').replace(',', '') - $(a).find('[data-price]').attr('data-price').replace(',', '') );

        $('.append_template').prepend(_sortedElements);
    }

    // GET THE STAR RATING
    $('#starsRating').on( 'change', function() { rating_filter = $(this).find('input[type="radio"]:checked').val() });

    // RESET BTN
    $('.reset--btn').on( 'click', () => location.reload() )

    // WHEN USER CLICK ON FILTER BTN
    $('.filter-search button').on('click', function() {
        filtrationFlag = false;
        noMoreFlag = true;
        apiFlag = 0;
        mods.forEach( _mod => _mod.pageCall = true )
        // SCROLL TO TOP
        $("body,html").animate({ scrollTop: 0 }, 10);

        $('.append_template').html('');
        //   SHOW LOADER FUCNTION
        loading();

        final_request(1);
        $('.reset--btn').css('display', 'block');
    });

</script>