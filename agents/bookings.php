
<?php
require_once '_config.php';
auth_check();

$title = T::bookings;
include "_header.php";

$agent_id = $USER_SESSION->backend_user_id;

$uri = explode('/', $_SERVER['REQUEST_URI']);
$root = ($_SERVER['HTTP_HOST'] == 'localhost') 
    ? (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . '/' . $uri[1]
    : (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'];

if (!empty($_GET['booking_edit']) && $_GET['booking_edit'] == "edit") {
    include_once "booking_update.php";
    die;
}
function compareByTimeStamp($time1, $time2) {
    return strtotime($time2['booking_date']) <=> strtotime($time1['booking_date']);
}

$perPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $perPage;
?>

<!-- <div class="mt-1"> -->
<div class="container mt-3">
<input type="text" id="searchInput" class="form-control mb-2" placeholder="Search...">
    <div class="table-responsive">
        <?php if (isset($_SESSION['booking_inserted'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <strong><?= T::success ?></strong> <?= T::booking_added_to_system ?>
            </div>
            <?php unset($_SESSION['booking_inserted']); endif; ?>

        <table class="table text-nowrap" id="bookingTable">
            <thead>
                <tr>
                    <th><?= T::booking ?> <?= T::date ?></th>
                    <th><?= T::customer ?> </th>
                    <th><?= T::hotel ?></th>
                    <th><?= T::check ?><?= T::in_out ?></th>
                    <th><?= T::sub ?><?= T::total ?></th>
                    <th><?= T::commission ?></th>
                    <th><?= T::booking ?> <?= T::status ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $hotel_data = $db->select("hotels_bookings", "*", ["agent_id" => $agent_id, "ORDER" => ["booking_id" => "DESC"]]);
                $flight_data = $db->select("flights_bookings", "*", ["ORDER" => ["booking_id" => "DESC"]]);
                $cars_data = $db->select("cars_bookings", "*", ["ORDER" => ["booking_id" => "DESC"]]);
                $tours_data = $db->select("tours_bookings", "*", ["ORDER" => ["booking_id" => "DESC"]]);
                $data = array_merge($hotel_data, $flight_data, $tours_data, $cars_data);
                usort($data, "compareByTimeStamp");

                // function for checkin and checkout format
                function formatDateRange($checkin, $checkout) {
                    $ci = new DateTime($checkin);
                    $co = new DateTime($checkout);
                    return ($ci->format('Y') !== $co->format('Y')) ?
                        $ci->format('M j') . ' - ' . $co->format('M j, Y') :
                        (($ci->format('M') !== $co->format('M')) ?
                        $ci->format('M j') . ' - ' . $co->format('M j, Y') :
                        $ci->format('M j-') . $co->format('j, Y'));
                }
                // function for checkin and checkout format
                
                $totalBookings = count($data);
                $data = array_slice($data, $start, $perPage);
                
                foreach ($data as $value) {
                    $userdata = json_decode($value['user_data']);
                    echo "<tr>";
                    echo "<td>" . date("d-m-Y", strtotime($value['booking_date'])) . "</td>";
                    echo "<td>{$userdata->first_name} {$userdata->last_name}</td>";
                    echo "<td>{$value['hotel_name']}</td>";
                    echo "<td>" . formatDateRange($value['checkin'], $value['checkout']) . "</td>";
                    echo "<td>{$value['subtotal']}</td>";
                    echo "<td>{$value['agent_fee']}</td>";
                    echo "<td><span class='badge bg-" . ($value['booking_status'] == 'confirmed' ? 'success' : ($value['booking_status'] == 'pending' ? 'warning' : 'danger')) . "'>{$value['booking_status']}</span></td>";
                    echo "</tr>";
                }
                ?>
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
</div>
<script>
    document.getElementById("searchInput").addEventListener("keyup", function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll("#bookingTable tbody tr");
        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
        });
    });
</script>

<?php include "_footer.php"; ?>