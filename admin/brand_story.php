<?php 
use Medoo\Medoo;

require_once '_config.php';
auth_check();

$title = T::brand_story;
include "_header.php";
?>

<div class="page_head bg-transparent">
  <div class="panel-heading">
    <div class="float-start">
      <p class="m-0 page_title"><?=T::brand_story?></p>
    </div>
    <div class="float-end"></div>
  </div>
</div>

<div class="container mt-3">
<?php 
include('./xcrud/xcrud.php');
$xcrud = Xcrud::get_instance();
$xcrud->table('brand_stories');
$xcrud->order_by('id','desc');

/* Show fields in form */
$xcrud->fields('desc_text,picture,status');

/* Columns in grid */
$xcrud->columns('desc_text,picture,status,created_at');

/* Images */
$xcrud->change_type('picture', 'image', false, array(
    'width' => 200,
    'path'  => upload_path,
    'url'   => upload_url
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
    $xcrud->field_callback('status','Enable_Disable');
    // $xcrud->column_callback('status', 'create_status_icon');
    $xcrud->column_callback('default', 'MakeDefault');
    $xcrud->column_callback('status', 'status_grid_text');
}

/* Layout tweaks */
$xcrud->language($USER_SESSION->backend_user_language);

/* Remove undefined callbacks for now */
// $xcrud->after_insert('refresh');
// $xcrud->after_update('refresh');

echo $xcrud->render();
?>
</div>

<?php include "_footer.php" ?>
