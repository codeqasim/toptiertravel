<div class="py-5 pt-2">
    <form id="signup" action="<?=root?>signup" method="post" class="mb-5">
        <div class="container">
            <div class="card card-style mt-5 col-md-5 mx-auto rounded-4">
            <div class="content mb-0 p-3 p-md-4">
            <h3><strong><?=T::signup?></strong></h3>
                    <p class="mb-4"></p>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="firstname" placeholder=" " name="first_name"
                            required>
                        <label for="firstname"><?=T::first_name?></label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="last_name" placeholder=" " name="last_name"
                            required>
                        <label for="last_name"><?=T::last_name?></label>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3">
                                <select name="phone_country_code" class="selectpicker w-100" data-live-search="true"
                                    required>
                                    <option value=""><?=T::select?> <?=T::country?></option>
                                    <?php foreach($meta['countries'] as $c) { ?>
                                    <option value="<?=$c->id?>"
                                        data-content="<img class='' src='./admin/assets/img/flags/<?=strtolower($c->iso)?>.svg' style='width: 20px; margin-right: 14px;color:#fff'><span class='text-dark'> <?=$c->nicename?> <strong>+<?=$c->phonecode?></strong></span>">
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-floating mb-3">
                                <input type="numbers" class="form-control" id="phone" placeholder=" " name="phone"
                                    required>
                                <label for="phone"><?=T::phone?></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="user_email" placeholder=" " name="user_email" required>
                        <label for="email"><?=T::email?> <?=T::address?></label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password" placeholder=" " name="password"
                            required>
                        <label for="password"><?=T::password?></label>
                    </div>


                    <div class="g-recaptcha" data-sitekey="6LdX3JoUAAAAAFCG5tm0MFJaCF3LKxUN4pVusJIF" data-callback="correctCaptcha"></div>
                    <script src="https://www.google.com/recaptcha/api.js"></script>
                    <script>
                    var correctCaptcha = function(response) {
                    $('#submitBTN').prop('disabled', false); };
                    </script>

                    <label class="form-check-label mt-2"><?=T::by_signup_i_agree_to_terms_and_policy?></label>

                    <hr>

                    <div class="btn-box pt-0 pb-2">
                        <div class="mt-3 row">
                            <div class="col-md-12">

                                <div class="signup_button">
                                    <button id="submitBTN" disabled style="height:44px" type="submit"
                                        class="btn-lg d-flex align-items-center justify-content-center btn btn-primary w-100"></span><?=T::signup?></span>
                                    </button>
                                </div>

                                <div class="loading_button" style="display:none">
                                    <button style="height:44px"
                                        class="loading_button gap-2 w-100 btn btn-primary btn-m rounded-sm font-700 text-uppercase btn-full"
                                        type="button" disabled>
                                        <span class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>
                                    </button>
                                </div>

                                <script>
                                $('.agree').click(function() {
                                    if ($(this).is(':checked')) {
                                        document.getElementById('submitBTN').disabled = false;
                                    } else {
                                        document.getElementById('submitBTN').disabled = true;
                                    }
                                });

                                $("#signup").submit(function() {
                                    $('.signup_button').hide();
                                    $('.loading_button').show();
                                })
                                </script>

                            </div>
                        </div>
            </div>
        </div>
        <input type="hidden" name="form_token" value="<?=$_SESSION["form_token"]?>">
    </form>
</div>
</div>
</div>