<?php
use Medoo\Medoo;

require_once '_config.php';
auth_check();
$title = "Annual User";
include "_header.php";

// Get the current year start and end dates
$start_of_year = date('Y-01-01');
$end_of_year = date('Y-12-31');

// Define the SQL query
$sql = "
    SELECT countries.iso, COUNT(users.id) as user_count
    FROM users
    INNER JOIN countries ON users.phone_country_code = countries.phonecode
    WHERE users.status = 1
    AND users.created_at BETWEEN :start_of_year AND :end_of_year
    GROUP BY countries.iso
    ORDER BY user_count DESC
    LIMIT 12
";

// Execute the query
$annual_users = $db->query($sql, [
    'start_of_year' => $start_of_year,
    'end_of_year' => $end_of_year
])->fetchAll();

// Initialize an array to hold the count of users per country ISO code
$user_counts = array();

// Process the annual users to count by ISO code
foreach ($annual_users as $user) {
    $iso_code = $user['iso']; // Get the iso code from the joined table
    $count = $user['user_count']; // Get the user count
    $user_counts[$iso_code] = $count;
}
?>

<!-- Styles -->
<style>
#userchartdiv {
  width: 100%;
  height: 400px;
}

.rounded-flag {
  border-radius: 12px; /* Adjust this value to set the border radius */
  overflow: hidden;
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
      icon: "./assets/img/flags/<?=strtolower($iso_code)?>.svg", // Adjust path if needed
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
          width: 24,
          height: 24,
          centerY: am5.p50,
          centerX: am5.p50,
          src: dataItem.dataContext.icon,
          adapter: {
            set: function (value, key, sprite) {
              if (key === "src") {
                return value + "?border-radius=12px"; // Apply the border-radius with URL parameter
              }
              return value;
            }
          }
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
    cornerRadiusTL: 10,
    cornerRadiusTR: 10, 
  });

  series.data.setAll(userData);

  // Make stuff animate on load
  series.appear();
  chart.appear(1000, 100);

}); // end am5.ready()
</script>

<!-- HTML -->
<div class="container mt-4">
  <!-- Bootstrap Card -->
  <div class="card">
    <div class="card-header">
      Annual Users
    </div>
    <div class="card-body">
      <div id="userchartdiv"></div>
    </div>
  </div>
</div>

<?php include "_footer.php"; ?>
