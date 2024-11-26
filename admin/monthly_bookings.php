<?php
use Medoo\Medoo;

require_once '_config.php';
auth_check();
$title = "Monthly Bookings";
include "_header.php";
?>
<div class="page_head bg-transparent">
<div class="panel-heading">
<div class="float-start">
<p class="m-0 page_title"><?=$title?></p>
</div>
<div class="float-end">
<!-- <a href="javascript:window.history.back();" data-toggle="tooltip" data-placement="top" title="Previous Page" class="loading_effect btn btn-warning">  Back</a> -->
</div>
</div>
</div>
<?php
// GET THE FIRST AND LAST DAY OF THE CURRENT MONTH
$firstDayOfMonth = date('Y-m-01');
$lastDayOfMonth = date('Y-m-t');

// INITIALIZE MONTH DAYS
$monthDays = [];
$currentDate = $firstDayOfMonth;
while (strtotime($currentDate) <= strtotime($lastDayOfMonth)) {
    $monthDays[$currentDate] = [
        'Cars' => [
            'total' => 0, 'unpaid' => 0, 'paid' => 0, 'refunded' => 0, 'disputed' => 0, 'pending' => 0, 'confirmed' => 0, 'cancelled' => 0
        ],
        'Flights' => [
            'total' => 0, 'unpaid' => 0, 'paid' => 0, 'refunded' => 0, 'disputed' => 0, 'pending' => 0, 'confirmed' => 0, 'cancelled' => 0
        ],
        'Hotels' => [
            'total' => 0, 'unpaid' => 0, 'paid' => 0, 'refunded' => 0, 'disputed' => 0, 'pending' => 0, 'confirmed' => 0, 'cancelled' => 0
        ],
        'Tours' => [
            'total' => 0, 'unpaid' => 0, 'paid' => 0, 'refunded' => 0, 'disputed' => 0, 'pending' => 0, 'confirmed' => 0, 'cancelled' => 0
        ],
        'Visa' => [  // Add this part for Visa bookings
            'total' => 0, 'unpaid' => 0, 'paid' => 0, 'refunded' => 0, 'disputed' => 0, 'pending' => 0, 'confirmed' => 0, 'cancelled' => 0
        ],
    ];
    $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
}

// FETCH DATA FOR EACH DAY IN THE CURRENT MONTH
foreach ($monthDays as $date => &$counts) {
    foreach (['Cars', 'Flights', 'Hotels', 'Tours'] as $type) {
        $typeLower = strtolower($type);
        $counts[$type]['total'] = $db->count("{$typeLower}_bookings", ['booking_date' => $date]);
        $counts[$type]['unpaid'] = $db->count("{$typeLower}_bookings", ['booking_date' => $date, 'payment_status' => 'unpaid']);
        $counts[$type]['paid'] = $db->count("{$typeLower}_bookings", ['booking_date' => $date, 'payment_status' => 'paid']);
        $counts[$type]['refunded'] = $db->count("{$typeLower}_bookings", ['booking_date' => $date, 'payment_status' => 'refunded']);
        $counts[$type]['disputed'] = $db->count("{$typeLower}_bookings", ['booking_date' => $date, 'payment_status' => 'disputed']);
        $counts[$type]['pending'] = $db->count("{$typeLower}_bookings", ['booking_date' => $date, 'booking_status' => 'pending']);
        $counts[$type]['confirmed'] = $db->count("{$typeLower}_bookings", ['booking_date' => $date, 'booking_status' => 'confirmed']);
        $counts[$type]['cancelled'] = $db->count("{$typeLower}_bookings", ['booking_date' => $date, 'booking_status' => 'cancelled']);
    }
    
    // ADD DATA FETCHING FOR VISA BOOKINGS
    $counts['Visa']['total'] = $db->count('visa_bookings', ['booking_date' => $date]);
    $counts['Visa']['unpaid'] = $db->count('visa_bookings', ['booking_date' => $date, 'booking_payment_status' => 'unpaid']);
    $counts['Visa']['paid'] = $db->count('visa_bookings', ['booking_date' => $date, 'booking_payment_status' => 'paid']);
    $counts['Visa']['refunded'] = $db->count('visa_bookings', ['booking_date' => $date, 'booking_payment_status' => 'refunded']);
    $counts['Visa']['disputed'] = $db->count('visa_bookings', ['booking_date' => $date, 'booking_payment_status' => 'disputed']);
    $counts['Visa']['pending'] = $db->count('visa_bookings', ['booking_date' => $date, 'booking_status' => 'pending']);
    $counts['Visa']['confirmed'] = $db->count('visa_bookings', ['booking_date' => $date, 'booking_status' => 'confirmed']);
    $counts['Visa']['cancelled'] = $db->count('visa_bookings', ['booking_date' => $date, 'booking_status' => 'cancelled']);
}
unset($counts); // Unset reference

