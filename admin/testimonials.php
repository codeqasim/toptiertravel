<?php 
use Medoo\Medoo;

require_once '_config.php';
auth_check();

$title = T::testimonials;
include "_header.php";
?>

<div class="page_head bg-transparent">
  <div class="panel-heading">
    <div class="float-start">
      <p class="m-0 page_title"><?=T::testimonials?></p>
    </div>
    <div class="float-end"></div>
  </div>
</div>

<div class="container mt-3">
<?php 
include('./xcrud/xcrud.php');
$xcrud = Xcrud::get_instance();
$xcrud->table('testimonials');
$xcrud->order_by('id','desc');

/* Show fields in form */
$xcrud->fields('name,ratings,title,description,profile_photo,photo,status');

/* Columns in grid */
$xcrud->columns('name,profile_photo,ratings,title,description,photo,status,created_at');

/* Images */
$xcrud->change_type('profile_photo', 'image', false, array(
    'width' => 200,
    'path'  => upload_path
));
$xcrud->change_type('photo', 'image', false, array(
    'width' => 600,
    'path'  => upload_path
));

/* Remove title */
$xcrud->unset_title();
$xcrud->unset_view();

/* Permissions */
if (!isset($permission_delete)){ 
    $xcrud->unset_remove(); 
}
if (!isset($permission_edit)){ 
    $xcrud->unset_edit(); 
} else {
    $xcrud->column_callback('status', 'create_status_icon');
    $xcrud->field_callback('status','Enable_Disable');
    $xcrud->column_callback('default', 'MakeDefault');
}

/* Layout tweaks */
$xcrud->column_width('name','200px');
$xcrud->column_width('email','400px');
$xcrud->language($USER_SESSION->backend_user_language);

/* Remove undefined callbacks for now */
// $xcrud->after_insert('refresh');
// $xcrud->after_update('refresh');

echo $xcrud->render();
?>
</div>

<?php include "_footer.php" ?>
