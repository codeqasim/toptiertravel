<?php
session_start();
// ======================== APP
$router->post('app', function() {

    // INCLUDE CONFIG
    include "./config.php";
    AUTH_CHECK();

    $user_id = $_POST['user_id'] ?? "";

    // APP SETTINGS
    $data = $db->select("settings","*", []);
    if(isset($data[0])){
        $data[0]['favicon_img'] = upload_url."global/".$data[0]['favicon_img'];
        $data[0]['header_logo_img'] = upload_url."global/".$data[0]['header_logo_img'];
        $data[0]['cover_img'] = upload_url."global/bg.png";
        $data[0]['newsletter_image'] = upload_url."global/".$data[0]['newsletter_image'];
    }
    $modules = $db->select("modules","*", [ "status" => 1, ]);

    // HOTELS SUGGESTIONS
    $params = array( "status" => 1, "ORDER" => [ "order" => "DESC", ], "LIMIT"=>[0,50] );

    $hotels_suggestions = $db->select("hotels_suggestions",[
        "[>]locations" => ["city"=>"city"]
    ],[
        "locations.country",
        "locations.id",
        "hotels_suggestions.city",
        "hotels_suggestions.status",
        "hotels_suggestions.order",
    ],
    [ "hotels_suggestions.status" => 1 ,"ORDER" => ["hotels_suggestions.order" => "DESC"]]
    );
    // $cars_suggestions = $db->select("cars_suggestions",[
    //     "[>]locations" => ["city"=>"city"]
    // ],[
    //     "locations.country",
    //     "locations.id",
    //     "cars_suggestions.city",
    //     "cars_suggestions.status",
    //     "cars_suggestions.order",
    // ],
    // [ "cars_suggestions.status" => 1 ,"ORDER" => ["cars_suggestions.order" => "DESC"]]
    // );


    $cars_suggestions = $db->select("cars_suggestions",[
        "[>]flights_airports" => ["city_airport"=>"code"]
    ],[
        "flights_airports.country",
        "flights_airports.airport",
        "flights_airports.city",
        "cars_suggestions.city_airport",
        "cars_suggestions.status",
    ],
    [ "cars_suggestions.status" => 1 ,"ORDER" => ["cars_suggestions.order" => "ASC"]]
    );

    $tours_suggestions = $db->select("tours_suggestions",[
        "[>]locations" => ["city"=>"city"]
    ],[
        "locations.country",
        "locations.id",
        "tours_suggestions.city",
        "tours_suggestions.status",
        "tours_suggestions.order",
    ],
    [ "tours_suggestions.status" => 1 ,"ORDER" => ["tours_suggestions.order" => "DESC"]]
    );

    // $featured_data = $db->query("SELECT
    // origin.city AS origin,
    // destination.city AS destination,
    // `flights_featured`.`status` AS status,
    // `flights_featured`.`price` AS price,
    // `flights_featured`.`from_airport` AS origin_code,
    // `flights_featured`.`to_airport` AS destination_code,
    // `flights_featured`.`airline` AS airline,
    // `flights_featured`.`id` AS id,
    // airline.name AS airline_name
    // FROM `flights_featured`
    // JOIN `flights_airports` AS origin ON from_airport = origin.code
    // JOIN `flights_airports` AS destination ON to_airport = destination.code
    // JOIN `flights_airlines` AS airline ON airline = airline.code"
    // )->fetchAll();

    $featured_data = $db->select("flights_featured", [
        "[>]flights_airports(origin)" => ["from_airport" => "code"],
        "[>]flights_airports(destination)" => ["to_airport" => "code"],
        "[>]flights_airlines(airline)" => ["airline" => "code"]
    ], [
        "origin.city(origin)",
        "destination.city(destination)",
        "flights_featured.status(status)",
        "flights_featured.price(price)",
        "flights_featured.from_airport(origin_code)",
        "flights_featured.to_airport(destination_code)",
        "flights_featured.airline(airline)",
        "flights_featured.id(id)",
        "airline.name(airline_name)"
    ]);

    foreach ($featured_data as $value){

        if ($value['status']==1){

            $default_currency = $db->select("currencies", "*", ["status" => 1,"default"=>1]);
            if(!empty($_POST["currency"])){
                $currency = $_POST["currency"];
            }else{
                $currency = $default_currency[0]['name'];
            }
            $current_currency_price =  $db->select("currencies", "*", ["name" => $default_currency[0]['name']]);
            // Get the exchange rate for the user's selected currency

            if (!empty($value['price']) && !empty($current_currency_price)) {
               $price = ceil(str_replace(',', '', $value['price']) / $current_currency_price[0]['rate']);
            } else {
                $price = 0;
            }
            $con_rate = $db->select("currencies", "*", ["name" => $currency]);
            $con_price = $price * $con_rate[0]['rate'];

            $featured_flight[] = array(
                "id"=>$value['id'],
                "origin_code"=>$value['origin_code'],
                "origin"=>$value['origin'],
                "destination_code"=>$value['destination_code'],
                "destination"=>$value['destination'],
                "price"=>number_format((float) $con_price, 2, '.', ''),
                "airline_name"=>$value['airline_name'],
                "airline"=>$value['airline'],
            );

        }

    }

    // TO PREVENT FROM FEATURED FLIGHTS ERROR
    empty($featured_flight)?$featured_flight="":$featured_flight=$featured_flight;

    $flights_suggestion = $db->select("flights_suggestions",[
        "[>]flights_airports" => ["city_airport"=>"code"]
    ],[
        "flights_airports.country",
        "flights_airports.airport",
        "flights_airports.city",
        "flights_suggestions.city_airport",
        "flights_suggestions.type",
        "flights_suggestions.status",
    ]);

    foreach ($flights_suggestion as $value){
        if ($value['status']==1){
            $flights_suggestions[] = array(
                "country"=>$value['country'],
                "airport"=>$value['airport'],
                "city"=>$value['city'],
                "city_airport"=>$value['city_airport'],
                "type"=>$value['type'],
                "type"=>$value['type'],
            );
        }
    }

    // TO PREVENT FROM SUGGESTIONS FLIGHTS ERROR
    empty($flights_suggestions)?$flights_suggestions="":$flights_suggestions=$flights_suggestions;


    // CURRENCIES
     $currencies = $db->select("currencies", [
         "[>]countries" => ["country" => "iso"]
    ], [
        "countries.nicename",
        "currencies.name",
        "currencies.default",
        "currencies.status",
        "currencies.rate",
        "countries.iso",
    ],[ "status" => 1, ]);

    $languages = $db->select("languages","*", [ "status" => 1, ]);
    $payment_gateways = $db->select("payment_gateways","*", [ "status" => 1, ]);

    // FEATURE TOURS
    $featured_tours = $db->select("tours", [
            "[>]locations" => ["location" => "city"] // join with locations
        ],
        [
            "tours.id",
            "tours.tour_basic_price",
            "tours.module",
            "tours.name",
            "tours.img",
            "tours.location",
            "tours.stars",
            "tours.status",
            "locations.city",
            "locations.country"
        ],[
            "tours.status" => 1,
            "tours.featured" => 1,
            "ORDER" => [ "tours.id" => "ASC" ]
        ]
    );

   $tours = array();
   foreach ($featured_tours as $value){

   $default_currency = $db->select("currencies", "*", ["status" => 1,"default"=>1]);
   if(!empty($_POST["currency"])){
       $currency = $_POST["currency"];
   }else{
       $currency = $default_currency[0]['name'];
   }
   $current_currency_price =  $db->select("currencies", ["rate"], ["name" => $default_currency[0]['name']]);
   // Get the exchange rate for the user's selected currency

   if (!empty($value['tour_basic_price']) && !empty($current_currency_price)) {
      $price = ceil(str_replace(',', '', $value['tour_basic_price']) / $current_currency_price[0]['rate']);
   } else {
       $price = 0;
   }
   $con_rate = $db->select("currencies", "*", ["name" => $currency]);
   $con_price = $price * $con_rate[0]['rate'];

   // Check if hotel is in user's favorites
    $is_favorite = 0; // Default to not favorite
    
    if ($user_id != "") { 
        $favorite_check = $db->select("user_favourites", "*", [
            "user_id" => $user_id,
            "item_id" => $value['id'],
            "module" => "tours"
        ]);
        
        if (!empty($favorite_check)) {
            $is_favorite = 1;
        }
    }
    
   if ($con_price != 0){
       $tours[] = (object)[
           "id"=>$value['id'],
           "basic_price"=>$value['tour_basic_price'],
           "adult"=>2,
           "child"=>0,
           "infants"=>0,
           "supplier"=>$value['module'],
           "name"=>$value['name'],
           "img"=>upload_url.$value['img'],
           "location"=>$value['location'],
           "city" => $value['city'],
           "country" => $value['country'],
           "stars"=>$value['stars'],
           "status"=>$value['status'],
           "price"=>number_format((float) $con_price, 2),
           "favorite" => $is_favorite
           ];
       }
   }
   
    //    FEATURE CARS
   $featured_cars = $db->select("cars",
     [
         "cars.id",
         "cars.price",
         "cars.module",
         "cars.currency",
         "cars.name",
         "cars.img",
         "cars.airport_code",
         "cars.stars",
         "cars.status",
     ],[
      "cars.status" => 1,
      "cars.featured" => 1,
      "ORDER" => [ "id" => "ASC" ]
     ]);
     $cars = array();
     foreach ($featured_cars as $value){

     $default_currency = $db->select("currencies", "*", ["status" => 1,"default"=>1]);
     if(!empty($_POST["currency"])){
         $currency = $_POST["currency"];
     }else{
         $currency = $default_currency[0]['name'];
     }
     $current_currency_price =  $db->select("currencies", ["rate"], ["name" => $default_currency[0]['name']]);
     // Get the exchange rate for the user's selected currency

     if (!empty($value['price']) && !empty($current_currency_price)) {
        $price = ceil(str_replace(',', '', $value['price']) / $current_currency_price[0]['rate']);
     } else {
         $price = 0;
     }
     $con_rate = $db->select("currencies", "*", ["name" => $currency]);
     $con_price = $price * $con_rate[0]['rate'];

     if ($con_price != 0){
         $cars[] = (object)[
             "id"=>$value['id'],
             "cars_price"=>$value['price'],
             "adult"=>2,
             "child"=>0,
             "infants"=>0,
             "supplier"=>$value['module'],
             "currency"=>$value['currency'],
             "name"=>$value['name'],
             "img"=>$value['img'],
             "location"=>$value['airport_code'],
             "stars"=>$value['stars'],
             "status"=>$value['status'],
             "price"=>number_format((float) $con_price, 2),
             ];
         }
     }

    // FEATURED HOTELS
    
    $featured_hotels = $db->select("hotels", [
         "[>]locations" => ["location" => "city"]
    ],
    [
        "hotels.id",
        "locations.city",
        "locations.country",
        "hotels.name",
        "hotels.img",
        "hotels.location",
        "hotels.stars",
        "hotels.status",
        "hotels.left_rooms",
    ],[
        "hotels.status" => 1,
        "hotels.featured" => 1,
        "ORDER" => [ "id" => "ASC" ]
    ]);

    $hotels = array();
    foreach ($featured_hotels as $value){

        $room_data = $db->select('hotels_rooms', [
            "[>]hotels_rooms_options" => ["id" => "room_id"]
        ], [
                'hotels_rooms_options.price'
            ], ['hotel_id' => $value['id']]);

    $default_currency = $db->select("currencies", "*", ["status" => 1,"default"=>1]);
    if(!empty($_POST["currency"])){
        $currency = $_POST["currency"];
    }else{
        $currency = $default_currency[0]['name'];
    }
    $current_currency_price =  $db->select("currencies", ["rate"], ["name" => $default_currency[0]['name']]);
    // Get the exchange rate for the user's selected currency

    if (!empty($room_data[0]['price']) && !empty($current_currency_price)) {
       $price = ceil(str_replace(',', '', $room_data[0]['price']) / $current_currency_price[0]['rate']);
    } else {
        $price = 0;
    }
    $con_rate = $db->select("currencies", "*", ["name" => $currency]);
    $con_price = $price * $con_rate[0]['rate'];

    // Fetch hotel amenities
    $hotel_amenities = $db->select('hotels_amenties_fk', [
        "[>]hotels_settings" => ["amenity_id" => "id"]
    ], [
        'hotels_settings.name'
    ], [
        'hotels_amenties_fk.hotel_id' => $value['id']
    ]);

    // Create amenities array
    $amenities = array();
    if (!empty($hotel_amenities)) {
        foreach ($hotel_amenities as $amenity) {
            $amenities[] = $amenity['name'];
        }
    }

    // Check if hotel is in user's favorites
    $is_favorite = 0; // Default to not favorite
    if ($user_id != "") { 
        $favorite_check = $db->select("user_favourites", "*", [
            "user_id" => $user_id,
            "item_id" => $value['id'],
            "module" => "hotels"
        ]);
        
        if (!empty($favorite_check)) {
            $is_favorite = 1;
        }
    }

    if ($price != 0){
        $hotels[] = (object)[
            "id"=>$value['id'],
            "city"=>$value['city'],
            "country"=>$value['country'],
            "name"=>$value['name'],
            "img"=>upload_url.$value['img'],
            "location"=>$value['location'],
            "stars"=>$value['stars'],
            "status"=>$value['status'],
            "left_rooms"=>$value['left_rooms'],
            "price"=>number_format((float) $con_price, 2),
            'amenities' => $amenities,
            "favorite" => $is_favorite
            ];
        }
    }

    // CMS
    $cms_pages = $db->select("cms", [
        "[>]cms_menu" => ["menu_id" => "id"]
    ], [
        "cms_menu.name",
        "cms.page_name",
        "cms.slug_url",
        "cms.category",
        "cms.id",

    ],[ "status" => 1, ]);

    $default_languages = $db->select("languages", "*", ["status" => 1,"default"=>1]);
    $lang = (!empty($_POST["language"])) ? $_POST["language"] : $default_languages[0]['name'];
    $defaultlanguagerow = $db->select("languages", "*", array('language_code' => $lang));
    $cms = [];

    foreach ($cms_pages as $value) {
        // Check if the $defaultlanguagerow array is set and the first element has an 'id'
        if (!empty($defaultlanguagerow) && isset($defaultlanguagerow[0]['id'])) {
            $translation = $db->select("cms_translations", "*", array("page_id" => $value['id'], 'language_id' => $defaultlanguagerow[0]['id']));

            // Check if $translation is not empty and the 'post_title' is set
            $post_title = (!empty($translation) && !empty($translation[0]['post_title'])) ? $translation[0]['post_title'] : str_replace('-', ' ', $value['slug_url']);

            // Append to $cms array
            $cms[] = (object) ['name' => $value['name'], 'page_name' => $post_title, 'slug_url' => $value['slug_url'], 'category' => $value['category']];
        } else {
            // Handle the case where $defaultlanguagerow is empty or does not have an 'id'
            error_log('Default language ID is missing for page ID: ' . $value['id']);
            // Optionally, set a default title or take other corrective measures
            $post_title = str_replace('-', ' ', $value['slug_url']);
            $cms[] = (object) ['name' => $value['name'], 'page_name' => $post_title, 'slug_url' => $value['slug_url'], 'category' => $value['category']];
        }
    }

    // BLOG
//     $blog = $db->select("blogs", [
//         "[>]blog_categories" => ["post_category" => "id"]
//    ], [
//        "blog_categories.cat_name",
//        "blogs.post_title",
//        "blogs.post_slug",
//        "blogs.post_img",
//        "blogs.id",

//    ],[ "status" =>1 ,"ORDER"=>['blogs.id'=>"DESC"]]
//     );

    // BLOG
    $blog = $db->select("blogs", [
        "blogs.post_title",
        "blogs.post_slug",
        "blogs.post_img",
        "blogs.id",

    ],[ "status" =>1 ,"LIMIT"=>3,"ORDER"=>['blogs.id'=>"DESC"]]
    );

    // BLOG
    // $blog = $db->select("blogs","*",[ "LIMIT"=>3,"status" =>1 ,"ORDER"=>['id'=>"DESC"]]);

    $our_services = $db->select("our_services", "*", [
        "LIMIT" => 3,
        "ORDER" => ["created_at" => "DESC"]
    ]);

    $services = [];
    foreach ($our_services as $value) {
        // Get translation for the current default language
        if(isset($defaultlanguagerow[0])){
        $translation = $db->select("our_services_translations", "*", array(
            "service_id" => $value['id'],
            'language_id' => $defaultlanguagerow[0]['id']
        ));
        }else{
            $translation = '';
        }
        
        // Use translation if available, otherwise fallback to original
        
        if (!empty($translation) && !empty($translation[0]['title'])) {
            $title = $translation[0]['title'];
            $description = $translation[0]['description'];
            $button_text = $translation[0]['button_text'];
        } else {
            $title = $value['title'];
            $description = $value['description']; 
            $button_text = $value['button_text'];
        }
       
        $services[] = (object) [
            'title' => $title,
            'description' => $description,
            'button_text' => $button_text,
            'slug' => $value['slug'],
            'background_image' => upload_url . $value['background_image']
        ];
        
    }
    
    $get_testimonials = $db->select("testimonials", "*", [
        "status" => "1",
        "ORDER"  => ["created_at" => "DESC"]
    ]);

    $testimonials = [];
    foreach ($get_testimonials as $value) {
        // Get translation for the current default language
        
        if(isset($defaultlanguagerow[0])){
        $translation = $db->select("testimonials_translations", "*", array(
            "testimonial_id" => $value['id'],
            'language_id' => $defaultlanguagerow[0]['id']
        ));
        }else{
            $translation = '';
        }
        
        // Use translation if available, otherwise fallback to original
        if (!empty($translation) && !empty($translation[0]['name'])) {
            $name = $translation[0]['name'];
            $country = $translation[0]['country'];
            $title = $translation[0]['title'];
            $description = $translation[0]['description'];
        } else {
            $name = $value['name'];
            $country = $value['country'];
            $title = $value['title'];
            $description = $value['description'];
        }
        
        $testimonials[] = (object) [
            'id' => $value['id'],
            'name' => $name,
            'country' => $country,
            'title' => $title,
            'description' => $description,
            'ratings' => $value['ratings'],
            'profile_photo' => upload_url . $value['profile_photo'],
            'photo' => upload_url . $value['photo'],
            'created_at' => $value['created_at'],
        ];
    }
    
    $display_modules = $db->select('modules','*',['status' => 1]);
    $availableModules = [];
    foreach ($display_modules as $display_module) {
        $moduleName = is_object($display_module) ? $display_module->name : $display_module['name'];
        $availableModules[] = $moduleName;
    }

    $respose = array ( "status"=> true, "message"=>"app main response",
    "data"=>array(
        "app"=> $data[0],
        "modules"=> $modules,
        "currencies"=> $currencies,
        "languages"=> $languages,
        "featured_hotels" => in_array('hotels', $availableModules) ? $hotels : [],
        "featured_tours" => in_array('tours', $availableModules) ? $tours : [],
        "featured_cars" => in_array('cars', $availableModules) ? $cars : [],
        "featured_flights" => in_array('flights', $availableModules) ? $featured_flight : [],
        "hotels_suggestions"=> $hotels_suggestions,
        "cars_suggestions"=> $cars_suggestions,
        "tours_suggestions"=> $tours_suggestions,
        "flights_suggestions"=> $flights_suggestions,
        "payment_gateways"=> $payment_gateways,
        "cms"=> $cms,
        "featured_blog"=> $blog,
        "our_services" => $services,
        "testimonials" => $testimonials,
    ),
    );

echo json_encode($respose);

});
// ======================== APP

