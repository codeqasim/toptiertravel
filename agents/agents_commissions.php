<?php 

use Medoo\Medoo;

require_once '_config.php';
auth_check();

$title = T::agent . " " . T::payments;
include "_header.php";

$agent_id = $USER_SESSION->backend_user_id;

$perPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $perPage;

$totalBookings = $db->count("hotels_bookings", ["agent_id" => $agent_id]);

$hotel_data = $db->select("hotels_bookings", "*", [
    "agent_id" => $agent_id,
    "ORDER" => ["booking_id" => "DESC"],
    "LIMIT" => [$start, $perPage]
]);

?>

<div class="container mt-3">
    <input type="text" id="searchInput" class="form-control mb-2" placeholder="Search...">
    <div class="table-responsive">
        <table class="table text-nowrap" id="commissiontable">
            <thead>
                <tr>
                    <th>Booking Date</th>
                    <th>Invoice</th>
                    <th>Customer</th>
                    <th>Hotel</th>
                    <th>Travel Dates</th>
                    <th>Subtotal</th>
                    <th>Commission</th>
                    <th>Payment Status</th>
                    <th>Payment Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($hotel_data as $booking): 
                    $userdata = json_decode($booking['user_data']);
                ?>
                <tr>
                    <td><?= $booking['booking_date'] ?></td>
                    <td><?= $booking['booking_ref_no'] ?? 'N/A' ?></td>
                    <td><?= $userdata->first_name . ' ' . $userdata->last_name ?></td>
                    <td><?= $booking['hotel_name'] ?></td>
                    <td><?= $booking['checkin'] . '___' . $booking['checkout'] ?></td>
                    <td><?= $booking['subtotal'] ?></td>
                    <td><?= $booking['agent_fee'] ?></td>
                    <td>
                        <span class="badge bg-<?= $booking['agent_payment_status'] == 'paid' ? 'success' : ($booking['agent_payment_status'] == 'pending' ? 'warning' : 'danger') ?>">
                            <?= $booking['agent_payment_status'] ?>
                        </span>
                    </td>
                    <td><?= $booking['payment_date'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <nav>
            <ul class="pagination">
                <?php for ($i = 1; $i <= ceil($totalBookings / $perPage); $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"> <?= $i ?> </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
</div>

<script>
    document.getElementById("searchInput").addEventListener("keyup", function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll("#commissiontable tbody tr");
        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
        });
    });
</script>

<?php include "_footer.php" ?> 
