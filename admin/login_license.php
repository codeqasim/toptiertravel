<div class="py-3">

<?php if (isset($_SESSION['license_msg'])) { ?>
<div class="alert alert-danger" role="alert">
<?php
echo $_SESSION['license_msg'];
unset($_SESSION['license_msg']);
?>
</div>
<?php } ?>

<p>Add Your License Key: <a target="_blank" href="https://docs.phptravels.com/support/license">Documentation</a></p>

<form name="form" action="./login.php" method="post" onsubmit="submissions()">

<div class="form-floating">
<input type="text" class="form-control" id="license_key" name="license_key" placeholder="License">
<label for="">License Key</label>
</div>

<button id="submit" class="submit_btn mt-3 btn btn-primary w-100 btn-lg" type="submit" style="height:50px">Submit</button>
<div class="d-none loading_btn">
    <button class="btn btn-primary w-100 mt-3" type="button" disabled style="height:50px">
        <span class="spinner-border spinner-border-sm" role="status"
            aria-hidden="true"></span>
        Checking ...
    </button>
</div>

<input type="hidden" name="form_token" value="<?=$_SESSION["form_token"]?>">
<input type="hidden" name="license" value="">

</form>

</div>

<script>
    function submissions() {
        let license_key = $("#license_key").val();
        if (license_key == "") {
            event.preventDefault();
            alert("License Key is required to check");
        } else {
            document.querySelector('.submit_btn').classList.add('d-none');
            document.querySelector('.loading_btn').classList.remove('d-none');
        }
    }
</script>
