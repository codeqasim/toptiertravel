<?php

use Medoo\Medoo;

// ======================== COUNTRIES
$router->post('visa_from_countries', function() {

    // INCLUDE CONFIG
    include "./config.php";
    AUTH_CHECK();

    $params = array( "country_status" => 1, );
    $respose = $db->select("visa_from_countries", "*", $params);

    echo json_encode($respose);

});

// ======================== COUNTRIES
$router->post('visa_to_countries', function() {

    // INCLUDE CONFIG
    include "./config.php";
    AUTH_CHECK();

    $params = array( "country_status" => 1, );
    $respose = $db->select("visa_to_countries", "*", $params);

    echo json_encode($respose);

});

// ======================== SUBMISSION
$router->post('visa_submission', function() {

    // INCLUDE CONFIG
    include "./config.php";
    AUTH_CHECK();

    $rand = rand(100, 99);
    $date = date('Ymdhis');
    $uid = $date.$rand;

    $params = array("iso"=>$_POST['from_country']);
    $from_country = $db->select("countries", "*", $params);
    $from_co = ($from_country[0]['nicename']);

    $params = array("iso"=>$_POST['to_country']);
    $to_country = $db->select("countries", "*", $params);
    $to_co = ($from_country[0]['nicename']);

    $params = array(
    'first_name' => $_POST['first_name'],
    'last_name' => $_POST['last_name'],
    'date' => $_POST['date'],
    'email' => $_POST['email'],
    'status' => 'waiting',
    'phone' => $_POST['phone'],
    'from_country' => $from_co,
    'to_country' => $to_co,
    'number_of_days' => $_POST['number_of_days'],
    'entry_type' => $_POST['entry_type'],
    'visa_type' => $_POST['visa_type'],
    'notes' => $_POST['notes'],
    'created_at' => date("Y-m-d H:i:s"),
    'res_code' => $uid,
    );

    $resp = $db->insert("visa_submissions", $params);

    // HOOK
    $email = $_POST['email'];
    $name = $_POST['first_name']." ".$_POST['last_name'];
    $hook = "visa_submissions";
    include "./hooks.php";

    $id = $db->id();
    $res = $db->select("visa_submissions","*", [ "id" => $id ]);

    $respose = array ( "status"=>true, "message"=>"visa submitted.", "data"=> $res[0] );
    echo json_encode($respose);

});
