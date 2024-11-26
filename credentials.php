<?php

$geo_ip_cred = "FBA236B88534FA9D314541BA5F6C4D46";

// Define all required API keys and constants at the top of the file for easier management and updates.
define('API_LAYER_KEY', 'tV49I6NAZAujknDAtR6ywfhlBlytvZ9P');
define('GEO_IP_CRED', "BD365E594553B668F99B361F3CD95613");
define('GOOGLE_MAP_KEY', 'AIzaSyBvPooGV84U2zlu--JO8IQQvKDakc_VJ6k');

// Google, Facebook, Twitter, Instagram Login Credentials
define('GOOGLE_CLIENT_ID', '48292472266-agb9cjbo6v6ala0cdrc4c1r936env6iu');
define('FACEBOOK_CLIENT_ID', '1065505746900715');
define('TWITTER_CONSUMER_KEY', 'MjVcT3r1vwAsSCN2hZqTCIpOc');
define('TWITTER_CONSUMER_SECRET', 'oG2vT3QmuWo6G9p5UlGQovtVZC45FI95dAxuTvZgz6IaTKvNoV');
define('INSTAGRAM_CONSUMER_KEY', '1051319635876673');
define('INSTAGRAM_CONSUMER_SECRET', '42cd4496926288cc9084e84ea06ef92a');

// Set up base URL and redirect URLs based on the environment
$protocol = isset($_SERVER['HTTPS']) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$uri = explode('/', $_SERVER['REQUEST_URI']);
$baseUri = $host === 'localhost' ? $uri[1] : '';
$rootUrl = "{$protocol}{$host}/{$baseUri}";

// Define social login redirect URLs
define('TWITTER_REDIRECT_URL', "{$rootUrl}Social_Login");
define('INSTAGRAM_REDIRECT_URL', "{$rootUrl}Social_Login");

// Define login enable flags
define('GOOGLE_LOGIN_ENABLED', false);
define('FACEBOOK_LOGIN_ENABLED', false);
define('TWITTER_LOGIN_ENABLED', false);
define('INSTAGRAM_LOGIN_ENABLED', false);