// ======================== CMS PAGES
$router->post('cms_page', function() {

    // INCLUDE CONFIG
    include "./config.php";
    $lang = isset($_POST['lang']) ? $_POST['lang'] : "english";
    $language_id = $db->select("languages", "*", array('language_code' => strtolower($lang)));

    // PARAMS
    $params = array(
        "slug_url[~]"=> $_POST['slug_url'],
      );

    // PAGE
    $data = $db->select("cms","*", $params);

    if (is_array($data) && count($data) > 0 && isset($data[0]['id']) &&
        is_array($language_id) && count($language_id) > 0 && isset($language_id[0]['id'])) {
        $translation = $db->select("cms_translations", "*", array("page_id" => $data[0]['id'], 'language_id' => $language_id[0]['id']));
        } else {
        $translation = null; // or set to a default translation
    }

    $data[0]['page_name'] = !empty($translation[0]['post_title']) 
        ? html_entity_decode($translation[0]['post_title'], ENT_QUOTES) 
        : html_entity_decode($data[0]['page_name'], ENT_QUOTES);

    $data[0]['content'] = !empty($translation[0]['post_desc']) 
        ? html_entity_decode($translation[0]['post_desc'], ENT_QUOTES) 
        : html_entity_decode($data[0]['content'], ENT_QUOTES);

    $respose = array ( "status"=>true, "message"=>"CMS page data", "data"=> $data );

echo json_encode($respose);

});
// ======================== CMS PAGES

