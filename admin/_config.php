<?php

// USING MEEDO NAMESPACE
use Medoo\Medoo;

// VENDORS
require_once __DIR__ . "../../vendor/autoload.php";
require_once __DIR__."/../_config.php";
require_once __DIR__."/../credentials.php";
require_once __DIR__."/_core.php";

$db = new Medoo([
'type' => 'mysql',
'host' => server,
'database' => dbname,
'username' => username,
'password' => password
]);