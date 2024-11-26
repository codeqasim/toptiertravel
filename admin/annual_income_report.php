<?php
use Medoo\Medoo;

require_once '_config.php';
auth_check();
$title = "Annual Income Report";
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
$default_Currency = $db->get("currencies", ["name"], ["default" => 1]);

function getCurrencyRate($currency) {
    global $db;
    $rate = $db->get("currencies", "rate", ["name" => $currency]);
    return $rate ?? 1;
}

$currentYear = date("Y");
$years = range($currentYear - 4, $currentYear);
$monthlyEarnings = array_fill_keys(
    ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    array_fill_keys($years, 0)
);

$allTransactions = $db->select("transactions", [
    "date", 
    "amount", 
    "currency"
]);

foreach ($allTransactions as $transaction) {
    $transactionDate = strtotime($transaction['date']);
    $transactionYear = date("Y", $transactionDate);
    $transactionMonth = date("F", $transactionDate);
    
    if (isset($monthlyEarnings[$transactionMonth][$transactionYear])) {
        $currencyRate = getCurrencyRate($transaction['currency']);
        $monthlyEarnings[$transactionMonth][$transactionYear] += round($transaction['amount'] / $currencyRate, 2);
    }
}

$chartData = [];
foreach ($monthlyEarnings as $month => $yearsData) {
    $dataEntry = ['month' => $month];
    foreach ($yearsData as $year => $value) {
        $dataEntry["year_$year"] = $value;
    }
    $chartData[] = $dataEntry;
}
?>
<!-- Styles -->
<style>
#chartdiv {
  width: 100%;
  height: 500px;
}
</style>

<!-- Resources -->
<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

<!-- Chart code -->
<script>
var chartData = <?php echo json_encode($chartData); ?>;
var years = <?php echo json_encode($years); ?>;
var defaultCurrency = "<?php echo $default_Currency['name']; ?>"; 

var seriesColors = ['#32CD32', '#1E90FF', '#FF6347', '#FFD700', '#00CED1'];

am5.ready(function() {
  // Create root element
  var root = am5.Root.new("chartdiv");
  
  // Set themes
  root.setThemes([am5themes_Animated.new(root)]);
  
  // Create chart
  var chart = root.container.children.push(am5xy.XYChart.new(root, {
    panX: false,
    panY: false,
    paddingLeft: 0,
    wheelX: "panX",
    wheelY: "zoomX",
    layout: root.verticalLayout
  }));

  // Add legend
  var legend = chart.children.push(am5.Legend.new(root, {
    centerX: am5.p50,
    x: am5.p50
  }));

  // Create axes
  var xRenderer = am5xy.AxisRendererX.new(root, {
    cellStartLocation: 0.1,
    cellEndLocation: 0.9,
    minGridDistance: 30 
  });

  var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
    categoryField: "month",
    renderer: xRenderer,
    tooltip: am5.Tooltip.new(root, {})
  }));

  xRenderer.grid.template.setAll({
    location: 1
  });

  xAxis.data.setAll(chartData);

  var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
    renderer: am5xy.AxisRendererY.new(root, {
      strokeOpacity: 0.1
    })
  }));

  function makeSeries(name, fieldName, colorIndex) {
    var series = chart.series.push(am5xy.ColumnSeries.new(root, {
      name: name,
      xAxis: xAxis,
      yAxis: yAxis,
      valueYField: fieldName,
      categoryXField: "month",
      fill: am5.color(seriesColors[colorIndex]),
      stroke: am5.color(seriesColors[colorIndex])
    }));

    series.columns.template.setAll({
      tooltipText: "{name}, {categoryX}: " + defaultCurrency + " {valueY}",
      width: am5.percent(90),
      tooltipY: 0,
      strokeOpacity: 0
    });

    series.data.setAll(chartData); 

    series.appear();

    series.bullets.push(function () {
      return am5.Bullet.new(root, {
        locationY: 0,
        sprite: am5.Label.new(root, {
          text: defaultCurrency + " {valueY}",
          fill: root.interfaceColors.get("alternativeText"),
          centerY: 0,
          centerX: am5.p50,
          populateText: true
        })
      });
    });

    legend.data.push(series);
  }

  years.forEach(function(year, index) {
    makeSeries(year.toString(), "year_" + year, index % seriesColors.length);
  });

  chart.appear(1000, 100);
});
</script>

<!-- HTML -->
<div class="container mt-4">
  <div class="card">
    <div class="card-header">
      Income Report For 5 Years
    </div>
    <div class="card-body">
      <div id="chartdiv"></div>
    </div>
  </div>
</div>

<?php include "_footer.php"; ?>
