<?php 

use Medoo\Medoo;

require_once '_config.php';
auth_check();

$title = T::suppliers." ".T::payment." ".T::status;
include "_header.php";

?>

<div class="page_head bg-transparent">
<div class="panel-heading">
<div class="float-start">
<p class="m-0 page_title"><?=T::suppliers?> <?=T::payment?>  <?=T::status?></p>
</div>
<div class="float-end">
<!-- <a href="javascript:window.history.back();" data-toggle="tooltip" data-placement="top" title="Previous Page" class="loading_effect btn btn-warning"><?=T::back?></a> -->
</div>
</div>
</div>

<div class="container mt-3">

<?php 
include('./xcrud/xcrud.php');
$xcrud = Xcrud::get_instance();
$xcrud->table('hotels_bookings');
$xcrud->columns('supplier_id,supplier_payment_status,supplier_cost,supplier_due_date');
$xcrud->fields('supplier_id,supplier_payment_status,supplier_cost,supplier_due_date');

$xcrud->relation('supplier_id','users','user_id',array('first_name','last_name','email'));

$xcrud->label('supplier_id','supplier');

$xcrud->unset_title();
$xcrud->unset_add();
// $xcrud->unset_csv();
$xcrud->unset_edit(); 
echo $xcrud->render();

?>

<?php include "_footer.php" ?>