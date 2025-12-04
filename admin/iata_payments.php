<?php 

use Medoo\Medoo;

require_once '_config.php';
auth_check();

$title = "IATA " . T::payments;
include "_header.php";

?>

<div class="page_head bg-transparent">
    <div class="panel-heading">
        <div class="float-start d-flex">
            <p class="m-0 page_title">IATA <?= T::payments ?></p>

            <div class="ms-4">
                <a href="?iata_payment_status=all" class="btn btn-primary">All</a>
                <a href="?iata_payment_status=paid" class="btn btn-success">Paid</a>
                <a href="?iata_payment_status=unpaid" class="btn btn-danger">Unpaid</a>
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
    $xcrud->columns('booking_ref_no,iata,iata_payment_status,iata_payment_date');
    $xcrud->fields('booking_ref_no,iata,iata_payment_status,iata_payment_date');

    $xcrud->label('booking_ref_no', 'Booking Reference');
    $xcrud->label('iata', 'IATA Amount');
    $xcrud->label('iata_payment_status', 'Payment Status');
    $xcrud->label('iata_payment_date', 'Payment Date');
    
    $xcrud->column_pattern('iata', '<strong>{value} USD</strong>');

    // Make booking_ref_no and iata read-only (not editable)
    $xcrud->change_type('booking_ref_no', 'label');
    $xcrud->change_type('iata', 'label');

    // Set payment status as dropdown with paid/unpaid options
    $xcrud->change_type('iata_payment_status', 'select', '', array(
        'paid' => 'Paid',
        'unpaid' => 'Unpaid'
    ));

    // Set payment date as date picker
    $xcrud->change_type('iata_payment_date', 'date');

    $xcrud->unset_title();
    $xcrud->unset_add();
    $xcrud->unset_remove();

    $xcrud->order_by('booking_id', 'desc');
    
    // Filter only records where iata is not null/empty
    $xcrud->where('iata !=', null);
    $xcrud->where('iata !=', '');
    $xcrud->where('iata !=', '0');
    
    $filter = $_GET['iata_payment_status'] ?? 'all';
    if ($filter === 'paid') {
        $xcrud->where('iata_payment_status', 'paid');
    } elseif ($filter === 'unpaid') {
        $xcrud->where('iata_payment_status', 'unpaid');
    }

    // Refresh page after update
    $xcrud->after_update('refresh');

    echo $xcrud->render();
    ?>

</div>

<?php include "_footer.php" ?>