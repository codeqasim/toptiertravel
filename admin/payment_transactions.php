<?php
use Medoo\Medoo;

require_once '_config.php';
auth_check();
$title = "Transaction Report";
include "_header.php";

// FETCH TRANSACTIONS FROM THE DATABASE
$transactions = $db->select('transactions', [
    'type',
    'currency',
    'payment_gateway'
], [
    "ORDER" => ["date" => "DESC"]
]);

// PREPARE THE DATA FOR CHART
$dataForChart = [];

// INITIALIZE ARRAYS FOR TYPE AND BREAKDOWN DETAILS
foreach ($transactions as $transaction) {
    $type = $transaction['type'];
    $currency = $transaction['currency'];
    $gateway = $transaction['payment_gateway'];

    if (!isset($dataForChart[$type])) {
        $dataForChart[$type] = [
            'type' => $type,
            'count' => 0,
            'currencies' => [],
            'gateways' => []
        ];
    }

    // INCREMENT THE COUNT FOR THE TYPE
    $dataForChart[$type]['count'] += 1;
    // INCREMENT THE COUNT FOR CURRENCY
    if (!isset($dataForChart[$type]['currencies'][$currency])) {
        $dataForChart[$type]['currencies'][$currency] = 0;
    }
    $dataForChart[$type]['currencies'][$currency] += 1;
    // INCREMENT THE COUNT FOR PAYMENT GATEWAY
    if (!isset($dataForChart[$type]['gateways'][$gateway])) {
        $dataForChart[$type]['gateways'][$gateway] = 0;
    }
    $dataForChart[$type]['gateways'][$gateway] += 1;
}

// CONVERT ASSOCIATIVE ARRAY TO INDEXED ARRAY
$dataForChart = array_values($dataForChart);

?>

<!-- STYLES -->
<style>
#chartdiv {
  width: 100%;
  height: 500px;
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
    root.setThemes([
      am5themes_Animated.new(root)
    ]);

    // CREATE CHART
    var chart = root.container.children.push(am5xy.XYChart.new(root, {
      panX: true,
      panY: true,
      wheelX: "panX",
      wheelY: "zoomX",
      pinchZoomX: true,
      paddingLeft: 0,
      paddingRight: 1
    }));

    // ADD CURSOR
    var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));
    cursor.lineY.set("visible", false);

    // CREATE AXES
    var xRenderer = am5xy.AxisRendererX.new(root, { 
      minGridDistance: 30, 
      minorGridEnabled: true
    });

    xRenderer.grid.template.setAll({
      location: 1
    });

    var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
      maxDeviation: 0.3,
      categoryField: "type", // USE 'TYPE' AS THE CATEGORY FROM THE SORTED DATA
      renderer: xRenderer,
      tooltip: am5.Tooltip.new(root, {})
    }));

    var yRenderer = am5xy.AxisRendererY.new(root, {
      strokeOpacity: 0.1
    });

    var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
      maxDeviation: 0.3,
      renderer: yRenderer
    }));

    // CREATE SERIES
    var series = chart.series.push(am5xy.ColumnSeries.new(root, {
      name: "Number of Transactions",
      xAxis: xAxis,
      yAxis: yAxis,
      valueYField: "count", // USE 'COUNT' FOR THE Y-AXIS VALUES
      sequencedInterpolation: true,
      categoryXField: "type",
      tooltip: am5.Tooltip.new(root, {
        labelText: "{valueY} transactions\nCurrencies: {currencies}\nGateways: {gateways}"  // TOOLTIP FORMAT
      })
    }));

    series.columns.template.setAll({
      cornerRadiusTL: 5,
      cornerRadiusTR: 5,
      strokeOpacity: 0
    });

    series.columns.template.adapters.add("fill", function (fill, target) {
      return chart.get("colors").getIndex(series.columns.indexOf(target));
    });

    series.columns.template.adapters.add("stroke", function (stroke, target) {
      return chart.get("colors").getIndex(series.columns.indexOf(target));
    });

    // SET DATA
    var chartData = <?php
        foreach ($dataForChart as &$data) {
            $data['currencies'] = json_encode($data['currencies']);
            $data['gateways'] = json_encode($data['gateways']);
        }
        echo json_encode($dataForChart);
    ?>;

    xAxis.data.setAll(chartData);
    series.data.setAll(chartData);

    // ANIMATE CHART
    series.appear(1000);
    chart.appear(1000, 100);

}); // END am5.ready()
</script>

<!-- HTML -->
<div class="container mt-4">
  <div class="card">
    <div class="card-header">
      Transaction Report
    </div>
    <div class="card-body">
      <div id="chartdiv"></div>
    </div>
  </div>
</div>

<?php include "_footer.php"; ?>