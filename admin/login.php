<?php

require_once '_config.php';

// REDIRECT TO DASHBOARD IF ALREADY LOGGED IN
if(isset($USER_SESSION->backend_user_login) == true ){
    REDIRECT("dashboard.php");
exit;
}

CSRF();

// LOGIN POST REQUEST
if ($_SERVER['REQUEST_METHOD'] === 'POST') { if (isset($_POST['license_key'])){ $license_key = $_POST['license_key']; $db->update("settings", [ "license_key" => $license_key ], [ "id" => 1 ]); REDIRECT("login.php"); die; }

    // EMAIL SANITIZATION
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $user = $db->query("SELECT * FROM `users` WHERE `email` LIKE '".$email."' AND `password` LIKE '".md5($_POST['password'])."'")->fetchAll();

    if (isset($user[0]['id'])){
        if ($user[0]['status'] == 1 ) {

            $lang = explode('_', $_POST['user_language']);

            // IF USER ACCOUNT STATUS ACTIVE
            $SESSION_ARRAY = array(
                "backend_user_login" => true,
                "backend_user_id" => $user[0]['user_id'],
                "backend_user_email" => $user[0]['email'],
                "backend_user_type" => $user[0]['user_type'],
                "backend_user_status" => $user[0]['status'],
                "backend_user_name" => $user[0]['first_name']. ' ' .$user[0]['last_name'],
                "backend_user_language" => $lang[0],
                "backend_user_language_position" => $lang[1],
            );

            $_SESSION['phptravels_backend_user'] = ENCODE($SESSION_ARRAY);

            // SEND EMAIL
            // $title = "User Login";
            // $template = "login";
            // $content = $_SERVER['REMOTE_ADDR'];
            // $receiver_email = $user[0]['email'];
            // $receiver_name = $user[0]['first_name'];
            // AJAXMAIL($template,$title,$content,$receiver_email,$receiver_name);

            // INSERT TO LOGS
            $user_id = $user[0]['user_id'];
            $log_type = "login";
            $datetime = date("Y-m-d h:i:sa");
            $desc = "user logged into account";
            logs($user_id,$log_type,$datetime,$desc);

            if ($user[0]['user_type'] == 'Agent') {
                REDIRECT('../agents/dashboard.php');
            }else {
                REDIRECT('dashboard.php');
            }

        // REDIRECT TO USER VERIFICATION PAGE
        } else {

         // INSERT TO LOGS
         $user_id = "";
         $log_type = "login";
         $datetime = date("Y-m-d h:i:sa");
         $desc = "user tried to loggin but account is not active";
         logs($user_id,$log_type,$datetime,$desc);

         ALERT_MSG('not_active');
         REDIRECT('login.php');

      }

    } else {

    // INSERT TO LOGS
    $user_id = "";
    $log_type = "login";
    $datetime = date("Y-m-d h:i:sa");
    $desc = "invalid user login credentials";
    logs($user_id,$log_type,$datetime,$desc);

    ALERT_MSG('invalid_login');
    REDIRECT("login.php");

    }

}

// GET DATA
$params = array();
$languages = GET('languages',$params);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Administrators Login</title>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel="shortcut icon" href="../uploads/global/favicon.png">
    <link rel="stylesheet" href="./assets/css/style.css" />
    <link rel="stylesheet" href="./assets/css/app.css" />
    <link rel="stylesheet" href="./spruha-assets/css/styles.min.css" />
    <script src="./assets/js/jquery-3.6.0.min.js"></script>
</head>

<body style="background-color: #e1e6ed;">


<div class="page main-signin-wrapper">

        <!-- Start::row-1 -->
        <div class="row signpages text-center">
            <div class="col-md-12">
                <div class="card mb-0">
                    <div class="row row-sm">
                        <div class="col-lg-6 col-xl-5 d-none d-lg-block text-center bg-primary details">
                            <div class="mt-5 pt-4 p-2 position-absolute">
                                <a href="index.html">
                                    <!-- <img src="./spruha-assets/images/brand-logos/desktop-white.png" class="header-brand-img mb-4" alt="logo"> -->
                                </a>
                                <div class="clearfix"></div>
                                <img src="./spruha-assets/images/svgs/user.svg" class="ht-100 mb-0" alt="user">
                                <h5 class="mt-4">Administrators Login</h5>
                                <span class="text-white-6 fs-13 mb-5 mt-xl-0">Please login here only if you have admin account else close this page</span>
                            </div>
                        </div>
                        <div class="col-lg-6 col-xl-7 col-xs-12 col-sm-12 login_form ">
                            <div class="main-container container-fluid">
                                <div class="row row-sm">
                                    <div class="card-body mt-2 mb-2">
                                        <div class="clearfix"></div>

                                        <?php include "login_page.php"; ?>

                                        <!-- <div class="text-start mt-5 ms-0">
                                            <div class="mb-1"><a href="forgot.html">Forgot password?</a></div>
                                            <div>Don't have an account? <a href="signup.html">Register Here</a></div>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End::row-1 -->

    </div>


    <script src="./assets/js/toast.js"></script>
    <script src="./assets/js/toast-alerts.js"></script>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/bootstrap-select.js"></script>

    <script>
    function submission() {
        document.querySelector('.d-none').classList.remove('d-none');
        document.querySelector('.login_button').classList.add('d-none');

        let email = $("#email").val();
        if (email == "") {
            event.preventDefault();
            alert("Email is required to login");
            window.location.href = "<?=root?>login.php";
        }

        let pass = $("#password").val();
        if (pass == "") {
            event.preventDefault();
            alert("Password is required to login");
            window.location.href = "<?=root?>login.php";
        }
    }

    var hash = window.location.hash.substr(1);
    if (hash == "invalid") {
        vt.error("Email or password incorrect", {
            title: "Invalid Credentials",
            position: "bottom-center",
            callback: function() {}
        })
    }

    if (hash == "password-reset-completed") {
        vt.success("Please check your email for the new password", {
            title: "Password Reset",
            position: "bottom-center",
            callback: function() {}
        })
    }
    </script>

    <style>
    .dropdown-toggle::after {
    right: 10px;
    color: rgb(86 86 86 / 48%);
    position: absolute;
    }
    /* .card {
    -webkit-box-shadow: 0 2px 6px rgb(0 0 0 / 20%);
    -moz-box-shadow: 0 2px 6px rgba(0,0,0,.2);
    box-shadow: 0 2px 6px rgb(0 0 0 / 20%);
    } */
    </style>

</body>

</html>