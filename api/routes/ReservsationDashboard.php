<?php
// HEADERS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header("X-Frame-Options: SAMEORIGIN");


/*==================
AGENT SALES AND COMMISSIONS API
==================*/
$router->post('agent/dashboard/reservations', function () {
    
    // INCLUDE CONFIG
    include "./config.php";

    required('user_id');

    $user_id = $_POST["user_id"];
    $interval = isset($_POST['interval']) ? $_POST['interval'] : null ;

    // CHECK USER
    $user = $db->select("users", "*", [ "user_id" => $user_id]);

        if(isset($user[0])){
            $user_data = (object)$user[0];
            
            $conditions = ["agent_id" => $user_id];

            // FETCH ALL BOOKINGS FOR THIS AGENT WITH CONDITIONS
            $hotel_sales = $db->select("hotels_bookings", "*", $conditions);

            if(isset($hotel_sales) && !empty($hotel_sales)){

                $total_reservations = 0;
                $total_revenue = 0;
                $occupancy_rate = 0;
                $avg_stay_length = 0;
                    foreach ($hotel_sales as $key => $hotel_sales) {
                        $total_reservations = count($hotel_sales);    
                        $total_revenue = ($hotel_sale['price_markup'] ?? 0) - ($hotel_sale['price_original'] ?? 0) - ($hotel_sale['agent_fee'] ?? 0);    
                        $occupancy_rate =  '';
                    }

                $this_week = date('Y-m-d', strtotime('-7 days'));

                $last_seven_days_sales = array_filter($hotel_sales, function($sale) use ($this_week) {
                    return isset($sale['booking_date']) && $sale['booking_date'] >= $this_week;
                });

                // Re-index array so keys are sequential
                $last_seven_days_sales = array_values($last_seven_days_sales);

                if(!empty($last_seven_days_sales)){
                    
                }

                $data = [
                    'total_reservations' => $total_reservations,
                    'total_revenue' => $total_revenue,
                    'occupancy_rate' => $occupancy_rate,
                    'avg_stay_length' => $avg_stay_length,
                    'last_seven_days_sales' => $last_seven_days_sales
                ];

                $response = array ( "status"=>true, "message"=>"data is retrieved", "data"=> $data );
            } else {
                $response = array ( "status"=>false, "message"=>"no sales found", "data"=> null );
            }
        } else {
            $response = array ( "status"=>false, "message"=>"no user found", "data"=> null );
        }
    echo json_encode($response);
});

?>