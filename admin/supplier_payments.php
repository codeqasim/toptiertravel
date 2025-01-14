<?php 

use Medoo\Medoo;

require_once '_config.php';
auth_check();

$title = T::supplier." ".T::payments;
include "_header.php";

?>

<div class="page_head bg-transparent">
<div class="panel-heading">
<div class="float-start">
<p class="m-0 page_title"><?=T::supplier?> <?=T::payments?></p>
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
$xcrud->columns('supplier_id,supplier_cost,supplier_due_date,supplier_payment_status');
$xcrud->fields('supplier_id,supplier_payment_status,supplier_cost,supplier_due_date');


$xcrud->relation('supplier_id', 'users', 'user_id', array('first_name', 'last_name', 'email'));

$xcrud->label('supplier_id', 'Supplier');
$xcrud->label('supplier_cost', 'Supplier Amount');
$xcrud->label('email', 'Supplier Email');
$xcrud->column_pattern('supplier_cost','<strong>{value} USD</strong> ');

$xcrud->unset_title();
$xcrud->unset_add();
// $xcrud->unset_csv();
$xcrud->unset_edit(); 
echo $xcrud->render();

?>

<?php include "_footer.php" ?>