<?php
use Medoo\Medoo;

require_once '_config.php';
auth_check();
$title = "Weekly User";
include "_header.php";

// Set error reporting to display errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the date 7 days ago
$seven_days_ago = date('Y-m-d', strtotime('-7 days'));

// Define the SQL query
$sql = "
    SELECT countries.iso, COUNT(users.id) as user_count
    FROM users
    INNER JOIN countries ON users.phone_country_code = countries.phonecode
    WHERE users.status = 1
    AND users.created_at >= :seven_days_ago
    GROUP BY countries.iso
    ORDER BY user_count DESC
    LIMIT 12
";

// Execute the query
$recent_users = $db->query($sql, ['seven_days_ago' => $seven_days_ago])->fetchAll();

// Initialize an array to hold the count of users per country ISO code
$user_counts = array();

// Process the recent users to count by ISO code
foreach ($recent_users as $user) {
    $iso_code = $user['iso']; // Get the iso code from the joined table
    $user_counts[$iso_code] = $user['user_count'];
}
?>

<!-- Styles -->
<style>
#userchartdiv {
  width: 100%;
  height: 400px;
}
</style>

<!-- Resources -->
<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

<!-- Chart code -->
<script>
am5.ready(function() {

// Create root element
var root = am5.Root.new("userchartdiv");

// Set themes
root.setThemes([am5themes_Animated.new(root)]);

// Create chart
var chart = root.container.children.push(am5xy.XYChart.new(root, {
  panX: false,
  panY: false,
  wheelX: "panX",
  wheelY: "zoomX",
  layout: root.verticalLayout
}));

// Data
var userData = [
  <?php foreach($user_counts as $iso_code => $count): ?>
  {
    country_iso: "<?=$iso_code?>",
    users: <?=$count?>,
    icon: "./assets/img/flags/<?=strtolower($iso_code)?>.svg",
    columnSettings: { fill: chart.get("colors").next() }
  },
  <?php endforeach; ?>
];

// Create axes
var xRenderer = am5xy.AxisRendererX.new(root, {
  minGridDistance: 30
});

var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
  categoryField: "country_iso",
  renderer: xRenderer,
  bullet: function(root, axis, dataItem) {
    return am5xy.AxisBullet.new(root, {
      location: 0.5,
      sprite: am5.Picture.new(root, {
        width: 24,  // Icon width
        height: 24, // Icon height
        centerY: am5.p50,
        centerX: am5.p50,
        src: dataItem.dataContext.icon,
      })
    });
  }

}));

xRenderer.grid.template.setAll({
  location: 1
});

xRenderer.labels.template.setAll({
  paddingTop: 20
});

xAxis.data.setAll(userData);

var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
  renderer: am5xy.AxisRendererY.new(root, {
    strokeOpacity: 0.1
  })
}));

// Add series
var series = chart.series.push(am5xy.ColumnSeries.new(root, {
  xAxis: xAxis,
  yAxis: yAxis,
  valueYField: "users",
  categoryXField: "country_iso"
}));

series.columns.template.setAll({
  tooltipText: "{categoryX}: {valueY} users", // This shows the ISO code on hover
  tooltipY: 0,
  strokeOpacity: 0,
  templateField: "columnSettings",
  cornerRadiusTL: 8,
  cornerRadiusTR: 8
});

series.data.setAll(userData);

// Make stuff animate on load
series.appear();
chart.appear(1000, 100);

}); // end am5.ready()

</script>

<div class="container mt-4">
  <div class="card">
    <div class="card-header">
      Weekly Users
    </div>
    <div class="card-body">
      <div id="userchartdiv"></div>
    </div>
  </div>
</div>

<?php include "_footer.php"; ?>
