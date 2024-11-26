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

</head>

<body id="fadein">
<!-- THEME PRIMARY COLOR -->
<style>:root {--theme-bg: <?=base()->app->default_theme?>}</style>
<?php
require_once "App/Views/DemoContent.php";
?>
    <header class="navbar fixed-top navbar-expand-lg p-lg-0">
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