<?php
session_start();

// Destroy PHP session
session_unset();
session_destroy();

// Expire JWT cookie
$domain = ($_SERVER['HTTP_HOST'] === 'localhost:8888') ? 'localhost' : '.toptiertravel.vip';
$secure = ($_SERVER['HTTP_HOST'] === 'localhost:8888') ? false : true;

setcookie(
    "admin_jwt",
    "",
    [
        'expires' => time() - 3600, // expire in the past
        'path' => '/',
        'domain' => $domain,
        'secure' => $secure,
        'httponly' => true,
        'samesite' => ($secure ? 'None' : 'Lax')
    ]
);

// Redirect to login
header("Location: login.php");
exit;
?>
