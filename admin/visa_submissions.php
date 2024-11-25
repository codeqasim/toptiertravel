<?php

use Medoo\Medoo;

require_once '_config.php';
auth_check();

$title = T::visa.' '.T::bookings;
include "_header.php";

?>

<div class="page_head">
<div class="panel-heading">
<div class="float-start">
<p class="m-0 page_title"><?=$title?></p>
</div>
<div class="float-end">
<!-- <a href="javascript:window.history.back();" data-toggle="tooltip" data-placement="top" title="Previous Page" class="loading_effect btn btn-warning">  Back</a> -->
</div>
</div>
</div>

<div class="container mt-3">

<?php
include('./xcrud/xcrud.php');
$xcrud = Xcrud::get_instance();
$xcrud->table('visa_submissions');
$xcrud->order_by('id','desc');
$xcrud->columns('status,from_country,to_country,date,first_name,last_name,email,phone,number_of_days,entry_type,visa_type');
$xcrud->fields('status,from_country,to_country,date,first_name,last_name,email,phone,number_of_days,entry_type,visa_type');
// $xcrud->relation('from_country','countries','iso','iso');
// $xcrud->relation('to_country','countries','iso','iso');
$xcrud->unset_title();
$xcrud->unset_csv();
$xcrud->unset_view();
$xcrud->unset_view();

$xcrud->label('number_of_days','Days');

// $xcrud->column_callback('from_country','country_flag');

// USER PERMISSIONS
if (!isset($permission_delete)){ $xcrud->unset_remove(); }
if (!isset($permission_edit)){
    $xcrud->unset_edit();

} else {


}

$xcrud->column_width('status','100px');
$xcrud->language($USER_SESSION->backend_user_language);

// REFRESH PAGE
$xcrud->after_insert('refresh');
$xcrud->after_update('refresh');

echo $xcrud->render();

?>

<?php include "_footer.php" ?>