<div class="py-5">
    <form id="login" action="<?=root?>login" method="post" class="mb-5">
        <div class="container">
            <div class="card card-style mt-5 col-md-5 mx-auto rounded-4">
                <div class="content mb-0 p-3 p-md-4">
                    <h3><strong><?=T::login?></strong></h3>
                    <p></p>
                    <div class="form-floating mb-3">
                        <input name="email" type="email" class="form-control" id="email" placeholder="name@example.com">
                        <label for="email"><?=T::email?> <?=T::address?></label>
                    </div>
                    <div class="form-floating mb-3">
                        <input name="password" type="password" class="form-control" id="password" placeholder="******">
                        <label for="password"><?=T::password?></label>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="custom-checkbox mb-0 d-flex align-items-center gap-3">
                            <input class="form-check-input m-0" type="checkbox" id="rememberchb">
                            <label for="rememberchb"><?=T::rememberme?></label>
                        </div>
                        <div class="custom-checkbox mb-0">
                            <label style="cursor:pointer" for="" data-bs-toggle="modal" data-bs-target="#reset"><?=T::reset?>
                                <?=T::password?></label>
                        </div>
                    </div>
                    <div class="btn-box pt-0 pb-2">
                        <div class="login_button">
                            <button id="submitBTN" style="height:44px" type="submit" class="btn btn-dark w-100"><span
                                    class=""><?=T::login?></span></button>
                        </div>
                        <div class="loading_button" style="display:none">
                            <button style="height:44px"
                                class="loading_button gap-2 w-100 btn btn-dark btn-m rounded-sm font-700 text-uppercase btn-full"
                                type="button" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </button>
                            <script>
                            $("#login").submit(function() {
                                $('.login_button').hide();
                                $('.loading_button').show();
                            })
                            </script>
                        </div>
                        <div class="mt-3 row">
                            <div class="col-md-12"><a
                                    class="d-flex align-items-center justify-content-center btn btn-outline-primary"
                                    style="height:44px" href="<?=root?>signup"><span
                                        class="ladda-label"><span></span><?=T::signup?></span></a></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
</div>
<input type="hidden" name="form_token" value="<?=$_SESSION["form_token"]?>">
</form>
<div class="modal fade" id="reset" tabindex="1" aria-labelledby="modal" aria-modal="true" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="modal"><?=T::reset?> <?=T::password?></h5>
                <button type="button" class="btn-close waves-effect" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form method="POST" action="./" id="forget_pass">
                <div class="modal-body">
                    <div class="input-group">
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="reset_mail" placeholder="name@example.com">
                            <label for="email"><?=T::email?> <?=T::address?></label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                <button style="height:44px" type="button" class="btn btn-outline-primary btn-sm" data-bs-dismiss="modal"><?=T::cancel?></button>
                <button style="height:44px" type="submit" class="submit_buttons btn btn-primary btn-sm"><span><?=T::reset?> <?=T::email?></span></button>
                <button style="height:44px;width:108px;display:none"
                class="loading_buttons gap-2 btn btn-primary btn-m rounded-sm"
                type="button" disabled>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<style>
    #twitterButton {
        background-color: rgb(26, 115, 232);
        width: 400px;
    }

    #twitterButton:hover {
        background-color: #1a73e8;
    }

    #instagramLoginButton {
        background-color: rgb(26, 115, 232);
        width: 400px;
    }

    #instagramLoginButton:hover {
        background-color: #1a73e8;
    }

</style>
<script>
$("#forget_pass").submit(function() {
    event.preventDefault();

    $('.submit_buttons').hide();
    $('.loading_buttons').show();

    var mail = $('#reset_mail').val();
    if (mail == ""){
        alert('Please add email address to reset password');
        $('.submit_buttons').show();
        $('.loading_buttons').hide();
    } else {

        var settings = {
        "url": "<?=root?>api/forget_password",
        "method": "POST",
        "timeout": 0,
        "headers": {
        "Content-Type": "application/x-www-form-urlencoded",
        },
        "data": {
        "email": mail
        }
        };

        $.ajax(settings).done(function(response) {

        console.log(response);

        if (response.status == true){
            alert('Your password has been reset please check your mailbox')
            $('.submit_buttons').show();
            $('.loading_buttons').hide();
            $("#reset").modal('hide');
        }

        if (response.status == false){
            alert('Invalid or no account found with this email')
            $('.submit_buttons').show();
            $('.loading_buttons').hide();
            $("#reset").modal('hide');
        }

        if (response.message == "not_activated"){
            alert('Your account is not activated please contact us for activation')
            $('.submit_buttons').show();
            $('.loading_buttons').hide();
            $("#reset").modal('hide');
        }

        });


    }
});

// $("#instagramLoginButton").click(function() {
//             $("#emailModal").modal("show");
//         });

//  $("#confirmEmailBtn").on("click", function() {

//         var instagram_consumer_key = "<?php //echo instagram_consumer_key; ?>";
//         // for local side work
//         // var instagram_redirect_url = "https://localhost/v9/Social_Login";
//         // for server side work
//         var instagram_redirect_url = "<?php //echo instagram_redirect_url; ?>";
//         // console.log(instagram_redirect_url);
//         var scope = "user_profile,user_media";
//         var instagramAuthUrl = `https://api.instagram.com/oauth/authorize/?client_id=${instagram_consumer_key}&redirect_uri=${instagram_redirect_url}&response_type=code&scope=${scope}`;

//         // Redirect to Instagram authentication
//             var insta_email = document.getElementById("emailInput").value;
//             if (insta_email) {
//                 // Store the email in session storage
//                 sessionStorage.setItem("insta_email", insta_email);
//                 // console.log('insta_email', insta_email);
//                 // Redirect to the next page after storing email in session storage
//                 window.location.href = instagramAuthUrl;
//                 // console.log( window.location.href = instagramAuthUrl);
//             } else {
//                 alert("Please enter a valid email address.");
//             }
//         });

</script>