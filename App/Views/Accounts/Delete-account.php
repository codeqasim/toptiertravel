<div class="container">
    <div class="row g-2 justify-content-center mt-5">
        <div class="col-12 col-md-9 col-lg-6">
         <div class="card">
            <div class="card-header py-3">
                <h5 class="fw-bold text-center mb-0"><?= T::user ." ".T::delete." ".T::account  ?></h5>
            </div>
            <div class="card-body p-2 p-sm-4">
                <form id="contact-form" action="" method="post">
                    <div class="form-floating mb-3 mt-3">
                        <input type="text" class="form-control" id="<?= T::email ?>" name="email" placeholder="" value="" required="">
                        <label for="<?= T::email ?>"><?= T::email ?></label>
                    </div>
                    <div class="overflow-hidden g-recaptcha" data-sitekey="6LdX3JoUAAAAAFCG5tm0MFJaCF3LKxUN4pVusJIF" data-callback="correctCaptcha"
                    data-expired-callback="expiredRecaptchaCallback"></div>
                    <div class="text-center mt-3">
                        <button type="submit" disabled class="sub--btn btn btn-primary px-5 py-2">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src='https://www.google.com/recaptcha/api.js' defer></script>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script>
    $(function () {
        correctCaptcha = function (response) {
            console.log( response )
            $('input[data-recaptcha]').val(response).trigger('change');
            $('.sub--btn').removeAttr('disabled')
        }
        expiredRecaptchaCallback = function () {
            $('input[data-recaptcha]').val("").trigger('change')
            $('.sub--btn').attr('disabled', 'disabled')
        }

        $('#contact-form').on('submit', function (e) {
            if (!e.isDefaultPrevented()) {
                var url = "<?=root.api_url?>user_delete";
                console.log( $(this).serialize() )
                $.ajax({
                    type: "POST",
                    url: url,
                    data: $(this).serialize(),
                    success: function (response) {
                        (response.status) ? vt.error('User account deleted',{ title: response.message, position: "bottom-center" }) :
                        vt.error('Please use different email',{ title: response.message, position: "bottom-center" }); 
                    }
                });
                return false;
            }
        })
    });
</script>
<style>
    .g-recaptcha > div {
        width: fit-content !important;
        margin-inline: auto;
    }
</style>