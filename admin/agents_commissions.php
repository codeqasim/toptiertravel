<?php 

use Medoo\Medoo;

require_once '_config.php';
auth_check();

$title = T::agent . " " . T::payments;
include "_header.php";

?>

<div class="page_head bg-transparent">
    <div class="panel-heading">
        <div class="float-start d-flex">
            <p class="m-0 page_title"><?= T::agent ?> <?= T::payments ?></p>

            <div class="ms-4">
                <a href="?agent_payment_status=all" class="btn btn-primary">All</a>
                <a href="?agent_payment_status=paid" class="btn btn-success">Paid</a>
                <a href="?agent_payment_status=unpaid" class="btn btn-danger">Unpaid</a>
            </div>
        </div>
        <div class="float-end">
            <a href="javascript:window.history.back();" data-toggle="tooltip" data-placement="top" title="Previous Page" class="loading_effect btn btn-warning"><?=T::back?></a>
        </div>
    </div>
</div>

<div class="container mt-3">

<?php 
    include('./xcrud/xcrud.php');
    $xcrud = Xcrud::get_instance();
    $xcrud->table('hotels_bookings');

    // Ensure 'subtotal' is included
    $xcrud->columns('agent_id,agent_payment_status,agent_payment_type,agent_fee,subtotal');
    $xcrud->fields('agent_id,agent_payment_status,agent_payment_type,agent_fee,subtotal');

    $xcrud->change_type('subtotal', 'hidden');

    $xcrud->column_callback('agent_fee','agent_fee_cal');

    $xcrud->relation('agent_id', 'users', 'user_id', array('first_name', 'last_name', 'email'));

    $xcrud->label('agent_id', 'Agent');
    $xcrud->label('agent_fee', 'Agent Commission');
    
    $xcrud->unset_title();
    $xcrud->unset_add();
    $xcrud->unset_edit(); 
    $xcrud->unset_remove(); 
    
    $xcrud->order_by('booking_id', 'desc');

    // Filtering Logic
    $filter = $_GET['agent_payment_status'] ?? 'all';
    if ($filter === 'paid') {
        $xcrud->where('agent_payment_status =', 'paid');
    } elseif ($filter === 'unpaid') {
        $xcrud->where('agent_payment_status =', 'unpaid');
    }
    
    echo $xcrud->render();
?>


</div>

<?php include "_footer.php" ?>
