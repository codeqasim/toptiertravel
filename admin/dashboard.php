<?php

require_once '_config.php';

auth_check();

$title = "Dashboard";

include "_header.php";

// dd(DECODE($_SESSION['phptravels_backend_user']));
// dd($user_permissions);

$params = array();
$flights_bookings = GET('flights_bookings',$params);
$hotels_bookings = GET('hotels_bookings',$params);
$tours_bookings = GET('tours_bookings',$params);
$cars_bookings = GET('cars_bookings',$params);
$visa_bookings = GET('visa_bookings',$params);
$users = GET('users',$params);
$bookings = count($flights_bookings) + count($hotels_bookings) + count($tours_bookings) + count($cars_bookings) + count($visa_bookings);

// Calculate Total Sales
$total_sales = 0;
$all_bookings = array_merge($flights_bookings, $hotels_bookings, $tours_bookings, $cars_bookings, $visa_bookings);
foreach($all_bookings as $booking) {
    $booking = (array)$booking;
    if(isset($booking['subtotal']) && $booking['subtotal'] > 0) {
        $total_sales += floatval($booking['subtotal']);
    }
}

// Calculate Net Profit
$net_profit = 0;
foreach($all_bookings as $booking) {
    $booking = (array)$booking;
    if(isset($booking['net_profit']) && $booking['net_profit'] > 0) {
        $net_profit += floatval($booking['net_profit']);
    }
}

// Supplier Payments Owed
$supplier_owed_params = array("supplier_payment_status" => "unpaid");
$supplier_owed_hotels = $db->select("hotels_bookings", "*", $supplier_owed_params);
$supplier_owed_amount = 0;
foreach($supplier_owed_hotels as $booking) {
    $booking = (array)$booking;
    if(isset($booking['supplier_cost']) && $booking['supplier_cost'] > 0) {
        $supplier_owed_amount += floatval($booking['supplier_cost']);
    }
}

// Commission Payments Owed (Agent Payments)
$agent_owed_params = array("agent_payment_status" => "pending");
$agent_owed_hotels = $db->select("hotels_bookings", "*", $agent_owed_params);
$commission_owed_amount = 0;
$commission_owed_count = count($agent_owed_hotels);
foreach($agent_owed_hotels as $booking) {
    $booking = (array)$booking;
    if(isset($booking['agent_fee']) && $booking['agent_fee'] > 0) {
        $commission_owed_amount += floatval($booking['agent_fee']);
    }
}

// Transactions (paid bookings) - excluding visa_bookings as it doesn't have payment_status
$transactions_params = array("payment_status" => "paid");
$transactions_flights = $db->select("flights_bookings", "*", $transactions_params);
$transactions_hotels = $db->select("hotels_bookings", "*", $transactions_params);
$transactions_tours = $db->select("tours_bookings", "*", $transactions_params);
$transactions_cars = $db->select("cars_bookings", "*", $transactions_params);
$transactions = count($transactions_flights) + count($transactions_hotels) + count($transactions_tours) + count($transactions_cars);

// Unpaid IATA
$iata_owed_params = array("iata_payment_status" => "unpaid");
$iata_owed = $db->select("hotels_bookings", "*", $iata_owed_params);
$iata_owed_count = 0;
$iata_owed_amount = 0;
foreach($iata_owed as $booking) {
    $booking = (array)$booking;
    if(isset($booking['iata']) && $booking['iata'] != '' && $booking['iata'] != '0' && $booking['iata'] != null) {
        $iata_owed_count++;
        $iata_owed_amount += floatval($booking['iata']);
    }
}

?>

<div class="page_head bg-transparent">
    <div class="panel-heading">
        <div class="float-start ">
            <p class="m-0 page_title"><?=T::dashboard?></p>
        </div>
        <div class="float-end">
        <!-- <a href="javascript:window.history.back();" data-toggle="tooltip" data-placement="top" title="Previous Page" class="loading_effect btn btn-warning"><?=T::back?></a> -->
        </div>
    </div>
</div>

<?php

