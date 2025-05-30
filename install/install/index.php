<?php

  // enviroment settings
  error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

  // set base path
  define('BASEPATH',dirname($_SERVER['PHP_SELF']));
  define('APPPATH', __DIR__.'../app/');

  // get functions
  require('includes/functions.php');

  // check requirements
  _check_requirements();
  // install
  if(isset($_POST['submit'])) {
      // [1] connect to the db
      $db = new mysqli($_POST['db_host'], $_POST['db_username'], $_POST['db_password'], $_POST['db_name']);
      if(mysqli_connect_error()) {
          _error(DB_ERROR);
      }
      $db->set_charset("utf8");
      // [2] check admin data
      /* check email */
      if(is_empty($_POST['admin_email']) && !valid_email($_POST['admin_email'])) {
          _error("Error", "Please enter a valid admin email");
      }
      /* check password */
      if(is_empty($_POST['admin_password']) && strlen($_POST['admin_password']) < 6) {
          _error("Error", "Please enter a valid admin password");
      }
      // [3] create the database
      $structure = "";
      $sqlquery = file_get_contents('database.sql');
      $db->query( 'SET @@global.max_allowed_packet = ' . 6 * 1024 * 1024 );
      $db->multi_query($sqlquery) or _error("Error", $db->error);
      $count = 0;
      // flush multi_queries
      do{
         require_once("progress.php");
        } while(mysqli_more_results($db) && mysqli_next_result($db));
      // [4] update system settings

      function clean($data) {
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      return $data;
      }

      $license = clean($_POST['license']);
      $siteTitle = clean($_POST['siteTitle']);
      $domain = clean($_POST['domain']);
      $email = clean($_POST['admin_email']);
      $pass = clean(md5($_POST['admin_password']));

      $db->query(sprintf("UPDATE users SET email = '$email', password =  '$pass' WHERE id = '1' "));
      $db->query(sprintf("UPDATE settings SET business_name = '$siteTitle', site_url =  '$domain', license_key =  '$license' WHERE id = '1' "));

      // $db->query(sprintf("UPDATE pt_accounts SET ai_first_name = 'Super Admin', accounts_email = %s, accounts_password = %s WHERE accounts_type='webadmin'", secure($_POST['admin_email']), secure(sha1($_POST['admin_password'])))) or _error("Error #101", $db->error);
      // $db->query(sprintf("UPDATE pt_app_settings SET site_url = %s,site_title = %s, date_f = 'd/m/Y', date_f_js = 'dd/mm/yyyy' WHERE user='webadmin'", secure($_POST['domain']), secure($_POST['siteTitle']))) or _error("Error #101", $db->error);

      write_config($_POST);
      sendEmail($_POST);
      _redirect($_POST['domain']."admin");
  }
  ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../api/assets/img/favicon.png">
    <title>Installer</title>
    <link rel="stylesheet" type="text/css" href="./includes/assets/css/installer.min.css" />
    <script src="includes/assets/js/modernizr.custom.js"></script>
  </head>
  <body>
    <div class="container">
      <div class="fs-form-wrap" id="fs-form-wrap">
        <div class="fs-title">
          <h1>Installation</h1>
        </div>
        <form id="myform" class="fs-form fs-form-full" autocomplete="off" action="" method="post">
          <ol class="fs-fields">
            <li>
              <p class="fs-field-label fs-anim-upper">
                Welcome to
                <strong>PHPTRAVELS </strong> installation! Just fill in the information of this process and get started.
              </p>
            </li>
            <li>
              <label class="fs-field-label fs-anim-upper" for="license" data-info="Your license key find guide on docs.phptravels.com">License Key?</label>
              <input class="fs-anim-lower" id="license" name="license" type="text" placeholder="Purchase license key" value=""/>
            </li>
            <li>
              <label class="fs-field-label fs-anim-upper" for="domain" data-info="Domain name on which PHPTRAVELS is going to be installed">Domain Name?</label>
              <input class="fs-anim-lower" id="domain" name="domain" type="text" placeholder="www.phptravels.com" value="<?php echo baseDomain(); ?>" required/>
            </li>
            <li>
              <label class="fs-field-label fs-anim-upper" for="siteTitle" data-info="What site title you want to use for your application">Business Name?</label>
              <input class="fs-anim-lower" id="siteTitle" name="siteTitle" type="text" placeholder="PHPTRAVELS" value="" required/>
            </li>
            <li>
              <label class="fs-field-label fs-anim-upper" for="db_name" data-info="The name of the database you want to run PHPTRAVELS in">What's Database Name?</label>
              <input class="fs-anim-lower" id="db_name" name="db_name" type="text" placeholder="PHPTRAVELS" required/>
            </li>
            <li>
              <label class="fs-field-label fs-anim-upper" for="db_username" data-info="Your MySQL username">What's Database Username?</label>
              <input class="fs-anim-lower" id="db_username" name="db_username" type="text" placeholder="username" required/>
            </li>
            <li>
              <label class="fs-field-label fs-anim-upper" for="db_password" data-info="Your MySQL password">What's Database Password?</label>
              <input class="fs-anim-lower" id="db_password" name="db_password" type="text" placeholder="password"/>
            </li>
            <li>
              <label class="fs-field-label fs-anim-upper" for="db_host" data-info="You should be able to get this info from your web host, if localhost does not work">What's Database Host?</label>
              <input class="fs-anim-lower" id="db_host" name="db_host" type="text" placeholder="localhost" value="localhost" required/>
            </li>
            <li>
              <label class="fs-field-label fs-anim-upper" for="admin_email" data-info="Double-check your email address before continuing.">Admin E-Mail</label>
              <input class="fs-anim-lower" id="admin_email" name="admin_email" type="email" placeholder="me@mail.com" value="admin@phptravels.com" required/>
            </li>
            <li>
              <label class="fs-field-label fs-anim-upper" for="admin_password" data-info=' The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers, and symbols like ! " ? $ % ^ & ).'>Admin Password</label>
              <input class="fs-anim-lower" id="admin_password" name="admin_password" type="text" placeholder="Admin password" value="adminpass" required/>
            </li>
          </ol>
          <button style="width: 100%;" class="fs-submit" name="submit" type="submit">Install Now</button>
        </form>
      </div>
    </div>
    <script src="./includes/assets/js/classie.js"></script>
    <script src="./includes/assets/js/fullscreenForm.js"></script>
    <script>
      (function() {
          var formWrap = document.getElementById( 'fs-form-wrap' );
          new FForm( formWrap, {
              onReview : function() {
              classie.add( document.body, 'overview' );
              }
          } );
      })();
    </script>
    <style>
    .fs-form-overview .fs-fields>li, .no-js .fs-form .fs-fields>li {
    margin: 0.5em 0 0em;
    padding: 0 0 0.5em 0;
    border-bottom: 1px solid rgba(0, 0, 0, .1);
    }
    .fs-form-overview .fs-fields::before {
    display: block;
    margin-bottom: 0.5em;
    }
    .fs-form-overview {
    padding: 1em 31% 4em;
    }
    .fs-form-overview {
    font-size: 1em;
    }
    .fs-fields>li label.fs-field-label {
    display: inline-block;
    padding: 0 5px 0em 0;
    font-weight: 700;
    pointer-events: none;
    }
    </style>
  </body>
</html>