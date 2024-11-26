<?php 

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $_SESSION['verify'] = true;
} else {

?>

<script src="https://code.jquery.com/jquery-3.6.1.min.js" crossorigin="anonymous"></script>
<div style="display:flex;justify-content:center;align-items:center;height:100%">

    <div class="g-recaptcha" data-sitekey="6LdX3JoUAAAAAFCG5tm0MFJaCF3LKxUN4pVusJIF" data-callback="correctCaptcha"></div>
    <script src="https://www.google.com/recaptcha/api.js"></script>
    <script>
    var correctCaptcha = function(response) {  

        var settings = {
        "url": "./verify.php",
        "method": "POST",
        "timeout": 0,
        "processData": false,
        "contentType": false,
        "data": ""
        };

        $.ajax(settings).done(function (response) {

            window.location.href = "./";
        
        }); 

    };
    </script>

</div>

<?php } ?>