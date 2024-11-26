<?php

/* ---------------------------------------------- */
// VISA INDEX PAGE
/* ---------------------------------------------- */
$router->get('(visa)', function ($nav_menu) {

    // META DETAILS
    $meta = array(
        "title" => "Visa",
        "meta_title" => "Visa",
        "meta_desc" => "",
        "meta_img" => "",
        "meta_url" => "",
        "meta_author" => "",
        "nav_menu" => $nav_menu,
    );

    views($meta,"Visa/Index");

});

// VISA SUBMISSION FORM
$router->get('(visa)/submit(.*)', function($nav_menu) {
    $url = explode('/', $_GET['url']);
    $count = count($url);

    $module = $url[0];
    $submit = $url[1];
    $from_country = $url[2];
    $to_country = $url[3];
    $date = $url[4];
    $ip = $_SERVER['REMOTE_ADDR'];
    $browser_version = $_SERVER['HTTP_USER_AGENT'];
    $request_type = 'web';

    if (empty($from_country) || empty($to_country)) {
        header('Location: '.root.'visa');
    }

    $data = array(
    'from_country' => $from_country,
    'to_country' => $to_country,
    'ip' => $ip,
    'browser_version' => $browser_version,
    'request_type' => $request_type,
    );

    // SEO META INFORMATION
    $title = "Submit Visa";
    $meta_title = "Submmit VIsa";
    $meta_appname = "";
    $meta_desc = "";
    $meta_img = "";
    $meta_url = "";
    $meta_author = "";
    $meta = "1";

    // META DETAILS
    $meta = array(
        "title" => "Visa",
        "meta_title" => "Visa",
        "meta_desc" => "",
        "meta_img" => "",
        "meta_url" => "",
        "meta_author" => "",
        "nav_menu" => $nav_menu,
    );

    views($meta,"Visa/Visa_form");
});

$router->post('submit/visa', function() {

    $params = array(
    'first_name' => $_POST['first_name'],
    'last_name' => $_POST['last_name'],
    'email' => $_POST['email'],
    'status' => 'waiting',
    'phone' => $_POST['phone'],
    'from_country' => $_POST['from_country'],
    'to_country' => $_POST['to_country'],
    'number_of_days' => $_POST['number_of_days'],
    'entry_type' => $_POST['entry_type'],
    'visa_type' => $_POST['visa_type'],
    'notes' => $_POST['notes'],
    'date' => $_POST['date'],
    );

    $RESPONSE = POST(api_url . 'visa_submission', $params);

    if (isset($RESPONSE->data)){
    $data = $RESPONSE->data;
    }

    // META DETAILS
    $meta = array(
        "title" => "Visa Success",
        "meta_title" => "Visa Success",
        "meta_desc" => "",
        "meta_img" => "",
        "meta_url" => "",
        "meta_author" => "",
        "data" => $data,
    );

    views($meta,"Visa/Success");
});


?>