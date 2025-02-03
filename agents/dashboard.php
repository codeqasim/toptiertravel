<?php
require_once '_config.php';
auth_check();
$title = "Agent Dashboard";
include "_header.php";

$user_id = $USER_SESSION->backend_user_id;

$agent_id = '20230311051923100';

$user = $db->select("users", '*', [
    "user_id" => $agent_id,
]);

$start_date = date('Y-m-01');
$end_date = date('Y-m-t');

$current_month_start = date('Y-m-01');
$current_month_end = date('Y-m-t');
$previous_month_start = date('Y-m-01', strtotime("first day of last month"));
$previous_month_end = date('Y-m-t', strtotime("last day of last month"));

$current_month_bookings = $db->select("hotels_bookings", '*', [
    "agent_id" => $agent_id,
    "booking_status" => "confirmed",
    "booking_date[<>]" => [$current_month_start, $current_month_end]
]);

$current_month_sale_rev = number_format(array_sum(array_column($current_month_bookings, 'price_original')), 1, '.', '');
$current_month_agent_fee_percentage = array_sum(array_column($current_month_bookings, 'agent_fee'));
$current_month_agent_fee = number_format(($current_month_sale_rev * $current_month_agent_fee_percentage) / 100, 1, '.', '');

$previous_month_bookings = $db->select("hotels_bookings", '*', [
    "agent_id" => $agent_id,
    "booking_status" => "confirmed",
    "booking_date[<>]" => [$previous_month_start, $previous_month_end]
]);

$previous_month_sale_rev = number_format(array_sum(array_column($previous_month_bookings, 'price_original')), 1, '.', '');
$previous_month_agent_fee_percentage = array_sum(array_column($previous_month_bookings, 'agent_fee'));
$previous_month_agent_fee = number_format(($previous_month_sale_rev * $previous_month_agent_fee_percentage) / 100, 1, '.', '');

// Ensure previous month sale revenue is never zero
$safe_previous_month_sale_rev = ($previous_month_sale_rev == 0) ? 1 : $previous_month_sale_rev;
$sale_rev_difference = $current_month_sale_rev - $previous_month_sale_rev;
$sale_rev_percent_change = ($sale_rev_difference / $safe_previous_month_sale_rev) * 100;
$sale_rev_percent_change = min($sale_rev_percent_change, 100);
$formatted_sale_rev_percent_change = number_format($sale_rev_percent_change, 1, '.', '');

// Ensure previous month agent fee is never zero
$safe_previous_month_agent_fee = ($previous_month_agent_fee == 0) ? 1 : $previous_month_agent_fee;
$agent_fee_difference = $current_month_agent_fee - $previous_month_agent_fee;
$agent_fee_percent_change = ($agent_fee_difference / $safe_previous_month_agent_fee) * 100;
$agent_fee_percent_change = min($agent_fee_percent_change, 100);
$formatted_agent_fee_percent_change = number_format($agent_fee_percent_change, 1, '.', '');

$desired_sale_rev = $previous_month_sale_rev * 2;  
$desired_agent_fee = $previous_month_agent_fee * 2;

$recent_sales = $db->select('hotels_bookings', '*', [
    "agent_id" => $agent_id,
    "booking_status" => "confirmed",
    "LIMIT" => 5,
    "ORDER" => ["booking_id" => "DESC"]
]);

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

// for pending and paid agent commision 

$total_confirmed_bookings = count($current_month_bookings);

$commission_status_counts = array_count_values(array_column($current_month_bookings, 'agent_commission_status'));

$paid_commission_bookings = $commission_status_counts['paid'] ?? 0;

$paid_commission_percentage = ($total_confirmed_bookings > 0) 
    ? ($paid_commission_bookings / $total_confirmed_bookings) * 100 
    : 0;

$formatted_paid_commission_percentage = number_format($paid_commission_percentage, 1);

// for pending and paid agent commision 


// for upcoming commission 

$current_month_price_markup_total = array_sum(array_column($current_month_bookings, 'price_markup'));

$current_month_agent_fee_percentage_total = array_sum(array_column($current_month_bookings, 'agent_fee'));

$current_month_agent_fee_total = number_format(($current_month_price_markup_total * $current_month_agent_fee_percentage_total) / 100, 1, '.', '');

// for upcoming commission 


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
        "booking_date[<>]" => [$start_date, $end_date]
    ]);
    $month_name = date('F', strtotime($start_date));
    $monthly_booking_counts[$month_name] = count($bookings);
}

// Get only the counts from the array and reindex
$booking_counts_string = "[" . implode(", ", array_values($monthly_booking_counts)) . "]";

// exit;
// for each month booking
?>

<script>
    window.paidCommissionPercentage = <?= $formatted_paid_commission_percentage ?>;
    window.permonthbookingcounts = <?= $booking_counts_string ?>;

    
