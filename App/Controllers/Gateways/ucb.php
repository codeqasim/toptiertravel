<?php

// GATEWAY NAME CONTROLLER ( IF SPACES IN NAME FOLLOW BY UNDERSCOPE INSTEAD DASH )

/*
$router->post('payment/ucb', function() {

	$payload = json_decode(base64_decode($_POST['payload']));
	if($payload->type == 'wallet'){
		$payload->price = $_POST['price'];
	}

	$rand =date('Ymdhis').rand();
	$_SESSION['bookingkey'] = $rand;

	$success_url = (root).'payment/success/?token='.$_POST['payload']."&amp;key=".$rand."&amp;type=0";
	$gateway = array_column(base()->payment_gateways, null, 'name')['UCB'];

	$data='<?xml version="1.0" encoding="UTF-8"?>';
	$data.="<TKKPG>";
	$data.="<Request>";
	$data.="<Operation>CreateOrder</Operation>";
	$data.="<Language>EN</Language>";
	$data.="<Order>";
	$data.="<OrderType>Purchase</OrderType>";
	$data.="<Merchant>".$gateway->c1."</Merchant>";
	$data.="<Amount>". $payload->price * 100 ."</Amount>";
	$data.="<Currency>050</Currency>";
	$data.="<Description>Test Transaction</Description>";
	$data.="<ApproveURL>".htmlentities($success_url)."</ApproveURL>";
	$data.="<CancelURL>".htmlentities(root)."</CancelURL>";
	$data.="<DeclineURL>".htmlentities(root)."</DeclineURL>";
	$data.="</Order></Request></TKKPG>";

	$xml=Postdata_ecomgateway($data);

	$OrderID=$xml->Response->Order->OrderID;
	$SessionID=$xml->Response->Order->SessionID;
	$URL=$xml->Response->Order->URL;
	$OrderStatus=$xml->Response->Order->OrderStatus;
	if ($OrderID!="" and $SessionID!=""){
		header("Location: " . $URL . "?ORDERID=" . $OrderID. "&SESSIONID=" . $SessionID . "");
		exit();
	}


});

function Postdata_ecomgateway($data)
{
	$hostname = '127.0.0.1';
	$port="647";

	$path = '/Exec';
	$content = '';

	$fp = fsockopen($hostname, $port, $errno, $errstr, 30);

	if (!$fp) die('<p>'.$errstr.' ('.$errno.')</p>');

	$headers = 'POST '.$path." HTTP/1.0\r\n";
	$headers .= 'Host: '.$hostname."\r\n";
	$headers .= "Content-type: application/x-www-form-urlencoded\r\n";
	$headers .= 'Content-Length: '.strlen($data)."\r\n\r\n";

	fwrite($fp, $headers.$data);

	while ( !feof($fp) ){
		if (substr($inStr,0,7)!=="<TKKPG>") continue;
		$content .= $inStr;
	}
	fclose($fp);
	$xml = simplexml_load_string($content);
	return ($xml);
}
 */

?>