// PREPARE DATA FOR THE CHART
$chartData = [];
foreach ($monthDays as $date => $counts) {
    $day = date('d', strtotime($date));
    $chartData[] = [
        'date' => $date,
        'day' => $day,
        'cars_total' => $counts['Cars']['total'],
        'cars_unpaid' => $counts['Cars']['unpaid'],
        'cars_paid' => $counts['Cars']['paid'],
        'cars_refunded' => $counts['Cars']['refunded'],
        'cars_disputed' => $counts['Cars']['disputed'],
        'cars_pending' => $counts['Cars']['pending'],
        'cars_confirmed' => $counts['Cars']['confirmed'],
        'cars_cancelled' => $counts['Cars']['cancelled'],
        'flights_total' => $counts['Flights']['total'],
        'flights_unpaid' => $counts['Flights']['unpaid'],
        'flights_paid' => $counts['Flights']['paid'],
        'flights_refunded' => $counts['Flights']['refunded'],
        'flights_disputed' => $counts['Flights']['disputed'],
        'flights_pending' => $counts['Flights']['pending'],
        'flights_confirmed' => $counts['Flights']['confirmed'],
        'flights_cancelled' => $counts['Flights']['cancelled'],
        'hotels_total' => $counts['Hotels']['total'],
        'hotels_unpaid' => $counts['Hotels']['unpaid'],
        'hotels_paid' => $counts['Hotels']['paid'],
        'hotels_refunded' => $counts['Hotels']['refunded'],
        'hotels_disputed' => $counts['Hotels']['disputed'],
        'hotels_pending' => $counts['Hotels']['pending'],
        'hotels_confirmed' => $counts['Hotels']['confirmed'],
        'hotels_cancelled' => $counts['Hotels']['cancelled'],
        'tours_total' => $counts['Tours']['total'],
        'tours_unpaid' => $counts['Tours']['unpaid'],
        'tours_paid' => $counts['Tours']['paid'],
        'tours_refunded' => $counts['Tours']['refunded'],
        'tours_disputed' => $counts['Tours']['disputed'],
        'tours_pending' => $counts['Tours']['pending'],
        'tours_confirmed' => $counts['Tours']['confirmed'],
        'tours_cancelled' => $counts['Tours']['cancelled'],
        'visa_total' => $counts['Visa']['total'],
        'visa_unpaid' => $counts['Visa']['unpaid'],
        'visa_paid' => $counts['Visa']['paid'],
        'visa_refunded' => $counts['Visa']['refunded'],
        'visa_disputed' => $counts['Visa']['disputed'],
        'visa_pending' => $counts['Visa']['pending'],
        'visa_confirmed' => $counts['Visa']['confirmed'],
        'visa_cancelled' => $counts['Visa']['cancelled'],
    ];
}
?>

<!-- STYLES -->
<style>
#chartdiv {
  width: 100%;
  height: 500px;
}

.chart-heading {
  font-size: 24px; /* Adjust the size as needed */
  font-weight: bold;
  margin-bottom: 20px; /* Space between heading and chart */
  color: #333; /* Adjust the color as needed */
}
</style>

<!-- RESOURCES -->
<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

