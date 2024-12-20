<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Affiliate Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <style>
        .nav-tabs .nav-link.active, .nav-pills .nav-link.active {
            background-color: #0d6efd;
            color: #fff !important;
        }
    </style>
</head>
<body class="bg-light">
<div class="container my-5">
    <!-- Top Cards Section -->
    <div class="row g-3">
        <!-- Estimated Commission (Donut Chart) -->
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="card-title">Estimated Commission</h5>
                    <div id="donutChart" class="my-4"></div>
                    <div class="d-flex justify-content-between mt-3">
                        <span class="text-muted">Completed trips</span>
                        <span class="fw-bold">USD 270.50</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Pending trips</span>
                        <span class="fw-bold">USD 280.00</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Clicks, Bookings, Commission Graph (Area Chart) -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <ul class="nav nav-tabs mb-3" id="chartTabs">
                        <li class="nav-item">
                            <a class="nav-link active" href="#" data-chart="clicks">Clicks</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-chart="bookings">Bookings</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-chart="commission">Commission</a>
                        </li>
                    </ul>
                    <div id="areaChart"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bookings Table Section -->
    <div class="card shadow-sm mt-4">
        <div class="card-body p-4">
            <h5 class="card-title">Bookings</h5>
            <p class="text-muted">Booking analytics for your links and content are available here. Data may be delayed by 1-2 business days.</p>
            <ul class="nav nav-pills mb-3" id="bookingsTabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#" data-status="all">Total Bookings (5)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-status="completed">Completed Trips (2)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-status="pending">Pending Trips (2)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-status="cancelled">Canceled Trips (1)</a>
                </li>
            </ul>
            <div class="table-responsive">
                <table class="table table-borderless" id="bookingsTable">
                    <thead>
                    <tr>
                        <th>Booked Date</th>
                        <th>Booked Product</th>
                        <th>Destination City</th>
                        <th>Brand</th>
                        <th>Affiliate Link</th>
                        <th>Estimated Commission (USD)</th>
                        <th>Trip Status</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr data-status="completed">
                        <td>2024-12-01</td>
                        <td>Hotel Booking</td>
                        <td>New York</td>
                        <td>Booking.com</td>
                        <td><a href="#">View Link</a></td>
                        <td>120.50</td>
                        <td>Completed</td>
                        <td>2024-12-05</td>
                        <td>2024-12-10</td>
                    </tr>
                    <tr data-status="pending">
                        <td>2024-12-02</td>
                        <td>Flight Ticket</td>
                        <td>Paris</td>
                        <td>Amadeus</td>
                        <td><a href="#">View Link</a></td>
                        <td>80.00</td>
                        <td>Pending</td>
                        <td>2024-12-15</td>
                        <td>2024-12-20</td>
                    </tr>
                    <tr data-status="completed">
                        <td>2024-12-03</td>
                        <td>Tour Package</td>
                        <td>Rome</td>
                        <td>Viator</td>
                        <td><a href="#">View Link</a></td>
                        <td>150.75</td>
                        <td>Completed</td>
                        <td>2024-12-12</td>
                        <td>2024-12-18</td>
                    </tr>
                    <tr data-status="cancelled">
                        <td>2024-12-04</td>
                        <td>Car Rental</td>
                        <td>Dubai</td>
                        <td>TBO</td>
                        <td><a href="#">View Link</a></td>
                        <td>65.00</td>
                        <td>Cancelled</td>
                        <td>2024-12-10</td>
                        <td>2024-12-15</td>
                    </tr>
                    <tr data-status="pending">
                        <td>2024-12-05</td>
                        <td>Hotel Booking</td>
                        <td>Tokyo</td>
                        <td>Agoda</td>
                        <td><a href="#">View Link</a></td>
                        <td>200.00</td>
                        <td>Pending</td>
                        <td>2024-12-20</td>
                        <td>2024-12-25</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <nav>
                <ul class="pagination justify-content-end">
                    <li class="page-item disabled">
                        <a class="page-link" href="#">Previous</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- ApexCharts Donut Chart -->
<script>
    var donutOptions = {
        series: [270.50, 280.00],
        chart: {type: 'donut', height: 300},
        labels: ['Completed Trips', 'Pending Trips'],
        responsive: [{breakpoint: 600, options: {legend: {position: 'bottom'}}}]
    };
    var donutChart = new ApexCharts(document.querySelector("#donutChart"), donutOptions);
    donutChart.render();
</script>

<!-- ApexCharts Area Chart -->
<script>
    // Initial chart data
    var chartData = {
        clicks: {
            name: "Clicks",
            data: [8107, 8128, 8122, 8165, 8340, 8423, 8423, 8514, 8481, 8487, 8506, 8626]
        },
        bookings: {
            name: "Bookings",
            data: [100, 120, 140, 200, 250, 300, 350, 400, 450, 500, 600, 650]
        },
        commission: {
            name: "Commission",
            data: [50, 60, 70, 100, 150, 200, 220, 240, 260, 300, 320, 350]
        }
    };

    var areaOptions = {
        series: [{
            name: chartData.clicks.name,
            data: chartData.clicks.data
        }],
        chart: {type: 'area', height: 350},
        xaxis: {categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]},
        stroke: {curve: 'smooth'}
    };
    var areaChart = new ApexCharts(document.querySelector("#areaChart"), areaOptions);
    areaChart.render();

    // Handle Chart Tabs
    document.querySelectorAll('#chartTabs .nav-link').forEach(function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelectorAll('#chartTabs .nav-link').forEach(link => link.classList.remove('active'));
            el.classList.add('active');
            var selectedChart = el.getAttribute('data-chart');
            areaChart.updateSeries([{
                name: chartData[selectedChart].name,
                data: chartData[selectedChart].data
            }]);
        });
    });
</script>

<!-- Bookings Tab Filter -->
<script>
    // Filter table rows based on status
    function filterTable(status) {
        const rows = document.querySelectorAll('#bookingsTable tbody tr');
        rows.forEach(row => {
            if (status === 'all') {
                row.style.display = '';
            } else {
                row.style.display = (row.getAttribute('data-status') === status) ? '' : 'none';
            }
        });
    }

    // Initial filter (all)
    filterTable('all');

    document.querySelectorAll('#bookingsTabs .nav-link').forEach(function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelectorAll('#bookingsTabs .nav-link').forEach(link => link.classList.remove('active'));
            el.classList.add('active');
            var status = el.getAttribute('data-status');
            filterTable(status);
        });
    });
</script>
</body>
</html>
