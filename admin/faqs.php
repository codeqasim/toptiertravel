<?php 

use Medoo\Medoo;

require_once '_config.php';
auth_check();

$title = T::hotel_faqs;
include "_header.php";

?>

<div class="page_head bg-transparent">
    <div class="panel-heading">
        <div class="float-start">
            <p class="m-0 page_title"><?=T::hotel_faqs?></p>
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
$xcrud->table('faqs');
$xcrud->order_by('id','desc');

// show columns in list view
$xcrud->columns('question,answer,created_at');

// make fields editable in add/edit
$xcrud->fields('question,answer');

// Filter only hotel_faqs category
$xcrud->where('category =', 'faqs');

// Force category value on insert/update
$xcrud->pass_var('category', 'faqs');

// Field types   
$xcrud->change_type('question', 'text');

$xcrud->unset_title();
$xcrud->unset_view();

// USER PERMISSIONS
if (!isset($permission_delete)){ 
    $xcrud->unset_remove(); 
}
if (!isset($permission_edit)){ 
    $xcrud->unset_edit(); 
}

$xcrud->column_width('question','300px');
$xcrud->column_width('answer','600px');
$xcrud->language($USER_SESSION->backend_user_language);

// REFRESH PAGE
$xcrud->after_insert('refresh');
$xcrud->after_update('refresh');

echo $xcrud->render();

?>

<?php include "_footer.php" ?>
