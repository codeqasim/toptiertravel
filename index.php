<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', 'error.logs');
    error_reporting(E_ALL);

    use AppRouter\Router;
    use Curl\Curl;

    // GET SERVER ROOT PATH
    $root=(isset($_SERVER['HTTPS']) ? "https://" : "http://").$_SERVER['HTTP_HOST'];
    $root.= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
    define('root', $root);

    require_once "vendor/autoload.php";
    require_once "_config.php";
    require_once "App/Lib/router.php";

    // USER SESSION STATUS
    session_start();

    // ENVIROMENT OF API SERVER
    define('ENVIRONMENT', 'production');

    // DD FUNCTION FOR DEBUG RESPONSES
    function dd($d)
    {
    if (!headers_sent()) {
        header("Content-Type: application/json");
        print_r(json_encode($d, true));
    } else {
        echo '<pre>';
        print_r($d);
        echo '</pre>';
    }
    die;
    }

    // X-FRAME OPTIONS
    // header("X-Frame-Options: SAMEORIGIN");

    // ERROR 404 PAGE
    $router = new Router(function ($method, $path, $statusCode, $exception) {
    http_response_code($statusCode);

    // META DETAILS
    $meta = array(
    "title" => "404",
    "meta_title" => "404",
    "meta_desc" => "",
    "meta_img" => "",
    "meta_url" => "",
    "meta_author" => "",
    );

    views($meta,"404");

    });

    function clean_var($value){
    $value= strtolower(str_replace(' ', '-', $value));
    return $value;
    }

    // DIRECTION FUNCTION
    function REDIRECT($url)
    {
    echo '<script>window.location.replace("' . $url . '");</script>';
    die;
    }

    // ALERT MSG
    function ALERT_MSG($msg)
    {
    echo '<script>sessionStorage.setItem("alert_msg", "' . $msg . '")</script>';
    }

        $api_key = "api_key001";
        function base() {
            global $api_key;
            $params['api_key'] = $api_key;

            // Check if there is a cached response and it's still valid
            if (isset($_SESSION['cached_response']) && time() < $_SESSION['cache_expire']) {
                return $_SESSION['cached_response'];
            } else {
                // Gather additional parameters if available
                if (!empty($_SESSION['phptravels_client_currency'])) {
                    $params['currency'] = $_SESSION['phptravels_client_currency'];
                }

                if (!empty($_SESSION['phptravels_client_language_name'])) {
                    $params['language'] = $_SESSION['phptravels_client_language_name'];
                }

                // Initialize cURL
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => root."api/app",  // Ensure that 'root' is defined or replace it with actual URL root
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $params,
                ));

                $response = curl_exec($curl);
                curl_close($curl);

                if ($response && isset(json_decode($response)->data)) {
                    // Cache the response and set an expiration time
                    $_SESSION['cached_response'] = json_decode($response)->data;
                    $_SESSION['cache_expire'] = time() + 0.1; // Cache expires in 5 seconds

                    return $_SESSION['cached_response'];
                } else {
                    include "App/Controllers/DemoController.php";
                    echo "<style>body{font-weight:bold;gap:5px;background: #eee; display: flex; justify-content: center; align-items: center; font-family: system-ui; color: #0f1736;font-size:14px}</style>";
                    echo '<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';
                    echo "<title>No Response</title>";
                    echo "NO RESPONSE FROM API";
                    die;
                }
            }
        }

    /* INIT MULTI LANG */
    require './App/Lib/I18n/i18n.class.php';
    $i18n = new i18n('./lang/{LANGUAGE}.json', './cache/', 'us');

    $default_lang = array_values(array_filter(base()->languages, fn($obj) => $obj->default == 1));

    // CHECK IF LANGUAGE EXIST IN SESSION
    if (isset($_SESSION['phptravels_client_language_country'])) {
        $i18n->setForcedLang($_SESSION['phptravels_client_language_country']);
        $i18n->init();
    } else {
        $_SESSION['phptravels_client_language_dir'] = ($default_lang[0]->type);
        $i18n->setForcedLang(strtolower($default_lang[0]->country_id));
        $i18n->init();
    }

    // SESSION SET DEFAULT LANGUAGE
    $router->get('/language/(.+)/(.+)/(.+)', function ($country, $name, $dir) {
        $_SESSION['phptravels_client_language_country'] = $country;
        $_SESSION['phptravels_client_language_name'] = $name;
        $_SESSION['phptravels_client_language_dir'] = $dir;
        REDIRECT(root);
    });

    // SESSION SET DEFAULT LANGUAGE
    $router->get('/(currency)/(.+)', function ($currency, $code) {
        $_SESSION['phptravels_client_currency'] = $code;
        if(!empty($_SESSION["data_flight"]) && !empty($_SESSION["url"])) {
            unset($_SESSION["data_flight"]);
            unset($_SESSION["url"]);
        }
        REDIRECT(root);
    });

    // SITE OFFLINE MESSAGE
    if(base()->app->site_offline==1){
        echo base()->app->offline_message;
        echo '<style>body{display: flex; justify-content: center; align-items: center; font-family: sans-serif;}</sctyle>';
        die;
    }

    // REDIRECT USER TO LOGIN WHEN NO GUEST BOOKING ALLOWED
    if (!isset($_SESSION['phptravels_client'])) {
        if(base()->app->guest_booking==0){

            $segments = array_values(array_filter(explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'))));

            if
                (isset($segments[0]) && (
                        $segments[0] == 'login' ||
                        $segments[0] == 'signup' ||
                        $segments[0] == 'signup_success' )

                or
                isset($segments[1]) && (
                    $segments[1] == 'activation')
                )
            {

            } else {
                REDIRECT(root . 'login');
            }

        }
    }

    function GET($endpoint){
        global $api_key;
        $params['api_key'] = $api_key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, root . "./" . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response);
    }

    function POST($endpoint,$params){

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => root . "./" . $endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $params,
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);

    }

    function POST_MODULES($endpoint,$params){

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $params,
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);

    }

    // AIRLINE LOGO FUNCTION
    function airline_logo($name){

        $url = "https://assets.duffel.com/img/airlines/for-light-background/full-color-logo/";

        if (empty($name)){
            $img = root."assets/img/no_flight.png";
        } else {

            // SHOW MISSING AIRINES LOGOS
            if ($name=="G9"){
                $img = root."assets/img/airlines/G9.png";
            } else if ($name=="F3"){
                $img = root."assets/img/airlines/F3.png";
            }else {
                $img = $url.$name.".svg";
            }

        }

        return $img;
    }

    // ADD SESSION TO FUNCTION
    // $_SESSION['app'] = base();

    if (!isset($_SESSION['app'])){
        // $_SESSION['app'] = base();
    }

    // SET DEFAUT CURRENCY IF DOES NOT HAVE ONE
    if (!isset($_SESSION['phptravels_client_currency'])){
        foreach (app()->currencies as $currency){ if($currency->default == 1){
            $_SESSION['phptravels_client_currency'] = $currency->name;
        } }
    }

    define('currency',$_SESSION['phptravels_client_currency'] );
    define('dev', 0 );
    define('duration', 5 );

    function app(){ return base(); }

    define('meta_url', "");
    define('meta_author', "");

    // DEFINE TEMAPLTE VIEWS
    define('invoice_layout', "App/Views/Invoice_layout.php");
    define('layout', "App/Views/Invoice_layout.php");

    function views($meta,$view){

        $body = "App/Views/".$view.".php";
        include "App/Views/Main.php";

    }

    $current_uri = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    // GET CLIENT IP
    function user_ip(){

        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'no-ip';
        return $ipaddress;

    }

    $router->get('sd', function () {
        session_destroy();
        echo '
        <style>body{background:#545454;display:flex;justify-content:center;align-items: center;}</style>
        <svg width="44" height="44" viewBox="0 0 44 44" xmlns="http://www.w3.org/2000/svg" stroke="#fff"> <g fill="none" fill-rule="evenodd" stroke-width="2"> <circle cx="22" cy="22" r="1"> <animate attributeName="r" begin="0s" dur="1.8s" values="1; 20" calcMode="spline" keyTimes="0; 1" keySplines="0.165, 0.84, 0.44, 1" repeatCount="indefinite" /> <animate attributeName="stroke-opacity" begin="0s" dur="1.8s" values="1; 0" calcMode="spline" keyTimes="0; 1" keySplines="0.3, 0.61, 0.355, 1" repeatCount="indefinite" /> </circle> <circle cx="22" cy="22" r="1"> <animate attributeName="r" begin="-0.9s" dur="1.8s" values="1; 20" calcMode="spline" keyTimes="0; 1" keySplines="0.165, 0.84, 0.44, 1" repeatCount="indefinite" /> <animate attributeName="stroke-opacity" begin="-0.9s" dur="1.8s" values="1; 0" calcMode="spline" keyTimes="0; 1" keySplines="0.3, 0.61, 0.355, 1" repeatCount="indefinite" /> </circle> </g> </svg>
        <meta http-equiv="refresh" content="1; URL=' . root . '"/>';
    });

    // GENEARATE QR FOR INVOICE PAGE
    $router->post('qr', function () {
        define("QRSERVER", "https://api.qrserver.com/");
        function get_qr($to_encode) {
            $size = 100;
            $encode_d = urlencode($to_encode);
            return '<img src="'.QRSERVER.'v1/create-qr-code/?data='.$encode_d.'&size='.$size.'x'.$size.'" alt="'.$to_encode.'" title="'.$to_encode.'" />';
        }
            if($_POST['get_qr']) {
                echo get_qr($_POST['get_qr']);
            };
    });

    // AUTOLOAD CONTROLLERS
    $controllers = [
        "HomeController",
        "HotelsController",
        "AccountsController",
        "BlogsController",
        "CarsController",
        "CmsController",
        "FlightsController",
        "GlobalController",
        "OffersController",
        "ToursController",
        "VisaController",
        "PaymentController"
    ];

    foreach ($controllers as $controller) {
        require_once "App/Controllers/{$controller}.php";
    }

    // DYNAMIC CONTROLLERS FOR PAYMENT GATEWAYS
    $controller = 'App/Controllers/Gateways';
    $indir = array_filter(scandir($controller), function ($item) use ($controller) {
        return !is_dir($controller . '/' . $item);
    });

    foreach ($indir as $key => $value) {
        include $controller . '/' . $value;
    }

    // GENERATE CSRF TOKEN
    function CSRF(){

        // dd($_REQUEST);
        // if ($_SERVER['REQUEST_METHOD'] === 'POST' AND $_POST['form_token'] == $_POST['form_token'] ) {
        if (isset($_POST['form_token']) ) {
            if (isset($_POST["form_token"]) && isset($_SESSION["form_token"]) && $_POST["form_token"]==$_SESSION["form_token"]) {
            } else {
                echo '<body style="background: #282828; color: #fff; font-family: sans-serif; display: flex; justify-content: center; align-items: center; gap: 25px; text-decoration: none !important;">';
                echo '<p role="alert">Incorrect CSRF Token</p>';
                echo '<a style="text-decoration: none; color: #141414; background: orange; padding: 14px 25px; border-radius: 6px;" href="javascript:window.history.back();">Back</a>';
                echo '</body>';
                die;
            }
        }

        $_SESSION["form_token"] = bin2hex(random_bytes(32));
    }

    // MAIL SENDER FUNCTION
    function MAILER($template,$title,$content,$receiver_email,$receiver_name){

        // Presuming base() function fetches config each time; caching those calls
        $config = base()->app;
        $sender_name = $config->email_sender_name;
        $sender_email = $config->email_sender_email;
        $api_key = $config->email_api_key;

        $website_url = root('../');

        // Check if cached template exists and is still valid
        $cachedTemplatePath = "./cache/".$template.".cache";
        if (file_exists($cachedTemplatePath) && (time() - filemtime($cachedTemplatePath)) < 86400) { // 86400 seconds = 24 hours
        $views = file_get_contents($cachedTemplatePath);
        } else {
        ob_start();
        include "./email/".$template.".php";
        $views = ob_get_clean();
        file_put_contents($cachedTemplatePath, $views);
        }

        $params = [
        "api_key" => $api_key,
        "to" => ["$receiver_name <$receiver_email>"],
        "sender" => "$sender_name <$sender_email>",
        "subject" => $title,
        "html_body" => $views,
        ];

        // Initialize cURL only once if you're sending multiple emails in a loop
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.smtp2go.com/v3/email/send");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        // If you loop through multiple emails, move the curl_exec into the loop
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        $res = curl_exec($ch);

        curl_close($ch);
        // Optionally log or echo the response

    }

    // STORE LOGS TO LOGGING FILE
    function logs($SearchType)
    {
        // $log = "IP: " . $_SERVER['REMOTE_ADDR'] . ' - ' . date("F j, Y, g:i a") . '- Type => ' . $SearchType . ' - URL => ' . $_GET['url'] . PHP_EOL .
        //     "------------------------------------" . PHP_EOL;
        // $logs_path = "App/Logs";
        // if (!file_exists($logs_path)) {
        //     mkdir("App/Logs", 0777);
        // } else {
        // }
        // ;
        // file_put_contents('App/Logs/log_' . date("j.n.Y") . '.log', $log, FILE_APPEND);
    };

    function SEARCH_SESSION($MODULE, $CITY){
    };

    $router->dispatchGlobal();