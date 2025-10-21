<?php
require_once '../vendor/autoload.php';
require_once '_config.php';

auth_check();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Handle Pusher toggle
    if (isset($_POST['pusher_service'])) {
        if ($_POST['pusher_service'] == "pusher" && $_POST['value'] == 1) {
            UPDATE('settings', ['default_pusher_service' => $_POST['pusher_service']], 1);
        }

        if ($_POST['pusher_service'] == "pusher" && $_POST['value'] == 0) {
            UPDATE('settings', ['default_pusher_service' => 0], 1);
        }
    }

    // Save Pusher credentials
    if ($_POST['pusher'] == "pusher") {
        $params = array(
            'pusher_app_id' => $_POST['pusher_app_id'],
            'pusher_key' => $_POST['pusher_key'],
            'pusher_secret' => $_POST['pusher_secret'],
            'pusher_cluster' => $_POST['pusher_cluster'],
        );

        $id = 1;
        UPDATE('settings', $params, $id);

        // Log action
        $user_id = $USER_SESSION->backend_user_id;
        $log_type = "pusher_settings";
        $datetime = date("Y-m-d h:i:sa");
        $desc = "updated pusher settings";
        logs($user_id, $log_type, $datetime, $desc . nl2br("\n") . json_encode($_REQUEST));

        ALERT_MSG('updated');
        REDIRECT("pusher_settings.php");
    }
}

// Page setup
$title = T::pusher_settings;
include "_header.php";

// Fetch settings
$params = array();
$settings = GET('settings', $params)[0];
?>

<div class="page_head">
    <div class="panel-heading">
        <div class="float-start">
            <p class="m-0 page_title"><?= T::pusher_settings ?></p>
        </div>
        <div class="float-end">
            <a href="javascript:window.history.back();" class="btn btn-warning"><?= T::back ?></a>
        </div>
    </div>
</div>

<div class="container">
    <div class="py-4">

        <form action="./pusher_settings.php" method="POST">
            <div class="panel-body">
                <div class="tab-content form-horizontal">

                    <p class="mt-3">Add your Pusher credentials below</p>

                    <div class="row form-group mb-4 mt-4 g-2">

                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" placeholder="" name="pusher_app_id"
                                    value="<?= htmlspecialchars($settings->pusher_app_id ?? '') ?>" class="form-control" required>
                                <label for="">Pusher App ID</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" placeholder="" name="pusher_key"
                                    value="<?= htmlspecialchars($settings->pusher_key ?? '') ?>" class="form-control" required>
                                <label for="">Pusher Key</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" placeholder="" name="pusher_secret"
                                    value="<?= htmlspecialchars($settings->pusher_secret ?? '') ?>" class="form-control" required>
                                <label for="">Pusher Secret</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" placeholder="" name="pusher_cluster"
                                    value="<?= htmlspecialchars($settings->pusher_cluster ?? '') ?>" class="form-control" required>
                                <label for="">Cluster</label>
                            </div>
                        </div>

                    </div>

                    <hr>

                </div>
            </div>
            <input type="hidden" name="pusher" value="pusher">
            <div class="mt-3">
                <?php if (isset($permission_edit)) { ?>
                    <button type="submit" class="btn btn-primary"><?= T::submit ?></button>
                <?php } ?>
            </div>
        </form>
    </div>
</div>

<?php include "_footer.php"; ?>
