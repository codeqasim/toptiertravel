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

    <header data-add-bg="bg-white" class="header  js-header" data-x="header" data-x-toggle="is-menu-opened">
      <div data-anim="fade" class="header__container header__container-1500 mx-auto px-30 sm:px-20">
        <div class="row justify-between items-center">

          <div class="col-auto">
            <div class="d-flex items-center">
              <a href="<?=root?>" class="header-logo mr-50" data-x="header-logo" data-x-toggle="is-logo-dark">
                <!-- <img src="img/general/logo-dark.svg" alt="logo icon"> -->
                <img src="<?=root?>uploads/global/logo.png" alt="logo icon">
              </a>

              <div class="header-menu " data-x="mobile-menu" data-x-toggle="is-menu-active">
                <div class="mobile-overlay"></div>

                <div class="header-menu__content">
                  <div class="mobile-bg js-mobile-bg"></div>

                  <div class="menu js-navList">
                    <ul class="menu__nav text-dark-1 -is-active">

                      <li class="menu-item-has-children">
                        <a data-barba href="<?=root?>">
                          <span class=""><?=T::home?></span>
                          <i class="icon icon-chevron-sm-down"></i>
                        </a>

                      </li>

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



                      <li class="menu-item-has-children d-none">
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

                      <li>
                        <a href="<?=root?>page/contact/us"><?=T::contact?></a>
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


            <?php if (app()->app->multi_currency == 1) {?>
                        <li class="nav-item dropdown multi_currency">
                            <a class="nav-link dropdown-toggle px-0 ps-3 text-center d-flex align-items-center justify-content-center gap-1 border-0 text-dark"
                                href="javascript:void(0)" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="h6 m-0 header_options">
                                    <?php if (!isset($_SESSION['phptravels_client_currency'])) { ?>
                                    <?php foreach (app()->currencies as $currency){ if($currency->default == 1){ echo $currency->name; } }?>
                                    <?php } else {?>
                                    <?=$_SESSION['phptravels_client_currency']?>
                                    <?php } ?>
                                </span>
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



              <div class="row x-gap-20 items-center xxl:d-none">


                <div class="col-auto">
                  <div class="w-1 h-20 bg-black-20"></div>
                </div>



                <?php if (app()->app->multi_language == 1) {?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle ps-3 p-0 py-2 px-0 text-center d-flex align-items-center justify-content-center gap-0 border-0"
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



              </div>

              <div class="d-flex items-center ml-20 is-menu-opened-hide md:d-none">


              <?php if (app()->app->user_registration == 1) {?>




                            <?php if(!isset($_SESSION['phptravels_client']->user_id)) { ?>

                                <a href="<?=root?>login" class="button px-30 fw-400 text-14 -blue-1 bg-dark-4 h-50 text-white px-5"><?=T::login?></a>

                                <a href="<?=root?>signup" class="button px-30 fw-400 text-14 border-dark-4 -blue-1 h-50 text-dark-4 ml-20"><?=T::signup?></a>

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