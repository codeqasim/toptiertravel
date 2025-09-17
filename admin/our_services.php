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
    'path'  => upload_path,
    'url'   => upload_url
));

/* Translation button */ 
$xcrud->button('./translations.php?service={id}','our_services','<i> Translation <svg  style="margin-left:10px" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg></i>');

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
