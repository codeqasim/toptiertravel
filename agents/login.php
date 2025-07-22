<?php

require_once '_config.php';

// REDIRECT TO DASHBOARD IF ALREADY LOGGED IN
if(isset($USER_SESSION->backend_user_login) == true ){
    REDIRECT("dashboard");
exit;
}

CSRF();

// LOGIN POST REQUEST
if ($_SERVER['REQUEST_METHOD'] === 'POST') { if (isset($_POST['license_key'])){ $license_key = $_POST['license_key']; $db->update("settings", [ "license_key" => $license_key ], [ "id" => 1 ]); REDIRECT("login"); die; }

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

            if ($user[0]['user_type'] == 'Admin') {
                REDIRECT('../admin/dashboard');
            }else {
                REDIRECT('dashboard');
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
         REDIRECT('login');

      }

    } else {

    // INSERT TO LOGS
    $user_id = "";
    $log_type = "login";
    $datetime = date("Y-m-d h:i:sa");
    $desc = "invalid user login credentials";
    logs($user_id,$log_type,$datetime,$desc);

    ALERT_MSG('invalid_login');
    REDIRECT("login");

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agents Login</title>
    <link rel="shortcut icon" href="../uploads/global/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        inter: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="./assets/js/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Background gradients and mesh pattern */
        .bg-mesh {
            background-image: radial-gradient(circle at 1px 1px, rgba(15, 23, 42, 0.15) 1px, transparent 0);
            background-size: 24px 24px;
        }

        /* Enhanced backdrop blur */
        .backdrop-blur-xl {
            backdrop-filter: blur(24px);
        }

        /* Smooth transitions */
        .transition-all {
            transition: all 0.3s ease-in-out;
        }

        /* Custom button hover effects */
        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(139, 92, 246, 0.3);
        }

        /* Floating label animation */
        .floating-label {
            position: relative;
        }

        .floating-label input:focus + label,
        .floating-label input:not(:placeholder-shown) + label {
            transform: translateY(-24px) scale(0.85);
            color: #8b5cf6;
        }

        .floating-label label {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: white;
            padding: 0 8px;
            color: #64748b;
            transition: all 0.3s ease;
            pointer-events: none;
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/40 relative overflow-x-hidden font-inter">
    <!-- Enhanced Background with Mesh Gradient -->
    <div class="fixed inset-0 bg-mesh pointer-events-none"></div>
    <div class="fixed top-0 right-0 w-96 h-96 bg-gradient-to-br from-violet-400/20 to-purple-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="fixed bottom-0 left-0 w-80 h-80 bg-gradient-to-tr from-blue-400/20 to-cyan-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Main Container -->
    <div class="min-h-screen flex items-center justify-center p-4 relative z-10">
        <div class="w-full max-w-6xl">
            <!-- Login Card -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 overflow-hidden">
                <div class="grid lg:grid-cols-2 min-h-[600px]">
                    <!-- Left Side - Branding -->
                    <div class="hidden lg:flex bg-gradient-to-br from-violet-600 via-purple-700 to-indigo-800 relative overflow-hidden">
                        <!-- Decorative Elements -->
                        <div class="absolute inset-0">
                            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                            <div class="absolute bottom-0 left-0 w-80 h-80 bg-white/5 rounded-full blur-3xl"></div>
                        </div>

                        <div class="relative z-10 flex flex-col items-center justify-center text-center text-white p-12">
                            <div class="mb-8">
                                <div class="w-24 h-24 bg-white/20 backdrop-blur-xl rounded-2xl flex items-center justify-center mb-6 mx-auto shadow-2xl">
                                    <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </div>

                            <h2 class="text-4xl font-bold mb-4">Agents Portal</h2>
                            <p class="text-xl text-white/80 mb-6 leading-relaxed">Welcome back to your travel management dashboard</p>
                            <p class="text-white/60 text-sm max-w-md">Please login here only if you have an agent account, otherwise close this page</p>

                            <!-- Decorative Features -->
                            <div class="mt-12 flex items-center space-x-8">
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                        </svg>
                                    </div>
                                    <p class="text-xs text-white/70">Secure</p>
                                </div>
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                    <p class="text-xs text-white/70">Fast</p>
                                </div>
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </div>
                                    <p class="text-xs text-white/70">Reliable</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side - Login Form -->
                    <div class="flex items-center justify-center p-8 lg:p-12">
                        <div class="w-full max-w-md">
                            <?php include "login_page.php"; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="./assets/js/toast.js"></script>
    <script src="./assets/js/toast-alerts.js"></script>

    <script>
    function submission() {
        // Show loading state
        const submitBtn = document.querySelector('.login_button');
        const loadingBtn = document.querySelector('.loading_button');

        submitBtn.classList.add('hidden');
        loadingBtn.classList.remove('hidden');

        let email = $("#email").val();
        if (email == "") {
            event.preventDefault();
            alert("Email is required to login");
            window.location.href = "<?=root?>login";
        }

        let pass = $("#password").val();
        if (pass == "") {
            event.preventDefault();
            alert("Password is required to login");
            window.location.href = "<?=root?>login";
        }
    }

    var hash = window.location.hash.substr(1);
    if (hash == "invalid") {
        // Using a simple alert for now, you can replace with your toast notification
        alert("Email or password incorrect");
    }

    if (hash == "password-reset-completed") {
        // Using a simple alert for now, you can replace with your toast notification
        alert("Please check your email for the new password");
    }
    </script>

</body>
</html>