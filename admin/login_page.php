
<div class="text-center">
    <img class="mb-3 rounded-3" src="../uploads/global/favicon.png" alt="favicon" style="height: 48px">
    <!-- <p class="mb-0"><strong>Administrators Login</strong></p> -->
    </div>

    <!-- Login submission form-->
    <form name="form" action="./login.php" method="post" onsubmit="submission()">
        <div class="mb-2 mt-3">
            <div class="form-floating">
                <input type="email" class="form-control" id="email" name="email"
                    placeholder="Email">
                <label for="">Email</label>
            </div>
        </div>
        <div class="mb-2">
            <div class="form-floating">
                <input type="password" class="form-control" id="password" name="password"
                    placeholder="Password">
                <label for="">Password</label>
            </div>
        </div>

        <select title="Select your language btn btn-primary" class="w-100 selectpicker"
            name="user_language" data-size="5">
            <?php foreach($languages as $lang){
            if ($lang->status == 1){
            ?>
            <option selected value="<?=strtolower($lang->country_id)?>_<?=strtolower($lang->type)?>" data-content="<img class='' src='./assets/img/flags/<?=strtolower($lang->country_id)?>.svg' style='width: 20px; margin-right: 14px;color:#fff'><span style='font-weight: 400; font-size: 14px;' class='text-dark'> <?=$lang->name?></span>">
            </option>
            <?php } } ?>
        </select>
        <script>
            $("[name='user_language']").val("us_ltr")
        </script>
        <div class="d-flex align-items-center justify-content-between mt-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked"
                    checked>
                <label class="form-check-label" for="flexCheckChecked">
                    <small>Remember Me</small>
                </label>
            </div>
            <div class="form-group d-flex align-items-center justify-content-betweenmb-0">
                <a class="small fw-500 text-decoration-none" href="login-forget-password.php">Forgot
                    Password?</a>
            </div>
        </div>
        <button id="submit" class="login_button mt-3 btn btn-primary w-100 btn-lg" type="submit"
            style="height:50px">Login</button>
        <div class="d-none">
            <button class="btn btn-primary w-100 mt-3" type="button" disabled style="height:50px">
                <span class="spinner-border spinner-border-sm" role="status"
                    aria-hidden="true"></span>
                Loading...
            </button>
        </div>
        <input type="hidden" name="form_token" value="<?=$_SESSION["form_token"]?>">
    </form>
</div>