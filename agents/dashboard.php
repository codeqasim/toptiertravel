<style>
    /* Adding space to the search box */
    .dataTables_filter {
        margin-top: 10px;
        margin-bottom: 10px;
    }

    .dataTables_paginate {
        margin-top: 10px !important;
        margin-bottom: 10px !important;
    }

    #bookingTable tbody td {
        font-size: 14px !important;
    }
</style>


<?php

require_once '_config.php';
auth_check();
$title = "Agent Dashboard";
include "_header.php";

$agent_id = $USER_SESSION->backend_user_id;

// $agent_id = '20230311051923100';

$user = $db->select("users", '*', [
    "user_id" => $agent_id,
]);

// for monthly bookings 


// for each month booking

$current_year = date('Y');
$months = range(1, 12);

$monthly_booking_counts = array_fill_keys(
    ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    0
);

foreach ($months as $month) {
    $start_date = "$current_year-$month-01";
    $end_date = date('Y-m-t', strtotime($start_date));
    
    $bookings = $db->select("hotels_bookings", '*', [
        "agent_id" => $agent_id,
        "booking_status" => "confirmed",
        "payment_status" => "paid",
        "booking_date[<>]" => [$start_date, $end_date]
    ]);
    $month_name = date('F', strtotime($start_date));
    $monthly_booking_counts[$month_name] = count($bookings);
}

// Get only the counts from the array and reindex
$booking_counts_string = "[" . implode(", ", array_values($monthly_booking_counts)) . "]";


$current_year = date('Y');
$previous_year = $current_year - 1;
$current_month = date('n'); 

// Get last 6 months including current month
$months = [];
for ($i = 0; $i < 6; $i++) {
    $month_num = $current_month - $i;
    if ($month_num <= 0) {
        $month_num += 12;
    }
    $months[] = $month_num;
}
$months = array_reverse($months); 

$month_names = array_map(function ($m) {
    return date('F', mktime(0, 0, 0, $m, 1));
}, $months);

$current_year_totals = [];
$previous_year_totals = [];
$current_year_paid_agent_fee = [];
$previous_year_paid_agent_fee = [];

foreach ($months as $month) {
    $start_date = "$current_year-$month-01";
    $end_date = date('Y-m-t', strtotime($start_date));

    $confirmed_bookings = $db->select("hotels_bookings", '*', [
        "agent_id" => $agent_id,
        "booking_status" => "confirmed",
        "booking_date[<>]" => [$start_date, $end_date]
    ]);

    $total_bookings = count($confirmed_bookings);
    $total_price_markup = array_sum(array_column($confirmed_bookings, 'price_markup'));

    $paid_bookings = array_filter($confirmed_bookings, function ($booking) {
        return $booking['agent_commission_status'] === 'paid';
    });

    $total_paid_price_markup = array_sum(array_column($paid_bookings, 'price_markup'));
    $total_paid_agent_fee_percentage = array_sum(array_column($paid_bookings, 'agent_fee'));
    $total_paid_agent_fee = array_sum(array_column($paid_bookings, 'agent_fee'));

    $current_year_totals[] = [
        "month" => date('F', strtotime($start_date)),
        "total_bookings" => $total_bookings,
        "total_price_markup" => $total_price_markup,
        "total_paid_price_markup" => $total_paid_price_markup,
        "total_paid_agent_fee" => $total_paid_agent_fee
    ];

    $current_year_paid_agent_fee[] = $total_paid_agent_fee;

    $start_date_prev = "$previous_year-$month-01";
    $end_date_prev = date('Y-m-t', strtotime($start_date_prev));

    $confirmed_bookings_prev = $db->select("hotels_bookings", '*', [
        "agent_id" => $agent_id,
        "booking_status" => "confirmed",
        "booking_date[<>]" => [$start_date_prev, $end_date_prev]
    ]);

    $total_bookings_prev = count($confirmed_bookings_prev);
    $total_price_markup_prev = array_sum(array_column($confirmed_bookings_prev, 'price_markup'));

    $paid_bookings_prev = array_filter($confirmed_bookings_prev, function ($booking) {
        return $booking['agent_commission_status'] === 'paid';
    });

    $total_paid_price_markup_prev = array_sum(array_column($paid_bookings_prev, 'price_markup'));
    $total_paid_agent_fee_percentage_prev = array_sum(array_column($paid_bookings_prev, 'agent_fee'));
    $total_paid_agent_fee_prev = array_sum(array_column($paid_bookings_prev, 'agent_fee'));

    $previous_year_totals[] = [
        "month" => date('F', strtotime($start_date_prev)),
        "total_bookings" => $total_bookings_prev,
        "total_price_markup" => $total_price_markup_prev,
        "total_paid_price_markup" => $total_paid_price_markup_prev,
        "total_paid_agent_fee" => $total_paid_agent_fee_prev
    ];

    $previous_year_paid_agent_fee[] = $total_paid_agent_fee_prev;
}

