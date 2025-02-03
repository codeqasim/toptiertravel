<?php

require_once '_config.php';
auth_check();

// POST MODULES STATUS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if(isset($_POST['smtp_service'])){
        if ($_POST['smtp_service']=="smtp" && $_POST['value']==1){
        UPDATE('settings',array('default_email_service' => $_POST['smtp_service']),1);
        }

        if ($_POST['smtp_service']=="smtp" && $_POST['value']==0){
        UPDATE('settings',array('default_email_service' => 0),1);
        }

        if ($_POST['smtp_service']=="smtp2go" && $_POST['value']==1){
        UPDATE('settings',array('default_email_service' => $_POST['smtp_service']),1);
        }

        if ($_POST['smtp_service']=="smtp2go" && $_POST['value']==0){
        UPDATE('settings',array('default_email_service' => 0),1);
        }

    }

    if($_POST['smtp']=="smtp"){

        $params = array(
        'smtp_host' => $_POST['smtp_host'],
        'smtp_port' => $_POST['smtp_port'],
        'smtp_username' => $_POST['smtp_username'],
        'smtp_password' => $_POST['smtp_password'],
        'smtp_security' => $_POST['smtp_security'],
        'smtp_sendername' => $_POST['smtp_sendername'],
        );
    }

    if($_POST['smtp']=="smtp2go"){
        $params = array(
        'email_api_key' => $_POST['email_api_key'],
        'email_sender_name' => $_POST['email_sender_name'],
        'email_sender_email' => $_POST['email_sender_email'],
        );
    }

    $id = 1;
    UPDATE('settings',$params,$id);

    // INSERT TO LOGS
    $user_id = $USER_SESSION->backend_user_id;
    $log_type = "email_settings";
    $datetime = date("Y-m-d h:i:sa");
    $desc = "updated email settings";
    logs($user_id,$log_type,$datetime,$desc.nl2br("\n").json_encode($_REQUEST));

    ALERT_MSG('updated');
    REDIRECT("email_settings.php");

}

$title = T::email_settings;
include "_header.php";

// GET DATA FROM API
$params = array();
$settings = GET('settings',$params)[0];

?>

<div class="page_head">
    <div class="panel-heading">
        <div class="float-start">
            <p class="m-0 page_title"><?=T::email_settings?></p>
        </div>
        <div class="float-end">
            <a href="javascript:window.history.back();" data-toggle="tooltip" data-placement="top" title="Previous Page"
                class="loading_effect btn btn-warning"><?=T::back?></a>
        </div>
    </div>
</div>

<div class="container">
    <div class="py-4">

        <div class="accordion" id="accordionExample">

        <div class="accordion-item">
            <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                SMTP
            </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse #show" data-bs-parent="#accordionExample">
            <div class="accordion-body">
            <?php include "email_smtp.php" ?>
            </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                SMTP2GO
            </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
            <div class="accordion-body">
              <?php include "email_smtp2go.php" ?>
            </div>
            </div>
        </div>

        </div>

    </div>
</div>
</div>


<?php include "_footer.php";