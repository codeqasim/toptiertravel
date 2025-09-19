<!DOCTYPE html>
<html lang="en" dir="<?= $USER_SESSION->backend_user_language_position ?>" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="close"><head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title><?php if (isset($title)) {echo $title;} ?></title>
    <link rel="shortcut icon" href="../uploads/global/favicon.png?v<?= rand(0, 99999999999) ?>">
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta http-equiv="Cache-control" content="private">
    <meta http-equiv="refresh" content="4000; ./login-logout.php" />
    <link rel="stylesheet" href="./assets/css/style.css" />
    <link rel="stylesheet" href="./assets/css/app.css" />
    <!-- Choices JS -->
    <script src="./spruha-assets/libs/choices.js/public/assets/scripts/choices.min.js"></script>

    <!-- Bootstrap Css -->
    <link id="style" href="./spruha-assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet" >

    <!-- Main Theme Js -->
    <script src="./spruha-assets/js/main.js"></script>

    <!-- Style Css -->
    <link href="./spruha-assets/css/styles.min.css" rel="stylesheet" >

    <!-- Icons Css -->
    <link href="./spruha-assets/css/icons.css" rel="stylesheet" >

    <!-- Node Waves Css -->
    <link href="./spruha-assets/libs/node-waves/waves.min.css" rel="stylesheet" >

    <!-- Simplebar Css -->
    <link href="./spruha-assets/libs/simplebar/simplebar.min.css" rel="stylesheet" >

    <!-- Color Picker Css -->
    <link rel="stylesheet" href="./spruha-assets/libs/flatpickr/flatpickr.min.css">
    <link rel="stylesheet" href="./spruha-assets/libs/@simonwep/pickr/themes/nano.min.css">

    <!-- Choices Css -->
    <link rel="stylesheet" href="./spruha-assets/libs/choices.js/public/assets/styles/choices.min.css">


    <link rel="stylesheet" href="./spruha-assets/libs/jsvectormap/css/jsvectormap.min.css">

    <link rel="stylesheet" href="./spruha-assets/libs/swiper/swiper-bundle.min.css">


    <script src="./assets/js/jquery-3.6.0.min.js"></script>
    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">-->
</head>