// ======================== Locations
$router->get('hotels_locations', function() {

    // INCLUDE CONFIG
    include "./config.php";

    // PARAMS
    $params = array(
        "status" => 1,
        "city[~]"=> $_GET['city'],
        "ORDER" => [ "id" => "DESC", ],
        "LIMIT" => 50
    );

    // LOCATIONS
    $data = $db->select("locations","*", $params );

    $respose = array ( "status"=>true, "message"=>"locations data", "data"=> $data );

echo json_encode($respose);

});

// ======================== TOURS LOCATIONS
$router->get('tours_locations', function() {

    // INCLUDE CONFIG
    include "./config.php";

    // PARAMS
    $params = array(
        "status" => 1,
        "city[~]"=> $_GET['city'],
        "ORDER" => [ "id" => "DESC", ],
        "LIMIT" => 50
    );

    // LOCATIONS
    $data = $db->select("locations","*", $params );

    $respose = array ( "status"=>true, "message"=>"locations data", "data"=> $data );

    echo json_encode($respose);

});
// ======================== TOURS LOCATIONS

// ======================== Cars Locations
$router->get('cars_locations', function() {

    // INCLUDE CONFIG
    include "./config.php";

    // PARAMS
    $params = array(
        "status" => 1,
        "city[~]"=> $_GET['city'],
        "ORDER" => [ "id" => "DESC", ],
        "LIMIT" => 50
    );

    // LOCATIONS
    $data = $db->select("locations","*", $params );

    $respose = array ( "status"=>true, "message"=>"locations data", "data"=> $data );

    echo json_encode($respose);

});

