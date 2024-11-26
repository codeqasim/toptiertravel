<!-- slick -->
<?php $featured_hotels = app()->featured_hotels; ?>

<!-- ================================
    START HOTEL AREA
================================= -->
<section class="layout-pt-md layout-pb-md">
    <div data-anim-wrap class="container mt-5 pt-5">
        <div data-anim-child="slide-up delay-1" class="mt-5 pt-5 row justify-center text-center">
            <div class="col-auto mt-5 pt-5">
                <div class="sectionTitle -md mt-5 pt-5">
                    <h2 class="sectionTitle__title mt-5 pt-5"><?= T::hotels_featured_hotels ?></h2>
                    <p class="sectionTitle__text mt-5 sm:mt-0"><?= T::these_alluring_destinations_are_picked_just_for_you ?></p>
                </div>
            </div>
        </div>

        <div class="row y-gap-30 pt-40 sm:pt-20">
            <?php
            foreach ($featured_hotels as $hotels) {
                $nationality = $_SESSION['hotels_nationality'] ?? "US";
                $supplier_name = $_SESSION['supplier_name'] ?? "hotels";

                $payload = [
                    "nationality" => $nationality,
                    "supplier_name" => $supplier_name
                ];

                $hash = base64_encode(json_encode($payload));

                $link = root . 'hotel/' . $hotels->id . '/' .
                    clean_var($hotels->name) . '/' .
                    date('d-m-Y', strtotime('+3 day')) . '/' .
                    date('d-m-Y', strtotime('+4 day')) . '/1/2/0/' .
                    $nationality . '/hotels';
            ?>
                <div data-anim-child="slide-up delay-2" class="col-xl-3 col-lg-3 col-sm-6">
                    <a href="<?=$link?>" class="rentalCard -type-1 rounded-4">
                        <div class="rentalCard__image">
                            <div class="cardImage ratio ratio-1:1">
                                <div class="cardImage__content">
                                    <img class="rounded-4 col-12" src="./uploads/<?= $hotels->img ?>" alt="image">
                                </div>
                            </div>
                        </div>
                        <div class="rentalCard__content mt-10">
                            <div class="text-14 text-light-1 lh-14 mb-5">
                                <strong><?= $hotels->city ?></strong>
                                <small><?= $hotels->country ?></small>
                            </div>
                            <h4 class="rentalCard__title text-dark-1 text-18 lh-16 fw-500">
                                <span><?= $hotels->name ?></span>
                            </h4>
                            <div class="d-flex items-center mt-20">
                                <div class="flex-center bg-blue-1 rounded-4 size-30 text-12 fw-600 text-white">
                                    <?= $hotels->stars . ".0" ?>
                                </div>
                                <div class="text-14 text-dark-1 fw-500 ml-10">Exceptional</div>
                                <div class="text-14 text-light-1 ml-10"><?= rand(33, 300) ?> reviews</div>
                            </div>
                            <div class="mt-5">
                                <div class="text-light-1">
                                    <span class="fw-500 text-dark-1"><?= currency ?> <?= $hotels->price ?></span> / per night
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

<style>
    @media screen and (max-width: 425px) {
        .hotel-area > .container > :first-child {
            padding: 20px !important;
        }
    }
</style>
