<?php 

use Medoo\Medoo;

require_once '_config.php';
auth_check();

$title = T::users;
include "_header.php";

?>

<div class="page_head bg-transparent">
<div class="panel-heading">
<div class="float-start">
<p class="m-0 page_title"><?=T::users?></p>
</div>
<div class="float-end">
<!-- <a href="javascript:window.history.back();" data-toggle="tooltip" data-placement="top" title="Previous Page" class="loading_effect btn btn-warning"><?=T::back?></a> -->
</div>
</div>
</div>

<div class="container mt-3">

<?php 

// Check if filtering by referral agent
if (isset($_GET['ref_agent_id'])) {
    // Get agent details
    $agent = $db->get('users', ['first_name', 'last_name', 'email'], ['user_id' => $_GET['ref_agent_id']]);
    $agentName = $agent ? $agent['first_name'] . ' ' . $agent['last_name'] : $_GET['ref_agent_id'];
    
    echo '<div class="d-flex justify-content-between align-items-center mb-3">
        <span><strong>Referrals for:</strong> ' . htmlspecialchars($agentName) . '</span>
        <a href="users.php' . (isset($_GET['user_type']) ? '?user_type=' . htmlspecialchars($_GET['user_type']) : '') . '" class="btn btn-primary">Back</a>
    </div>';
}

include('./xcrud/xcrud.php');
$xcrud = Xcrud::get_instance();
$xcrud->table('users');
$xcrud->order_by('id','desc');
$xcrud->columns('status,first_name,last_name,email,phone,user_type,currency_id,balance');
$xcrud->fields('created_at,user_id,status,first_name,last_name,email,password,phone,user_type,currency_id');
$xcrud->relation('currency_id','currencies','name','name');
$xcrud->relation('country_code','countries','id','nicename');
$xcrud->relation('user_type','users_roles','type_name','type_name');
$xcrud->relation('phone_country_code','countries','id','nicename');

$xcrud->column_callback('email', 'format_email');

// USER PERMISSIONS
// Disable delete when viewing referrals OR if no permission
if (!isset($permission_delete) || isset($_GET['ref_agent_id'])){ 
    $xcrud->unset_remove(); 
}

// Disable add when viewing referrals OR if no permission
if (!isset($permission_add) || isset($_GET['ref_agent_id'])){ 
    $xcrud->unset_add(); 
}

// Handle edit permissions
if (!isset($permission_edit) || isset($_GET['ref_agent_id'])){ 
    // Disable edit when viewing referrals
} else {
    $xcrud->column_callback('status', 'create_status_icon');
    $xcrud->field_callback('status','Enable_Disable');
    $xcrud->button('./profile.php?user_id={user_id}','User Profile','<i><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path><polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon></svg></i>');
    
    // Only show View Referrals button when NOT already viewing referrals
    if (!isset($_GET['ref_agent_id'])) {
        $xcrud->button('./users.php?ref_agent_id={user_id}' . (isset($_GET['user_type']) ? '&user_type=' . htmlspecialchars($_GET['user_type']) : ''), 'View Referrals', '');
    }
}

$rand = rand(100, 99);
$date = date('Ymdhis');
$user_id = $date.$rand;

$xcrud->change_type('created_at','hidden');  
$xcrud->pass_default('created_at',date("Y-m-d h:i:s"));

$xcrud->change_type('user_id','hidden');                
$xcrud->change_type('email_code','hidden');                
$xcrud->pass_default('user_id',$user_id);
$xcrud->change_type('password', 'password', 'md5', array('maxlength'=>20,'placeholder'=>'enter password'));

$xcrud->validation_required('first_name');
$xcrud->validation_required('last_name');
$xcrud->validation_required('email');
$xcrud->validation_required('password');
$xcrud->validation_required('user_type');
$xcrud->validation_required('currency_id');

$xcrud->label('currency_id','Currency');

if (isset($_GET['user_type'])){ 
    $xcrud->where('user_type', $_GET['user_type']); 
}

// Filter by referral agent if provided
if (isset($_GET['ref_agent_id'])) {
    $xcrud->where('ref_id', $_GET['ref_agent_id']);
}

$xcrud->unset_title();
$xcrud->unset_view();
$xcrud->unset_csv();
$xcrud->unset_edit(); 
$xcrud->language($USER_SESSION->backend_user_language);

// REFRESH PAGE
$xcrud->after_insert('refresh');
$xcrud->after_update('refresh');

$xcrud->after_insert('email_new_account');

$xcrud->column_width('status','5%');

echo $xcrud->render();

?>

<?php include "_footer.php" ?>