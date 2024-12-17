<?php //dd($_SESSION);?>

<div class="py-3">
    <div class="sticky-top bg-white">
        <div class="author-content">
            <div class="lh-1 rounded p-3 py-4 mb-0 text-center align-items-center gap-3">
                <div class="author-img avatar-sm mx-auto">
                    <img src="<?=root?>assets/img/user.png" alt="user" style="width:50px;height:auto">
                </div>
                <div class="d-block"></div>
                <div class="w-100 text-center mt-3">
                    <h6 class="mb-0"><strong style="text-transform:capitalize"><?=$_SESSION['phptravels_client']->first_name?> <?=$_SESSION['phptravels_client']->last_name?></strong></h6>
                    <span class="author__meta"><?=T::welcomeback?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end sidebar-nav -->

<div>
    <ul class="sidebar-menu list-items w-100 g-1 user_menu">
        
        <div class="sidebar__item">
            <div class="sidebar__button <?= isset($meta['dashboard_active']) ? '-is-active' : '' ?>">
                <a href="<?=root.('dashboard')?>" class="d-flex items-center text-15 lh-1 fw-500">
                    <svg class="mr-15" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                    </svg>
                    <?=T::dashboard?>
                </a>
            </div>
        </div>

        <div class="sidebar__item">
            <div class="sidebar__button <?= isset($meta['bookings_active']) ? '-is-active' : '' ?>">
                <a href="<?=root.('bookings')?>" class="d-flex items-center text-15 lh-1 fw-500">
                    <svg class="mr-15" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21.5 12H16c-.7 2-2 3-4 3s-3.3-1-4-3H2.5"/>
                        <path d="M5.5 5.1L2 12v6c0 1.1.9 2 2 2h16a2 2 0 002-2v-6l-3.4-6.9A2 2 0 0016.8 4H7.2a2 2 0 00-1.8 1.1z"/>
                    </svg>
                    <?=T::mybookings?>
                </a>
            </div>
        </div>

        <!-- Reports Menu Item (for Agent users only) -->
        <?php if($_SESSION['phptravels_client']->user_type == "Agent"): ?>
        <div class="sidebar__item">
            <div class="sidebar__button <?= isset($meta['reports_active']) ? '-is-active' : '' ?>">
                <a href="<?=root.('reports/'.date("Y"))?>" class="d-flex items-center text-15 lh-1 fw-500">
                    <svg class="mr-15" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M13 2H6a2 2 0 0 0-2 2v16c0 1.1.9 2 2 2h12a2 2 0 0 0 2-2V9l-7-7z"/>
                        <path d="M13 3v6h6"/>
                    </svg>
                    <?=T::reports?>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <div class="sidebar__item">
            <div class="sidebar__button <?= isset($meta['profile_active']) ? '-is-active' : '' ?>">
                <a href="<?=root.('profile')?>" class="d-flex items-center text-15 lh-1 fw-500">
                    <svg class="mr-15" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5.52 19c.64-2.2 1.84-3 3.22-3h6.52c1.38 0 2.58.8 3.22 3"/>
                        <circle cx="12" cy="10" r="3"/>
                        <circle cx="12" cy="12" r="10"/>
                    </svg>
                    <?=T::myprofile?>
                </a>
            </div>
        </div>

        <div class="sidebar__item">
            <div class="sidebar__button">
                <a href="<?=root.('logout')?>" class="d-flex items-center text-15 lh-1 fw-500">
                    <svg class="mr-15" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 3H6a2 2 0 0 0-2 2v14c0 1.1.9 2 2 2h4M16 17l5-5-5-5M19.8 12H9"/>
                    </svg>
                    <?=T::logout?>
                </a>
            </div>
        </div>
    </ul>
</div>

<!-- ==============================
       END DASHBOARD NAV
================================= -->

<style>
    .header-area{position:relative;z-index:9999;}
    .info-area.info-bg {display:none}
    .cta-area,.header-top-bar,.footer-area{display:none}
    .header-menu-wrapper{padding: 0 50px}
    .menu-sidebar { display:none }
    body { background: #f3f5fd; }

.sidebar__button.-is-active {
    color: #3554D1 !important;
}

.sidebar__button.-is-active svg {
    stroke: #3554D1 !important; 
}

.sidebar__button.-is-active a {
    color: #3554D1 !important;
}


</style>

<script>
    function display_c(){
        var refresh=1000; // Refresh rate in milliseconds
        mytime=setTimeout('display_ct()',refresh);
    }

    function display_ct() {
        var x = new Date();
        document.getElementById('ct').innerHTML = x;
        display_c();
    }

// console.log(location.pathname.split("/")[2])
</script>