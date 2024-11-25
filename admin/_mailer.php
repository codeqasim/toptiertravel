<?php

// MAIL SENDER FUNCTION
function MAILER($template,$title,$content,$receiver_email,$receiver_name){

        $params = array();
        $settings = GET('settings',$params)[0];

        $sender_name = $settings->email_sender_name;
        $sender_email = $settings->email_sender_email;
        $website_url = root.('../');

        ob_start();
        include "../email/".$template.".php";
        $views = ob_get_clean();

        // SEND EMAIL VIA SMTP
        if($settings->default_email_service=="smtp"){

            include "_mailer_smtp.php";

        }

        // SEND EMAIL VIA SMTP2GO
        else if($settings->default_email_service=="smtp2go"){

            $params = array(
            "api_key" => $settings->email_api_key,
            "to" => array("".$receiver_name." <".$receiver_email.">"),
            "sender" => "".$sender_name." <".$sender_email.">",
            "subject" => $title,
            "html_body" => $views,
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"https://api.smtp2go.com/v3/email/send");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            $res = curl_exec($ch);
            curl_close($ch);
            echo $res;

        }

    }

?>