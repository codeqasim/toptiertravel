<?php
$router->post('payment/coinsbuy', function() {

    $payload = json_decode(base64_decode($_POST['payload']));
    $url = root."payment/coinsbuy_create_invoice";
    $creds = '<form style="width: 100%;" action="'.$url.'" method="post">';
    $creds .= '<input type="hidden" name="payload" value="' . $_POST['payload'] . '">';
    $creds .= '<input style="background: #5469d4;" class="pay" type="submit" value="'.T::paynow.' '.($payload->currency).' '.(number_format($payload->price,2)).'">
        </form>';
    $body = $creds;
    include "App/Views/Pay_view.php";

});

$router->post('payment/coinsbuy_create_invoice', function() {

    if (!is_dir('coinsbuy')) {
        create_coinsbuy_directory();
    }


    $payload = json_decode(base64_decode($_POST['payload']));
    if($payload->type == 'wallet'){
        $payload->price = $_POST['price'];
    }
    $rand =date('Ymdhis').rand();
    $_SESSION['bookingkey'] = $rand;
    // SUCCESS URL
    $success_url = (root).'payment/success/?token='.$_POST['payload']."&key=".$rand."&type=0&gateway=coinsbuy";
    $gateway = array_column(base()->payment_gateways, null, 'name')['coinsbuy'];

    file_put_contents("coinsbuy/".$payload->booking_ref_no."_coinsbuy_logs.json", json_encode(["data"=>$success_url]));

    if($gateway->dev_mode==1){
        $base = "https://api-sandbox.coinsbuy.com/";
    }else{
        $base = "https://api.coinsbuy.com/";
    }

    $data = [
        'data' => [
            'type' => 'auth-token',
            'attributes' => [
                'login' => $gateway->c1,
                'password' => $gateway->c2
            ]
        ]
    ];

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $base.'token/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/vnd.api+json'
        ],
        CURLOPT_POSTFIELDS => json_encode($data)
    ]);

    $response = curl_exec($curl);
    if (curl_errno($curl)) {
        echo 'Error: ' . curl_error($curl);
    } else {
        $rsp = json_decode($response);
        $token = $rsp->data->attributes->access;

        $curl = curl_init();

        $data = [
            'data' => [
                'type' => 'deposit',
                'attributes' => [
                    'label' => ucfirst($payload->module_type)." Booking",
                    'tracking_id' => $payload->booking_ref_no,
                    'time_limit' => '900',
                    'inaccuracy' => 5,
                    'target_amount_requested' => $payload->price,
                    'callback_url' =>  (root).'coinsbuy/callback.php',
                    'confirmations_needed' => 2,
                    "payment_page_redirect_url"=>(root),
                    "payment_page_button_text" => "Return to Home"
                ],
                'relationships' => [
//                    'currency' => [
//                        'data' => [
//                            'type' => 'currency',
//                            'id' => '2145'
//                        ]
//                    ],
                    'wallet' => [
                        'data' => [
                            'type' => 'wallet',
                            'id' => $gateway->c3
                        ]
                    ]
                ]
            ]
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => $base.'deposit/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer '.$token,
                'Content-Type: application/vnd.api+json'
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        $rep = json_decode($response);
        header('Location: ' .$rep->data->attributes->payment_page);

    }

    curl_close($curl);
});

function create_coinsbuy_directory()
{

$directory = 'coinsbuy';

if (!is_dir($directory)) {
    mkdir($directory, 0777, true);
}
$file = $directory . '/callback.php';

$phpCode = <<<PHP
<?php

function handleCallback(\$rawData) {
    \$data = json_decode(\$rawData, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['error' => 'Invalid JSON data: ' . json_last_error_msg()];
    }
    if (!isset(\$data['data']) || !isset(\$data['included'])) {
        return ['error' => 'Missing required fields in the callback data'];
    }
    return \$data;
}

\$rawData = file_get_contents('php://input');
\$callbackResult = handleCallback(\$rawData);

if (isset(\$callbackResult['error'])) {
    error_log('Coinsbuy callback error: ' . \$callbackResult['error']);
    http_response_code(403);
} else {
    processCallbackData(\$callbackResult);
    http_response_code(200);
}

function processCallbackData(\$data)
{
    error_log('Received callback data: ' . print_r(\$data, true));

    \$transactionId = \$data['data']['id'] ?? null;
    \$newStatus = \$data['included'][1]['attributes']['status'] ?? null;

    if (\$transactionId && \$newStatus) {
        \$jsonData = file_get_contents(\$data['data']['attributes']['tracking_id']."_coinbuy_logs.json");
        \$get_link = json_decode(\$jsonData);
        if (\$get_link === null) {
            die('Error decoding JSON: ' . json_last_error_msg());
        }
        \$link = \$get_link->data;

        \$curl = curl_init();
        curl_setopt_array(\$curl, array(
            CURLOPT_URL => \$link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: PHPSESSID=e2f2f13vdhuf3mkf2ridvtj2f9'
            ),
        ));

        \$response = curl_exec(\$curl);
        curl_close(\$curl);
        error_log('API callback data: ' . print_r(\$response, true));
        unlink(\$data['data']['attributes']['tracking_id']."_coinbuy_logs.json");
        error_log("Updated transaction \$transactionId to status: \$newStatus");
    }
}
PHP;

file_put_contents($file, $phpCode);

}

