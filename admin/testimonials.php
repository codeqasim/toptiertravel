<?php 

use Medoo\Medoo;

require_once '_config.php';
auth_check();

$title = T:: testimonials;
include "_header.php";

?>

<div class="page_head bg-transparent">
<div class="panel-heading">
<div class="float-start">
<p class="m-0 page_title"><?=T::testimonials?></p>
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
$xcrud->table('testimonials');
$xcrud->order_by('id','desc');
$xcrud->columns('name,profile_photo,ratings,title,description,photo,status,created_at');
$xcrud->column_class('profile_photo', 'zoom_img');
$xcrud->change_type('profile_photo', 'image', true, array('width' => 200, 'path' => '../../uploads/'));
$xcrud->column_class('photo', 'zoom_img');
$xcrud->change_type('photo', 'image', true, array('width' => 600, 'path' => '../../uploads/'));
// $xcrud->fields('status,name,country,rate,status');
// $xcrud->validation_required('name');
// $xcrud->validation_required('country');
// $xcrud->validation_required('rate');

$xcrud->unset_title();
// $xcrud->unset_csv();
$xcrud->unset_view();

// USER PERMISSIONS
if (!isset($permission_delete)){ $xcrud->unset_remove(); }
if (!isset($permission_edit)){ 
    $xcrud->unset_edit(); 
   
} else {

    $xcrud->column_callback('status', 'create_status_icon');
    $xcrud->field_callback('status','Enable_Disable');
    $xcrud->column_callback('default', 'MakeDefault');
    
}

$xcrud->column_width('name','200px');
$xcrud->column_width('email','400px');
$xcrud->language($USER_SESSION->backend_user_language);

// REFRESH PAGE
$xcrud->after_insert('refresh');
$xcrud->after_update('refresh');

echo $xcrud->render();

?>

<?php include "_footer.php" ?>