
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

</main>
<section data-aos="fade-up" class="footer-area">
<div class="container">
      <div class="">
         <div class="row g-0">

         <div class="col-xl-10 col-lg-12 col-sm-12">
    <ul class="foot_menu w-100">
      <li class="footm row w-100">
        <ul class="dropdown-menu-item row">
          <?php
          $menu = (base()->cms);
          foreach ($menu as $m) {
            if ($m->name == "Footer") {
          ?>
          <li class="col-md-3">
            <a href="<?= root ?>page/<?= $m->slug_url ?>" class="fadeout waves-effect"><?= $m->page_name ?></a>
          </li>
          <?php
            }
          }
          ?>
        </ul>
      </li>
    </ul>
  </div>
  <div class="col-xl-2 col-lg-4 col-sm-6">
  <div class="d-flex items-center px-20 py-10 rounded-4 border-light">
  <a href="<?=app()->app->ios_store?>" target="_blank" class="d-flex items-center w-100 text-decoration-none text-dark">
    <div class="icon-apple text-24"></div>
    <div class="ml-20">
      <div class="text-14 text-light-1">Download on</div>
      <div class="text-15 lh-1 fw-500">Apple Store</div>
    </div>
  </a>
</div>

<div class="d-flex items-center px-20 py-10 rounded-4 border-light mt-20">
  <a href="<?=app()->app->android_store?>" target="_blank" class="d-flex items-center w-100 text-decoration-none text-dark">
    <div class="icon-play-market text-24"></div>
    <div class="ml-20">
      <div class="text-14 text-light-1">Get it on</div>
      <div class="text-15 lh-1 fw-500">Google Play</div>
    </div>
  </a>
</div>

  </div>
         </div>
         <!-- end row -->
         <div style="margin-top:15px;" class="py-20 border-top-light">
          <div class="row justify-between items-center y-gap-10">
            <div class="col-auto">
              <div class="row x-gap-30 y-gap-10">
                <div class="col-auto">
                  <div class="d-flex items-center">
                    <!-- <?=T::all_rights_reserved?> <?=(app()->app->business_name)?> -->
                    <!-- © 2024 <?=(app()->app->business_name)?> <?=T::all_rights_reserved?> -->
                    © <?= date('Y') ?> <?=(app()->app->business_name)?> All rights reserved.
                  </div>
                </div>

                <div class="col-auto">
                  <!-- <div class="d-flex x-gap-15">
                    <a href="#">Privacy</a>
                    <a href="#">Terms</a>
                    <a href="#">Site Map</a>
                  </div> -->
                </div>
              </div>
            </div>

            <div class="col-auto">
              <div class="row y-gap-10 items-center">
                <div class="col-auto">
                  <div class="d-flex items-center">
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
                  </div>
                </div>

               <div class="col-auto">
  <div class="d-flex x-gap-20 items-center">
  <?php if (!empty(app()->app->social_facebook)){?>
    <a href="<?=app()->app->social_facebook?>" target="_blank" class="text-dark">
      <i class="fab fa-facebook text-14"></i>
    </a>
<?php } ?>

<?php if (!empty(app()->app->social_twitter)){?>
    <a href="<?=app()->app->social_twitter?>" target="_blank" class="text-dark">
      <i class="fab fa-twitter text-14"></i>
    </a>
<?php } ?>

<?php if (!empty(app()->app->social_linkedin)){?>
    <a href="<?=app()->app->social_linkedin?>" target="_blank" class="text-dark">
      <i class="fab fa-linkedin text-14"></i>
    </a>
<?php } ?>

<?php if (!empty(app()->app->social_instagram)){?>
    <a href="<?=app()->app->social_instagram?>" target="_blank" class="text-dark">
      <i class="fab fa-instagram text-14"></i>
    </a>
<?php } ?>

<?php if (!empty(app()->app->social_google)) { ?>
    <a href="<?= app()->app->social_google ?>" target="_blank" class="text-dark">
        <i class="fab fa-google text-14"></i>
    </a>
<?php } ?>

<?php if (!empty(app()->app->social_youtube)){?>
    <a href="<?=app()->app->social_youtube?>" target="_blank" class="text-dark">
      <i class="fab fa-youtube text-14"></i>
    </a>
<?php } ?>

<?php if (!empty(app()->app->social_whatsapp)){?>
    <a href="<?=app()->app->social_whatsapp?>" target="_blank" class="text-dark">
      <i class="fab fa-whatsapp text-14"></i>
    </a>
<?php } ?>

  </div>
</div>
              </div>
            </div>
          </div>
        </div>
         <!-- end row -->
      </div>
      <!-- end container -->
      <div class="pb-4"></div>

   </div>
</section>
</body>

<script src="<?=root?>assets/js/app.js"></script>
<script src="<?=root?>assets/js/bootstrap-select.js"></script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAAz77U5XQuEME6TpftaMdX0bBelQxXRlM"></script>
<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>

<script src="<?=root?>assets/js/vendors.js"></script>
<script src="<?=root?>assets/js/main.js"></script>

<?php
   // CHECK IF USER LOGGED AS A ADMIN
   // if(isset($_SESSION['phptravels_backend_user'])){
   //     $admin = (json_decode(base64_decode($_SESSION['phptravels_backend_user'])));
   //     if($admin->backend_user_login==1){
   //     ALERT_MSG('admin_logged');
   //     }
   // }
?>
