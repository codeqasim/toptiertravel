<?php

// API CREDENTIALS CONFIDENTIAL ONLY FOR PHPTRAVELS.NET USE

// DUFFEL
if (isset($value['name']) && $value['name'] == "duffel") {
    if (empty($getvalue[0]['c1'])) {
        $param['c1'] = "duffel_live_GD2JnMFg8JWvvcvNgWFBYMe6eidAbS8NhZTHg9qdIzQ";
    //  $param['c1'] = "duffel_live_Gd2_W3z5r1e2zCaTJxodtJ5fN4QEdyEANOEmlGz1QXQ";
    }
}


// TRAVELPORT
if (isset($value['name']) && $value['name'] == "travelport") {
    if(empty($getvalue[0]['c1'])){
        $param['c1'] = "Universal API/uAPI2521640764-f33f1063";
    }
    if(empty($getvalue[0]['c2'])){
        $param['c2'] = "7g?P_3Ax6j";
    }
    if(empty($getvalue[0]['c3'])){
        $param['c3'] = "P7165793";
    }
    if(empty($getvalue[0]['c4'])){
        $param['c4'] = "1G";
    }
    if(empty($getvalue[0]['c5'])){
        $param['c5'] = "https://apac.universal-api.pp.travelport.com/B2BGateway/connect/uAPI/";
    }
    if(empty($getvalue[0]['env'])){
        $param['env'] = "dev";
    }
}

// AMADEUS
if (isset($value['name']) && $value['name'] == "amadeus") {
    if(empty($getvalue[0]['c1'])){
        $param['c1'] = "client_credentials";
    }
    if(empty($getvalue[0]['c2'])){
        $param['c2'] = "7hJlQAGIb9i2BJrKIX9Hit77IQ7LmFwh";
    }
    if(empty($getvalue[0]['c3'])){
        $param['c3'] = "8OVyABxzMaJ0Bcs3";
    }
    if(empty($getvalue[0]['evn'])){
        $param['evn'] = "pro";
    }
}

// TBO
if (isset($value['name']) && $value['name'] == "tbo") {
    if(empty($getvalue[0]['c1'])){
        $param['c1'] = "Onttest";
    }
    if(empty($getvalue[0]['c2'])){
        $param['c2'] = "Ot@131020";
    }
}

// KIWI
if (isset($value['name']) && $value['name'] == "kiwi") {
    if(empty($getvalue[0]['c1'])){
        $param['c1'] = "booknowphptravelsv2";
    }
    if(empty($getvalue[0]['c2'])){
        $param['c2'] = "QF06OfG8GUNdKB3vmU2T0tRGJTVwra_n";
    }
}

// AGODA
if(!empty($_POST['module_name']) && $_POST['module_name'] == "agoda" or !empty($supplier_name) && $supplier_name == "agoda"){
    if(empty($getvalue[0]['c1'])){
        $param['c1'] = "1923592";
    }
    if(empty($getvalue[0]['c2'])){
        $param['c2'] = "4a3d9548-eb5e-4235-9f52-16c4baea765c";
    }
}

// HOTELBEDS
if(!empty($_POST['module_name']) && $_POST['module_name'] == "hotelbeds" or !empty($supplier_name) && $supplier_name == "hotelbeds"){
    if(empty($getvalue[0]['c1'])){
        $param['c1'] = "531c46fc346c6729b9e9094f65abef70";
    }
    if(empty($getvalue[0]['c2'])){
        $param['c2'] = "1e83c000f3";
    }
}

// VIATOR
if(!empty($_POST['module_name']) && $_POST['module_name'] == "viator" or !empty($supplier_name) && $supplier_name == "viator"){
    if(empty($getvalue[0]['c1'])){
        $param['c1'] = "cd7229f3-194e-4e5b-80d4-b2804f05f7f6";
    }
}

// TIQET
if(!empty($_POST['module_name']) && $_POST['module_name'] == "tiqets" or !empty($supplier_name) && $supplier_name == "tiqets"){
    if(empty($getvalue[0]['c1'])){
        $param['c1'] = "fZdFl0JKlfgz5j5vNXaMxAHlLJ2ZzJVA";
    }
}

// discover
if(!empty($_POST['module_name']) && $_POST['module_name'] == "discover" or !empty($supplier_name) && $supplier_name == "discover"){
    if(empty($getvalue[0]['c1'])){
        $param['c1'] = "p9IFzgmu2v0N";
        $param['c2'] = "uFiaCLq20m2dBAAf";
    }
}

// cartrawler
if(!empty($_POST['module_name']) && $_POST['module_name'] == "cartrawler" or !empty($supplier_name) && $supplier_name == "cartrawler"){
    if(empty($getvalue[0]['c1'])){
        $param['c1'] = "645237";
    }
}