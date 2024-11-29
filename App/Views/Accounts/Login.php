<section class="layout-pt-lg layout-pb-lg bg-blue-2">
  <div class="container">
    <div class="row justify-center">
      <div class="col-xl-6 col-lg-7 col-md-9">
        <div class="px-50 py-50 sm:px-20 sm:py-20 bg-white shadow-4 rounded-1">
          <div class="row y-gap-20">
            <div class="col-12">
              <h1 class="text-22 fw-500">Welcome back</h1>
              <p class="mt-10">Don't have an account yet? 
                <a href="<?=root?>signup" class="text-blue-1">Sign up for free</a>
              </p>
            </div>

            <div class="col-12">
              <form id="login" action="<?=root?>login" method="post">
                <div class="form-input mb-3">
                  <input name="email" type="email" class="form-control" id="email" placeholder="name@example.com" required>
                  <label class="lh-1 text-14 text-light-1" for="email"><?=T::email?> <?=T::address?></label>
                </div>

                <div class="form-input mb-3">
                  <input name="password" type="password" class="form-control" id="password" placeholder="******" required>
                  <label class="lh-1 text-14 text-light-1" for="password"><?=T::password?></label>
                </div>

                <div class="col-12">
                  <a href="#" class="text-14 fw-500 text-blue-1 underline" data-bs-toggle="modal" data-bs-target="#reset"><?=T::reset?> <?=T::password?></a>
                </div>

                <div class="col-12">
                  <button id="submitBTN" type="submit" class="button py-20 -dark-1 bg-blue-1 text-white w-100">
                    Sign In <div class="icon-arrow-top-right ml-15"></div>
                  </button>
                </div>
              </form>
            </div>
<!-- <div class="row y-gap-20 pt-30">
                            <div class="col-12">
                                <div class="text-center px-30">Or sign in with</div>
                                <button class="button col-12 -outline-blue-1 text-blue-1 py-15 rounded-8 mt-10">
                                    <i class="icon-apple text-15 mr-10"></i> Facebook
                                </button>
                                <button class="button col-12 -outline-red-1 text-red-1 py-15 rounded-8 mt-15">
                                    <i class="icon-apple text-15 mr-10"></i> Google
                                </button>
                                <button class="button col-12 -outline-dark-2 text-dark-2 py-15 rounded-8 mt-15">
                                    <i class="icon-apple text-15 mr-10"></i> Apple
                                </button>
                            </div>
                        </div> -->
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="reset" tabindex="1" aria-labelledby="modal" aria-modal="true" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-light"> 
          <h5 class="modal-title" id="modal"><?=T::reset?> <?=T::password?></h5>
          <button type="button" class="btn-close waves-effect" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="./" id="forget_pass">
          <div class="modal-body">
            <div class="form-floating mb-3">
              <input  type="email" class="form-control bg-light" id="reset_mail" placeholder="name@example.com" required>
              <label for="reset_mail"><?=T::email?> <?=T::address?></label>
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
</section>

<script>
 
  $("#login").submit(function() {
      $('.login_button').hide();
      $('.loading_button').show();
  });

  $("#forget_pass").submit(function(event) {
      event.preventDefault();

      $('.submit_buttons').hide();
      $('.loading_buttons').show();

      var mail = $('#reset_mail').val();
      if (mail == "") {
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

              if (response.status == true) {
                  alert('Your password has been reset, please check your mailbox');
                  $('.submit_buttons').show();
                  $('.loading_buttons').hide();
                  $("#reset").modal('hide');
              }

              if (response.status == false) {
                  alert('Invalid or no account found with this email');
                  $('.submit_buttons').show();
                  $('.loading_buttons').hide();
                  $("#reset").modal('hide');
              }

              if (response.message == "not_activated") {
                  alert('Your account is not activated, please contact us for activation');
                  $('.submit_buttons').show();
                  $('.loading_buttons').hide();
                  $("#reset").modal('hide');
              }
          });
      }
  });
</script>

<style>
  #twitterButton, #instagramLoginButton {
      background-color: rgb(26, 115, 232);
      width: 400px;
  }

  #twitterButton:hover, #instagramLoginButton:hover {
      background-color: #1a73e8;
  }
</style>