// ======================== FLIGHTS LOCATIONS
$router->get('flights_locations', function() {

        // $curl = curl_init();
        // curl_setopt_array($curl, array(
        // CURLOPT_URL => 'https://www.kayak.com/mvm/smartyv2/search?f=j&s=airportonly&where='.$_GET['city'],
        // CURLOPT_RETURNTRANSFER => true,
        // CURLOPT_ENCODING => '',
        // CURLOPT_MAXREDIRS => 10,
        // CURLOPT_TIMEOUT => 0,
        // CURLOPT_FOLLOWLOCATION => true,
        // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        // CURLOPT_CUSTOMREQUEST => 'GET',
        // ));
        // $data = curl_exec($curl);
        // curl_close($curl);
        // echo ($data);

        // INCLUDE CONFIG
        include "./config.php";

        // PARAMS
        $params = array(
            "status" => 1,
            "code[=]"=> $_GET['city'],
            "ORDER" => [ "id" => "DESC", ],
            "LIMIT" => 50
        );

        // LOCATIONS
        $data = $db->select("flights_airports","*", $params );

        if (empty($data)) {

         // PARAMS
        $params = array(
            "status" => 1,
            "city[~]"=> $_GET['city'],
            "ORDER" => [ "id" => "DESC", ],
            "LIMIT" => 50
        );
        $data = $db->select("flights_airports","*", $params );

        if(empty($data)){
            // PARAMS
            $params = array(
                "status" => 1,
                "airport[~]"=> $_GET['city'],
                "ORDER" => [ "id" => "DESC", ],
                "LIMIT" => 50
            );
            $data = $db->select("flights_airports","*", $params );
            echo json_encode(utf8ize($data));
            die;
        }
        echo json_encode(utf8ize($data));
        die;

        }

        $respose = array ( "status"=>true, "message"=>"locations data", "data"=> $data );
        echo json_encode(utf8ize($data));

});

