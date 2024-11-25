<?php

use Medoo\Medoo;
require_once '_config.php';
auth_check();

$title = T::visa.' '.T::countries;
include "_header.php";

?>

<div class="page_head">
    <div class="panel-heading">
        <div class="float-start">
            <p class="m-0 page_title"><?=T::hotels?> <?=T::settings?></p>
         </div>
        <div class="float-end">
            <a href="javascript:window.history.back();" data-toggle="tooltip" data-placement="top" title="Previous Page"
                class="loading_effect btn btn-warning"><?=T::back?></a>
        </div>
    </div>
</div>
<div class="container mt-4 mb-4">
<div class="card">
  <div class="card-body p-5">
   <div class="nav nav-tabs nav-pills nav-justified pb-3" id="nav-tab" role="tablist">
    <a class="nav-link active" id="" data-bs-toggle="tab" href="#nav-hotels_types" role="tab" aria-controls="nav-home" aria-selected="true"><?=T::from.' '.T::countries?></a>
    <a class="nav-link" id="" data-bs-toggle="tab" href="#nav-hotels_amenities" role="tab" aria-controls="nav-profile" aria-selected="false"><?=T::to.' '.T::countries?></a>
  </div>
<div class="tab-content py-4" id="">
  <div class="tab-pane fade show active" id="nav-hotels_types" role="tabpanel" aria-labelledby="">

    <?php
    include('./xcrud/xcrud.php');

    function settings($name){
    $xcrud = Xcrud::get_instance();
    $xcrud->table($name);
    $xcrud->columns('country_status,nicename,iso');
    $xcrud->fields('country_status,nicename,iso');
    $xcrud->field_callback('country_status','Enable_Disable');
    $xcrud->column_callback('country_status', 'create_status_icon');
    $xcrud->unset_csv();
    $xcrud->unset_title();
    $xcrud->unset_view();
    $xcrud->column_width('country_status','120px');
    echo $xcrud->render();

    }

    settings('visa_from_countries');
    ?>

  </div>
  <div class="tab-pane fade" id="nav-hotels_amenities" role="tabpanel" aria-labelledby="">
    <?php settings('visa_to_countries'); ?>
  </div>

    </div>

  </div>
 </div>
</div>

<style>
  .xcrud-rightside, .xcrud-top-actions {margin-top:0 !important; padding-bottom: 15px;}
  .xcrud-list-container { padding: 0; }
  .xcrud-list thead { background: #eee}
  table { margin-top: 10px }
</style>

<?php include "_footer.php"; ?>