if(isset($user_permissions->admin->page_access)){

?>

<div class="container mt-3">

    <div class="row mb-0 g-2">

    <!-- Total Sales -->
    <div class="col-md-3 mb-2">
        <div class="card custom-card">
            <div class="card-body">
                <div class="card-item">
                    <div class="card-item-icon card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" 
                            viewBox="0 0 24 24" fill="none" stroke="#000000ff" stroke-width="2" 
                            stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>
                    <div class="card-item-title mb-2">
                        <label class="main-content-label fs-13 fw-bold mb-1">Total Sales</label>
                        <span class="d-block fs-12 mb-0 text-muted">Revenue Generated</span>
                    </div>
                    <div class="card-item-body">
                        <div class="card-item-stat">
                            <h4 class="fw-bold text-black">USD <?= number_format($total_sales, 2); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Net Profit -->
    <div class="col-md-3 mb-2">
        <div class="card custom-card">
            <div class="card-body">
                <div class="card-item">
                    <div class="card-item-icon card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" 
                            viewBox="0 0 24 24" fill="none" stroke="#000000ff" stroke-width="2" 
                            stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                    </div>
                    <div class="card-item-title mb-2">
                        <label class="main-content-label fs-13 fw-bold mb-1">Net Profit</label>
                        <span class="d-block fs-12 mb-0 text-muted">Total Earnings</span>
                    </div>
                    <div class="card-item-body">
                        <div class="card-item-stat">
                            <h4 class="fw-bold text-black">USD <?= number_format($net_profit, 2); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Supplier Payments Owed -->
    <div class="col-md-3 mb-2">
        <a href="<?=root?>supplier_payments.php?supplier_payment_status=unpaid">
        <div class="card custom-card">
            <div class="card-body">
                <div class="card-item">
                    <div class="card-item-icon card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" 
                            viewBox="0 0 24 24" fill="none" stroke="#000000ff" stroke-width="2" 
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                            <line x1="1" y1="10" x2="23" y2="10"></line>
                        </svg>
                    </div>
                    <div class="card-item-title mb-2">
                        <label class="main-content-label fs-13 fw-bold mb-1">Supplier Payments</label>
                        <span class="d-block fs-12 mb-0 text-muted">Amount Owed</span>
                    </div>
                    <div class="card-item-body">
                        <div class="card-item-stat">
                            <h4 class="fw-bold text-black">USD <?= number_format($supplier_owed_amount, 2); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </a>
    </div>

    <!-- Commission Payments Owed -->
    <div class="col-md-3 mb-2">
        <div class="card custom-card">
            <div class="card-body">
                <div class="card-item">
                    <div class="card-item-icon card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" 
                            viewBox="0 0 24 24" fill="none" stroke="#000000ff" stroke-width="2" 
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                    </div>
                    <div class="card-item-title mb-2">
                        <label class="main-content-label fs-13 fw-bold mb-1">Commission Owed</label>
                        <span class="d-block fs-12 mb-0 text-muted"><?= $commission_owed_count ?> Pending</span>
                    </div>
                    <div class="card-item-body">
                        <div class="card-item-stat">
                            <h4 class="fw-bold" style="color: #000000ff;">USD <?= number_format($commission_owed_amount, 2); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-2 mb-2">
    <a href="<?=root?>users.php?pages=1">
    <div class="card custom-card">
        <div class="card-body">
            <div class="card-item">
                <div class="card-item-icon card-icon">
                    <svg class="text-primary" xmlns="http://www.w3.org/2000/svg" width="25" height="25" 
                        viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="1.5" 
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div class="card-item-title mb-2">
                    <label class="main-content-label fs-13 fw-bold mb-1"><?= T::users ?></label>
                    <span class="d-block fs-12 mb-0 text-muted">Registered Users</span>
                </div>
                <div class="card-item-body">
                    <div class="card-item-stat">
                        <h4 class="fw-bold"><?= count($users); ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </a>
</div>

<div class="col-md-2 mb-2">
    <a href="<?=root?>bookings.php">
    <div class="card custom-card">
        <div class="card-body">
            <div class="card-item">
                <div class="card-item-icon card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" 
                        viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="1.5" 
                        stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
                <div class="card-item-title mb-2">
                    <label class="main-content-label fs-13 fw-bold mb-1"><?= T::bookings ?></label>
                    <span class="d-block fs-12 mb-0 text-muted">Total Bookings</span>
                </div>
                <div class="card-item-body">
                    <div class="card-item-stat">
                        <h4 class="fw-bold"><?= ($bookings); ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </a>
</div>
<?php

// PARAMS
$params = array(
    "booking_status" => "cancelled",
    "ORDER" => ["booking_id" => "DESC"],
    "LIMIT" => 50
);

$flights_cancellation = $db->select("flights_bookings", "*", $params);
$hotels_cancellation = $db->select("hotels_bookings", "*", $params);
$tours_cancellation = $db->select("tours_bookings", "*", $params);
$cars_cancellation = $db->select("cars_bookings", "*", $params);
$visa_cancellation = $db->select("visa_bookings", "*", $params);

$cancelled = array_merge($flights_cancellation, $hotels_cancellation, $tours_cancellation, $cars_cancellation, $visa_cancellation);
?>

<div class="col-md-2 mb-2">
    <a href="<?=root?>bookings.php?booking_id=&module=&booking_status=cancelled&payment_status=&booking_date=">
    <div class="card custom-card">
        <div class="card-body">
            <div class="card-item">
                <div class="card-item-icon card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" 
                        viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" 
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                    </svg>
                </div>
                <div class="card-item-title mb-2">
                    <label class="main-content-label fs-13 fw-bold mb-1"><?= T::cancelled . ' ' . T::bookings ?></label>
                    <span class="d-block fs-12 mb-0 text-muted">Total Cancelled</span>
                </div>
                <div class="card-item-body">
                    <div class="card-item-stat">
                        <h4 class="fw-bold"><?= count($cancelled); ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </a>
</div>

<?php

// PARAMS
$params = array(
    "payment_status" => "unpaid",
    "ORDER" => ["booking_id" => "DESC"],
    "LIMIT" => 50
);

$flights_payment = $db->select("flights_bookings", "*", $params);
$hotels_payment = $db->select("hotels_bookings", "*", $params);
$tours_payment = $db->select("tours_bookings", "*", $params);
$cars_payment = $db->select("cars_bookings", "*", $params);

$unpaid_status = array_merge($flights_payment, $hotels_payment, $tours_payment, $cars_payment);
?>

<div class="col-md-2 mb-2">
    <a href="<?=root?>bookings.php?booking_id=&module=&booking_status=&payment_status=unpaid&booking_date=">
    <div class="card custom-card">
        <div class="card-body">
            <div class="card-item">
                <div class="card-item-icon card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" 
                        viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="1.5" 
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div class="card-item-title mb-2">
                    <label class="main-content-label fs-13 fw-bold mb-1"><?= T::unpaid . ' ' . T::bookings ?></label>
                    <span class="d-block fs-12 mb-0 text-muted">Total Unpaid</span>
                </div>
                <div class="card-item-body">
                    <div class="card-item-stat">
                        <h4 class="fw-bold"><?= count($unpaid_status); ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </a>
</div>

<!-- Transactions -->
<div class="col-md-2 mb-2">
    <div class="card custom-card">
        <div class="card-body">
            <div class="card-item">
                <div class="card-item-icon card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" 
                        viewBox="0 0 24 24" fill="none" stroke="#000000ff" stroke-width="1.5" 
                        stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                        <polyline points="17 6 23 6 23 12"></polyline>
                    </svg>
                </div>
                <div class="card-item-title mb-2">
                    <label class="main-content-label fs-13 fw-bold mb-1">Transactions</label>
                    <span class="d-block fs-12 mb-0 text-muted">Completed</span>
                </div>
                <div class="card-item-body">
                    <div class="card-item-stat">
                        <h4 class="fw-bold text-black"><?= $transactions; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Unpaid IATA -->
<div class="col-md-2 mb-2">
    <a href="<?=root?>iata_payments.php?iata_payment_status=unpaid">
    <div class="card custom-card">
        <div class="card-body">
            <div class="card-item">
                <div class="card-item-icon card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" 
                        viewBox="0 0 24 24" fill="none" stroke="#000000ff" stroke-width="2" 
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <div class="card-item-title mb-2">
                    <label class="main-content-label fs-13 fw-bold mb-1">Unpaid IATA</label>
                    <span class="d-block fs-12 mb-0 text-muted"></span>
                </div>
                <div class="card-item-body">
                    <div class="card-item-stat">
                        <h4 class="fw-bold" style="color: #000000ff;">USD <?= number_format($iata_owed_amount,2); ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </a>
</div>


    <?php
        // PARAMS
        $params = array(
            "ORDER" => [ "traffic" => "DESC", ],
            "LIMIT" => 10
        );
        $countries = $db->select("countries","*",$params);
        ?>

        <div class="row g-2" style="margin-top:-20px;">
            <div class="col-md-6">
            <div class="card custom-card overflow-hidden">
    <div class="card-header border-bottom-0">
        <div>
            <label class="card-title"><?=T::ten_most_visited_countries?></label>
            <!-- <span class="d-block fs-12 mb-0 text-muted">Visualizing the ten most visited countries based on traffic data</span> -->
        </div>
    </div>
<hr>
    <div class="card-body p-3" >
        <!-- Styles -->
        <style>
            #chartdiv {
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
            am5.ready(function () {
                // Create root element
                var root = am5.Root.new("chartdiv");

                // Set themes
                root.setThemes([am5themes_Animated.new(root)]);

                // Create chart
                var chart = root.container.children.push(
                    am5xy.XYChart.new(root, {
                        panX: false,
                        panY: false,
                        wheelX: "panX",
                        wheelY: "zoomX",
                        layout: root.verticalLayout,
                    })
                );

                // Data
                var colors = chart.get("colors");
                var data = [
                    <?php foreach($countries as $country) {
                        if ($country['traffic'] >= 1) { ?>
                            {
                                country: "<?=$country['iso']?>",
                                visits: <?=$country['traffic']?>,
                                icon: "./assets/img/flags/<?=strtolower($country['iso'])?>.svg",
                                columnSettings: { fill: colors.next() },
                            },
                    <?php } } ?>
                ];

                // Create axes
                var xRenderer = am5xy.AxisRendererX.new(root, { minGridDistance: 30 });

                var xAxis = chart.xAxes.push(
                    am5xy.CategoryAxis.new(root, {
                        categoryField: "country",
                        renderer: xRenderer,
                        bullet: function (root, axis, dataItem) {
                            return am5xy.AxisBullet.new(root, {
                                location: 0.5,
                                sprite: am5.Picture.new(root, {
                                    width: 24,
                                    height: 24,
                                    centerY: am5.p50,
                                    centerX: am5.p50,
                                    src: dataItem.dataContext.icon,
                                }),
                            });
                        },
                    })
                );

                xRenderer.grid.template.setAll({ location: 1 });
                xRenderer.labels.template.setAll({ paddingTop: 20 });
                xAxis.data.setAll(data);

                var yAxis = chart.yAxes.push(
                    am5xy.ValueAxis.new(root, {
                        renderer: am5xy.AxisRendererY.new(root, { strokeOpacity: 0.1 }),
                    })
                );

                // Add series
                var series = chart.series.push(
                    am5xy.ColumnSeries.new(root, {
                        xAxis: xAxis,
                        yAxis: yAxis,
                        valueYField: "visits",
                        categoryXField: "country",
                    })
                );

                series.columns.template.setAll({
                    tooltipText: "{categoryX}: {valueY}",
                    tooltipY: 0,
                    strokeOpacity: 0,
                    templateField: "columnSettings",
                });

                series.data.setAll(data);

                // Make stuff animate on load
                series.appear();
                chart.appear(1000, 100);
            });
        </script>

        <!-- HTML -->
        <div id="chartdiv"></div>
    </div>
</div>

            </div>

            <?php

            // PARAMS
            $params = array(
                "cancellation_request"=>1,
                "cancellation_status"=>0,
                "ORDER" => [ "booking_id" => "DESC", ],
                "LIMIT" => 50
            );

            $flights_cancellation = $db->select("flights_bookings","*",$params);
            $hotels_cancellation = $db->select("hotels_bookings","*",$params);
            $tours_cancellation = $db->select("tours_bookings","*",$params);
            $cars_cancellation = $db->select("cars_bookings","*",$params);
            $visa_cancellation = $db->select("visa_bookings","*",$params);

            $cancellation=(array_merge($flights_cancellation,$hotels_cancellation,$tours_cancellation,$cars_cancellation,$visa_cancellation));
            ?>

            <div class="col-md-6">
            <div class="card custom-card overflow-hidden">
        <div class="card-header border-bottom-0">
            <div>
                <label class="card-title"><?=T::booking.' '.T::cancellation.' '.T::request?></label>
            </div>
        </div>
        <hr>
        <div class="card-body" style="height: 432px; overflow: auto;">
            <?php if(empty($cancellation)) { ?>
                <div class="h-100 d-flex align-items-center justify-content-center">
                    <?=T::no?> <?=T::booking?> <?=T::cancellation?> <?=T::request?>
                </div>
            <?php } else { ?>
                <ul class="list-group list-group-flush">
                    <?php foreach($cancellation as $cancel) { 
                        if($cancel['cancellation_status'] == 0) { ?>
                        <li class="list-group-item d-flex justify-content-between text-capitalize align-items-center fadeout_<?=$cancel['booking_ref_no']?>">
                            <strong>
                                <?=$cancel['module_type']?> <?=T::id?> <small><?=$cancel['booking_ref_no']?></small>
                            </strong>
                            <div>
                                <a href="<?=root.("../")?><?=$cancel['module_type']?>/invoice/<?=$cancel['booking_ref_no']?>" target="_blank" class="btn btn-outline-primary">
                                    <?=T::invoice?>
                                </a>
                                <span href="#" data-id="<?=$cancel['booking_ref_no']?>" 
                                      data-module="<?=strtolower($cancel['module_type'])?>" 
                                      onclick="mark_completed_<?=$cancel['booking_ref_no']?>(this)" 
                                      class="btn btn-primary">
                                    <?=T::mark_completed?>
                                </span>
                            </div>

                            <script>
                                function mark_completed_<?=$cancel['booking_ref_no']?>(d) {
                                    var cancel_id = d.getAttribute("data-id");
                                    var cancel_module = d.getAttribute("data-module");

                                    if (confirm("<?=T::are_you_sure_it_completed?>")) {
                                        var form = new FormData();
                                        form.append("cancellation_update", "");
                                        form.append("booking_status", "cancelled");
                                        form.append("cancellation_module", cancel_module);
                                        form.append("cancellation_id", cancel_id);

                                        var settings = {
                                            "url": "./_post.php",
                                            "method": "POST",
                                            "timeout": 0,
                                            "processData": false,
                                            "mimeType": "multipart/form-data",
                                            "contentType": false,
                                            "data": form
                                        };

                                        $.ajax(settings).done(function (response) {
                                            console.log(response);
                                            jQuery(".fadeout_<?=$cancel['booking_ref_no']?>").fadeOut("slow", function() {
                                                jQuery(".fadeout_<?=$cancel['booking_ref_no']?>").attr("style", "display: none !important");
                                            });
                                        });
                                    }
                                }
                            </script>
                        </li>
                    <?php } } ?>
                </ul>
            <?php } ?>
        </div>
    </div>
            </div>
        </div>

        <!-- <div class="card chart-container">
        <canvas id="chart"></canvas>
        </div>

        </div>

        <script>
            const ctx = document.getElementById("chart").getContext('2d');
            const myChart = new Chart(ctx, {
                type: 'line',
                data: {
                labels: ["jan 2023", "monday", "tuesday","wednesday", "thursday", "friday", "saturday"],
                datasets: [{
                    label: 'Last week',
                    backgroundColor: 'rgba(161, 198, 247, 1)',
                    borderColor: 'rgb(47, 128, 237)',
                    data: [3000, 4000, 2000, 5000, 8000, 1, 2000],
                }]
                },
                options: {
                scales: {
                    yAxes: [{
                    ticks: {
                        beginAtZero: true,
                    }
                    }]
                }
                },
            });
        </script> -->

        <?php } ?>

    <script>
    $('.count').each(function () {
        $(this).prop('Counter',0).animate({
            Counter: $(this).text()
        }, {
            duration: 3000,
            easing: 'swing',
            step: function (now) {
                $(this).text(Math.ceil(now));
            }
        });
    });
    </script>

<?php include "_footer.php" ?>