$current_year_paid_agent_fee = "[" . implode(", ", $current_year_paid_agent_fee) . "]";
$previous_year_paid_agent_fee = "[" . implode(", ", $previous_year_paid_agent_fee) . "]";

?>

<script>
    // for monthly bookings
    window.permonthbookingcounts = <?= $booking_counts_string ?>;
    // for monthly bookings


    window.current_year_paid_agent_fee_js = <?= $current_year_paid_agent_fee ?>;
    window.previous_year_paid_agent_fee_js = <?= $previous_year_paid_agent_fee ?>;
</script>
<div class="container-fluid">

    <!-- Start::page-header -->

    <div class="d-md-flex d-block align-items-center justify-content-between page-header-breadcrumb">
        <div>
            <h2 class="main-content-title fs-24 mb-1">Welcome To Dashboard</h2>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Project Dashboard</li>
            </ol>
        </div>
        <div class="d-flex">
            <div class="justify-content-center">
                <button type="button" class="btn btn-white btn-icon-text my-2 me-2 d-inline-flex align-items-center">
                    <i class="fe fe-download me-2 fs-14"></i> Import
                </button>
                <button type="button" class="btn btn-white btn-icon-text my-2 me-2 d-inline-flex align-items-center">
                    <i class="fe fe-filter me-2 fs-14"></i> Filter
                </button>
                <button type="button" class="btn btn-primary my-2 btn-icon-text d-inline-flex align-items-center">
                    <i class="fe fe-download-cloud me-2 fs-14"></i> Download Report
                </button>
            </div>
        </div>
    </div>

    <!-- End::page-header -->

    <!-- Start::row-1 -->
    <div class="row row-sm">
        <div class="col-sm-12 col-lg-12 col-xl-8">
            <!-- Start::row -->
            <div class="row row-sm banner-img">
                <div class="col-sm-12 col-lg-12 col-xl-12">
                    <div class="card bg-primary custom-card card-box">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="offset-xl-3 offset-sm-6 col-xl-8 col-sm-6 col-12">
                                    <h4 class="d-flex mb-3">
                                        <span class="fw-bold text-fixed-white ">Hello
                                            <?=$user[0]['first_name'].' '.$user[0]['last_name']?>
                                        </span>
                                    </h4>
                                    <p class="tx-white-7 mb-1">The world is a book, and those who do not travel read
                                        only one page."
                                        - Saint Augustine
                                </div>
                                <img src="../assets/img/agent/agent.png" alt="user-img" style="height:165px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End::row -->

            <!-- Start::row -->
            <div class="row row-sm">
                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="card-item">
                                <div class="card-item-icon card-icon">
                                    <svg class="text-primary" xmlns="http://www.w3.org/2000/svg"
                                        enable-background="new 0 0 24 24" height="24" viewBox="0 0 24 24" width="24">
                                        <g>
                                            <rect height="14" opacity=".3" width="14" x="5" y="5" />
                                            <g>
                                                <rect fill="none" height="24" width="24" />
                                                <g>
                                                    <path
                                                        d="M19,3H5C3.9,3,3,3.9,3,5v14c0,1.1,0.9,2,2,2h14c1.1,0,2-0.9,2-2V5C21,3.9,20.1,3,19,3z M19,19H5V5h14V19z" />
                                                    <rect height="5" width="2" x="7" y="12" />
                                                    <rect height="10" width="2" x="15" y="7" />
                                                    <rect height="3" width="2" x="11" y="14" />
                                                    <rect height="2" width="2" x="11" y="10" />
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                </div>
                                <div class="card-item-title mb-2">
                                    <label class="main-content-label fs-13 fw-bold mb-1">Total Sales Revenue</label>
                                    <span class="d-block fs-12 mb-0 text-muted">Previous month vs this months</span>
                                </div>
                                <?php
                                        // Fetch current month revenue
                                        $current_revenue = (float) ($db->sum("hotels_bookings", "price_markup", [
                                            "agent_id" => $agent_id,
                                            "booking_status" => "confirmed",
                                            "payment_status" => "paid",
                                            "agent_payment_status" => "paid",
                                            "booking_date[<>]" => [date("Y-m-01"), date("Y-m-t")]
                                        ]) ?? 0);
                                        
                                        // Fetch previous month revenue
                                        $previous_revenue = (float) ($db->sum("hotels_bookings", "price_markup", [
                                            "agent_id" => $agent_id,
                                            "booking_status" => "confirmed",
                                            "payment_status" => "paid",
                                            "agent_payment_status" => "paid",
                                            "booking_date[<>]" => [
                                                date("Y-m-01", strtotime("-1 month")),
                                                date("Y-m-t", strtotime("-1 month"))
                                            ]
                                        ]) ?? 0);
                                        
                                        $percentage_change = 0;
                                        $status = "higher";
                                        
                                        if ($previous_revenue > 0) {
                                            $percentage_change = (($current_revenue - $previous_revenue) / $previous_revenue) * 100;
                                            
                                            // Ensure percentage stays within valid range
                                            $percentage_change = max(min($percentage_change, 100), -100);
                                        
                                            $status = ($percentage_change >= 0) ? "higher" : "lower";
                                        } elseif ($current_revenue > 0) {
                                            $percentage_change = 100;
                                            $status = "higher";
                                        }
                                        
                                        // If both revenues are 0, keep it neutral
                                        if ($current_revenue == 0 && $previous_revenue == 0) {
                                            $percentage_change = 0;
                                            $status = "higher";
                                        }
                                        ?>
                                <div class="card-item-body">
                                    <div class="card-item-stat">
                                        <h4 class="fw-bold">$
                                            <?= number_format($current_revenue, 2) ?>
                                        </h4>
                                        <small>
                                            <b
                                                class="text-<?= ($status == 'higher') ? 'success' : (($status == 'lower') ? 'danger' : 'secondary') ?>">
                                                <?= abs(round($percentage_change, 2)) ?>%
                                            </b>
                                            <?= $status ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="card-item">
                                <div class="card-item-icon card-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                                        <path d="M0 0h24v24H0V0z" fill="none" />
                                        <path
                                            d="M12 4c-4.41 0-8 3.59-8 8 0 1.82.62 3.49 1.64 4.83 1.43-1.74 4.9-2.33 6.36-2.33s4.93.59 6.36 2.33C19.38 15.49 20 13.82 20 12c0-4.41-3.59-8-8-8zm0 9c-1.94 0-3.5-1.56-3.5-3.5S10.06 6 12 6s3.5 1.56 3.5 3.5S13.94 13 12 13z"
                                            opacity=".3" />
                                        <path
                                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM7.07 18.28c.43-.9 3.05-1.78 4.93-1.78s4.51.88 4.93 1.78C15.57 19.36 13.86 20 12 20s-3.57-.64-4.93-1.72zm11.29-1.45c-1.43-1.74-4.9-2.33-6.36-2.33s-4.93.59-6.36 2.33C4.62 15.49 4 13.82 4 12c0-4.41 3.59-8 8-8s8 3.59 8 8c0 1.82-.62 3.49-1.64 4.83zM12 6c-1.94 0-3.5 1.56-3.5 3.5S10.06 13 12 13s3.5-1.56 3.5-3.5S13.94 6 12 6zm0 5c-.83 0-1.5-.67-1.5-1.5S11.17 8 12 8s1.5.67 1.5 1.5S12.83 11 12 11z" />
                                    </svg>
                                </div>
                                <div class="card-item-title mb-2">
                                    <label class="main-content-label fs-13 fw-bold mb-1">Total Commission</label>
                                    <span class="d-block fs-12 mb-0 text-muted">Total commission you earned</span>
                                </div>
                                <?php
                                // Fetch current month revenue
                                        $current_agent_fee = (float) ($db->sum("hotels_bookings", "agent_fee", [
                                            "agent_id" => $agent_id,
                                            "booking_status" => "confirmed",
                                            "payment_status" => "paid",
                                            "agent_payment_status" => "paid",
                                            
                                            "booking_date[<>]" => [date("Y-m-01"), date("Y-m-t")]
                                        ]) ?? 0);
                                        
                                        // Fetch previous month revenue
                                        $previous_agent_fee = (float) ($db->sum("hotels_bookings", "agent_fee", [
                                            "agent_id" => $agent_id,
                                            "booking_status" => "confirmed",
                                            "payment_status" => "paid",
                                            "agent_payment_status" => "paid",
                                            "booking_date[<>]" => [
                                                date("Y-m-01", strtotime("-1 month")),
                                                date("Y-m-t", strtotime("-1 month"))
                                            ]
                                        ]) ?? 0);
                                        
                                        $percentage_change = 0;
                                        $status = "Increased";
                                        
                                        if ($previous_agent_fee > 0) {
                                            $percentage_change = (($current_agent_fee - $previous_agent_fee) / $previous_agent_fee) * 100;
                                            
                                            // Ensure percentage stays within valid range
                                            $percentage_change = max(min($percentage_change, 100), -100);
                                        
                                            $status = ($percentage_change >= 0) ? "Increased" : "Decreased";
                                        } elseif ($current_agent_fee > 0) {
                                            $percentage_change = 100;
                                            $status = "Increased";
                                        }
                                        
                                        // If both revenues are 0, keep it neutral
                                        if ($current_agent_fee == 0 && $previous_agent_fee == 0) {
                                            $percentage_change = 0;
                                            $status = "Increased";
                                        }
                                        ?>
                                <div class="card-item-body">
                                    <div class="card-item-stat">
                                        <h4 class="fw-bold">$
                                            <?= number_format($current_agent_fee, 2) ?>
                                        </h4>
                                        <small>
                                            <b
                                                class="text-<?= ($status == 'Increased') ? 'success' : (($status == 'decreased') ? 'danger' : 'secondary') ?>">
                                                <?= abs(round($percentage_change, 2)) ?>%
                                            </b>
                                            <?= $status ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-4">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="card-item">
                                <div class="card-item-icon card-icon">
                                    <svg class="text-primary" xmlns="http://www.w3.org/2000/svg" height="24"
                                        viewBox="0 0 24 24" width="24">
                                        <path d="M0 0h24v24H0V0z" fill="none" />
                                        <path
                                            d="M12 4c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8zm1.23 13.33V19H10.9v-1.69c-1.5-.31-2.77-1.28-2.86-2.97h1.71c.09.92.72 1.64 2.32 1.64 1.71 0 2.1-.86 2.1-1.39 0-.73-.39-1.41-2.34-1.87-2.17-.53-3.66-1.42-3.66-3.21 0-1.51 1.22-2.48 2.72-2.81V5h2.34v1.71c1.63.39 2.44 1.63 2.49 2.97h-1.71c-.04-.97-.56-1.64-1.94-1.64-1.31 0-2.1.59-2.1 1.43 0 .73.57 1.22 2.34 1.67 1.77.46 3.66 1.22 3.66 3.42-.01 1.6-1.21 2.48-2.74 2.77z"
                                            opacity=".3" />
                                        <path
                                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.73-2.77-.01-2.2-1.9-2.96-3.66-3.42z" />
                                    </svg>
                                </div>
                                <div class="card-item-title  mb-2">
                                    <label class="main-content-label fs-13 fw-bold mb-1">Partner Commission
                                    </label>
                                    <span class="d-block fs-12 mb-0 text-muted">Previous month vs this
                                        months</span>
                                </div>
                                <div class="card-item-body">
                                    <div class="card-item-stat">
                                        <h4 class="fw-bold">$0.00</h4>
                                        <small><b class="text-danger">12%</b> decrease</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End::row -->

            <!-- Start::row -->
            <div class="row">
                <div class="col-sm-12 col-lg-12 col-xl-12">
                    <div class="card custom-card overflow-hidden">
                        <div class="card-header border-bottom-0">
                            <div>
                                <label class="card-title">Monthly Bookings</label>
                                <!-- <span
                                                class="d-block fs-12 mb-0 text-muted">The Project Budget is a tool
                                                used by project managers to estimate the total cost of a
                                                project</span> -->
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="project"></div>
                        </div>
                    </div>
                </div><!-- col end -->
                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                <div class="card custom-card overflow-hidden">
                        <div class="card-header d-block border-bottom-0 pb-0">
                            <div>
                                <div class="d-md-flex">
                                    <label class="main-content-label my-auto pt-2">Upcoming Commission</label>
                                    <div class="ms-auto mt-3 d-flex">
                                        <div class="me-3 d-flex text-muted fs-13"><span
                                                class="legend bg-primary rounded-circle"></span>Paid
                                        </div>
                                        <div class="d-flex text-muted fs-13"><span
                                                class="legend bg-light rounded-circle"></span>Pending
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6 my-auto">
                                    <h6 class="mb-3 fs-14 fw-normal">UPCOMING COMMISSION</h6>
                                    <div class="text-start">
                                        <?php
                                            $agent_fee_total = (float) ($db->sum("hotels_bookings", "agent_fee", [
                                                "agent_id" => $agent_id,
                                                "booking_status" => "confirmed",
                                                "payment_status" => "paid",
                                                // "agent_payment_status" => "pending",

                                                "booking_date[<>]" => [date("Y-m-01"), date("Y-m-t")]
                                            ]) ?? 0);
                                        ?>

                                        <h3 class="fw-bold me-3 mb-2 text-primary">$<?= $agent_fee_total?>
                                        </h3>
                                        <p class="fs-13 my-auto text-muted">
                                            <?php echo date("M d", strtotime("last day of previous month")) . " - " . date("M d (Y)"); ?>
                                        </p>
                                    </div>
                                </div>
                                <?php
                                
