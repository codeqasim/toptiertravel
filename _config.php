<?php

use Medoo\Medoo;
define('DEBUG', false);

// EXECUTE ONLY ON LOCALHOST
$whitelist = array( '127.0.0.1', '::1' );
if(in_array($_SERVER['REMOTE_ADDR'], $whitelist)){

// DEVELOPMENT MODE
error_reporting(E_ALL);
ini_set('display_errors', DEBUG ? 'On' : 'Off');
}

// API KEY
define('api_key','api_key001');
define('api_url', "api/"); // ADD SLASH IN THE END OF STRING
define('api_modules', "https://api.phptravels.com"); // ADD Modules Path
define('upload_path', __DIR__ . '/uploads/'); 
define('upload_url', 'http://localhost:8888/toptiertravel/uploads/');
define('user_upload_url', 'http://localhost:8888/toptiertravel/assets/uploads/');
define("JWT_SECRET", "d9a3f7e81e2b4f20d4a87539ad7ccf46a8a5a9c04fbeb1f47e6e0f1adf382d12");

define('server','localhost');
define('dbname',"iataco_top1");
define('username',"root");
define('password',"root");