<!-- CHART CODE -->
<script>
am5.ready(function() {

    // CREATE ROOT ELEMENT
    var root = am5.Root.new("chartdiv");

    // SET THEMES
    root.setThemes([ am5themes_Animated.new(root) ]);

    // CREATE CHART
    var chart = root.container.children.push(am5xy.XYChart.new(root, {
        panX: true,
        panY: true,
        wheelX: "panX",
        wheelY: "zoomX",
        pinchZoomX: true,
        layout: root.verticalLayout
    }));

    // ADD LEGEND
    var legend = chart.children.push(am5.Legend.new(root, {
        centerX: am5.p50,
        x: am5.p50
    }));

    // CREATE AXES
    var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
        categoryField: "day",
        renderer: am5xy.AxisRendererX.new(root, {
            cellStartLocation: 0.1,
            cellEndLocation: 0.9,
            minGridDistance: 30
        }),
        tooltip: am5.Tooltip.new(root, {})
    }));

    var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
        renderer: am5xy.AxisRendererY.new(root, {
            strokeOpacity: 0.1
        })
    }));

    // CREATE SERIES FUNCTION
    function createSeries(name, field, unpaidField, paidField, refundedField, disputedField, pendingField, confirmedField, cancelledField, color) {
        var series = chart.series.push(am5xy.ColumnSeries.new(root, {
            name: name,
            xAxis: xAxis,
            yAxis: yAxis,
            valueYField: field,
            categoryXField: "day"
        }));

        series.columns.template.setAll({
            tooltipText: "{name}\n" +
             "Total: {valueY}\n" +
             "Payment Status:\n" +
             "  • Unpaid: {"+ unpaidField +"}\n" +
             "  • Paid: {"+ paidField +"}\n" +
             "  • Refunded: {"+ refundedField +"}\n" +
             "  • Disputed: {"+ disputedField +"}\n" +
             "Booking Status:\n" +
             "  • Pending: {"+ pendingField +"}\n" +
             "  • Confirmed: {"+ confirmedField +"}\n" +
             "  • Cancelled: {"+ cancelledField +"}",
            width: am5.percent(90),
            tooltipY: 0,
            strokeOpacity: 0,
            fill: color,
            stroke: color
        });

        series.data.setAll(data);

        series.appear(1000);
        legend.data.push(series);
    }

    // CHART DATA
    var data = <?php echo json_encode($chartData); ?>;

    // SET DATA
    xAxis.data.setAll(data);

    // CREATE SERIES FOR EACH BOOKING TYPE WITH PAYMENT AND BOOKING STATUS DETAILS
    createSeries("Cars", "cars_total", "cars_unpaid", "cars_paid", "cars_refunded", "cars_disputed", "cars_pending", "cars_confirmed", "cars_cancelled", am5.color(0x1f77b4));
    createSeries("Flights", "flights_total", "flights_unpaid", "flights_paid", "flights_refunded", "flights_disputed", "flights_pending", "flights_confirmed", "flights_cancelled", am5.color(0xff7f0e));
    createSeries("Hotels", "hotels_total", "hotels_unpaid", "hotels_paid", "hotels_refunded", "hotels_disputed", "hotels_pending", "hotels_confirmed", "hotels_cancelled", am5.color(0x2ca02c));
    createSeries("Tours", "tours_total", "tours_unpaid", "tours_paid", "tours_refunded", "tours_disputed", "tours_pending", "tours_confirmed", "tours_cancelled", am5.color(0xd62728));
    createSeries("Visa", "visa_total", "visa_unpaid", "visa_paid", "visa_refunded", "visa_disputed", "visa_pending", "visa_confirmed", "visa_cancelled", am5.color(0x9467bd));

    // ANIMATE CHART ON LOAD
    chart.appear(1000, 100);

});
</script>

<div class="container mt-4">
  <div class="card">
    <div class="card-header">
      Monthly Bookings
    </div>
    <div class="card-body">
      <div id="chartdiv"></div>
    </div>
  </div>
</div>

<?php include "_footer.php"; ?>
