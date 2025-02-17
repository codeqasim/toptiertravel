<?php 

use Medoo\Medoo;

require_once '_config.php';
auth_check();

$title = T::agent . " " . T::payments;
include "_header.php";

?>

<div class="page_head bg-transparent">
    <div class="panel-heading">
        <div class="float-end">
            <a href="javascript:window.history.back();" data-toggle="tooltip" data-placement="top" title="Previous Page" class="loading_effect btn btn-warning"><?=T::back?></a>
        </div>
    </div>

    <!-- Added Form Inside Page Head -->
</div>


<div class="container mt-3">
<div class="">
        <form method="GET">
            <div class="row g-3 align-items-center">
                <div class="col-md-3">
                    <div class="form-floating">
                        <select class="form-select" id="agent_payment_status" name="agent_payment_status" required>
                            <option value="all" <?= ($_GET['agent_payment_status'] ?? 'all') == 'all' ? 'selected' : '' ?>><?= T::all ?></option>
                            <option value="pending" <?= ($_GET['agent_payment_status'] ?? '') == 'pending' ? 'selected' : '' ?>><?= T::pending ?></option>
                            <option value="paid" <?= ($_GET['agent_payment_status'] ?? '') == 'paid' ? 'selected' : '' ?>><?= T::paid ?></option>
                            <option value="cancelled" <?= ($_GET['agent_payment_status'] ?? '') == 'cancelled' ? 'selected' : '' ?>><?= T::cancelled ?></option>
                        </select>
                        <label for="agent_payment_status">Payment Status</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <select class="form-select" id="agent_payment_type" name="agent_payment_type" required>
                            <option value="all" <?= ($_GET['agent_payment_type'] ?? 'all') == 'all' ? 'selected' : '' ?>><?= T::all ?></option>
                            <option value="wire" <?= ($_GET['agent_payment_type'] ?? '') == 'wire' ? 'selected' : '' ?>><?= T::wire ?></option>
                            <option value="zelle" <?= ($_GET['agent_payment_type'] ?? '') == 'zelle' ? 'selected' : '' ?>><?= T::zelle ?></option>
                            <option value="paypal" <?= ($_GET['agent_payment_type'] ?? '') == 'paypal' ? 'selected' : '' ?>><?= T::paypal ?></option>
                            <option value="venmo" <?= ($_GET['agent_payment_type'] ?? '') == 'venmo' ? 'selected' : '' ?>><?= T::venmo ?></option>
                        </select>
                        <label for="agent_payment_type">Payment Type</label>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-center">
                    <button type="submit" class="btn btn-primary w-100" style="height: 55px;">Filter</button>
                </div>
            </div>
        </form>
    </div>
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
    $xcrud->unset_csv(); 
    $xcrud->unset_add();
    // $xcrud->unset_edit(); 
    $xcrud->unset_remove(); 
    
    $xcrud->order_by('booking_id', 'desc');


    $filter_status = $_GET['agent_payment_status'] ?? 'all';
    if ($filter_status !== 'all' && !empty($filter_status)) {
        $xcrud->where('agent_payment_status =', $filter_status);
    }

    $filter_type = $_GET['agent_payment_type'] ?? 'all';
    if ($filter_type !== 'all' && !empty($filter_type)) {
        $xcrud->where('agent_payment_type =', $filter_type);
    }

    echo $xcrud->render();
?>
</div>


<?php include "_footer.php" ?>
