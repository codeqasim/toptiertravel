<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Google fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">

  <!-- Stylesheets -->
  <!-- <link rel="stylesheet" href="css/vendors.css"> -->
  <!-- <link rel="stylesheet" href="css/main.css"> -->

  <!-- DataTables -->
  <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-1.13.8/sl-1.7.0/datatables.min.css" rel="stylesheet">

</head>


  <div class="header-margin"></div>
  <div class="dashboard" data-x="dashboard" data-x-toggle="-is-sidebar-open">
    <div class="dashboard__sidebar bg-white scroll-bar-1">
      <div class="sidebar -dashboard">
        <?php require "Sidebar.php"; ?>
      </div>
    </div>

    <div class="dashboard__main">
      <div class="m-2">
        <!-- Booking Table -->
        <div class="bg-white border rounded-2 p-4">
          <h3 class="text-20 lh-14 fw-600 mb-3">Bookings</h3>
          <div class="table-responsive " style="overflow-x: auto;">
            <table class="table table-striped table-bordered align-middle text-capitalized" style="width:1300px">
              <thead>
                <tr>
                  <th>Client Name</th>
                  <th>Booking ID</th>
                  <th>PNR</th>
                  <th>Payment Status</th>
                  <th>Booking Status</th>
                  <th>Type</th>
                  <th>Date</th>
                  <th>Price</th>
                  <?php if (isset($_SESSION['phptravels_client']) && $_SESSION['phptravels_client']->user_type === "Agent") { ?>
                    <th>Commission</th>
                  <?php } ?>
                  <th>Action</th>
                </tr>
                <tr class="filters">
                  <th><input type="search" class="form-control border" placeholder="Search Name" /></th>
                  <th><input type="search" class="form-control border" placeholder="Search Booking ID" /></th>
                  <th><input type="search" class="form-control border" placeholder="Search by PNR" /></th>
                  <th><select class="form-select"><option value="">All</option></select></th>
                  <th><select class="form-select"><option value="">All</option></select></th>
                  <th><select class="form-select"><option value="">All</option></select></th>
                  <th><input type="search" class="form-control border" placeholder="Search by Date" /></th>
                  <th><input type="search" class="form-control border" placeholder="Search by Price" /></th>
                  <?php if (isset($_SESSION['phptravels_client']) && $_SESSION['phptravels_client']->user_type === "Agent") { ?>
                    <th><input type="search" class="form-control border" placeholder="Commission" /></th>
                  <?php } ?>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php if (!is_null($booking_array) && !empty($booking_array)) {
                  foreach ($booking_array as &$ma) $tmp[] = &$ma->booking_ref_no;
                  array_multisort($tmp, SORT_DESC, $booking_array);
                  foreach (($booking_array) as $i => $booking) { ?>
                    <tr>
                      <td><strong><?= (json_decode($booking->guest)[0]->first_name) ?> <?= (json_decode($booking->guest)[0]->last_name) ?></strong></td>
                      <td><?= $booking->booking_ref_no ?></td>
                      <td><?= $booking->pnr ?></td>
                      <td><?= $booking->payment_status ?></td>
                      <td><?= $booking->booking_status ?></td>
                      <td><?= $booking->module_type ?></td>
                      <td><?= (new DateTime($booking->booking_date))->format("M d Y"); ?></td>
                      <td><strong><?= $booking->currency_markup ?> <?= $booking->price_markup ?></strong></td>
                      <?php if (isset($_SESSION['phptravels_client']) && $_SESSION['phptravels_client']->user_type === "Agent") { ?>
                        <td><?= !empty($booking->agent_fee) ? $booking->currency_markup . " " . $booking->agent_fee : "" ?></td>
                      <?php } ?>
                      <td>
                        <a href="<?= root . $booking->module_type ?>/invoice/<?= $booking->booking_ref_no ?>" target="_blank" class="btn btn-dark text-white">
                          Invoice
                        </a>
                      </td>
                    </tr>
                  <?php }
                } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAAz77U5XQuEME6TpftaMdX0bBelQxXRlM"></script>
  <script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>
  <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-1.13.8/sl-1.7.0/datatables.min.js"></script>
  <script>
    $(document).ready(function () {
      var table = $('table').DataTable({
        "ordering": false // Disable sorting
      });

      table.columns([0, 1, 2, 3, 6, 7, 8]).every(function () {
        var that = this;
        $('input', this.header()).on('keyup change', function () {
          if (that.search() !== this.value) {
            that.search(this.value).draw();
          }
        });
      });

      table.columns([3, 4, 5]).every(function () {
        var that = this;
        var column = this;

        var select = $('select', this.header());
        column.data().unique().sort().each(function (d, j) {
          select.append('<option value="' + d + '">' + d + '</option>');
        });

        select.on('change', function () {
          var val = $.fn.dataTable.util.escapeRegex($(this).val());
          that.search(val ? '^' + val + '$' : '', true, false).draw();
        });
      });
    });
  </script>
      <style>
    table{ width:100%; }
    #example_filter{ float:right; }
    #example_paginate{ float:right; }
    label { display: inline-flex; margin-bottom: .5rem; margin-top: .5rem; }
    .dataTables_filter input { margin-left: 10px; }
    table { vertical-align: middle !important; }
    .dataTables_filter { display: flex; justify-content: end; align-items: center; }
    .dataTables_filter input {border:1px solid black;}
    .paging_simple_numbers { display: flex; justify-content: end; align-items: center; }
    tr:hover{background-color: rgba(128,137,150,0.1);}
    .newsletter-section {display:none}
    .pagination { color: #fff !important }
    thead input { width: 100%;}
    /* table th:last-child input { display: none; } */
    .page-item a {color: #fff !important}
    .disabled>.page-link, .page-link.disabled {background : rgba(33, 37, 41, 0.75) !important }
    </style>

</body>

</html>
