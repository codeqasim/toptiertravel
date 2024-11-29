<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="css/vendors.css">
    <link rel="stylesheet" href="css/main.css">

</head>

<div class="header-margin"></div>

<?php
        $profile_data=$meta['data'];
        $countries=$meta['countries'];
        ?>

<div class="dashboard" data-x="dashboard" data-x-toggle="-is-sidebar-open">
    <div class="dashboard__sidebar bg-white scroll-bar-1">


        <div class="sidebar -dashboard">
            <?php require "Sidebar.php"?>
        </div>


    </div>

    <div class="dashboard__main">
        <div class="m-2">
            <div class="row">
                <div class="">
                    <div class="form-box">
                        <div class="form-title-wrap border-bottom-0 pb-0">
                            <h5 class="fw-bold mb-3">
                                <?= T::profileinformation ?>
                            </h5>
                        </div>
                        <hr>
                        <div class="p-4">
                            <form id="profile" action="<?= root ?>profile" method="post">
                                <div class="">
                                    <div class="alert alert-success d-none">
                                        <?= T::profileupdatedsuccessfully ?>
                                    </div>
                                    <div class="row x-gap-20 y-gap-20">
                                        <div class="col-md-6">
                                            <div class="form-input">
                                                <input type="text" required name="first_name" id="<?= T::first_name ?>"
                                                    placeholder=" " value="<?= $profile_data->first_name ?>">
                                                <label class="lh-1 text-16 text-light-1" for="<?= T::first_name ?>">
                                                    <?= T::first_name ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-input">
                                                <input type="text" required name="last_name" id="<?= T::last_name ?>"
                                                    placeholder=" " value="<?= $profile_data->last_name ?>">
                                                <label class="lh-1 text-16 text-light-1" for="<?= T::last_name ?>">
                                                    <?= T::last_name ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-input">
                                                <input type="email" required name="email" id="<?= T::email ?>"
                                                    value="<?= $profile_data->email ?>" readonly>
                                                <label class="lh-1 text-16 text-light-1" for="<?= T::email ?>">
                                                    <?= T::email ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-input">
                                                <input type="password" required name="password" id="<?= T::password ?>">
                                                <label class="lh-1 text-16 text-light-1" for="<?= T::password ?>">
                                                    <?= T::password ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-input">
                                                <select required name="phone_country_code"
                                                    class="selectpicker phone w-100" data-live-search="true">
                                                    <option value="">
                                                        <?= T::select ?>
                                                        <?= T::country ?>
                                                    </option>
                                                    <?php foreach ($countries as $c) { ?>
                                                    <option value="<?= $c->id ?>"
                                                        data-content="<img class='' src='./assets/img/flags/<?= strtolower($c->iso) ?>.svg' style='width: 20px; margin-right: 14px;color:#fff'><span class='text-dark'> <?= $c->nicename ?> <strong>+<?= $c->phonecode ?></strong></span>">
                                                    </option>
                                                    <?php } ?>
                                                </select>
                                                <script>$('.phone').val('<?= $profile_data->phone_country_code ?>')</script>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-input">
                                                <input type="number" required name="phone" id="<?= T::phone ?>"
                                                    placeholder=" " value="<?= $profile_data->phone ?>">
                                                <label class="lh-1 text-16 text-light-1" for="<?= T::phone ?>">
                                                    <?= T::phone ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-input">
                                                <select required name="country_code" class="selectpicker country w-100"
                                                    data-live-search="true">
                                                    <option value="">
                                                        <?= T::select ?>
                                                        <?= T::country ?>
                                                    </option>
                                                    <?php foreach ($countries as $c) { ?>
                                                    <option value="<?= $c->id ?>"
                                                        data-content="<img class='' src='./assets/img/flags/<?= strtolower($c->iso) ?>.svg' style='width: 20px; margin-right: 14px;color:#fff'><span class='text-dark'> <?= $c->nicename ?></span>">
                                                    </option>
                                                    <?php } ?>
                                                </select>
                                                <script>$('.country').val('<?= $profile_data->country_code ?>')</script>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-input">
                                                <input type="text" required name="state" id="<?= T::state ?>"
                                                    placeholder=" " value="<?= $profile_data->state ?>">
                                                <label class="lh-1 text-16 text-light-1" for="<?= T::state ?>">
                                                    <?= T::state ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-input">
                                                <input type="text" required name="city" id="<?= T::city ?>"
                                                    placeholder=" " value="<?= $profile_data->city ?>">
                                                <label class="lh-1 text-16 text-light-1" for="<?= T::city ?>">
                                                    <?= T::city ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-input">
                                                <input type="text" required name="address1" id="<?= T::address ?>"
                                                    placeholder=" " value="<?= $profile_data->address1 ?>">
                                                <label class="lh-1 text-16 text-light-1" for="<?= T::address ?>">
                                                    <?= T::address ?> 1
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-input">
                                                <input type="text" name="address2" id="<?= T::address ?>"
                                                    placeholder=" " value="<?= $profile_data->address2 ?>">
                                                <label class="lh-1 text-16 text-light-1" for="<?= T::address ?>">
                                                    <?= T::address ?> 2
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="btn-box mt-4">
                                                <button style="height:44px" type="submit"
                                                    class="w-100 submit_button btn btn-primary btn-m rounded-sm">
                                                    <?= T::updateprofile ?>
                                                </button>
                                                <div class="loading_button" style="display:none">
                                                    <button style="height:44px"
                                                        class="loading_button w-100 btn btn-primary btn-m rounded-sm"
                                                        type="button" disabled>
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="form_token" value="<?= $_SESSION[" form_token"] ?>">
                                        <input type="hidden" name="user_id" value="<?= $profile_data->user_id ?>">
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $("#profile").submit(function () {
                $('.submit_button').hide();
                $('.loading_button').show();
            })
        </script>

        <style>
            .newsletter-section {
                display: none
            }
        </style>

        <!-- JavaScript -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"
            integrity="sha512-QSkVNOCYLtj73J4hbmVoOV6KVZuMluZlioC+trLpewV8qMjsWqlIQvkn1KGX2StWvPMdWGBqim1xlC8krl1EKQ=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAAz77U5XQuEME6TpftaMdX0bBelQxXRlM"></script>
        <script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>

        <script src="js/vendors.js"></script>
        <script src="js/main.js"></script>
        </body>

</html>