function utf8ize( $mixed ) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = utf8ize($value);
        }
    } elseif (is_string($mixed)) {
        return mb_convert_encoding($mixed, "UTF-8", "UTF-8");
    }
    return $mixed;
}
// ======================== FLIGHTS LOCATIONS

// ======================== NEWSLETTER
$router->post('newsletter-subscribe', function() {

    // INCLUDE CONFIG
    include "./config.php";

    required('name');
    required('email');

    // EMAIL EXIST VALIDATION
    $exist_mail = $db->select('newsletter', [ 'email', ], [ 'email' => $_POST['email'], ]);
    if (isset($exist_mail[0]['email'])) {
        $respose = array ( "status"=>false, "message"=>"email already exist.", "data"=> "" );
        echo json_encode($respose);
        die;
    }

    $datetime = date("Y-m-d h:i:sa");

    // PARAMS
    $params = array(
        "name" => $_POST['name'],
        "email"=> $_POST['email'],
        "created_at"=> $datetime,
    );

    $data = $db->insert("newsletter", $params );

    $respose = array ( "status"=>true, "message"=>"newsletter email subscribed", "data"=> $data);

    // HOOK
    $name=$_POST['name'];
    $email=$_POST['email'];

    $hook="newsletter_subscribe";
    include "./hooks.php";

    echo json_encode($respose);

});
// ======================== NEWSLETTER