<?php
if (DECODE($_SESSION['phptravels_backend_user'])->backend_user_type == "Agent" || DECODE($_SESSION['phptravels_backend_user'])->backend_user_type == "agent"){
    REDIRECT("./login-logout.php");
    exit;
}else {

?>

<?php if($USER_SESSION->backend_user_language_position=="rtl"){?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="./assets/css/rtl.css" />
    <style>
        *{text-decoration:none !important}
        main header .btn{border:transparent !important}
        header ul .alerts  button {display: flex}
    </style>
<?php } ?>

<body>
<div class="bodyload">
    <div class="rotatingDiv"></div>
</div>
<script>
    setTimeout(function() {
        $('.bodyload').fadeOut();
    }, 100);

    $(document).on("click", ".loadeffect, .loading_effect", function() {
        var newUrl = $(this).attr("href");
        if (!newUrl || newUrl[0] === "#") {
            location.hash = newUrl;
            return;
        }
        $('.bodyload').fadeIn();
        location = newUrl;
        return false;
    });
</script>

<main>

    <?php $url_name = basename($_SERVER['PHP_SELF'], ".php");


    // dd($url_name);

    $params = array("user_id" => $USER_SESSION->backend_user_id);
    $user_type_id = GET('users', $params)[0]->user_type;

    // dd($user_type_id);

    if (empty($user_type_id)) {
        echo '<div style="gap:10px; width: 100%; display: flex; justify-content: center; align-items: center; background: #1b2a47; color: #fff; font-size: 14px;">This user has no " User Type "
<a href="./login-logout.php" data-toggle="tooltip" data-placement="top" title="Previous Page" class="loading_effect btn btn-warning">  Logout</a>
</div>';
        exit;
    }

    $params = array("type_name" => $user_type_id);
    $data = GET('users_roles', $params)[0]->permissions;
    $user_permissions = (json_decode($data));

    // $access_not_allowed = '<div style="gap:10px; width: 100%; display: flex; justify-content: center; align-items: center; background: #1b2a47; color: #fff; font-size: 14px;">Page Access Not Allowed
    // <a href="javascript:window.history.back();" data-toggle="tooltip" data-placement="top" title="Previous Page" class="loading_effect btn btn-warning">  Back</a>
    // </div>';

    foreach ($pages as $p => $i) {

        if (isset($user_permissions->modules->edit)) {
            if ($url_name == "module-setting") {
                // echo $access_not_allowed;
                REDIRECT("./login-logout.php");
                exit;
            }
        }

        if (!isset($user_permissions->$p->page_access) && $url_name == $p) {
            // echo $access_not_allowed;
            REDIRECT("./login-logout.php");
            exit;
        }
    }
    if (isset($user_permissions->$url_name->add)) {
        $permission_add = 1;
    }
    if (isset($user_permissions->$url_name->edit)) {
        $permission_edit = 1;
    } else {
        $alert_edit = 1;
    }
    if (isset($user_permissions->$url_name->view)) {
        $permission_view = 1;
    }
    if (isset($user_permissions->$url_name->delete)) {
        $permission_delete = 1;
    }

    $params = array("status" => 1);
    $modules=(GET("modules",$params));

    $temp_module = array_unique(array_column($modules, 'type'));
    $module_status = array_intersect_key($modules, $temp_module);

    $keys = array_column($module_status, 'order');
    array_multisort($keys, SORT_ASC, $module_status);

    foreach ($module_status as $module){
        if($module->type == "flights" && $module->status == 1 && $module->name =="flights"){$flight_active = 1;}
        if($module->type == "hotels" && $module->status == 1 && $module->name =="hotels"){$hotels_active = 1;}
        if($module->type == "tours" && $module->status == 1 && $module->name =="tours"){$tours_active = 1;}
        if($module->type == "cars" && $module->status == 1 && $module->name =="cars"){$cars_active = 1;}
        if($module->type == "visa" && $module->status == 1 && $module->name =="visa"){$visa_active = 1;}
        if($module->type == "extra" && $module->status == 1 && $module->name =="blog"){$blog_active = 1;}

    } ?>
         <!-- app-header -->
         <header class="app-header">

            <!-- Start::main-header-container -->
            <div class="main-header-container container-fluid">

                <!-- Start::header-content-left -->
                <div class="header-content-left">

                    <div class="header-element">
                        <!-- Start::header-link -->
                        <a aria-label="Hide Sidebar" class="sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle" data-bs-toggle="sidebar" href="javascript:void(0);"><span></span></a>
                    </div>
                <div class="header-content-right">
            </div>
        </header>
        <!-- /app-header -->
        <!-- Start::app-sidebar -->
        <aside class="app-sidebar sticky" id="sidebar">

            <!-- Start::main-sidebar-header -->
            <!-- <div class="main-sidebar-header"> -->
            <div class="main-sidebar-header p-3 d-flex items-align-center justify-content-between border-bottom pb-3 mb-2">
        <a href="./dashboard.php" class="loadeffect d-flex align-items-center link-light text-decoration-none gap-3">
            <img src="../uploads/global/favicon.png?v<?= rand(0, 99999999999) ?>"
                style="max-width: 30px; border-radius:10px">
            <span class="fw-semibold"><?= T::dashboard ?></span>
        </a>
        <a href="<?= root . ('../') ?>" target="_blank">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <g fill="none" fill-rule="evenodd">
                    <path
                        d="M18 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8c0-1.1.9-2 2-2h5M15 3h6v6M10 14L20.2 3.8" />
                </g>
            </svg>
        </a>
    <!-- </div> -->
            </div>
            <!-- End::main-sidebar-header -->

            <!-- Start::main-sidebar -->
            <div class="main-sidebar" id="sidebar-scroll">
                <!-- Start::nav -->
                    <!-- Start::nav -->
    <nav class="main-menu-container nav nav-pills flex-column sub-open">
        <div class="slide-left" id="slide-left">
            <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"> <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path> </svg>
        </div>
        <ul class="main-menu">
            <!-- Start::slide__category -->
            <!-- <li class="slide__category"><span class="category-name">Dashboard</span></li> -->
            <!-- End::slide__category -->

            <!-- Start::slide -->

            <li class="slide">
<a href="./dashboard.php" class="side-menu__item <?php if ($url_name == 'dashboard') { echo "active"; } ?>">
<span class="shape1"></span>
<span class="shape2"></span>
<i class="ti-home side-menu__icon"></i>
<span class="side-menu__label"><?= T::dashboard ?></span>
</a>
</li>
            
        
            <!-- <li class="slide">
            <a href="./dashboard.php" class="side-menu__item">
                <span class="shape1"></span>
                <span class="shape2"></span>
                <i class="ti-home side-menu__icon"></i>
                <span class="side-menu__label">Dashboard</span>
            </a>
         </li> -->
            <!-- End::slide -->
             <!-- start slide -->
             <?php
// ADMIN PERMISSION
if (DECODE($_SESSION['phptravels_backend_user'])->backend_user_type == "Admin" || DECODE($_SESSION['phptravels_backend_user'])->backend_user_type == "admin") {

    $params = array('status' => 1);
    $count = GET('notifications', $params);
    $notificationCount = count($count);

?>
<li class="slide">
    <a href="javascript:void(0);" class="side-menu__item <?php if ($url_name == "alerts"){ echo "active"; } ?>" data-bs-toggle="dropdown">
        <span class="shape1"></span>
        <span class="shape2"></span>
        <i class="side-menu__icon fas fa-bell"></i>
        <span class="side-menu__label"><?= T::alerts ?></span>
        <span class="side-menu__angle bg-danger p-1 rounded-5 px-2 notificaition_count_number" style="font-size:10px"><?= $notificationCount ?></span>
    </a>
<?php if ($notificationCount > 0) { ?>
    <ul class="dropdown-menu drapdown" style="z-index:9999; border-radius:0; position:fixed !important; margin: 0 240px; transform: none !important;">
        <hr>
        <?php
        array_multisort(array_column($count, 'date'), SORT_DESC, $count);
        foreach ($count as $c) { ?>
            <li>
                <a class="dropdown-item notification_<?=$c->id?>" href="#">
                    <button style="border-radius: 5px !important;" class="btn btn-warning btn-sm p-3 py-0" onclick="notification(<?=$c->id?>)">
                        <svg class="m-0" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="#ffffff" stroke="#ffffff" stroke-width="0" stroke-linecap="round" version="1.1">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                    </button>
                    <span class="px-2"><?=$c->name?></span>
                </a>
            </li>
        <?php } ?>
    </ul>
</li>
<?php
    }
}
?>

<script>
function notification(name) {
    $('.notification_' + name).fadeOut(200);
    var form = new FormData();
    form.append("notification_delete", "");
    form.append("id", name);
    var settings = {
        "url": "./_post.php",
        "method": "POST",
        "timeout": 0,
        "processData": false,
        "mimeType": "multipart/form-data",
        "contentType": false,
        "data": form
    };
    $.ajax(settings).done(function (response) {
        $('.notificaition_count_number').html(parseInt($('.notificaition_count_number').html(), 10) - 1);
    });
}
</script>


<!-- Bookings -->
 <!--
  <?php
if (isset($user_permissions->bookings->page_access)) {
?>
<li class="slide">
<a href="./bookings.php" class="side-menu__item <?php if ($url_name == 'bookings') { echo "active"; } ?>">
<span class="shape1"></span>
<span class="shape2"></span>
<i class="ti-calendar side-menu__icon"></i>
<span class="side-menu__label"><?= T::bookings ?></span>
</a>
</li> -->

<!-- End Bookings -->






<!-- Start::slide -->
<!-- Start::Markups Slide -->
<?php
if (isset($user_permissions->bookings->page_access)) {
?>
<li class="slide has-sub">
<a href="javascript:void(0);" class="side-menu__item">
<span class="shape1"></span>
<span class="shape2"></span>
<i class="ti-calendar side-menu__icon"></i>
<span class="side-menu__label"><?= T::bookings ?></span>
<i class="fe fe-chevron-right side-menu__angle"></i>
</a>
<?php
}
?>
<ul class="slide-menu child1">
<?php
if (isset($user_permissions->booking_add->page_access)) {
?>
<li class="slide">
<a href="./booking_add.php" class="side-menu__item <?php if ($url_name == 'booking_add') { echo "active"; } ?>">
<?= T::add ?> <?= T::booking ?>
</a>
</li>
<?php
}
?>
<?php
if (isset($user_permissions->all_bookings->page_access)) {
?>
<li class="slide">
<a href="./bookings.php" class="side-menu__item <?php if ($url_name == 'bookings') { echo "active"; } ?>">
<?= T::all ?> <?= T::bookings ?>
</a>
</li>
<?php
}
?>
<?php
if (isset($user_permissions->supplier_payments->page_access)) {
?>
<li class="slide">
<a href="./supplier_payments.php" class="side-menu__item <?php if ($url_name == 'supplier_payments') { echo "active"; } ?>">
<?= T::supplier ?> <?= T::payments?>
</a>
</li>
<?php
}
?>
<?php
if (isset($user_permissions->agents_commissions->page_access)) {
?>
<li class="slide">
<a href="./agents_commissions.php" class="side-menu__item <?php if ($url_name == 'agents_commissions') { echo "active"; } ?>">
<?= T:: agents ?> <?= T::commission?>
</a>
</li>
<?php
}
?>
</ul>
</li>
 <!-- End::Markups Slide -->
 <?php } ?>




<!-- end slide -->
<!-- Start::slide -->
<?php
if (
    isset($user_permissions->settings->page_access) ||
    isset($user_permissions->payment_gateways->page_access) ||
    isset($user_permissions->currencies->page_access) ||
    isset($user_permissions->locations->page_access) ||
    isset($user_permissions->email_settings->page_access) ||
    isset($user_permissions->languages->page_access) ||
    isset($user_permissions->users_roles->page_access) ||
    isset($user_permissions->countries->page_access)
) { ?>
<!-- Start::Settings Slide -->
<li class="slide has-sub">
    <a href="javascript:void(0);" class="side-menu__item">
        <span class="shape1"></span>
        <span class="shape2"></span>
        <i class="ti-settings side-menu__icon"></i>
        <span class="side-menu__label">Settings</span>
        <i class="fe fe-chevron-right side-menu__angle"></i>
    </a>
    <ul class="slide-menu child1">
        <li class="slide side-menu__label1">
            <a href="javascript:void(0)">Settings</a>
        </li>
        <!-- General Settings -->
        <?php if (isset($user_permissions->settings->page_access)) { ?>
        <li class="slide">
            <a href="./settings.php" class="side-menu__item <?= $url_name == 'settings' ? 'active' : '' ?>">
                <?= T::general ?> <?= T::settings ?>
            </a>
        </li>
        <?php } ?>

        <!-- Users & Roles -->
        <?php if (isset($user_permissions->users_roles->page_access)) { ?>
        <li class="slide">
            <a href="./users_roles.php" class="side-menu__item <?= $url_name == 'users_roles' ? 'active' : '' ?>">
                <?= T::users_roles ?>
            </a>
        </li>
        <?php } ?>

        <!-- Payment Gateways -->
        <?php if (isset($user_permissions->payment_gateways->page_access)) { ?>
        <li class="slide">
            <a href="./payment_gateways.php" class="side-menu__item <?= in_array($url_name, ['payment_gateways', 'payment_gateway']) ? 'active' : '' ?>">
                <?= T::payment_gateways ?>
            </a>
        </li>
        <?php } ?>

        <!-- Currencies -->
        <?php if (isset($user_permissions->currencies->page_access)) { ?>
        <li class="slide">
            <a href="./currencies.php" class="side-menu__item <?= $url_name == 'currencies' ? 'active' : '' ?>">
                <?= T::currencies ?>
            </a>
        </li>
        <?php } ?>

        <!-- Email Settings -->
        <?php if (isset($user_permissions->email_settings->page_access)) { ?>
        <li class="slide">
            <a href="./email_settings.php" class="side-menu__item <?= $url_name == 'email_settings' ? 'active' : '' ?>">
                <?= T::email_settings ?>
            </a>
        </li>
        <?php } ?>

        <!-- Locations -->
        <?php if (isset($user_permissions->locations->page_access)) { ?>
        <li class="slide">
            <a href="./locations.php" class="side-menu__item <?= $url_name == 'locations' ? 'active' : '' ?>">
                <?= T::locations ?>
            </a>
        </li>
        <?php } ?>

        <!-- Languages -->
        <?php if (isset($user_permissions->languages->page_access)) { ?>
        <li class="slide">
            <a href="./languages.php" class="side-menu__item <?= $url_name == 'languages' ? 'active' : '' ?>">
                <?= T::languages ?>
            </a>
        </li>
        <?php } ?>

        <!-- Countries -->
        <?php if (isset($user_permissions->countries->page_access)) { ?>
        <li class="slide">
            <a href="./countries.php" class="side-menu__item <?= $url_name == 'countries' ? 'active' : '' ?>">
                <?= T::countries ?>
            </a>
        </li>
        <?php } ?>
    </ul>
</li>
<?php } ?>
<!-- End::Settings Slide -->

<!-- Start::slide -->
<?php
if (isset($user_permissions->modules->page_access)) {
?>

<li class="slide">
<a href="./modules.php" class="side-menu__item <?php if ($url_name == "modules"){ echo "active"; } ?>">
<span class="shape1"></span>
<span class="shape2"></span>
<i class="fas fa-tags side-menu__icon"></i>
<span class="side-menu__label"><?= T::modules ?></span>
</a>
</li>

<?php
}
?>

<!-- End::slide -->

<!-- Start::slide -->
<!-- Start::Markups Slide -->
<?php
if (isset($user_permissions->markups->page_access)) {
?>
<li class="slide has-sub">
<a href="javascript:void(0);" class="side-menu__item">
<span class="shape1"></span>
<span class="shape2"></span>
<i class="ti-layers side-menu__icon"></i>
<span class="side-menu__label"><?= T::markups ?></span>
<i class="fe fe-chevron-right side-menu__angle"></i>
</a>

<ul class="slide-menu child1">
<li class="slide side-menu__label1">
<a href="javascript:void(0)">Markups</a>
</li>

<!-- Users -->
<li class="slide">
<a href="./markups.php?module=users" class="side-menu__item
    <?php if (isset($_GET['module']) && $_GET['module'] == 'users') { echo 'active'; } ?>">
    <?= T::users ?>
</a>
</li>

<!-- Dynamic Modules -->
<?php foreach ($module_status as $module) { ?>

<?php if (isset($module->type)) { ?>
    <?php if ($module->type == "hotels") { ?>
        <li class="slide">
            <a href="./markups.php?module=hotels" class="side-menu__item
                <?php if (isset($_GET['module']) && $_GET['module'] == 'hotels') { echo 'active'; } ?>">
                <?= T::hotels ?>
            </a>
        </li>
    <?php } ?>

    <?php if ($module->type == "flights") { ?>
        <li class="slide">
            <a href="./markups.php?module=flights" class="side-menu__item
                <?php if (isset($_GET['module']) && $_GET['module'] == 'flights') { echo 'active'; } ?>">
                <?= T::flights ?>
            </a>
        </li>
    <?php } ?>

    <?php if ($module->type == "tours") { ?>
        <li class="slide">
            <a href="./markups.php?module=tours" class="side-menu__item
                <?php if (isset($_GET['module']) && $_GET['module'] == 'tours') { echo 'active'; } ?>">
                <?= T::tours ?>
            </a>
        </li>
    <?php } ?>

    <?php if ($module->type == "cars") { ?>
        <li class="slide">
            <a href="./markups.php?module=cars" class="side-menu__item
                <?php if (isset($_GET['module']) && $_GET['module'] == 'cars') { echo 'active'; } ?>">
                <?= T::cars ?>
            </a>
        </li>
    <?php } ?>

    <!-- Optional Visa Module -->
    <?php if ($module->type == "visa") { ?>
        <li class="slide">
            <a href="./markups.php?module=visa" class="side-menu__item
                <?php if (isset($_GET['module']) && $_GET['module'] == 'visa') { echo 'active'; } ?>">
                <?= T::visa ?>
            </a>
        </li>
    <?php } ?>

<?php } ?>

<?php } ?>
</ul>
</li>
<?php } ?>
<!-- End::Markups Slide -->

<!-- Start::Users Slide -->
<?php
if (isset($user_permissions->users->page_access)) {
    $params = array();
    $users_roles = GET('users_roles', $params);
?>
<li class="slide has-sub">
    <a href="javascript:void(0);" class="side-menu__item">
        <span class="shape1"></span>
        <span class="shape2"></span>
        <i class="fa fa-user side-menu__icon"></i>
        <span class="side-menu__label"><?= T::users ?></span>
        <i class="fe fe-chevron-right side-menu__angle"></i>
    </a>

    <ul class="slide-menu child1">
        <li class="slide side-menu__label1">
            <a href="javascript:void(0)">Users</a>
        </li>

        <!-- All Users Page -->
        <?php if (isset($user_permissions->users->page_access)) { ?>
        <li class="slide">
            <a href="./users.php?users=all-users"
               class="side-menu__item <?php if (isset($_GET['users']) && $_GET['users'] == 'all-users') { echo 'active'; } ?>">
                <?= T::all . ' ' . T::users ?>
            </a>
        </li>
        <?php } ?>

        <hr class="m-0 my-1 mt-2 user_roles">

        <!-- Dynamic User Roles -->
        <?php foreach ($users_roles as $u) { ?>
        <li class="slide user_roles">
            <a href="./users.php?user_type=<?= strtolower($u->type_name) ?>"
               class="side-menu__item <?php if (isset($_GET['user_type']) && $_GET['user_type'] == $u->type_name) { echo 'active'; } ?>">
                <?= $u->type_name ?>
            </a>
        </li>
        <?php } ?>
    </ul>
</li>
<?php } ?>
<!-- End::Users Slide -->

<!-- Start::CMS Slide -->
<?php
if (isset($user_permissions->cms->page_access)) {
?>
<li class="slide has-sub">
<a href="javascript:void(0);" class="side-menu__item">
<span class="shape1"></span>
<span class="shape2"></span>
<i class="ti-book side-menu__icon"></i>
<span class="side-menu__label"><?= T::cms ?></span>
<i class="fe fe-chevron-right side-menu__angle"></i>
</a>

<ul class="slide-menu child1">
<li class="slide side-menu__label1">
<a href="javascript:void(0)">CMS</a>
</li>

<!-- Add Page -->
<?php
if (isset($user_permissions->cms->add) || isset($user_permissions->cms->add)) {
?>
<li class="slide">
<a href="./cms.php?addpage=1" class="side-menu__item
    <?php if (isset($_GET['addpage'])) { echo 'active'; } ?>">
    <?= T::cms_add_page ?>
</a>
</li>
<?php } ?>

<!-- Pages -->
<li class="slide">
<a href="./cms.php?pages=1" class="side-menu__item
    <?php if (isset($_GET['pages']) || isset($_GET['page'])) { echo 'active'; } ?>">
    <?= T::cms_pages ?>
</a>
</li>

<!-- Menu -->
<li class="slide">
<a href="./cms.php?menu=1" class="side-menu__item
    <?php if (isset($_GET['menu'])) { echo 'active'; } ?>">
    <?= T::cms_menu ?>
</a>
</li>
</ul>
</li>
<?php } ?>
<!-- End::CMS Slide -->
<!-- Start::Blog Slide -->
<?php
if (isset($user_permissions->blogs->page_access)) {
    if (!empty($blog_active) && $blog_active == 1) {
?>
<li class="slide has-sub">
<a href="javascript:void(0);" class="side-menu__item">
<span class="shape1"></span>
<span class="shape2"></span>
<i class="ti-write side-menu__icon"></i>
<span class="side-menu__label"><?= T::blog ?></span>
<i class="fe fe-chevron-right side-menu__angle"></i>
</a>
<ul class="slide-menu child1">
<li class="slide side-menu__label1">
<a href="javascript:void(0)">Blogs</a>
</li>

<!-- Add Blog Page -->
<?php
if (isset($user_permissions->blogs->add) || isset($user_permissions->blogs->add)) {
?>
<li class="slide">
<a href="./blogs.php?addpage=1" class="side-menu__item <?php if (isset($_GET['addpage'])) { echo 'active'; } ?>">
    <?= T::Blog_add_page ?>
</a>
</li>
<?php
}
?>

<!-- Blog Pages -->
<li class="slide">
<a href="./blogs.php?pages=1" class="side-menu__item <?php if (isset($_GET['pages']) || isset($_GET['page'])) { echo 'active'; } ?>">
    <?= T::Blog_pages ?>
</a>
</li>

<!-- Blog Categories -->
<li class="slide">
<a href="./blogs.php?category=1" class="side-menu__item <?php if (isset($_GET['category'])) { echo 'active'; } ?>">
    <?= T::blog_category ?>
</a>
</li>
</ul>
</li>
<?php
    }
}
?>
<!-- End::Blog Slide -->

<!-- Start::Info & Services Slide -->
 <?php
if (
    isset($user_permissions->newsletter->page_access) ||
    isset($user_permissions->our_services->page_access) ||
    isset($user_permissions->testimonials->page_access) ||
    isset($user_permissions->brand_story->page_access) ||
    isset($user_permissions->hotel_faqs->page_access) ||
    isset($user_permissions->faqs->page_access)
) {
?>
<li class="slide has-sub">
    <a href="javascript:void(0);" class="side-menu__item">
        <span class="shape1"></span>
        <span class="shape2"></span>
        <i class="fa fa-info-circle side-menu__icon"></i>
        <span class="side-menu__label"><?=T::info_hub?></span>
        <i class="fe fe-chevron-right side-menu__angle"></i>
    </a>

    <ul class="slide-menu child1">
        <li class="slide side-menu__label1">
            <a href="javascript:void(0)">Info Hub</a>
        </li>

        <!-- Newsletter -->
        <?php if (isset($user_permissions->newsletter->page_access)) { ?>
        <li class="slide">
            <a href="./newsletter.php"
               class="side-menu__item <?php if ($url_name == 'newsletter') { echo 'active'; } ?>">
                <i class="ti-email side-menu__icon"></i>
                <?= T::newsletter ?>
            </a>
        </li>
        <?php } ?>

        <!-- Our Services -->
        <?php if (isset($user_permissions->our_services->page_access)) { ?>
        <li class="slide">
            <a href="./our_services.php"
               class="side-menu__item <?php if ($url_name == 'our_services') { echo 'active'; } ?>">
                <i class="ti-briefcase side-menu__icon"></i>
                <?= T::our_services ?>
            </a>
        </li>
        <?php } ?>

        <!-- Testimonials -->
        <?php if (isset($user_permissions->testimonials->page_access)) { ?>
        <li class="slide">
            <a href="./testimonials.php"
               class="side-menu__item <?php if ($url_name == 'testimonials') { echo 'active'; } ?>">
                <i class="ti-comments side-menu__icon"></i>
                <?= T::testimonials ?>
            </a>
        </li>
        <?php } ?>

        <!-- Brand Story -->
        <?php if (isset($user_permissions->brand_story->page_access)) { ?>
        <li class="slide">
            <a href="./brand_story.php"
               class="side-menu__item <?php if ($url_name == 'brand_story') { echo 'active'; } ?>">
                <i class="ti-book side-menu__icon"></i>
                <?= T::brand_story ?>
            </a>
        </li>
        <?php } ?>

        <!-- Hotel FAQs -->
        <?php if (isset($user_permissions->hotel_faqs->page_access)) { ?>
        <li class="slide">
            <a href="./hotel_faqs.php"
               class="side-menu__item <?php if ($url_name == 'hotel_faqs') { echo 'active'; } ?>">
                <i class="ti-help-alt side-menu__icon"></i>
                <?= T::hotel_faqs ?>
            </a>
        </li>
        <?php } ?>

        <!-- General FAQs -->
        <?php if (isset($user_permissions->faqs->page_access)) { ?>
        <li class="slide">
            <a href="./faqs.php"
               class="side-menu__item <?php if ($url_name == 'faqs') { echo 'active'; } ?>">
                <i class="ti-info-alt side-menu__icon"></i>
                <?= T::faqs ?>
            </a>
        </li>
        <?php } ?>
    </ul>
</li>
<?php } ?>
<!-- End::Info & Services Slide -->

<!-- Transactions -->
<?php
if (isset($user_permissions->transactions->page_access)) {
?>
<li class="slide">
<a href="./transactions.php" class="side-menu__item <?php if ($url_name == 'transactions') { echo "active"; } ?>">
<span class="shape1"></span>
<span class="shape2"></span>
<i class="ti-money side-menu__icon"></i>
<span class="side-menu__label"><?= T::transactions ?></span>
</a>
</li>
<?php } ?>
<!-- End Transactions -->

<!-- Flights -->
<?php
if (
    isset($user_permissions->flights->page_access) ||
    isset($user_permissions->flights_airports->page_access) ||
    isset($user_permissions->flights_airlines->page_access) ||
    isset($user_permissions->flights_featured->page_access) ||
    isset($user_permissions->flights_suggestions->page_access)
) {
    if(!empty($flight_active) && $flight_active == 1){
?>
<li class="slide has-sub">
<a href="javascript:void(0);" class="side-menu__item">
<span class="shape1"></span>
<span class="shape2"></span>
<i class="fa fa-plane side-menu__icon"></i>
<span class="side-menu__label"><?= T::flights ?></span>
<i class="fe fe-chevron-right side-menu__angle"></i>
</a>
<ul class="slide-menu child1">
<li class="slide side-menu__label1">
<a href="javascript:void(0)">Flights</a>
</li>

<!-- Flights Page -->
<?php if (isset($user_permissions->flights->page_access)) { ?>
<li class="slide">
<a href="./flights.php" class="side-menu__item <?php if ($url_name == "flights") { echo 'active'; } ?>">
    <?= T::flights ?>
</a>
</li>
<?php } ?>

<!-- Flights Airports Page -->
<?php if (isset($user_permissions->flights_airports->page_access)) { ?>
<li class="slide">
<a href="./flights_airports.php" class="side-menu__item <?php if ($url_name == "flights_airports") { echo 'active'; } ?>">
    <?= T::flights . ' ' . T::airports ?>
</a>
</li>
<?php } ?>

<!-- Flights Airlines Page -->
<?php if (isset($user_permissions->flights_airlines->page_access)) { ?>
<li class="slide">
<a href="./flights_airlines.php" class="side-menu__item <?php if ($url_name == "flights_airlines") { echo 'active'; } ?>">
    <?= T::flights . ' ' . T::airlines ?>
</a>
</li>
<?php } ?>

<!-- Flights Featured Page -->
<?php if (isset($user_permissions->flights_featured->page_access)) { ?>
<li class="slide">
<a href="./flights_featured.php" class="side-menu__item <?php if ($url_name == "flights_featured") { echo 'active'; } ?>">
    <?= T::flights . ' ' . T::featured ?>
</a>
</li>
<?php } ?>

<!-- Flights Suggestions Page -->
<?php if (isset($user_permissions->flights_suggestions->page_access)) { ?>
<li class="slide">
<a href="./flights_suggestions.php" class="side-menu__item <?php if ($url_name == "flights_suggestions") { echo 'active'; } ?>">
    <?= T::flights_suggestions ?>
</a>
</li>
<?php } ?>
</ul>
</li>
<?php } } ?>
<!-- End Flights -->

<!-- Hotels -->
<?php
if (
    isset($user_permissions->hotels->page_access) ||
    isset($user_permissions->hotels_settings->page_access) ||
    isset($user_permissions->hotels_suggestions->page_access)
) {
    if (!empty($hotels_active) && $hotels_active == 1) {
?>
<li class="slide has-sub">
<a href="javascript:void(0);" class="side-menu__item">
    <span class="shape1"></span>
    <span class="shape2"></span>
     <i class="fa fa-hotel side-menu__icon"></i>
    <span class="side-menu__label"><?= T::hotels ?></span>
    <i class="fe fe-chevron-right side-menu__angle"></i>
</a>
<ul class="slide-menu child1">
<li class="slide side-menu__label1">
    <a href="javascript:void(0)"><?= T::hotels ?></a>
</li>

<?php if (isset($user_permissions->hotels->page_access)) { ?>
<li class="slide">
    <a href="./hotels.php" class="side-menu__item <?php if ($url_name == "hotels") { echo 'active'; } ?>">
        <?= T::hotels ?>
    </a>
</li>
<?php } ?>

<?php if (isset($user_permissions->hotels_settings->page_access)) { ?>
<li class="slide">
    <a href="./hotels_settings.php" class="side-menu__item <?php if ($url_name == "hotels_settings") { echo 'active'; } ?>">
       <?=T::hotels . ' ' . T::settings ?>
    </a>
</li>
<?php } ?>

<?php if (isset($user_permissions->hotels_suggestions->page_access)) { ?>
<li class="slide">
    <a href="./hotels_suggestions.php" class="side-menu__item <?php if ($url_name == "hotels_suggestions") { echo 'active'; } ?>">
        <?= T::hotels . ' ' . T::suggestions ?>
    </a>
</li>
<?php } ?>
</ul>
</li>
<?php } } ?>
<!-- End Hotels -->

<!-- tours -->
<?php
if (
    isset($user_permissions->tours->page_access) ||
    isset($user_permissions->tours_settings->page_access) ||
    isset($user_permissions->tours_suggestions->page_access)
) {
    if (!empty($tours_active) && $tours_active == 1) {
?>
<li class="slide has-sub">
    <a href="javascript:void(0);" class="side-menu__item">
        <span class="shape1"></span>
        <span class="shape2"></span>
        <i class="ti-map side-menu__icon"></i>
        <span class="side-menu__label"><?= T::tours ?></span>
        <i class="fe fe-chevron-right side-menu__angle"></i>
    </a>

    <ul class="slide-menu child1">
        <li class="slide side-menu__label1">
            <a href="javascript:void(0)"><?= T::tours ?></a>
        </li>

        <?php if (isset($user_permissions->tours->page_access)) { ?>
            <li class="slide">
                <a href="./tours.php" class="side-menu__item <?php if ($url_name == "tours") { echo 'active'; } ?>">
                    <?= T::tours ?>
                </a>
            </li>
        <?php } ?>

        <?php if (isset($user_permissions->tours_settings->page_access)) { ?>
            <li class="slide">
                <a href="./tours_settings.php" class="side-menu__item <?php if ($url_name == "tours_settings") { echo 'active'; } ?>">
                    <?= T::tours . ' ' . T::settings ?>
                </a>
            </li>
        <?php } ?>

        <?php if (isset($user_permissions->tours_suggestions->page_access)) { ?>
            <li class="slide">
                <a href="./tours_suggestions.php" class="side-menu__item <?php if ($url_name == "tours_suggestions") { echo 'active'; } ?>">
                    <?= T::tours . ' ' . T::suggestions ?>
                </a>
            </li>
        <?php } ?>
    </ul>
</li>
<?php
    }
}
?>
<!-- tours -->

<!-- cars -->
<?php
if (
    isset($user_permissions->cars->page_access) ||
    isset($user_permissions->cars_settings->page_access) ||
    isset($user_permissions->cars_suggestions->page_access)
) {
    if (!empty($cars_active) && $cars_active == 1) {
?>
<li class="slide has-sub">
    <a href="javascript:void(0);" class="side-menu__item">
        <span class="shape1"></span>
        <span class="shape2"></span>
        <i class="ti-car side-menu__icon"></i>
        <span class="side-menu__label"><?= T::cars ?></span>
        <i class="fe fe-chevron-right side-menu__angle"></i>
    </a>

    <ul class="slide-menu child1">
        <li class="slide side-menu__label1">
            <a href="javascript:void(0)"><?= T::cars ?></a>
        </li>

        <?php if (isset($user_permissions->cars->page_access)) { ?>
            <li class="slide">
                <a href="./cars.php" class="side-menu__item <?php if ($url_name == "cars") { echo 'active'; } ?>">
                    <?= T::cars ?>
                </a>
            </li>
        <?php } ?>

        <?php if (isset($user_permissions->cars_suggestions->page_access)) { ?>
            <li class="slide">
                <a href="./cars_suggestions.php" class="side-menu__item <?php if ($url_name == "cars_suggestions") { echo 'active'; } ?>">
                    <?= T::cars . ' ' . T::suggestions ?>
                </a>
            </li>
        <?php } ?>
    </ul>
</li>
<?php
    }
}
?>
<!-- cars -->

<!-- visa -->
<?php
if (
    isset($user_permissions->visa->page_access) ||
    isset($user_permissions->visa_countries->page_access) ||
    isset($user_permissions->visa_submissions->page_access)
) {
    if (!empty($visa_active) && $visa_active == 1) {
?>
<li class="slide has-sub">
    <a href="javascript:void(0);" class="side-menu__item">
        <span class="shape1"></span>
        <span class="shape2"></span>
        <i class="ti-credit-card side-menu__icon"></i>
        <span class="side-menu__label"><?= T::visa ?></span>
        <i class="fe fe-chevron-right side-menu__angle"></i>
    </a>

    <ul class="slide-menu child1">
        <li class="slide side-menu__label1">
            <a href="javascript:void(0)"><?= T::visa ?></a>
        </li>

        <?php if (isset($user_permissions->visa_countries->page_access)) { ?>
            <li class="slide">
                <a href="./visa_countries.php" class="side-menu__item <?php if ($url_name == "visa_countries") { echo 'active'; } ?>">
                    <?= T::visa . ' ' . T::countries ?>
                </a>
            </li>
        <?php } ?>

        <?php if (isset($user_permissions->visa_submissions->page_access)) { ?>
            <li class="slide">
                <a href="./visa_submissions.php" class="side-menu__item <?php if ($url_name == "visa_submissions") { echo 'active'; } ?>">
                    <?= T::visa . ' ' . T::bookings ?>
                </a>
            </li>
        <?php } ?>
    </ul>
</li>
<?php
    }
}
?>
<!-- visa -->

<!-- reports -->
<?php
if (
    isset($user_permissions->reports->page_access) ||
    isset($user_permissions->weekly_bookings->page_access) ||
    isset($user_permissions->monthly_bookings->page_access) ||
    isset($user_permissions->annually_bookings->page_access) ||
    isset($user_permissions->weekly_users->page_access) ||
    isset($user_permissions->monthly_users->page_access) ||
    isset($user_permissions->annually_users->page_access) ||
    isset($user_permissions->payment_transactions->page_access) ||
    isset($user_permissions->annual_income_report->page_access)
) {
?>
<li class="slide has-sub">
    <a href="javascript:void(0);" class="side-menu__item">
        <span class="shape1"></span>
        <span class="shape2"></span>
        <i class="ti-clipboard side-menu__icon"></i>
        <span class="side-menu__label"><?= T::reports ?></span>
        <i class="fe fe-chevron-right side-menu__angle"></i>
    </a>

    <ul class="slide-menu child1">
        <li class="slide side-menu__label1">
            <a href="javascript:void(0)"><?= T::reports ?></a>
        </li>

        <?php if (isset($user_permissions->weekly_bookings->page_access)) { ?>
            <li class="slide">
                <a href="./weekly_bookings.php" class="side-menu__item <?php if ($url_name == "weekly_bookings") { echo "active"; } ?>">
                    Weekly Bookings
                </a>
            </li>
        <?php } ?>

        <?php if (isset($user_permissions->monthly_bookings->page_access)) { ?>
            <li class="slide">
                <a href="./monthly_bookings.php" class="side-menu__item <?php if ($url_name == "monthly_bookings") { echo "active"; } ?>">
                    Monthly Bookings
                </a>
            </li>
        <?php } ?>

        <?php if (isset($user_permissions->annually_bookings->page_access)) { ?>
            <li class="slide">
                <a href="./annually_bookings.php" class="side-menu__item <?php if ($url_name == "annually_bookings") { echo "active"; } ?>">
                    Annually Bookings
                </a>
            </li>
        <?php } ?>

        <?php if (isset($user_permissions->weekly_users->page_access)) { ?>
            <li class="slide">
                <a href="./weekly_users.php" class="side-menu__item <?php if ($url_name == "weekly_users") { echo "active"; } ?>">
                    Weekly Users
                </a>
            </li>
        <?php } ?>

        <?php if (isset($user_permissions->monthly_users->page_access)) { ?>
            <li class="slide">
                <a href="./monthly_users.php" class="side-menu__item <?php if ($url_name == "monthly_users") { echo "active"; } ?>">
                    Monthly Users
                </a>
            </li>
        <?php } ?>

        <?php if (isset($user_permissions->annually_users->page_access)) { ?>
            <li class="slide">
                <a href="./annually_users.php" class="side-menu__item <?php if ($url_name == "annually_users") { echo "active"; } ?>">
                    Annually Users
                </a>
            </li>
        <?php } ?>

        <?php if (isset($user_permissions->payment_transactions->page_access)) { ?>
            <li class="slide">
                <a href="./payment_transactions.php" class="side-menu__item <?php if ($url_name == "payment_transactions") { echo "active"; } ?>">
                    Payment Transactions
                </a>
            </li>
        <?php } ?>

        <?php if (isset($user_permissions->annual_income_report->page_access)) { ?>
            <li class="slide">
                <a href="./annual_income_report.php" class="side-menu__item <?php if ($url_name == "annual_income_report") { echo "active"; } ?>">
                    Annual Income Report
                </a>
            </li>
        <?php } ?>
    </ul>
</li>
<?php
}
?>
<!-- reports -->
      </ul>
        <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"> <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path> </svg></div>
    </nav>
                <!-- End::nav -->
                <div class="p-3 pb-1 d-none">
            <button
                    type="button"
                    id="load1"
                    data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing Order" class="cache btn btn-primary w-100 m-y rounded-4" style="border-radius:5px!important">
                Reset Cache
            </button>
        </div>

        <script>
            // POST REQUEST FOR CLEAN CACHE
            $('.cache').on('click', function() {
                var $this = $(this);
                $this.text("Cleaning Cache...");

                setTimeout(function() {
                    $this.text("Done");

                    setTimeout(function() {
                        $this.text("Clean Cache");
                    }, 1000);

                }, 2000);

                var form = new FormData();
                form.append("name", "");

                var settings = {
                    "url": "<?=root?>",
                    "method": "POST",
                    "timeout": 0,
                    "processData": false,
                    "contentType": false,
                    "data": form
                };

                $.ajax(settings).done(function (response) {
                    console.log(response);
                });

            });
        </script>

        </ul>
        <hr class="my-2">
        <div class="dropdown p-2 mb-2 mx-2">
            <a style="font-weight: 400;font-size: 14px;" href="#"
               class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1"
               data-bs-toggle="dropdown" aria-expanded="false">
                <img src="./assets/img/user.png" alt="" width="24" height="24" class="user_circle me-2">
                <?= $USER_SESSION->backend_user_name ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                <li><a class="dropdown-item loadeffect" href="./dashboard.php"><?= T::dashboard ?></a></li>

                <?php if (isset($user_permissions->settings->page_access)) { ?>
                    <li><a class="dropdown-item loadeffect" href="./settings.php"><?= T::settings ?></a></li>
                <?php } ?>

                <?php if (isset($user_permissions->logs->page_access)) { ?>
                    <li><a class="dropdown-item loadeffect" href="./logs.php"><?= T::logs ?></a></li>
                <?php } ?>

                <?php if (isset($user_permissions->profile->page_access)) { ?>
                    <li><a class="dropdown-item loadeffect" href="./profile.php"><?= T::profile ?></a></li>
                <?php } ?>

                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item loadeffect" href="login-logout.php"><?= T::logout ?></a></li>
            </ul>
        </div>
</div>
<!-- End::main-sidebar -->
            </div>
            <!-- End::main-sidebar -->

        </aside>
        <!-- End::app-sidebar -->

        <!-- Start::app-content -->

    </div>
    <section class="main-content app-content w-100" style="overflow:auto" >

<?php if (isset($alert_edit) && $url_name != "dashboard" && $url_name != "transactions") { ?>
<!-- EDIT ALERT -->
<!-- <div class="alert alert-warning m-0">
<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
<?= T::content_editing_role ?>
</div> -->
<?php } ?>
<?php

}
?>
  <script>

    // POPUP ALERTS MATERIAL STYLE
    // $.alert({
    // icon: '',
    // theme: 'material',
    // closeIcon: true,
    // animation: 'scale',
    // type: 'orange',
    // title: 'Alert!',
    // content: 'Simple alert!',
    // });

  </script>

<style>
    .select2-selection--single{
        padding:0 !important;
    }
    .select2-selection__rendered{
    position: relative !important;
    height: 46px !important;
    padding: 5px 22px !important;
    }
</style>