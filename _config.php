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

define('server','localhost');
define('dbname',"phptrave2_db");
define('username',"phptrave2_db");
define('password',"phptrave2_db");