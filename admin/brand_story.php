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
        <div class="float-end">
            <!-- <a href="javascript:window.history.back();" data-toggle="tooltip" data-placement="top" title="Previous Page" class="loading_effect btn btn-warning">Back</a> -->
        </div>
    </div>
</div>

<div class="container mt-3">

<?php 
include('./xcrud/xcrud.php');
$xcrud = Xcrud::get_instance();
$xcrud->table('brand_stories');
$xcrud->order_by('id','desc');

// show columns in list view
$xcrud->columns('desc_text,picture,status,created_at');

// make fields editable in add/edit
$xcrud->fields('desc_text,picture,status');

// IMAGE field
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
    $xcrud->column_callback('status', 'create_status_icon');
    $xcrud->field_callback('status','Enable_Disable');
    $xcrud->column_callback('default', 'MakeDefault');
}

// USER PERMISSIONS
$xcrud->column_width('id','80px');
$xcrud->column_width('text','400px');
$xcrud->column_width('image','200px');
$xcrud->column_width('status','150px');
$xcrud->language($USER_SESSION->backend_user_language);

// REFRESH PAGE
$xcrud->after_insert('refresh');
$xcrud->after_update('refresh');

echo $xcrud->render();

?>

<?php include "_footer.php" ?>
