<!-- <div class="hotels--filter-wrapper offcanvas-body"> -->
<div class="">
    <div id="hotelsFilter">
        <!-- STAR RATING  -->
        <div class="sidebar__item" id="starsRating">
            <div class="card-header bg-white">
                <a class="btn collapsed d-flex justify-content-between align-items-center p-0 text-black" data-bs-toggle="collapse" href="#starsFilter">
                    <?=T::star?> <?=T::rating?>
                    <span class="drop--Icon d-flex justify-content-center align-items-center rounded-pill border border-black p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                    </span>
                </a>
            </div>

            <?php
            function stars($one, $two){
                for ($i = 1; $i <= $one; $i++) {
                    echo
                    '<svg class="stars" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>';
                }

                for ($i = 1; $i <= $two; $i++) {
                    echo
                    '<svg class="stars_o" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>';
                }
            }
            ?>

            <div id="starsFilter" class="collapse show" data-bs-parent="#hotelsFilter">
                <div class="card-body px-4 py-3">
                    <ul class="list-group">
                        <li class="list-group-item border-0 rounded-3 p-1">
                            <div class="form-check d-flex align-items-center gap-2 mb-0">
                                <input type="radio" class="" id="starRating1" name="starRating" value="1" style="height: 1em !important; width: 1em !important;">
                                <label class="form-check-label w-100 fw-semibold" for="starRating1">
                                    1 <?=stars(1,4)?>
                                </label>
                            </div>
                        </li>
                        <li class="list-group-item border-0 rounded-3 p-1">
                            <div class="form-check d-flex align-items-center gap-2 mb-0">
                                <input type="radio" class="" id="starRating2" name="starRating" value="2" style="height: 1em !important; width: 1em !important;">
                                <label class="form-check-label w-100 fw-semibold" for="starRating2">
                                    2 <?=stars(2,3)?>
                                </label>
                            </div>
                        </li>
                        <li class="list-group-item border-0 rounded-3 p-1">
                            <div class="form-check d-flex align-items-center gap-2 mb-0">
                                <input type="radio" class="" id="starRating3" name="starRating" value="3" style="height: 1em !important; width: 1em !important;">
                                <label class="form-check-label w-100 fw-semibold" for="starRating3">
                                    3 <?=stars(3,2)?>
                                </label>
                            </div>
                        </li>
                        <li class="list-group-item border-0 rounded-3 p-1">
                            <div class="form-check d-flex align-items-center gap-2 mb-0">
                                <input type="radio" class="" id="starRating4" name="starRating" value="4" style="height: 1em !important; width: 1em !important;">
                                <label class="form-check-label w-100 fw-semibold" for="starRating4">
                                    4 <?=stars(4,1)?>
                                </label>
                            </div>
                        </li>
                        <li class="list-group-item border-0 rounded-3 p-1">
                            <div class="form-check d-flex align-items-center gap-2 mb-0">
                                <input type="radio" class="" id="starRating5" name="starRating" value="5" style="height: 1em !important; width: 1em !important;">
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
        <div class="sidebar__item" id="priceRange">
            <div class="card-header bg-white">
                <a class="btn collapsed d-flex justify-content-between align-items-center p-0 text-black" data-bs-toggle="collapse" href="#rangeFilter">
                    <?=T::pricerange?>
                    <span class="drop--Icon d-flex justify-content-center align-items-center rounded-pill border border-black p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                    </span>
                </a>
            </div>
            <div id="rangeFilter" class="collapse show" data-bs-parent="#hotelsFilter">
                <div class="card-body px-4 py-3">
                    <input type="text" id="PriceRange" value="" />
                </div>
            </div>
        </div>

        <!-- SORT ORDER  -->
        <div class="sidebar__item" id="sortOrder">
            <div class="card-header bg-white">
                <a class="btn collapsed d-flex justify-content-between align-items-center p-0 text-black" data-bs-toggle="collapse" href="#sortFilter">
                    <?=T::price?> <?=T::sort_by?>
                    <span class="drop--Icon d-flex justify-content-center align-items-center rounded-pill border border-black p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                    </span>
                </a>
            </div>
            <div id="sortFilter" class="collapse show" data-bs-parent="#Filter">
                <div class="card-body py-3">
                    <div class="row g-2">
                        <div class="col-12 mb-2 form-check d-flex align-items-center mb-0 gap-2 border rounded-3 " style="padding:1rem 2rem !important;">
                            <input type="radio" class="" id="desc" name="sortOrder" default="false" value="desc" style="height: 1em !important; width: 1em !important;">
                            <label class="form-check-label w-100 fw-semibold" for="desc">
                                <?=T::highest_to_lower?>
                            </label>
                        </div>

                        <div class="col-12 mb-2 form-check d-flex align-items-center mb-0 gap-2 border rounded-3" style="padding:1rem 2rem !important;">
                            <input type="radio" class="" id="asc" name="sortOrder" default="true" value="asc" checked style="height: 1em !important; width: 1em !important;">
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
            <button onclick="location.reload()" class="btn btn-outline-primary rounded-3 w-100 py-3 mb-2"><?=T::reset?></button>
        </div>

        <div class="w-100 filter--input filter-search">
            <button class="btn btn-primary w-100 h-100 text-capitalize" data-bs-dismiss="offcanvas"><?=T::apply_filters?></button>
        </div>
    </div>
</div>
