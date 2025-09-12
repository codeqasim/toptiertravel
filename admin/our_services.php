<?php 
use Medoo\Medoo;

require_once '_config.php';
auth_check();

$title = T::our_services;
include "_header.php";
?>

<div class="page_head bg-transparent">
  <div class="panel-heading">
    <div class="float-start">
      <p class="m-0 page_title"><?=T::our_services?></p>
    </div>
    <div class="float-end"></div>
  </div>
</div>

<div class="container mt-3">
<?php 
include('./xcrud/xcrud.php');
$xcrud = Xcrud::get_instance();
$xcrud->table('our_services');
$xcrud->order_by('id','desc');

/* Editable fields for Add/Edit */
$xcrud->fields('title,description,button_text,slug,background_image');

/* Columns in listing */
$xcrud->columns('title,description,button_text,slug,background_image,created_at');

/* Image upload setup */
$xcrud->change_type('background_image', 'image', false, array(
    'width' => 200,
    'path'  => upload_path   // 👈 adjust if needed
));

/* UI cleanup */
$xcrud->unset_title();
$xcrud->unset_view();

/* Permissions */
if (!isset($permission_delete)){ 
    $xcrud->unset_remove(); 
}
if (!isset($permission_edit)){ 
    $xcrud->unset_edit(); 
} else {
    // No status-related callbacks here 🚫
    $xcrud->column_callback('default', 'MakeDefault'); // only if your table has a `default` column
}

/* Language */
$xcrud->language($USER_SESSION->backend_user_language);

/* Remove undefined callbacks for now */
// $xcrud->after_insert('refresh');
// $xcrud->after_update('refresh');

echo $xcrud->render();
?>
</div>

<?php include "_footer.php" ?>
