<?php

require_once '../_config.php';
require_once './_config.php';
auth_check();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // file_put_contents("_post.log", print_r($_REQUEST, true));

        // NOTIFICATION
        if (isset($_POST['cancellation_update'])) {
        
        // Prepare parameters
        $params = array(
            'booking_ref_no' => $_POST['cancellation_id']
        );
        
        // Make API call to confirm cancellation if module is hotels
        if(isset($_POST['cancellation_module']) && $_POST['cancellation_module'] == 'hotels'){
            
            // Build the URL
            $url = api_url . $_POST['cancellation_module'] . '/cancellation/confirm';
            
            // Initialize cURL
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => http_build_query($params), // or json_encode($params) if API expects JSON
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded' // Change to 'Content-Type: application/json' if using json_encode
                ),
            ));
            
            $response = curl_exec($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            
            if (curl_errno($curl)) {
                $error = curl_error($curl);
                curl_close($curl);
                echo json_encode(array('status' => false, 'message' => 'cURL error: ' . $error));
                die;
            }
            
            curl_close($curl);
            
            // Decode the response
            $api_response = json_decode($response);
            // // Check if API call was successful
            // if (!$api_response || (isset($api_response->status) && $api_response->status === false)) {
            //     $error_msg = isset($api_response->message) ? $api_response->message : 'Cancellation confirmation failed';
            //     echo json_encode(array('status' => false, 'message' => $error_msg));
            //     die;
            // }
        }
        
        // Update database
        $db->update($_POST['cancellation_module']."_bookings", [ "cancellation_status" =>  1,"booking_status" =>  "cancelled" ], ["booking_ref_no" => $_POST['cancellation_id'] ]);
        die;
    }

    // NOTIFICATION
    if (isset($_POST['notification'])) {
        $db->insert("notifications", [ "name" => $_POST['name'],"description" => $_POST['description'], "date" =>  date('Y-m-d H:i:s') ]);
        die;
    }

    if (isset($_POST['notification_get'])) {
        $resp = $db->select("notifications", "*");
        $last_row = end($resp);
        // $data = array( "id"=>$last_row['id'] );
        print_r(json_encode($last_row['id']));
        die;
    }

    if (isset($_POST['notification_delete'])) {
        $db->update("notifications", ["status" => "0", ], ["id" => $_POST['id'] ]);
        die;
    }

    // UPLOAD FILES
    if (isset($_REQUEST['upload'])) {

        $DIR = "../uploads/";
        $allowedfileExtensions = array('jpeg','jpg', 'gif', 'png', 'zip', 'txt', 'xls', 'doc','pdf','webp');
        $code = rand(100000, 999999);

        if (!empty($_FILES["file"]["name"])) {
            $file_name      = $_FILES["file"]["name"];
            $temp_name      = $_FILES["file"]["tmp_name"];
            $imgtype        = $_FILES["file"]["type"];
            $ext            = pathinfo($file_name, PATHINFO_EXTENSION);
            $img            = $code.'-'.date("d-m-Y") . "-" . time() . "." . $ext;
            $target_path    = $DIR.$img;

            $fileNameCmps = explode(".", $file_name);
            $fileExtension = strtolower(end($fileNameCmps));

            if (in_array($fileExtension, $allowedfileExtensions))
            {

            if(move_uploaded_file($temp_name, $target_path)) {
            }else{ exit("Error While uploading image on the server"); }
            $date = date("yy:m:d:h:i");
            $sql = $db->query("INSERT INTO `listings_images` (`image_id`, `image_name`,`image_listing_id`, `image_created_at`) VALUES (NULL, '$img', '".$_REQUEST['upload']."','$date');");
            $sql = $db->query("UPDATE `listings` SET `thumb_img` = '".$img."' WHERE `listings`.`id` = ".$_REQUEST['upload'].";");

            }
        }

    }

    // DELETE IMAGES DB AND FILES
    if (isset($_REQUEST['product_image_id'])) {
    $sql = $db->query("DELETE FROM `listings_images` WHERE `image_id` = '".$_POST['product_image_id']."';");
    echo "deleted".$_POST['product_image_id'];
    unlink("../uploads/".$_POST['image_name']."");
    }

    // CONDITION FOR DELETE MULTIPLE ITEMS
    if (isset($_POST['module_order'])) {
        $db->update("modules", [ "order" => $_POST['order_id'] ], [ "id" => $_POST['module_id'] ]);
    }

    // CONDITION FOR DELETE MULTIPLE ITEMS
    if (isset($_POST['table_name'])) {
        foreach ($_POST['items'] as $i){
            $data = $db->delete($_POST['table_name'], [ "id" => $_POST['items'] ]);
        }
    }

    // UPDATE ITEM STATUS
    if (isset($_POST['status'])) {
        $params = array( "status" => $_POST['status'],);
        $id = $_POST['id'];
        $data = UPDATE($_POST['table_name'],$params,$id);
    }

    // MAKE ITEM AS A DEFAULT
    if (isset($_POST['default_status'])) {
        $data = $db->query('UPDATE '.$_POST['table_name'].' SET `default` = 0');

        if ($_POST['table_name']=="currencies"){
            $params = array(
                "default" => $_POST['default_status'],
                "rate" => 1,
            );
        } else {
            $params = array(
                "default" => $_POST['default_status'],
            );
        }
        $id = $_POST['id'];
        $data = UPDATE($_POST['table_name'],$params,$id);
    }

    // MAKE ITEM AS A FEATURE
    if (isset($_POST['feature_status'])) {
        $params = array( "featured" => $_POST['feature_status'],);
        $id = $_POST['id'];
        $data = UPDATE($_POST['table_name'],$params,$id);

    }

    // SEND MAIL
    if (isset($_POST['ajaxemail'])) {

        if (isset($_POST['title'])){ $title = $_POST['title']; }
        if (isset($_POST['template'])){ $template = $_POST['template']; }

        $content = "";

        $receiver_email = $_POST['email'];
        $receiver_name = $_POST['first_name'];
        MAILER($template,$title,$content,$receiver_email,$receiver_name);
    }

}

if (isset($_GET['email'])) {

    header("Content-Type: application/json");
    require_once './_config.php';

    // PARAMS
    $params = array(
        "status" => 1,
        "email[~]"=> $_GET['email'],
        "ORDER" => [ "id" => "DESC", ],
        "LIMIT" => 50
    );

    // USERS
    $data = $db->select("users","*", $params );
    $respose = array ( "status"=>true, "message"=>"locations data", "data"=> $data );

    echo json_encode($respose);
    die;

}

?>