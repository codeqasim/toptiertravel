<form action="./email_settings.php" method="POST">
            <div class="">

                <div class="panel-body">
                    <div class="tab-content form-horizontal">

                    <div class="d-flex gap-3">
                        Default

                        <div class="form-check form-switch">
                        <label class="form-check-label" for="module_1"></label>
                        <input <?php if($settings->default_email_service=="smtp"){echo"checked";}?> style="width: 40px; height: 20px;cursor:pointer" class="form-check-input" data-status="1" data-value="1" data-item="" id="checkedbox" type="checkbox">
                        </div>

                        <script>
                            $(document).ready(function() {
                            $('#checkedbox').change(function() {
                                var isChecked = $(this).prop('checked');
                                var value = isChecked ? 1 : 0; // Convert boolean to integer

                                // Make a POST request to a certain endpoint
                                $.ajax({
                                    url: 'email_settings.php',
                                    type: 'POST',
                                    // contentType: 'application/json',
                                    data: { value: value, smtp_service: 'smtp' },
                                    success: function(response) {
                                        console.log(response);
                                    },

                                });
                                setTimeout(function() {
                                location.reload();
                                }, 1000); // 3000 milliseconds = 3 seconds

                            });
                        });

                        </script>

                    </div>

                        <p class="mt-3">Add your SMTP credentials below</p>

                        <div class="row form-group mb-4 mt-4 g-2">

                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" placeholder="" name="smtp_host"
                                        value="<?=$settings->smtp_host?>" id="hostname" class="form-control" required>
                                    <label for="">SMTP Host</label>
                                </div>
                            </div>

                            <div class="col-md-1">
                                <div class="form-floating">
                                    <input type="text" placeholder="" name="smtp_port"
                                        value="<?=$settings->smtp_port?>" id="port" class="form-control" required>
                                    <label for="">Port</label>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-floating">
                                    <input type="text" placeholder="" name="smtp_username"
                                        value="<?=$settings->smtp_username?>" id="username" class="form-control" required>
                                    <label for="">Username</label>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-floating">
                                    <input type="text" placeholder="" name="smtp_password"
                                        value="<?=$settings->smtp_password?>" id="password" passwod="password" class="form-control" required>
                                    <label for="">Password</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-floating">
                                <select class="form-select" id="security" name="smtp_security">
                                    <option value="tls">TLS</option>
                                    <option value="ssl">SSL</option>
                                    <option value="">None</option>
                                </select>
                                <label for="">Security</label>
                                </div>
                            </div>

                            <script>
                            $("[name='smtp_security']").val("<?=$settings->smtp_security?>")
                            </script>

                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" placeholder="" name="smtp_sendername"
                                        value="<?=$settings->smtp_sendername?>" class="form-control" required>
                                    <label for=""><?=T::sender_name?></label>
                                </div>
                            </div>

                        </div>

                        <hr>

                    </div>
                </div>
                <input type="hidden" name="smtp" value="smtp">
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
            <div class="col-md-6">

                <div class="form-floating d-none">
                    <input type="email" placeholder="" id="from_email" name="" value="phptravelsmail@gmail.com" class="form-control" required>
                    <label for="">From Email</label>
                </div>

                <div class="form-floating mt-2">
                    <input type="email" placeholder="" id="to_email" name="" value="" class="form-control" required>
                    <label for="">To Email</label>
                </div>

            </div>
        </div>

        <hr class="mt-4 col">
        <div class="mt-3">
        <?php if (isset($permission_edit)){ ?>
            <button type="submit" class="test_mails btn btn-primary"> <?=T::send_test_email?></button>
        <?php } ?>

            <button class="btn btn-primary loading_button" type="button" disabled style="display:none">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <?=T::email_test_email?>
            </button>

            <div style="display:none" class="mt-4 alert alert-secondary">
            <p id="result"></p>
            </div>

        </div>
    </div>


<script>

        $('.test_mails').on('click', function(e) {
            e.preventDefault();

            var test_mail_id = $("#test_mail_id").val();

            if (test_mail_id == "") {
                alert('<?=T::test_mail_msg_1?> '+test_mail_id);
                window.location.reload();
            } else {
                $(".loading_button").show();
                $(".test_mail").hide();

                var form = new FormData();
                form.append("from_email", $("#from_email").val());
                form.append("to_email", $("#to_email").val());
                form.append("email", test_mail_id);
                form.append("hostname", $("#hostname").val());
                form.append("username", $("#username").val());
                form.append("password", $("#password").val());
                form.append("port", $("#port").val());
                form.append("security", $("#security").val());

                var settings = {
                    "url": "./email_smtp_test.php",
                    "method": "POST",
                    "timeout": 0,
                    "processData": false,
                    "mimeType": "multipart/form-data",
                    "contentType": false,
                    "data": form
                };

                $.ajax(settings).done(function(response) {
                    $('.alert').fadeIn(250);
                    $('#result').text(response);
                    console.log(response);

                    alert('<?=T::test_mail_msg_2?>');
                    $(".loading_button").hide();
                    $(".test_mail").show();
                });
            }
        });

</script>