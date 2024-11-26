<?php

$router->get('visitor_details', function () {

    header('Access-Control-Allow-Origin: *');
    header("Content-Type: application/json");

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'http://ip-api.com/json/' .$_SERVER['REMOTE_ADDR'],
//    CURLOPT_URL => 'http://ip-api.com/json/182.189.6.189',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    if(!empty((json_decode($response)->countryCode))){
        $countryCode=(json_decode($response)->countryCode);

        $data = array(
            'ip' => $_SERVER['REMOTE_ADDR'],
            'country_code' => $countryCode,
            'date' => date('Y-m-d H:i:s')
        );

        echo json_encode($data);

    }

});

$router->get('theme/(.+)', function ($theme) {

    $_SESSION['theme']=$theme;
    header("Location: ".root);
    // echo $theme;

});

$router->get('(sitemap.xml)', function ($nav_menu) {

    header("Content-type: text/xml");
    include "./sitemap/sitemap.php";

});

$router->get('(sitemap-pages.xml)', function ($nav_menu) {

    header('Content-type: application/xml; charset=utf-8');
    include "./sitemap/sitemap_pages.php";

});

$router->get('(supplier)', function ($nav_menu) {

    REDIRECT(root.'admin');

});

?>