</script>
<div class="container-fluid">

                <!-- Start::page-header -->

                <div class="d-md-flex d-block align-items-center justify-content-between mt-4">
                    <div>
                        <h2 class="main-content-title fs-24 mb-1">Welcome To Top Tier Travel</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Agent Dashboard</li>
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
                                                    <span class="fw-bold text-fixed-white ">Hello <?=$user[0]['first_name'].' '.$user[0]['last_name']?></span>
                                                </h4>
                                                <p class="tx-white-7 mb-1">The world is a book, and those who do not travel read only one page."
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
                                            <svg class="text-primary" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                                                <path d="M0 0h24v24H0V0z" fill="none" />
                                                <path d="M12 4c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8zm1.23 13.33V19H10.9v-1.69c-1.5-.31-2.77-1.28-2.86-2.97h1.71c.09.92.72 1.64 2.32 1.64 1.71 0 2.1-.86 2.1-1.39 0-.73-.39-1.41-2.34-1.87-2.17-.53-3.66-1.42-3.66-3.21 0-1.51 1.22-2.48 2.72-2.81V5h2.34v1.71c1.63.39 2.44 1.63 2.49 2.97h-1.71c-.04-.97-.56-1.64-1.94-1.64-1.31 0-2.1.59-2.1 1.43 0 .73.57 1.22 2.34 1.67 1.77.46 3.66 1.22 3.66 3.42-.01 1.6-1.21 2.48-2.74 2.77z" opacity=".3" />
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.73-2.77-.01-2.2-1.9-2.96-3.66-3.42z" />
                                            </svg>
                                        </div>
                                        <div class="card-item-title mb-2">
                                            <label class="main-content-label fs-13 fw-bold mb-1">Total Sales Revenue</label>
                                            <span class="d-block fs-12 mb-0 text-muted">Previous month vs this months</span>
                                        </div>
                                        <div class="card-item-body">
                                            <div class="card-item-stat">
                                                <h4 class="fw-bold">$<?php echo $current_month_sale_rev; ?></h4>
                                                <small>
                                                    <b class="<?php echo ($sale_rev_percent_change > 0) ? 'text-success' : 'text-danger'; ?>">
                                                        <?php echo number_format(abs($sale_rev_percent_change), 2); ?>%
                                                    </b>
                                                    <?php echo ($sale_rev_percent_change > 0) ? 'higher' : 'lower'; ?>
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
                    <svg class="text-primary" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                        <path d="M0 0h24v24H0V0z" fill="none" />
                        <path d="M12 4c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8zm1.23 13.33V19H10.9v-1.69c-1.5-.31-2.77-1.28-2.86-2.97h1.71c.09.92.72 1.64 2.32 1.64 1.71 0 2.1-.86 2.1-1.39 0-.73-.39-1.41-2.34-1.87-2.17-.53-3.66-1.42-3.66-3.21 0-1.51 1.22-2.48 2.72-2.81V5h2.34v1.71c1.63.39 2.44 1.63 2.49 2.97h-1.71c-.04-.97-.56-1.64-1.94-1.64-1.31 0-2.1.59-2.1 1.43 0 .73.57 1.22 2.34 1.67 1.77.46 3.66 1.22 3.66 3.42-.01 1.6-1.21 2.48-2.74 2.77z" opacity=".3" />
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.73-2.77-.01-2.2-1.9-2.96-3.66-3.42z" />
                    </svg>
                </div>
                <div class="card-item-title mb-2">
                    <label class="main-content-label fs-13 fw-bold mb-1">Total Commission</label>
                    <span class="d-block fs-12 mb-0 text-muted">Total Commission You Earned</span>
                </div>
                <div class="card-item-body">
                    <div class="card-item-stat">
                        <h4 class="fw-bold">$<?php echo $current_month_agent_fee; ?></h4>
                        <small>
                            <b class="<?php echo ($agent_fee_percent_change > 0) ? 'text-success' : 'text-danger'; ?>">
                                <?php echo number_format(abs($agent_fee_percent_change), 2); ?>%
                            </b>
                            <?php echo ($agent_fee_percent_change > 0) ? 'Increased' : 'Decreased'; ?>
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
                                                <svg class="text-primary" xmlns="http://www.w3.org/2000/svg"
                                                    height="24" viewBox="0 0 24 24" width="24">
                                                    <path d="M0 0h24v24H0V0z" fill="none" />
                                                    <path
                                                        d="M12 4c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8zm1.23 13.33V19H10.9v-1.69c-1.5-.31-2.77-1.28-2.86-2.97h1.71c.09.92.72 1.64 2.32 1.64 1.71 0 2.1-.86 2.1-1.39 0-.73-.39-1.41-2.34-1.87-2.17-.53-3.66-1.42-3.66-3.21 0-1.51 1.22-2.48 2.72-2.81V5h2.34v1.71c1.63.39 2.44 1.63 2.49 2.97h-1.71c-.04-.97-.56-1.64-1.94-1.64-1.31 0-2.1.59-2.1 1.43 0 .73.57 1.22 2.34 1.67 1.77.46 3.66 1.22 3.66 3.42-.01 1.6-1.21 2.48-2.74 2.77z"
                                                        opacity=".3" />
                                                    <path
                                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.73-2.77-.01-2.2-1.9-2.96-3.66-3.42z" />
                                                </svg>
                                            </div>
                                            <div class="card-item-title  mb-2">
                                                <label class="main-content-label fs-13 fw-bold mb-1">partner
                                                 Commission</label>
                                                <span class="d-block fs-12 mb-0 text-muted">Previous month vs this
                                                    months</span>
                                            </div>
                                            <div class="card-item-body">
                                                <div class="card-item-stat">
                                                    <h4 class="fw-bold">$8,500</h4>
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
                                                <label class="main-content-label my-auto pt-2">Commission Paid</label>
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
                                                <h6 class="mb-3 fs-14 fw-normal">UPCOMIG COMMISSION</h6>
                                                <div class="text-start">
                                                    <h3 class="fw-bold me-3 mb-2 text-primary">$<?=$current_month_agent_fee_total?></h3>
                                                    <p class="fs-13 my-auto text-muted">
                                                        <?php echo date("M d", strtotime("last day of previous month")) . " - " . date("M d (Y)"); ?>
                                                    </p>

                                                </div>
                                            </div>
                                            <div class="col-md-6 my-auto">
                                                <div id="todaytask"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- col end -->
                            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                            <div class="card custom-card">
                                    <div class="card-header  border-bottom-0 pb-0">
                                        <div>
                                            <div class="d-flex">
                                                <label class="main-content-label my-auto pt-2">Top Clients</label>
                                            </div>
                                            <span class="d-block fs-12 mt-2 mb-0 text-muted">Hare are your three top clients. </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mt-4">
                                            <div class="col-5">
                                                <span class="">John Doe</span>
                                            </div>
                                            <div class="col-3 my-auto">
                                                <div class="progress ht-6 my-auto progress-animate">
                                                    <div class="progress-bar ht-6 wd-70p" role="progressbar"
                                                        aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="d-flex">
                                                    <span class="fs-13"><i
                                                            class="text-danger fe fe-arrow-down"></i><b>12.34%</b></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-4">
                                            <div class="col-5">
                                                <span class="">John Doe</span>
                                            </div>
                                            <div class="col-3 my-auto">
                                                <div class="progress ht-6 my-auto progress-animate">
                                                    <div class="progress-bar ht-6 wd-40p" role="progressbar"
                                                        aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="d-flex">
                                                    <span class="fs-13"><i
                                                            class="text-success  fe fe-arrow-up"></i><b>12.75%</b></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                            </div><!-- col end -->
                            <div class="col-lg-12">
                                <div class="card custom-card mg-b-20 tasks">
                                    <div class="card-body">
                                        <div class="card-header border-bottom-0 pt-0 ps-0 pe-0 pb-2 d-flex">
                                            <div>
                                                <div class="card-title">RECENT RESERVATIONS</div>
                                            </div>
                                            <div class="">
                                            <div  >
                                            <a href="./bookings.php" style = "margin-left:12px !important;"
                                                    class="btn text-white bg-black me-2"
                                                    type="button"
                                                >
                                                SEE ALL
                                                RESERVATIONS </a>
                                            
                                            </div>
                                            </div>
                                            <div class="ms-auto d-flex flex-wrap gap-2">
                                                <div class="contact-search3 me-3 ">
                                                    <button type="button" class="btn border-0"><i class="fe fe-search fw-semibold text-muted" aria-hidden="true"></i></button>
                                                    <input type="text" class="form-control h-6" id="typehead1" placeholder="Search here..." autocomplete="off">
                                                </div>
                                                <div class="ms-auto d-flex dropdown">
                                                    <a href="javascript:void(0);" class="btn dropdown-toggle btn-sm btn-wave waves-effect waves-light btn-primary d-inline-flex align-items-center" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-equalizer-line me-1"></i>Sort by</a>
                                                    <!-- <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                        <li><a class="dropdown-item" href="javascript:void(0);">Task</a></li>
                                                        <li><a class="dropdown-item" href="javascript:void(0);">Team</a></li>
                                                        <li><a class="dropdown-item" href="javascript:void(0);">Status</a></li>
                                                        <li class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item" href="javascript:void(0);"><i class="fa fa-cog me-2"></i>Settings</a></li>
                                                    </ul> -->
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        $reservation = $db->select('hotels_bookings', ['first_name', 'last_name', 'hotel_name', 'booking_date', 'booking_status'], [
                                            "agent_id" => $agent_id,
                                            "LIMIT" => 4,
                                            "ORDER" => ["booking_id" => "DESC"]
                                        ]);
                                        ?>

                                        <div class="table-responsive tasks">
                                            <table class="table card-table table-vcenter text-nowrap mb-0 border">
                                                <thead>
                                                    <tr>
                                                        <th class="wd-lg-10p">Name</th>
                                                        <th class="wd-lg-20p text-center">Hotel</th>
                                                        <th class="wd-lg-20p text-center">Date</th>
                                                        <th class="wd-lg-20p">Priority</th>
                                                        <th class="wd-lg-20p">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($reservation as $booking): ?>
                                                        <tr>
                                                            <td class="fw-medium">
                                                                <div class="form-check">
                                                                    <label class="form-check-label"><?php echo $booking['first_name'] . ' ' . $booking['last_name']; ?></label>
                                                                </div>
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
                                                                    'confirmed' => 'bg-primary-transparent',
                                                                    'pending' => 'bg-warning-transparent',
                                                                    'cancelled' => 'bg-danger-transparent'
                                                                ];
                                                                $status_class = $status_classes[$booking['booking_status']] ?? 'bg-secondary-transparent';
                                                                ?>
                                                                <span class="badge bg-pill rounded-pill <?php echo $status_class; ?>">
                                                                    <?php echo ucfirst($booking['booking_status']); ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="float-end mt-3">
                                            <nav aria-label="Page navigation" class="pagination-style-3">
                                                <ul class="pagination mb-0 flex-wrap">
                                                    <li class="page-item disabled">
                                                        <a class="page-link" href="javascript:void(0);">
                                                            Prev
                                                        </a>
                                                    </li>
                                                    <li class="page-item active"><a class="page-link" href="javascript:void(0);">1</a></li>
                                                    <li class="page-item"><a class="page-link" href="javascript:void(0);">2</a></li>
                                                    <li class="page-item">
                                                        <a class="page-link" href="javascript:void(0);">
                                                            <i class="bi bi-three-dots"></i>
                                                        </a>
                                                    </li>
                                                    <li class="page-item"><a class="page-link" href="javascript:void(0);">16</a></li>
                                                    <li class="page-item">
                                                        <a class="page-link text-primary" href="javascript:void(0);">
                                                            next
                                                        </a>
                                                    </li>
                                                </ul>
                                            </nav>
                                        </div>
                                    </div>
                                </div>

                            </div><!-- col end -->
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
                                            <a href="https://toptiertravel.site/" class="mb-0 fs-18 mt-2"><b class="text-primary">https://toptiertravel.site/</b></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card custom-card">
    <div class="card-header border-bottom-0 pb-0">
        <div>
            <div class="d-flex">
                <label class="main-content-label my-auto pt-2">Top Destinations</label>
            </div>
            <span class="d-block fs-12 mt-2 mb-0 text-muted">Here are your three top destinations.</span>
        </div>
    </div>
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
                <span class=""><?php echo $location; ?></span>
            </div>
            <div class="col-3 my-auto">
                <div class="progress ht-6 my-auto progress-animate">
                    <div class="progress-bar ht-6 wd-<?php echo $progress_width; ?>p" role="progressbar"
                        aria-valuenow="<?php echo $progress_width; ?>" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="d-flex">
                    <span class="fs-13">
                        <i class="<?php echo $arrow_class; ?>"></i><b><?php echo $formatted_percentage_change; ?>%</b>
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
                                                <label class="main-content-label fs-13 fw-bold mb-1">Share Your Referral Link With Your Partners

                                                </label>
                                                <span class="d-block fs-12 mb-0 text-muted">Build a network of partners and earn a commission for every sale!</span>
                                            </div>
                                            <a href="https://toptiertravel.site/" class="mb-0 fs-18 mt-2"><b class="text-primary">https://toptiertravel.site/</b></a>
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
                                                <h5 class="fs-15 my-auto text-muted fw-normal">Client :
                                                </h5>
                                                <h5 class="fs-15 my-auto ms-3">John Deo</h5>
                                            </div>
                                            <div class="d-flex mb-0">
                                                <h5 class="fs-13 my-auto text-muted fw-normal">Deadline :
                                                </h5>
                                                <h5 class="fs-13 my-auto text-muted ms-2">25 Dec 2020</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col col-auto">
                                        <div class="mt-3">
                                            <div class="">
                                                <img alt="" class="ht-50"
                                                    src="../assets/img/agent/client.png">
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
