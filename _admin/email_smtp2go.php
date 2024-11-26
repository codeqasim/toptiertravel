<form action="./email_settings.php" method="POST">
    <div class="">
        <div class="panel-body">
            <div class="tab-content form-horizontal">
                <div class="d-flex gap-3 mb-3">
                    Default
                    <div class="form-check form-switch">
                        <label class="form-check-label" for="module_1"></label>
                        <input <?php if($settings->default_email_service=="smtp2go"){echo"checked";}?> style="width: 40px; height: 20px;cursor:pointer" class="form-check-input" data-status="1" data-value="1" data-item="" id="checkedbox2" type="checkbox">
                    </div>
                </div>
                <p><strong><?=T::api_credentials?></strong></p>
                <p><?=T::signup_email_api?></p>
                <div class="row form-group mb-4 mt-4">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" placeholder="" name="email_api_key" value="<?=$settings->email_api_key?>" class="form-control" required>
                            <label for=""><?=T::api_key?></label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="text" placeholder="" name="email_sender_name" value="<?=$settings->email_sender_name?>" class="form-control" required>
                            <label for=""><?=T::sender_name?></label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="text" placeholder="" name="email_sender_email" value="<?=$settings->email_sender_email?>" class="form-control" required>
                            <label for=""><?=T::sender_email?></label>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
        </div>
        <input type="hidden" name="smtp" value="smtp2go">
        <div class="mt-3">
            <?php if (isset($permission_edit)){ ?>
                <button type="submit" class="btn btn-primary mdc-ripple-upgraded"> <?=T::submit?></button>
            <?php } ?>
        </div>
    </div>
</form>

<div class="card p-5 mt-3 bg-light">
    <p class="mb-4"><?=T::to_test_email?></p>
    <div class="row">
        <div class="col-md-3">
            <div class="form-floating">
                <input type="email" placeholder="" id="test_mail_ids" name="" value="" class="form-control" required>
                <label for=""><?=T::sender_email?></label>
            </div>
        </div>
    </div>
    <hr class="mt-4 col">
    <div class="mt-3">
        <?php if (isset($permission_edit)){ ?>
            <button type="button" class="test_mail btn btn-primary"> <?=T::send_test_email?></button>
        <?php } ?>
        <button class="btn btn-primary loading_button" type="button" disabled style="display:none">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <?=T::email_test_email?>
        </button>
        <div style="display:none" class="mt-4 alert alert-secondary">
            <p id="results"></p>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#checkedbox2').change(function() {
            var isChecked = $(this).prop('checked');
            var value = isChecked ? 1 : 0; // Convert boolean to integer

            // Make a POST request to a certain endpoint
            $.ajax({
                url: 'email_settings.php',
                type: 'POST',
                data: { value: value, smtp_service: 'smtp2go' },
                success: function(response) {
                    console.log(response);
                }
            });
            setTimeout(function() {
                location.reload();
            }, 1000); // 1000 milliseconds = 1 second
        });

        $('.test_mail').on('click', function(e) {
            e.preventDefault();

            var test_mail_id = $("#test_mail_ids").val();

            if (test_mail_id == "") {
                alert('<?=T::test_mail_msg_1?> '+test_mail_id);
                window.location.reload();
            } else {
                $(".loading_button").show();
                $(".test_mail").hide();

                var form = new FormData();
                form.append("ajaxemail", "");
                form.append("title", "Test Email");
                form.append("template", "test_email");
                form.append("email", test_mail_id);
                form.append("first_name", "Test Success");
                form.append("Content", "test");

                var settings = {
                    "url": "./_post.php",
                    "method": "POST",
                    "timeout": 0,
                    "processData": false,
                    "mimeType": "multipart/form-data",
                    "contentType": false,
                    "data": form
                };

                $.ajax(settings).done(function(response) {
                    $('.alert').fadeIn(250);
                    $('#results').text(response);
                    console.log(response);

                    alert('<?=T::test_mail_msg_2?>');
                    $(".loading_button").hide();
                    $(".test_mail").show();
                });
            }
        });
    });
</script>
