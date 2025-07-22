<?php

require_once '_config.php';
if(isset($_SESSION['admin_user_login']) == true ){ header("Location: dashboard.php"); exit; }

// LOGIN POST REQUEST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // RESET PASSWORD EMAIL
    $email = str_replace(' ', '', $_POST['email']);
    $new_password = rand(100000, 999999);
    $user = $db->query("SELECT * FROM `users` WHERE `email` LIKE '".$email."'")->fetchAll();
    if (isset($user[0]['email'])) {} else {

    ALERT_MSG('wrong_email');
    REDIRECT("login-forget-password.php");

    die; }

    $params = array( "password" => md5($new_password) );
    $id = $user[0]['id'];
    $data = UPDATE('users',$params,$id);

    // SEND EMAIL
    $title = "Forget Password";
    $template = "forget_password";
    $content = $new_password;
    $receiver_email = $user[0]['email'];
    $receiver_name = $user[0]['first_name'];
    MAILER($template,$title,$content,$receiver_email,$receiver_name);

    // INSERT TO LOGS
    $user_id = $user[0]['user_id'];
    $log_type = "forget_password";
    $datetime = date("Y-m-d h:i:sa");
    $desc = "Generated new password from forget password page and sent to email";
    logs($user_id,$log_type,$datetime,$desc);

    // REDIRECT PAGE
    ALERT_MSG('reset_password');
    REDIRECT("login.php");
    die;

   }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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

        .btn-hover-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(71, 85, 105, 0.15);
        }

        /* Pulse animation for icons */
        .pulse-slow {
            animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
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
        <div class="w-full max-w-md">
            <!-- Reset Password Card -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8 lg:p-10">
                <!-- Header Section -->
                <div class="text-center mb-8">
                    <!-- Logo with animation -->


                    <!-- Reset Password Icon -->
                    <div class="mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-amber-100 to-orange-100 rounded-2xl flex items-center justify-center mx-auto shadow-lg">
                            <svg class="w-8 h-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-3a1 1 0 011-1h2.586l6.243-6.243C11.978 9.927 12 9.464 12 9a6 6 0 016-6z" />
                            </svg>
                        </div>
                    </div>

                    <h1 class="text-3xl font-bold text-slate-900 mb-2">Reset Password</h1>
                    <p class="text-slate-600 leading-relaxed max-w-sm mx-auto">
                        Enter your email address and we'll send you a new password
                    </p>
                </div>

                <!-- Reset Password Form -->
                <form name="form" action="./login-forget-password.php" method="post" onsubmit="submission()" class="space-y-6">
                    <!-- Email Field -->
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-medium text-slate-700">Email Address</label>
                        <div class="relative">
                            <input
                                type="email"
                                id="email"
                                name="email"
                                placeholder="Enter your email address"
                                class="w-full px-4 py-3 pl-12 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-300 bg-white/80 backdrop-blur-sm"
                                required
                            >
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4">
                                <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">We'll email you a new password within minutes</p>
                    </div>

                    <!-- Submit Button -->
                    <div class="space-y-3">
                        <button
                            id="submit"
                            type="submit"
                            class="login_button w-full bg-gradient-to-r from-violet-600 to-purple-600 text-white py-3 px-4 rounded-xl font-semibold text-lg shadow-lg hover:shadow-xl transition-all duration-300 btn-hover focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2"
                        >
                            <div class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Send New Password
                            </div>
                        </button>

                        <!-- Back to Login Button -->
                        <a
                            href="<?=root?>login"
                            class="backlogin w-full bg-white border-2 border-slate-300 text-slate-700 py-3 px-4 rounded-xl font-semibold text-lg shadow-lg hover:shadow-xl hover:border-slate-400 transition-all duration-300 btn-hover-secondary focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 flex items-center justify-center"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Login
                        </a>

                        <!-- Loading Button (Hidden by default) -->
                        <button
                            class="loading_button hidden w-full bg-gradient-to-r from-violet-600 to-purple-600 text-white py-3 px-4 rounded-xl font-semibold text-lg shadow-lg opacity-75 cursor-not-allowed"
                            type="button"
                            disabled
                        >
                            <div class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Sending...
                            </div>
                        </button>
                    </div>
                </form>

                <!-- Security Notice -->
                <div class="mt-8 p-4 bg-blue-50 rounded-xl border border-blue-200">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-blue-900 mb-1">Security Notice</p>
                            <p class="text-xs text-blue-700">For your security, the new password will be automatically generated and sent to your email address.</p>
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
            const backBtn = document.querySelector('.backlogin');

            submitBtn.classList.add('hidden');
            backBtn.classList.add('hidden');
            loadingBtn.classList.remove('hidden');

            let email = $("#email").val();
            if (email == "") {
                event.preventDefault();
                alert("Email is required to reset password");
                window.location.href = "<?=root?>login-forget-password.php";
            }
        }

        var hash = window.location.hash.substr(1);
        if (hash == "invalid") {
            // Using a simple alert for now, you can replace with your toast notification
            alert("Email incorrect please check again");
        }
    </script>

</body>
</html>