// ======================== FAVOURITES
$router->post('favourites', function() {

    // INCLUDE CONFIG
    include "./config.php";

    required('item_id');
    required('module');
    required('user_id');

    $user_id = $_POST['user_id'];
    $item_id = $_POST['item_id'];
    $module = $_POST['module'];

    // CHECK IF FAVOURITE ALREADY EXISTS
    $existing_favourite = $db->select('user_favourites', ['id'], [
        'user_id' => $user_id,
        'item_id' => $item_id,
        'module' => $module
    ]);

    $action = '';
    if (!empty($existing_favourite) && isset($existing_favourite[0]['id'])) {
        // REMOVE FROM FAVOURITES
        $db->delete("user_favourites", [
            "user_id" => $user_id,
            "item_id" => $item_id,
            "module"    => $module
        ]);
        $action = "removed";
    } else {
        // ADD TO FAVOURITES
        $params = [
            "user_id" => $user_id,
            "item_id" => $item_id,
            "module"    => $module
        ];
        $db->insert("user_favourites", $params);
        $action = "added";
    }

    $response = array(
        "status" => true, 
        "message" => "item $action to favourites", 
        "data" => array(
            "message" => "Item successfully $action to favourites"
        )
    );

    echo json_encode($response);

});

// ======================== FAVOURITES

