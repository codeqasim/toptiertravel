<!DOCTYPE HTML>

<?php
// CHECK AND ADD LANGUAGE CODE TO DIR
if (isset($_SESSION['phptravels_client_language_dir'])){ $lang_dir = $_SESSION['phptravels_client_language_dir'];
} else { foreach (app()->languages as $lang){ if($lang->default == 1){
    $lang_dir = ($lang->type);
    $_SESSION['defualt_lang'] = $lang->name;
} } }

// CHECK AVAILABLE MODULE TYPE
$modules = app()->modules;
$temp_module = array_unique(array_column($modules, 'type'));
$module_status = array_intersect_key($modules, $temp_module);
?>

<html lang="en" dir="<?=$lang_dir?>">

<head>
    <title><?= $meta['title'] ?? '' ?></title>
    <link rel="shortcut icon" href="<?=root?>uploads/global/favicon.png">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/gh/codeqasim/cdn@main/scripts.js"></script>
    <link rel="stylesheet" type="text/css" href="<?=root?>assets/css/app.css">
    <link rel="stylesheet" type="text/css" href="<?=root?>assets/css/themes/<?= base()->app->theme_name ?>.css">
    <?php if ($lang_dir == "RTL" || ($_SESSION['phptravels_client_language_dir'] ?? '') == "RTL"): ?>
        <link rel="stylesheet" href="<?=root?>assets/css/bootstrap.rtl.min.css">
        <link rel="stylesheet" type="text/css" href="<?=root?>assets/css/rtl.css">
    <?php endif; ?>
    <link rel="stylesheet" type="text/css" href="<?=root?>assets/css/theme.css">
    <?php if (!empty($_SESSION['theme'])): ?>
        <link rel="stylesheet" type="text/css" href="<?=root?>assets/css/themes/<?=$_SESSION['theme']?>.css">
    <?php endif; ?>
    <?php if (!empty($_SESSION['phptravels_client']) && strtolower($_SESSION['phptravels_client']->user_type) == "agent"): ?>
        <link rel="stylesheet" type="text/css" href="<?=root?>assets/css/agent.css">
    <?php endif; ?>
    <?= base()->app->javascript ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/vendors.css">
    <link rel="stylesheet" href="assets/css/main.css">

</head>