$total_paid_bookings = (int) ($db->count("hotels_bookings", [
    "agent_id" => $agent_id,
    "booking_status" => "confirmed",
    "payment_status" => "paid",
    "booking_date[<>]" => [date("Y-m-01"), date("Y-m-t")]
]) ?? 0);

$paid_commission_bookings = (int) ($db->count("hotels_bookings", [
    "agent_id" => $agent_id,
    "booking_status" => "confirmed",
    "payment_status" => "paid",
    "agent_payment_status" => "paid",
    "booking_date[<>]" => [date("Y-m-01"), date("Y-m-t")]
]) ?? 0);

$paid_commission_percentage = ($total_paid_bookings > 0) 
    ? round(($paid_commission_bookings / $total_paid_bookings) * 100, 2)
    : 0;
?>

                                <script>           

window.paidCommissionPercentage = <?= $paid_commission_percentage ?>;

                                </script>
                                <div class="col-md-6 my-auto">
                                    <div id="todaytask"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- col end -->
                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                <?php
$current_month_start = date('Y-m-01');
$current_month_end = date('Y-m-t');
$previous_month_start = date('Y-m-01', strtotime("first day of last month"));
$previous_month_end = date('Y-m-t', strtotime("last day of last month"));

$current_bookings = $db->select("hotels_bookings", ["email", "first_name", "last_name", "price_markup"], [
    "agent_id" => $agent_id,
    "booking_status" => "confirmed",
    "booking_date[<>]" => [$current_month_start, $current_month_end]
]);