// Invoice Payment API Endpoint
$router->post('invoice/pay', function () {
    include "./config.php";
    header('Content-Type: application/json');

    try {
        // Check if booking reference number is provided
        if (empty($_POST['booking_ref_no'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'booking_ref_no is required',
                'error_code' => 'MISSING_BOOKING_REF'
            ]);
            exit;
        }

        $booking_ref_no = trim($_POST['booking_ref_no']);
        $payment_gateway = isset($_POST['payment_gateway']) ? strtolower(trim($_POST['payment_gateway'])) : '';

        // Get booking details from database
        $invoice_data = $db->select('hotels_bookings', '*', ['booking_ref_no' => $booking_ref_no]);

        if (empty($invoice_data)) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Invoice not found',
                'error_code' => 'INVOICE_NOT_FOUND'
            ]);
            exit;
        }

        $data = $invoice_data[0];

        // Make sure we have a payment gateway specified
        if (empty($payment_gateway) && empty($data['payment_gateway'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'payment_gateway is required',
                'error_code' => 'MISSING_GATEWAY'
            ]);
            exit;
        }

        // Use POST gateway if provided, otherwise use the one from database
        $payment_gateway = !empty($payment_gateway) ? $payment_gateway : strtolower($data['payment_gateway']);
        
        // Don't allow payment if invoice is already paid
        if ($data['payment_status'] === 'paid') {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invoice is already paid',
                'error_code' => 'ALREADY_PAID',
                'data' => [
                    'booking_ref_no' => $data['booking_ref_no'],
                    'payment_status' => $data['payment_status'],
                    'booking_status' => $data['booking_status']
                ]
            ]);
            exit;
        }

        // Block payment if there's a pending cancellation request
        if (!empty($data['cancellation_request']) && $data['cancellation_request'] == 1) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Cancellation request pending',
                'error_code' => 'CANCELLATION_PENDING'
            ]);
            exit;
        }

        // Get list of active payment gateways from database
        $available_gateways = $db->select("payment_gateways", "name", ["status" => 1]);

        // Convert gateway names to lowercase and replace spaces with underscores
        $available_gateways = array_map(function($name) {
            return strtolower(str_replace(' ', '_', $name));
        }, $available_gateways);

        $gateway_match = in_array($payment_gateway, $available_gateways);
        
        // Return error if requested gateway is not available
        if (!$gateway_match) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid payment gateway',
                'error_code' => 'INVALID_GATEWAY',
                'available_gateways' => array_map(fn($gw) => strtolower(str_replace(' ', '_', $gw)), $available_gateways)
            ]);
            exit;
        }

        // Extract user information from booking data
        $user_data = json_decode($data['user_data'], true);
        if (empty($user_data['email'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid user data in booking',
                'error_code' => 'INVALID_USER_DATA'
            ]);
            exit;
        }
        
        // Build payment data payload
        $payload = [
            'booking_ref_no' => $data['booking_ref_no'],
            'currency' => $data['currency_markup'],
            'price' => $data['price_markup'],
            'client_email' => $user_data['email'],
            'invoice_url' => $_POST['invoice_url'] ?? root . 'invoice/' . $data['booking_ref_no'],
            'type' => 'invoice',
            'user_id' => $user_data['user_id'] ?? '',
            'module_type' => $data['module_type'],
        ];

        $encoded_payload = base64_encode(json_encode($payload));
        $payment_url = root . 'payment/' . $payment_gateway;

        // Return payment information to client
        echo json_encode([
            'success' => true,
            'message' => 'Payment details retrieved successfully',
            'data' => [
                'booking_ref_no' => $data['booking_ref_no'],
                'payment_status' => $data['payment_status'],
                'booking_status' => $data['booking_status'],
                'currency' => $data['currency_markup'],
                'amount' => $data['price_markup'],
                'payment_gateway' => $payment_gateway,
                'payment_url' => $payment_url,
                'payload' => $encoded_payload,
                'client_email' => $user_data['email']
            ]
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error',
            'error_code' => 'INTERNAL_ERROR',
            'error' => $e->getMessage()
        ]);
    }
});

