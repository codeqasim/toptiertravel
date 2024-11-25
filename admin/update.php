<?php

use Medoo\Medoo;
require_once '_config.php';
auth_check();

$title = "Updates";
include "_header.php";

?>

<div class="p-5">

<?php include "updated.php" ?>

</div>

<?php include "_footer.php" ?>