$previous_bookings = $db->select("hotels_bookings", ["email", "price_markup"], [
    "agent_id" => $agent_id,
    "booking_status" => "confirmed",
    "booking_date[<>]" => [$previous_month_start, $previous_month_end]
]);

$client_markup = [];

foreach ($current_bookings as $booking) {
    $email = $booking["email"];
    
    if (!isset($client_markup[$email])) {
        $client_markup[$email] = [
            "total_markup" => 0,
            "first_name" => $booking["first_name"],
            "last_name" => $booking["last_name"],
            "previous_markup" => 0 
        ];
    }

    // Add price markup
    $client_markup[$email]["total_markup"] += $booking["price_markup"];
}

foreach ($previous_bookings as $booking) {
    $email = $booking["email"];

    if (isset($client_markup[$email])) {
        $client_markup[$email]["previous_markup"] += $booking["price_markup"];
    }
}

foreach ($client_markup as &$client) {
    $previous = $client["previous_markup"];
    $current = $client["total_markup"];

    $safe_previous = ($previous == 0) ? 1 : $previous;
    $change = (($current - $previous) / $safe_previous) * 100;

    $client["percent_change"] = max(min($change, 100), -100); 

    $client["percent_change"] = round($client["percent_change"], 1);
}

$unique_clients = array_values($client_markup);