<body id="fadein">
<!-- THEME PRIMARY COLOR -->
<style>:root {--theme-bg: <?=base()->app->default_theme?>}</style>
<?php
require_once "App/Views/DemoContent.php";
?>
    <header class="navbar fixed-top navbar-expand-lg p-lg-0 d-none">
        <div class="container">
            <!-- logo  -->
            <div class="d-flex">
                <a href="<?=root?>" class="fadeout navbar-brand m-0 py-2 px-2 rounded-2">
                    <img class="logo p-1 rounded" style="max-width: 140px;max-height: 48px;" src="<?=root?>uploads/global/logo.png" alt="logo">
                </a>
            </div>

            <!-- toggle button  -->
            <button class="navbar-toggler rounded-1" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- nav items  -->
            <div class="collapse navbar-collapse justify-content-between" id="navbarSupportedContent">

                <!-- left  -->
                <div class="nav-item--left ms-lg-0">
                    <ul class="header_menu navbar-nav">

                    <?php
                    $keys = array_column($module_status, 'order');
                    array_multisort($keys, SORT_ASC, $module_status);

                    foreach ($module_status as $module) {
                        if (!isset($module->type)) continue;

                        $isActive = isset($meta['nav_menu']) && $meta['nav_menu'] == $module->type;

                        $text = '';
                        $link = '';

                        switch ($module->type) {
                            case "hotels":
                                $text = T::hotels;
                                $link = "hotels";
                                break;
                            case "flights":
                                $text = T::flights;
                                $link = "flights";
                                break;
                            case "tours":
                                $text = T::tours;
                                $link = "tours";
                                break;
                            case "cars":
                                $text = T::cars;
                                $link = "cars";
                                break;
                            case "visa":
                                $text = T::visa;
                                $link = "visa";
                                break;
                            case "extra":
                                $text = T::blog;
                                $link = "blogs";
                                break;
                            default:
                                continue 2; // Skip the rest of the current iteration and continue to the next iteration of the outer loop
                        }
                        ?>
                        <li>
                            <a class="nav-link fadeout <?= $isActive ? 'active' : '' ?>" href="<?= root . $link ?>" style="#color:#3f3f3f!important">
                                <?= $text ?>
                            </a>
                        </li>
                    <?php } ?>

                        <?php  $menu=(base()->cms);
                        foreach ($menu as $m){
                            if ($m->name=="Header"){
                                ?>
                                <li>
                                    <a class="nav-link fadeout" href="<?=root?>page/<?=$m->slug_url?>" style="#color:#3f3f3f!important">
                                        <?=$m->page_name?>
                                    </a>
                                </li>
                            <?php } }?>
                    </ul>
                </div>

                <!-- right  -->
                <div class="nav-item--right" role="search">
                    <ul class="navbar-nav gap-2 me-auto mb-2 mb-lg-0">

                        <?php if (app()->app->multi_language == 1) {?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle btn ps-3 p-0 py-2 px-0 text-center d-flex align-items-center justify-content-center gap-0 border"
                                href="javascript:void(0)" role="button" data-bs-toggle="dropdown" aria-expanded="false">

                                <?php if (!isset($_SESSION['phptravels_client_language_country'])) { ?>
                                <img class="mx-2" style="width:18px"
                                    src="<?=root?>assets/img/flags/<?php foreach (app()->languages as $lang){ if($lang->default == 1){ echo strtolower($lang->country_id); } }?>.svg"
                                    alt="flag">
                                <?php } else {?>
                                <img class="mx-2" style="width:18px"
                                    src="<?=root?>assets/img/flags/<?=$_SESSION['phptravels_client_language_country']?>.svg"
                                    alt="flag">
                                <?php } ?>

                                <strong class="h6 m-0 header_options text-dark">
                                    <?php if (!isset($_SESSION['phptravels_client_language_name'])) { ?>
                                    <?php foreach (app()->languages as $lang){ if($lang->default == 1){ echo $lang->name; $def_language = $lang->name; } }?>
                                    <?php } else {?>
                                    <?=$_SESSION['phptravels_client_language_name']?>
                                    <?php } ?>
                                </strong>
                                <svg class="mx-1" xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                    viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 9l6 6 6-6" />
                                </svg>
                            </a>
                            <ul class="dropdown-menu bg-white rounded-3 p-2">
                                <?php foreach(app()->languages as $lang){ ?>
                                <li><a class="dropdown-item d-flex gap-3 fadeout"
                                        href="<?=root?>language/<?=strtolower($lang->country_id)?>/<?=$lang->name?>/<?=$lang->type?>">
                                        <img style="width:18px"
                                             src="<?=root?>assets/img/flags/<?=strtolower($lang->country_id)?>.svg"
                                             alt="flag"> </i><span><?=$lang->name?></span></a></li>
                                <?php } ?>
                            </ul>
                        </li>
                        <?php } ?>

                        <?php if (app()->app->multi_currency == 1) {?>
                        <li class="nav-item dropdown multi_currency">
                            <a class="nav-link dropdown-toggle btn px-0 ps-3 text-center d-flex align-items-center justify-content-center gap-1 border text-dark"
                                href="javascript:void(0)" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <strong class="h6 m-0 header_options">
                                    <?php if (!isset($_SESSION['phptravels_client_currency'])) { ?>
                                    <?php foreach (app()->currencies as $currency){ if($currency->default == 1){ echo $currency->name; } }?>
                                    <?php } else {?>
                                    <?=$_SESSION['phptravels_client_currency']?>
                                    <?php } ?>
                                </strong>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M6 9l6 6 6-6" />
                                </svg>
                            </a>
                            <ul class="dropdown-menu bg-white rounded-3 p-2">
                            <?php
                            foreach (app()->currencies as $currency) { { ?>
                                <li><a class="dropdown-item fadeout" href="<?=root?>currency/<?=$currency->name?>">
                                        <img class="mx-2" style="width:18px"
                                            src="<?=root?>assets/img/flags/<?=strtolower($currency->iso)?>.svg"
                                            alt="flag">
                                        <span><strong><?=$currency->name?></strong></span>
                                        <span class="mx-2">-</span> <small><?=$currency->nicename?></small>
                                    </a></li>
                                <?php } } ?>
                            </ul>
                        </li>
                        <?php } ?>

                        <?php if (app()->app->user_registration == 1) {?>
                        <li class="nav-item dropdown">
                            <a class="bg-light nav-link dropdown-toggle btn btn-outline-secondary px-0 ps-3 text-center d-flex align-items-center justify-content-center gap-2 border"
                                href="javascript:void(0)" role="button" data-bs-toggle="dropdown" aria-expanded="false">

                                <svg stroke="#000" class="pe-1" xmlns="http://www.w3.org/2000/svg" width="20"
                                    height="20" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>

                                <strong class="m-0 text-dark text-uppercase">
                                    <?=T::account?>
                                </strong>
                                <svg stroke="#000" xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                    viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 9l6 6 6-6" />
                                </svg>
                            </a>

                            <?php if(!isset($_SESSION['phptravels_client']->user_id)) { ?>
                            <ul class="dropdown-menu bg-white rounded-3 p-2">
                                <li><a class="dropdown-item fadeout" href="<?=root?>login"> <small><strong><?=T::login?> </strong></small></i></a></li>
                                <li><a class="dropdown-item fadeout" href="<?=root?>signup"> <small><strong><?=T::signup?> </strong></small></i></a></li>
                            </ul>
                            <?php } ?>

                            <?php if(isset($_SESSION['phptravels_client']->user_id)) { ?>
                            <ul class="dropdown-menu bg-white rounded-3 p-2">
                                <li><a class="dropdown-item fadeout" href="<?=root?>dashboard"> <?=T::dashboard?></i></a></li>
                                <li><a class="dropdown-item fadeout" href="<?=root?>bookings"> <?=T::bookings?></i></a></li>
                                <?php if($_SESSION['phptravels_client']->user_type ==  "Agent"){ ?>
                                <li><a class="dropdown-item fadeout" href="<?=root.('reports/'.date("Y"))?>"> <?=T::reports?></i></a></li>
                                <?php } ?>
                                <!-- <li><a class="dropdown-item fadeout" href="<?=root?>wallet"> <?=T::wallet?></i></a></li> -->
                                <li><a class="dropdown-item fadeout" href="<?=root?>profile"> <?=T::profile?></i></a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item fadeout" href="<?=root?>logout"> <?=T::logout?></i></a></li>
                            </ul>
                            <?php } ?>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <!-- nav items end  -->
        </div>
    </header>



    <header data-add-bg="bg-white" class="header  js-header" data-x="header" data-x-toggle="is-menu-opened">
      <div data-anim="fade" class="header__container header__container-1500 mx-auto px-30 sm:px-20">
        <div class="row justify-between items-center">

          <div class="col-auto">
            <div class="d-flex items-center">
              <a href="index.html" class="header-logo mr-50" data-x="header-logo" data-x-toggle="is-logo-dark">
                <img src="img/general/logo-dark.svg" alt="logo icon">
                <img src="img/general/logo-dark.svg" alt="logo icon">
              </a>


              <div class="header-menu " data-x="mobile-menu" data-x-toggle="is-menu-active">
                <div class="mobile-overlay"></div>

                <div class="header-menu__content">
                  <div class="mobile-bg js-mobile-bg"></div>

                  <div class="menu js-navList">
                    <ul class="menu__nav text-dark-1 -is-active">

                      <li class="menu-item-has-children">
                        <a data-barba href="">
                          <span class="mr-10">Home</span>
                          <i class="icon icon-chevron-sm-down"></i>
                        </a>


                        <ul class="subnav">
                          <li class="subnav__backBtn js-nav-list-back">
                            <a href="#"><i class="icon icon-chevron-sm-down"></i> Home</a>
                          </li>

                          <li><a href="index.html">Home 1</a></li>

                          <li><a href="home-2.html">Home 2</a></li>

                          <li><a href="home-3.html">Home 3</a></li>

                          <li><a href="home-4.html">Home 4</a></li>

                          <li><a href="home-5.html">Home 5</a></li>

                          <li><a href="home-6.html">Home 6</a></li>

                          <li><a href="home-7.html">Home 7</a></li>

                          <li><a href="home-8.html">Home 8</a></li>

                          <li><a href="home-9.html">Home 9</a></li>

                          <li><a href="home-10.html">Home 10</a></li>

                        </ul>

                      </li>


                      <li class="menu-item-has-children -has-mega-menu">
                        <a data-barba href="#">
                          <span class="mr-10">Categories</span>
                          <i class="icon icon-chevron-sm-down"></i>
                        </a>

                        <div class="mega">
                          <div class="tabs -underline-2 js-tabs">
                            <div class="tabs__controls row x-gap-40 y-gap-10 lg:x-gap-20 pb-30 js-tabs-controls">

                              <div class="col-auto">
                                <button class="tabs__button text-light-1 fw-500 js-tabs-button is-tab-el-active" data-tab-target=".-tab-item-1">Hotel</button>
                              </div>

                              <div class="col-auto">
                                <button class="tabs__button text-light-1 fw-500 js-tabs-button " data-tab-target=".-tab-item-2">Tour</button>
                              </div>

                              <div class="col-auto">
                                <button class="tabs__button text-light-1 fw-500 js-tabs-button " data-tab-target=".-tab-item-3">Activity</button>
                              </div>

                              <div class="col-auto">
                                <button class="tabs__button text-light-1 fw-500 js-tabs-button " data-tab-target=".-tab-item-4">Holiday Rentals</button>
                              </div>

                              <div class="col-auto">
                                <button class="tabs__button text-light-1 fw-500 js-tabs-button " data-tab-target=".-tab-item-5">Car</button>
                              </div>

                              <div class="col-auto">
                                <button class="tabs__button text-light-1 fw-500 js-tabs-button " data-tab-target=".-tab-item-6">Cruise</button>
                              </div>

                              <div class="col-auto">
                                <button class="tabs__button text-light-1 fw-500 js-tabs-button " data-tab-target=".-tab-item-7">Flights</button>
                              </div>

                            </div>

                            <div class="tabs__content js-tabs-content">
                              <div class="tabs__pane -tab-item-1 is-tab-el-active">
                                <div class="mega__content">
                                  <div class="mega__grid">

                                    <div class="mega__item">
                                      <div class="text-15 fw-500">Hotel List</div>
                                      <div class="y-gap-5 text-15 pt-5">

                                        <div><a href="hotel-list-1.html">Hotel List v1</a></div>

                                        <div><a href="hotel-list-2.html">Hotel List v2</a></div>

                                        <div><a href="hotel-half-map.html">Hotel List v3</a></div>

                                        <div><a href="hotel-grid-1.html">Hotel List v4</a></div>

                                        <div><a href="hotel-grid-2.html">Hotel List v5</a></div>

                                      </div>
                                    </div>

                                    <div class="mega__item">
                                      <div class="text-15 fw-500">Hotel Single</div>
                                      <div class="y-gap-5 text-15 pt-5">

                                        <div><a href="hotel-single-1.html">Hotel Single v1</a></div>

                                        <div><a href="hotel-single-2.html">Hotel Single v2</a></div>

                                      </div>
                                    </div>

                                    <div class="mega__item">
                                      <div class="text-15 fw-500">Hotel Booking</div>
                                      <div class="y-gap-5 text-15 pt-5">

                                        <div><a href="booking-pages.html">Booking Page</a></div>

                                      </div>
                                    </div>

                                  </div>

                                  <div class="mega__image d-flex relative">
                                    <img src="#" data-src="img/backgrounds/7.png" alt="image" class="rounded-4 js-lazy">

                                    <div class="absolute w-full h-full px-30 py-24">
                                      <div class="text-22 fw-500 lh-15 text-white">Things to do on <br> your trip</div>
                                      <button class="button h-50 px-30 -blue-1 text-dark-1 bg-white mt-20">Experinces</button>
                                    </div>
                                  </div>
                                </div>
                              </div>

                              <div class="tabs__pane -tab-item-2">
                                <div class="mega__content">
                                  <div class="mega__grid">

                                    <div class="mega__item">
                                      <div class="text-15 fw-500">Tour List</div>
                                      <div class="y-gap-5 text-15 pt-5">

                                        <div><a href="tour-list-1.html">Tour List v1</a></div>

                                        <div><a href="tour-grid-1.html">Tour List v2</a></div>

                                      </div>
                                    </div>

                                    <div class="mega__item">
                                      <div class="text-15 fw-500">Tour Pages</div>
                                      <div class="y-gap-5 text-15 pt-5">

                                        <div><a href="tour-map.html">Tour Map</a></div>

                                        <div><a href="tour-single.html">Tour Single</a></div>

                                      </div>
                                    </div>

                                  </div>

                                  <div class="mega__image d-flex relative">
                                    <img src="img/backgrounds/7.png" alt="image" class="rounded-4">

                                    <div class="absolute w-full h-full px-30 py-24">
                                      <div class="text-22 fw-500 lh-15 text-white">Things to do on <br> your trip</div>
                                      <button class="button h-50 px-30 -blue-1 text-dark-1 bg-white mt-20">Experinces</button>
                                    </div>
                                  </div>
                                </div>
                              </div>

                              <div class="tabs__pane -tab-item-3">
                                <div class="mega__content">
                                  <div class="mega__grid">

                                    <div class="mega__item">
                                      <div class="text-15 fw-500">Activity List</div>
                                      <div class="y-gap-5 text-15 pt-5">

                                        <div><a href="activity-list-1.html">Activity List v1</a></div>

                                        <div><a href="activity-grid-1.html">Activity List v2</a></div>

                                      </div>
                                    </div>

                                    <div class="mega__item">
                                      <div class="text-15 fw-500">Activity Pages</div>
                                      <div class="y-gap-5 text-15 pt-5">

                                        <div><a href="activity-map.html">Activity Map</a></div>

                                        <div><a href="activity-single.html">Activity Single</a></div>

                                      </div>
                                    </div>

                                  </div>

                                  <div class="mega__image d-flex relative">
                                    <img src="img/backgrounds/7.png" alt="image" class="rounded-4">

                                    <div class="absolute w-full h-full px-30 py-24">
                                      <div class="text-22 fw-500 lh-15 text-white">Things to do on <br> your trip</div>
                                      <button class="button h-50 px-30 -blue-1 text-dark-1 bg-white mt-20">Experinces</button>
                                    </div>
                                  </div>
                                </div>
                              </div>

                              <div class="tabs__pane -tab-item-4">
                                <div class="mega__content">
                                  <div class="mega__grid">

                                    <div class="mega__item">
                                      <div class="text-15 fw-500">Rental List</div>
                                      <div class="y-gap-5 text-15 pt-5">

                                        <div><a href="rental-list-1.html">Rental List v1</a></div>

                                        <div><a href="rental-grid-1.html">Rental List v2</a></div>

                                      </div>
                                    </div>

                                    <div class="mega__item">
                                      <div class="text-15 fw-500">Rental Pages</div>
                                      <div class="y-gap-5 text-15 pt-5">

                                        <div><a href="rental-map.html">Rental Map</a></div>

                                        <div><a href="rental-single.html">Rental Single</a></div>

                                      </div>
                                    </div>

                                  </div>

                                  <div class="mega__image d-flex relative">
                                    <img src="img/backgrounds/7.png" alt="image" class="rounded-4">

                                    <div class="absolute w-full h-full px-30 py-24">
                                      <div class="text-22 fw-500 lh-15 text-white">Things to do on <br> your trip</div>
                                      <button class="button h-50 px-30 -blue-1 text-dark-1 bg-white mt-20">Experinces</button>
                                    </div>
                                  </div>
                                </div>
                              </div>

                              <div class="tabs__pane -tab-item-5">
                                <div class="mega__content">
                                  <div class="mega__grid">

                                    <div class="mega__item">
                                      <div class="text-15 fw-500">Car List</div>
                                      <div class="y-gap-5 text-15 pt-5">

                                        <div><a href="car-list-1.html">Car List v1</a></div>

                                        <div><a href="car-grid-1.html">Car List v2</a></div>

                                      </div>
                                    </div>

                                    <div class="mega__item">
                                      <div class="text-15 fw-500">Car Pages</div>
                                      <div class="y-gap-5 text-15 pt-5">

                                        <div><a href="car-map.html">Car Map</a></div>

                                        <div><a href="car-single.html">Car Single</a></div>

                                      </div>
                                    </div>

                                  </div>

                                  <div class="mega__image d-flex relative">
                                    <img src="img/backgrounds/7.png" alt="image" class="rounded-4">

                                    <div class="absolute w-full h-full px-30 py-24">
                                      <div class="text-22 fw-500 lh-15 text-white">Things to do on <br> your trip</div>
                                      <button class="button h-50 px-30 -blue-1 text-dark-1 bg-white mt-20">Experinces</button>
                                    </div>
                                  </div>
                                </div>
                              </div>

                              <div class="tabs__pane -tab-item-6">
                                <div class="mega__content">
                                  <div class="mega__grid">

                                    <div class="mega__item">
                                      <div class="text-15 fw-500">Cruise List</div>
                                      <div class="y-gap-5 text-15 pt-5">

                                        <div><a href="cruise-list-1.html">Cruise List v1</a></div>

                                        <div><a href="cruise-grid-1.html">Cruise List v2</a></div>

                                      </div>
                                    </div>

                                    <div class="mega__item">
                                      <div class="text-15 fw-500">Cruise Pages</div>
                                      <div class="y-gap-5 text-15 pt-5">

                                        <div><a href="cruise-map.html">Cruise Map</a></div>

                                        <div><a href="cruise-single.html">Cruise Single</a></div>

                                      </div>
                                    </div>

                                  </div>

                                  <div class="mega__image d-flex relative">
                                    <img src="img/backgrounds/7.png" alt="image" class="rounded-4">

                                    <div class="absolute w-full h-full px-30 py-24">
                                      <div class="text-22 fw-500 lh-15 text-white">Things to do on <br> your trip</div>
                                      <button class="button h-50 px-30 -blue-1 text-dark-1 bg-white mt-20">Experinces</button>
                                    </div>
                                  </div>
                                </div>
                              </div>

                              <div class="tabs__pane -tab-item-7">
                                <div class="mega__content">
                                  <div class="mega__grid">

                                    <div class="mega__item">
                                      <div class="text-15 fw-500">Flight List</div>
                                      <div class="y-gap-5 text-15 pt-5">

                                        <div><a href="flights-list.html">Flight list v1</a></div>

                                      </div>
                                    </div>

                                  </div>

                                  <div class="mega__image d-flex relative">
                                    <img src="img/backgrounds/7.png" alt="image" class="rounded-4">

                                    <div class="absolute w-full h-full px-30 py-24">
                                      <div class="text-22 fw-500 lh-15 text-white">Things to do on <br> your trip</div>
                                      <button class="button h-50 px-30 -blue-1 text-dark-1 bg-white mt-20">Experinces</button>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <ul class="subnav mega-mobile">
                          <li class="subnav__backBtn js-nav-list-back">
                            <a href="#"><i class="icon icon-chevron-sm-down"></i> Category</a>
                          </li>

                          <li class="menu-item-has-children">
                            <a data-barba href="#">
                              <span class="mr-10">Hotel</span>
                              <i class="icon icon-chevron-sm-down"></i>
                            </a>

                            <ul class="subnav">
                              <li class="subnav__backBtn js-nav-list-back">
                                <a href="#"><i class="icon icon-chevron-sm-down"></i> Hotel</a>
                              </li>


                              <li><a href="hotel-list-1.html">Hotel List v1</a></li>

                              <li><a href="hotel-list-2.html">Hotel List v2</a></li>

                              <li><a href="hotel-single-1.html">Hotel Single v1</a></li>

                              <li><a href="hotel-single-2.html">Hotel Single v2</a></li>

                              <li><a href="booking-pages.html">Booking Page</a></li>

                            </ul>
                          </li>

                          <li class="menu-item-has-children">
                            <a data-barba href="#">
                              <span class="mr-10">Tour</span>
                              <i class="icon icon-chevron-sm-down"></i>
                            </a>

                            <ul class="subnav">
                              <li class="subnav__backBtn js-nav-list-back">
                                <a href="#"><i class="icon icon-chevron-sm-down"></i> Tour</a>
                              </li>

                              <li><a href="tour-list-1.html">Tour List v1</a></li>

                              <li><a href="tour-grid-1.html">Tour List v2</a></li>

                              <li><a href="tour-map.html">Tour Map</a></li>

                              <li><a href="tour-single.html">Tour Single</a></li>

                            </ul>
                          </li>

                          <li class="menu-item-has-children">
                            <a data-barba href="#">
                              <span class="mr-10">Activity</span>
                              <i class="icon icon-chevron-sm-down"></i>
                            </a>

                            <ul class="subnav">
                              <li class="subnav__backBtn js-nav-list-back">
                                <a href="#"><i class="icon icon-chevron-sm-down"></i> Activity</a>
                              </li>

                              <li><a href="activity-list-1.html">Activity List v1</a></li>

                              <li><a href="activity-grid-1.html">Activity List v2</a></li>

                              <li><a href="activity-map.html">Activity Map</a></li>

                              <li><a href="activity-single.html">Activity Single</a></li>

                            </ul>
                          </li>

                          <li class="menu-item-has-children">
                            <a data-barba href="#">
                              <span class="mr-10">Rental</span>
                              <i class="icon icon-chevron-sm-down"></i>
                            </a>

                            <ul class="subnav">
                              <li class="subnav__backBtn js-nav-list-back">
                                <a href="#"><i class="icon icon-chevron-sm-down"></i> Rental</a>
                              </li>

                              <li><a href="rental-list-1.html">Rental List v1</a></li>

                              <li><a href="rental-grid-1.html">Rental List v2</a></li>

                              <li><a href="rental-map.html">Rental Map</a></li>

                              <li><a href="rental-single.html">Rental Single</a></li>

                            </ul>
                          </li>

                          <li class="menu-item-has-children">
                            <a data-barba href="#">
                              <span class="mr-10">Car</span>
                              <i class="icon icon-chevron-sm-down"></i>
                            </a>

                            <ul class="subnav">
                              <li class="subnav__backBtn js-nav-list-back">
                                <a href="#"><i class="icon icon-chevron-sm-down"></i> Car</a>
                              </li>

                              <li><a href="car-list-1.html">Car List v1</a></li>

                              <li><a href="car-grid-1.html">Car List v2</a></li>

                              <li><a href="car-map.html">Car Map</a></li>

                              <li><a href="car-single.html">Car Single</a></li>

                            </ul>
                          </li>

                          <li class="menu-item-has-children">
                            <a data-barba href="#">
                              <span class="mr-10">Cruise</span>
                              <i class="icon icon-chevron-sm-down"></i>
                            </a>

                            <ul class="subnav">
                              <li class="subnav__backBtn js-nav-list-back">
                                <a href="#"><i class="icon icon-chevron-sm-down"></i> Cruise</a>
                              </li>

                              <li><a href="cruise-list-1.html">Cruise List v1</a></li>

                              <li><a href="cruise-grid-1.html">Cruise List v2</a></li>

                              <li><a href="cruise-map.html">Cruise Map</a></li>

                              <li><a href="cruise-single.html">Cruise Single</a></li>

                            </ul>
                          </li>

                          <li class="menu-item-has-children">
                            <a data-barba href="#">
                              <span class="mr-10">Flights</span>
                              <i class="icon icon-chevron-sm-down"></i>
                            </a>

                            <ul class="subnav">
                              <li class="subnav__backBtn js-nav-list-back">
                                <a href="#"><i class="icon icon-chevron-sm-down"></i> Flights</a>
                              </li>

                              <li><a href="flights-list.html">Flights List v1</a></li>

                            </ul>
                          </li>
                        </ul>
                      </li>

                      <li>
                        <a href="destinations.html">
                          Destinations
                        </a>
                      </li>


                      <li class="menu-item-has-children">
                        <a data-barba href="">
                          <span class="mr-10">Blog</span>
                          <i class="icon icon-chevron-sm-down"></i>
                        </a>


                        <ul class="subnav">
                          <li class="subnav__backBtn js-nav-list-back">
                            <a href="#"><i class="icon icon-chevron-sm-down"></i> Blog</a>
                          </li>

                          <li><a href="blog-list-1.html">Blog list v1</a></li>

                          <li><a href="blog-list-2.html">Blog list v2</a></li>

                          <li><a href="blog-single.html">Blog single</a></li>

                        </ul>

                      </li>


                      <li class="menu-item-has-children">
                        <a data-barba href="">
                          <span class="mr-10">Pages</span>
                          <i class="icon icon-chevron-sm-down"></i>
                        </a>


                        <ul class="subnav">
                          <li class="subnav__backBtn js-nav-list-back">
                            <a href="#"><i class="icon icon-chevron-sm-down"></i> Pages</a>
                          </li>

                          <li><a href="404.html">404</a></li>

                          <li><a href="about.html">About</a></li>

                          <li><a href="become-expert.html">Become expert</a></li>

                          <li><a href="help-center.html">Help center</a></li>

                          <li><a href="login.html">Login</a></li>

                          <li><a href="signup.html">Register</a></li>

                          <li><a href="terms.html">Terms</a></li>

                          <li><a href="invoice.html">Invoice</a></li>

                          <li><a href="ui-elements.html">UI elements</a></li>

                        </ul>

                      </li>


                      <li class="menu-item-has-children">
                        <a data-barba href="">
                          <span class="mr-10">Dashboard</span>
                          <i class="icon icon-chevron-sm-down"></i>
                        </a>


                        <ul class="subnav">
                          <li class="subnav__backBtn js-nav-list-back">
                            <a href="#"><i class="icon icon-chevron-sm-down"></i> Dashboard</a>
                          </li>

                          <li><a href="db-dashboard.html">Dashboard</a></li>

                          <li><a href="db-booking.html">Booking</a></li>

                          <li><a href="db-settings.html">Settings</a></li>

                          <li><a href="db-wishlist.html">Wishlist</a></li>

                          <li><a href="db-vendor-dashboard.html">Vendor dashboard</a></li>

                          <li><a href="db-vendor-add-hotel.html">Vendor add hotel</a></li>

                          <li><a href="db-vendor-booking.html">Vendor booking</a></li>

                          <li><a href="db-vendor-hotels.html">Vendor hotels</a></li>

                          <li><a href="db-vendor-recovery.html">Vendor recovery</a></li>

                        </ul>

                      </li>


                      <li>
                        <a href="contact.html">Contact</a>
                      </li>
                    </ul>
                  </div>

                  <div class="mobile-footer px-20 py-20 border-top-light js-mobile-footer">
                  </div>
                </div>
              </div>

            </div>
          </div>


          <div class="col-auto">
            <div class="d-flex items-center">

              <div class="row x-gap-20 items-center xxl:d-none">
                <div class="col-auto">
                  <button class="d-flex items-center text-14 text-dark-1" data-x-click="currency">
                    <span class="js-currencyMenu-mainTitle">USD</span>
                    <i class="icon-chevron-sm-down text-7 ml-10"></i>
                  </button>
                </div>

                <div class="col-auto">
                  <div class="w-1 h-20 bg-black-20"></div>
                </div>

                <div class="col-auto">
                  <button class="d-flex items-center text-14 text-dark-1" data-x-click="lang">
                    <img src="img/general/lang.png" alt="image" class="rounded-full mr-10">
                    <span class="js-language-mainTitle">United Kingdom</span>
                    <i class="icon-chevron-sm-down text-7 ml-15"></i>
                  </button>
                </div>
              </div>


              <div class="d-flex items-center ml-20 is-menu-opened-hide md:d-none">
                <a href="login.html" class="button px-30 fw-400 text-14 -blue-1 bg-dark-4 h-50 text-white">Become An Expert</a>
                <a href="signup.html" class="button px-30 fw-400 text-14 border-dark-4 -blue-1 h-50 text-dark-4 ml-20">Sign In / Register</a>
              </div>

              <div class="d-none xl:d-flex x-gap-20 items-center pl-30" data-x="header-mobile-icons" data-x-toggle="text-white">
                <div><a href="login.html" class="d-flex items-center icon-user text-inherit text-22"></a></div>
                <div><button class="d-flex items-center icon-menu text-inherit text-20" data-x-click="html, header, header-logo, header-mobile-icons, mobile-menu"></button></div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </header>


    

    <script>
    // HEADER NAVBAR
    let resize = true;
    $(window).scroll(function() {
        var nav = $('#navbarMain');
        var top = 50;
        if ($(window).scrollTop() >= top && resize) {
            $("header").addClass('swap_navbar');
        } else if(resize) {
            $("header").removeClass('swap_navbar');
        }
    });

    $(window).on("load", (function() {
        if($(window).innerWidth() + 10 < 769) {
            $("header").addClass('swap_navbar');
            resize = false;
        }
        else {
            $("header").removeClass('swap_navbar');
            resize = true;
        }
    }));

    $(window).on("load", (function() {
    var scroll = $(window).scrollTop();
    if (scroll > 20 ) {
        $("header").addClass('swap_navbar');
    }
    }) )

    // Check if website is being executed from localhost
    <?php if ($_SERVER['HTTP_HOST'] !== 'localhost') { ?>
        setTimeout(function() {
            // Get user's country
            var requestUrl = "<?=root?>visitor_details";
            fetch(requestUrl)
            .then(function(response) { return response.json(); })
            .then(function(c) {
                if (typeof c.country_code === "undefined") {
                    // If country_code is undefined, log and return or handle appropriately
                    console.log("Localhost traffic not counted");
                } else {
                    console.log(c.country_code);
                    var country = c.country_code.toUpperCase();
                    // Submit to db
                    var req = '<?=root.api_url?>traffic?country_code=' + country;
                    fetch(req);
                }
            });
        }, 5000);
    <?php } ?>

    </script>

<div class="bodyload" style="margin-top:80px;display:none">
 <div class="rotatingDiv"></div>
</div>

<main>