// Alternative endpoint for direct payment processing
$router->post('invoice/process-payment', function () {
    include "./config.php";
    header('Content-Type: application/json');
    
    try {
        // Verify that payload is present in request
        if (!isset($_POST['payload']) || empty($_POST['payload'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'payload is required',
                'error_code' => 'MISSING_PAYLOAD'
            ]);
            exit;
        }
        
        // Verify that payment gateway is specified
        if (!isset($_POST['payment_gateway']) || empty($_POST['payment_gateway'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'payment_gateway is required',
                'error_code' => 'MISSING_GATEWAY'
            ]);
            exit;
        }
        
        // Decode the payment payload
        $payload = json_decode(base64_decode($_POST['payload']));
        $payment_gateway = strtolower(trim($_POST['payment_gateway']));
    
        if (!$payload) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid payload format',
                'error_code' => 'INVALID_PAYLOAD'
            ]);
            exit;
        }
        
        // Handle different payment gateways
        switch ($payment_gateway) {
            case 'stripe':
                // Format gateway name for database lookup
                $gatewayName = str_replace('_', ' ', ucwords(strtolower($payment_gateway)));

                // Retrieve Stripe gateway configuration from database
                $gateway = $db->get("payment_gateways", "*", [
                    "name" => $gatewayName,
                    "status" => 1
                ]);

                if (!$gateway) {
                    http_response_code(400);
                    echo json_encode(["success" => false, "message" => "Stripe gateway not found in DB"]);
                    exit;
                }

                // Make sure we have the Stripe secret key configured
                if (empty($gateway['c2'])) {
                    http_response_code(500);
                    echo json_encode(["success" => false, "message" => "Stripe secret key missing in DB"]);
                    exit;
                }

                // Initialize Stripe with secret key
                \Stripe\Stripe::setApiKey($gateway['c2']);

                // Generate unique booking session key
                $rand = date('Ymdhis') . rand();
                $_SESSION['bookingkey'] = $rand;

                // Prepare success callback URL
                $token = urlencode($_POST['payload']);
                $success_url = root . "payment/success/?token={$token}&gateway=stripe&key=&type=0";

                $success_url = str_replace('/api','',$success_url);

                // Convert amount to cents for Stripe
                $amount = intval($payload->price * 100);

                // Create Stripe checkout session
                $session = \Stripe\Checkout\Session::create([
                    'customer_email'      => $payload->client_email,
                    'payment_method_types'=> ['card'],
                    'mode'                => 'payment',
                    'metadata'            => [
                        'booking_ref_no'  => $payload->booking_ref_no,
                        'client_email'    => $payload->client_email,
                        'gateway'         => 'stripe'
                    ],
                    'line_items' => [[
                        'price_data' => [
                            'currency'     => strtolower($payload->currency),
                            'unit_amount'  => $amount,
                            'product_data' => [
                                'name'        => 'Travel Booking',
                                'description' => "Booking for Invoice {$payload->booking_ref_no}",
                            ],
                        ],
                        'quantity' => 1,
                    ]],
                    'success_url' => $success_url . "&session_id={CHECKOUT_SESSION_ID}",
                    'cancel_url'  => $payload->invoice_url
                ]);

                // Return Stripe session details
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Stripe session created',
                    'data' => [
                        'session_id' => $session->id,
                        'checkout_url' => $session->url,
                        'publishable_key' => $gateway['c1']
                    ]
                ]);
                break;
                
            // Add other payment gateways here
            default:
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Payment gateway not supported in API',
                    'error_code' => 'GATEWAY_NOT_SUPPORTED'
                ]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Payment processing failed',
            'error_code' => 'PROCESSING_ERROR',
            'error' => $e->getMessage()
        ]);
    }
});

?>