usort($unique_clients, function ($a, $b) {
    return $b["total_markup"] <=> $a["total_markup"];
});

$top_clients = array_slice($unique_clients, 0, 3);

?>
<div class="card custom-card">
    <div class="card-header border-bottom-0 pb-0">
        <div>
            <div class="d-flex">
                <label class="main-content-label my-auto pt-2">Top Clients</label>
            </div>
            <span class="d-block fs-12 mt-2 mb-0 text-muted">Here are your three top clients.</span>
        </div>
    </div>
    <div class="card-body">
        <?php foreach ($top_clients as $client): ?>
            <?php 
                $arrow_class = ($client["percent_change"] >= 0) ? "text-success fe fe-arrow-up" : "text-danger fe fe-arrow-down"; 
                $progress_value = abs($client["percent_change"]); 
                $change_word = ($client["percent_change"] >= 0) ? "Increase" : "Decrease";
            ?>
            <div class="row mt-1">
                <div class="col-5">
                    <span><?= htmlspecialchars($client["first_name"] . ' ' . $client["last_name"]) ?></span>
                </div>
                <div class="col-3 my-auto">
                    <div class="progress ht-6 my-auto progress-animate">
                        <div class="progress-bar ht-6 wd-<?= $progress_value ?>p" role="progressbar" style="width: <?= $progress_value ?>%;"></div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="d-flex">
                        <span class="fs-13">
                            <i class="<?= $arrow_class ?>"></i>
                            <b><?= abs($client["percent_change"]) ?>%</b> <?= $change_word ?> 
                        </span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
                </div>
                <!-- col end -->
                <div class="col-lg-12">
                    <div class="card custom-card mg-b-20 tasks">
                        <div class="card-body">
                            <div class="card-header border-bottom-0 pt-0 ps-0 pe-0 pb-2 d-flex">
                                <div>
                                    <div class="card-title">RECENT RESERVATIONS</div>
                                </div>
                                <div class="">
                                    <div>
                                        <a href="./bookings.php" style="margin-left:12px !important;"
                                            class="btn text-white bg-black me-2" type="button">
                                            SEE ALL
                                            RESERVATIONS </a>

                                    </div>
                                </div>
                            </div>
                            <?php $reservation = $db->select('hotels_bookings', ['first_name', 'last_name', 'hotel_name', 'booking_date', 'booking_status'], ["agent_id" => $agent_id,"ORDER" => ["booking_id" => "DESC"]]); ?>
                            <div class="table-responsive tasks">
                                <table id="bookingTable" class="table table-striped table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Name</th>
                                            <th class="text-center">Hotel</th>
                                            <th class="text-center">Date</th>
                                            <th>Priority</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($reservation as $booking): ?>
                                        <tr>
                                            <td>
                                                <?php echo $booking['first_name'] . ' ' . $booking['last_name']; ?>
                                            </td>
                                            <td class="text-nowrap">
                                                <?php echo $booking['hotel_name']; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo date('M d', strtotime($booking['booking_date'])); ?>
                                            </td>
                                            <td class="text-secondary">Normal</td>
                                            <td>
                                                <?php
                                                                $status_classes = [
                                                                    'confirmed' => 'bg-primary',
                                                                    'pending' => 'bg-warning',
                                                                    'cancelled' => 'bg-danger'
                                                                ];
                                                                $status_class = $status_classes[$booking['booking_status']] ?? 'bg-secondary';
                                                                ?>
                                                <span class="badge rounded-pill <?php echo $status_class; ?>">
                                                    <?php echo ucfirst($booking['booking_status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                            <!-- DataTables JS -->
                            <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
                            <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

                            <script>
                                $(document).ready(function () {
                                    $('#bookingTable').DataTable({
                                        "paging": true,
                                        "searching": true,
                                        "ordering": true,
                                        "info": true,
                                        "lengthChange": false,
                                        "pageLength": 4
                                    });
                                });
                            </script>

                        </div>
                    </div>

                </div>
                <!-- col end -->
            </div>
            <!-- End::row -->

        </div><!-- col end -->

        <div class="col-sm-12 col-lg-12 col-xl-4 banner-img">
            <div class="card custom-card card-dashboard-calendar">
                <label class="main-content-label mb-2 pt-1">Recent Sales</label>
                <span class="d-block fs-12 mb-2 text-muted">Hare are the last 5 sales you've made
                </span>
                <table class="table m-b-0 transcations mt-2">
                    <tbody>
                        <?php
                        $recent_sales = $db->select('hotels_bookings', '*', [
                            "agent_id" => $agent_id,
                            "booking_status" => "confirmed",
                            "LIMIT" => 5,
                            "ORDER" => ["booking_id" => "DESC"]
                        ]);
                                        foreach ($recent_sales as $r_sales) {
                                            echo ' 
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-middle ms-3">
                                                        <div class="d-inline-block">
                                                            <h6 class="mb-1">' . $r_sales['first_name'] . ' ' . $r_sales['last_name'] . '</h6>
                                                            <p class="mb-0 fs-13 text-muted">' . $r_sales['location'] . '</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-inline-block">
                                                        <h6 class="mb-2 fs-15 fw-semibold">$' . number_format($r_sales['price_markup'], 3, '.', '') . '</h6>
                                                        <p class="mb-0 tx-11 text-muted">' . date('d M Y', strtotime($r_sales['booking_date'])) . '</p>
                                                    </div>
                                                </td>
                                            </tr>';
                                        }
                                    ?>
                    </tbody>
                </table>
            </div>
            <div class="card custom-card">
                <div class="card-body">
                    <div class="card-item">
                        <div class="card-item-icon card-icon">
                            <img src="../assets/img/agent/bost.png" alt="bost-img" style="height:50px;">
                        </div>
                        <div class="card-item-title mb-2">
                            <label class="main-content-label fs-13 fw-bold mb-1">Share Your Travel Link With Clients

                            </label>
                            <span class="d-block fs-12 mb-0 text-muted">Share your travel link with your network!</span>
                        </div>
                        <a href="https://toptiertravel.site/signup?ref=<?=$agent_id?>&type=client"
                            class="mb-0 fs-18 mt-2"><b class="text-primary">https://toptiertravel.site/signup?ref=<?=$agent_id?>&type=client
                            </b></a>
                    </div>
                </div>
            </div>
            <?php
            $current_month_start = date('Y-m-01'); // First day of the current month
            $current_month_end = date('Y-m-t'); // Last day of the current month

            $previous_month_start = date('Y-m-01', strtotime('first day of last month')); // First day of the previous month
            $previous_month_end = date('Y-m-t', strtotime('last day of last month')); // Last day of the previous month

            ?>

            <div class="card custom-card">
                <div class="card-header border-bottom-0 pb-0">
                    <div>
                        <div class="d-flex">
                            <label class="main-content-label my-auto pt-2">Top Destinations</label>
                        </div>
                        <span class="d-block fs-12 mt-2 mb-0 text-muted">Here are your three top destinations.</span>
                    </div>
                </div>
                <?php
                    $top_dest_current = $db->query("SELECT location, COUNT(*) as booking_count 
                    FROM hotels_bookings 
                    WHERE agent_id = :agent_id AND booking_status = 'confirmed' 
                    AND booking_date BETWEEN :current_month_start AND :current_month_end
                    GROUP BY location 
                    ORDER BY booking_count DESC 
                    LIMIT 3", [
                    ':agent_id' => $agent_id,
                    ':current_month_start' => $current_month_start,
                    ':current_month_end' => $current_month_end
                    ])->fetchAll(PDO::FETCH_ASSOC);
                    
                    $top_dest_previous = $db->query("SELECT location, COUNT(*) as booking_count 
                    FROM hotels_bookings 
                    WHERE agent_id = :agent_id AND booking_status = 'confirmed' 
                    AND booking_date BETWEEN :previous_month_start AND :previous_month_end
                    GROUP BY location", [
                    ':agent_id' => $agent_id,
                    ':previous_month_start' => $previous_month_start,
                    ':previous_month_end' => $previous_month_end
                    ])->fetchAll(PDO::FETCH_ASSOC);
                    
                    $previous_bookings_map = [];
                    foreach ($top_dest_previous as $prev) {
                        $previous_bookings_map[$prev['location']] = $prev['booking_count'] ?? 0;
                    }
                    
                    foreach ($top_dest_current as &$current) {
                        $location = $current['location'];
                        $current['previous_booking_count'] = $previous_bookings_map[$location] ?? 0;
                    }
                ?>
                <div class="card-body">
                    <?php foreach ($top_dest_current as $destination) {
                                        $location = $destination['location'];
                                        $current_count = $destination['booking_count'];
                                        $previous_count = isset($previous_bookings_map[$location]) ? $previous_bookings_map[$location] : 0;
                                        
                                        // Calculate percentage change
                                        if ($previous_count > 0) {
                                            $percentage_change = (($current_count - $previous_count) / $previous_count) * 100;
                                        } else {
                                            $percentage_change = $current_count > 0 ? 100 : 0; // If there were no previous bookings, assume 100% growth
                                        }
                                        
                                        $arrow_class = $percentage_change >= 0 ? 'text-success fe fe-arrow-up' : 'text-danger fe fe-arrow-down';
                                        $formatted_percentage_change = number_format(abs($percentage_change), 2, '.', '');
                                        
                                        // Calculate progress bar width
                                        $progress_width = min($current_count * 10, 100); 
                                    ?>
                    <div class="row mt-4">
                        <div class="col-5">
                            <span class="">
                                <?php echo $location; ?>
                            </span>
                        </div>
                        <div class="col-3 my-auto">
                            <div class="progress ht-6 my-auto progress-animate">
                                <div class="progress-bar ht-6 wd-<?php echo $progress_width; ?>p" role="progressbar"
                                    aria-valuenow="<?php echo $progress_width; ?>" aria-valuemin="0"
                                    aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="d-flex">
                                <span class="fs-13">
                                    <i class="<?php echo $arrow_class; ?>"></i><b>
                                        <?php echo $formatted_percentage_change; ?>%
                                    </b>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <div class="card custom-card">
                <div class="card-body">
                    <div class="card-item">
                        <div class="card-item-icon card-icon">
                            <img src="../assets/img/agent/bost.png" alt="bost-img" style="height:50px;">
                        </div>
                        <div class="card-item-title mb-2">
                            <label class="main-content-label fs-13 fw-bold mb-1">Share Your Referral Link With Your
                                Partners

                            </label>
                            <span class="d-block fs-12 mb-0 text-muted">Build a network of partners and earn a
                                commission for every sale!</span>
                        </div>
                        <a href="https://toptiertravel.site/signup?ref=<?=$agent_id?>&type=partner" class="mb-0 fs-18 mt-2"><b
                                class="text-primary">https://toptiertravel.site/signup?ref=<?=$agent_id?>&type=partner</b></a>
                    </div>
                </div>
            </div>
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex">
                        <label class="main-content-label my-auto">Monthly Commission
                        </label>
                        <div class="ms-auto  d-flex">
                            <div class="me-3 d-flex text-muted fs-13">Running</div>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div>

                        </div>
                        <div id="websitedesign"></div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="mt-4">
                                <div class="d-flex mb-2">
                                    <h5 class="fs-15 my-auto text-muted fw-normal">Agent :
                                    </h5>
                                    <h5 class="fs-15 my-auto ms-3">
                                        <?=$user[0]['first_name'].' '.$user[0]['last_name']?>
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="col col-auto">
                            <div class="mt-3">
                                <div class="">
                                    <img alt="" class="ht-50" src="../assets/img/agent/client.png">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- col end -->
    </div>
    <!-- End::row-1 -->

</div>

<div class="d-none" id="ongoingprojects"></div>
<div class="d-none" id="ongoingprojects2"></div>
<?php include "_footer.php" ?>