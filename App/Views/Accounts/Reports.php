<div class="container-fluid">
<div class="row g-3">
<?php
$profile_data=$meta['data'];
?>

<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-1.13.8/sl-1.7.0/datatables.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-1.13.8/sl-1.7.0/datatables.min.js"></script>

<div class="col-md-2">
    <?php require "Sidebar.php" ?>
    </div>

    <!-- ================================
    START PROFILE AREA
    ================================= -->
    <section class="col-md-10">

            <div class="">
            <div class="px-0 py-3">
                <div class="row">
                    <div class="">
                        <div class="form-box">
                            <div class="form-title-wrap border-bottom-0 pb-0">
                                <h5 class="fw-bold mb-3"><?= T::reports ?></h5>
                            </div>
                            <hr>

                            <div class="p-4 pt-0">

                            <?php // echo $meta['year'] ?>

                            <a href="<?=root."reports/".date("Y",strtotime("-2 year"))?>">
                            <button type="button" class="btn <?php if ($meta['year']==date("Y",strtotime("-2 year"))){ echo "btn-dark"; } else { echo "btn-primary"; } ?>"><?=date("Y",strtotime("-2 year"))?></button>
                            </a>

                            <a href="<?=root."reports/".date("Y",strtotime("-1 year"))?>">
                            <button type="button" class="btn <?php if ($meta['year']==date("Y",strtotime("-1 year"))){ echo "btn-dark"; } else { echo "btn-primary"; } ?>"><?=date("Y",strtotime("-1 year"))?></button>
                            </a>

                            <a href="<?=root."reports/".date("Y")?>">
                            <button type="button" class="btn <?php if ($meta['year']==date("Y")){ echo "btn-dark"; } else { echo "btn-primary"; } ?>"><?=date("Y")?></button>
                            </a>

                            <hr>

                        <div class="accordion" id="accordionExample">
                        <?php

                            // Get the current year
                            $currentYear = $meta['year'];

                            // Loop through each month
                            for ($month = 1; $month <= 12; $month++) {
                                // Get the first and last day of the month
                                $firstDay = date('Y-m-d', strtotime("$currentYear-$month-01"));
                                $lastDay = date('Y-m-t', strtotime("$currentYear-$month-01"));

                                $start_date = new DateTime($firstDay);
                                $end_date = new DateTime($lastDay);
                                $date_range = new DatePeriod($start_date, new DateInterval('P1D'), $end_date->modify('+1 day'));
                                ?>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne<?=date('F', strtotime($firstDay))?>" aria-expanded="true" aria-controls="collapseOne<?=date('F', strtotime($firstDay))?>">
                                <?=date('F', strtotime($firstDay)). ' ' .$currentYear?>
                            </button>
                            </h2>
                            <div id="collapseOne<?=date('F', strtotime($firstDay))?>" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                            <div class="accordion-body">

                                   <table class="table table-striped table-bordered align-middle text-capitalized dataTable no-footer">
                                    <thead>
                                        <tr>
                                            <th><strong>Sales Date</strong></th>

                                            <th class="table-primary">Hotel Booked</th>
                                            <th class="table-primary">Hotel Sales</th>
                                            <th class="table-primary">Hotel Commission</th>

                                            <th class="table-info">Flights Booked</th>
                                            <th class="table-info">Flights Sales</th>
                                            <th class="table-info">Flights Commission</th>

                                            <th class="table-success">Tours Booked</th>
                                            <th class="table-success">Tours Sales</th>
                                            <th class="table-success">Tours Commission</th>

                                            <th class="table-warning">Cars Booked</th>
                                            <th class="table-warning">Cars Sales</th>
                                            <th class="table-warning">Cars Commission</th>


                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $flight_booked = 0;
                                    $flights_sales = 0;
                                    $flights_commission = 0;

                                    $hotel_booked = 0;
                                    $hotel_sales = 0;
                                    $hotel_commission = 0;

                                    $tours_booked = 0;
                                    $tours_sales = 0;
                                    $tours_commission = 0;


                                    $cars_booked = 0;
                                    $cars_sales = 0;
                                    $cars_commission = 0;
                                    foreach ($date_range as $date) {
                                        foreach ($profile_data as $value){
                                            if($date->format('Y-m-d') == $value->booking_date){

                                                $flight_booked+= $value->flights_booked;
                                                $flights_sales+= $value->flights_sales;
                                                $flights_commission+= $value->flights_commission;


                                                $hotel_booked+= $value->hotel_booked;
                                                $hotel_sales+= $value->hotel_sales;
                                                $hotel_commission+= $value->hotel_commission;


                                                $tours_booked+= $value->tours_booked;
                                                $tours_sales+= $value->tours_sales;
                                                $tours_commission+= $value->tours_commission;


                                                $cars_booked+= $value->cars_booked;
                                                $cars_sales+= $value->cars_sales;
                                                $cars_commission+= $value->cars_commission;
                                        ?>
                                        <tr>
                                            <th style="width:60px"><?=$value->booking_date?></th>

                                            <th class="table-primary"><?=$value->hotel_booked?></th>
                                            <th class="table-primary"><?=$value->hotel_sales?></th>
                                            <th class="table-primary"><?=$value->hotel_commission?></th>

                                            <th class="table-info"><?=$value->flights_booked?></th>
                                            <th class="table-info"><?=$value->flights_sales?></th>
                                            <th class="table-info"><?=$value->flights_commission?></th>

                                            <th class="table-success"><?=$value->tours_booked?></th>
                                            <th class="table-success"><?=$value->tours_sales?></th>
                                            <th class="table-success"><?=$value->tours_commission?></th>

                                            <th class="table-warning"><?=$value->cars_booked?></th>
                                            <th class="table-warning"><?=$value->cars_sales?></th>
                                            <th class="table-warning"><?=$value->cars_commission?></th>

                                        </tr>
                                    <?php } } }?>
                                        <tr class="bg-dark text-white border-0">
                                            <th><strong class="text-white">TOTAL</strong></th>
                                            <th class="table-primary bg-dark text-white border-0"><?=($hotel_booked !== 0) ? $hotel_booked : '';?></th>
                                            <th class="table-primary bg-dark text-white border-0"><?=($hotel_sales !== 0) ? $hotel_sales : '';?></th>
                                            <th class="table-primary bg-dark text-white border-0"><?=($hotel_commission !== 0) ? $hotel_commission : '';?></th>
                                            <th class="table-info bg-dark text-white border-0"><?=($flight_booked !== 0) ? $flight_booked : '';?></th>
                                            <th class="table-info bg-dark text-white border-0"><?=($flights_sales !== 0) ? $flights_sales : '';?></th>
                                            <th class="table-info bg-dark text-white border-0"><?=($flights_commission !== 0) ? $flights_commission : '';?></th>
                                            <th class="table-success bg-dark text-white border-0"><?=($tours_booked !== 0) ? $tours_booked : '';?></th>
                                            <th class="table-success bg-dark text-white border-0"><?=($tours_sales !== 0) ? $tours_sales : '';?></th>
                                            <th class="table-success bg-dark text-white border-0"><?=($tours_commission !== 0) ? $tours_commission : '';?></th>
                                            <th class="table-warning bg-dark text-white border-0"><?=($cars_booked !== 0) ? $hotel_booked : '';?></th>
                                            <th class="table-warning bg-dark text-white border-0"><?=($cars_sales !== 0) ? $cars_sales : '';?></th>
                                            <th class="table-warning bg-dark text-white border-0"><?=($cars_commission !== 0) ? $cars_commission : '';?></th>
                                          </tr>
                                    </tbody>
                                   </table>
                        </div>
                            </div>
                        </div>

                        <?php


                                // echo "<table border='1'>";
                                // echo "<tr><th>Date</th><th>Data</th></tr>";

                                //     for ($currentDay = strtotime($firstDay); $currentDay <= strtotime($lastDay); $currentDay += 86400) {
                                //         $currentDate = date('Y-m-d', $currentDay);
                                //         $dayData = 'YourDataForDay';
                                //         echo "<tr><td>$currentDate</td><td>$dayData</td></tr>";
                                //     }

                                // echo "</table>";
                            }
                            ?>



                        </div>







                            <?php

                            // if (isset($meta['data'])){ $bookings = array_merge($meta['data']->flights,$meta['data']->hotels,$meta['data']->tours,$meta['data']->cars);} else { $bookings = []; }

                            //     // print_r($bookings);

                            //     usort($bookings, function ($a, $b) {
                            //         return (int)$b->booking_date - (int)$a->booking_date;
                            //     });

                            //     foreach ($bookings as $booking) {
                            //         echo $booking->booking_date.' ';
                            //         echo $booking->module_type.' ';
                            //         echo $booking->booking_ref_no;
                            //         echo "<hr />";
                            //      }

                            ?>




                            </div>
                        </div><!-- end form-box -->
                    </div><!-- end col-lg-12 -->
                </div><!-- end row -->
            </div>
        </div>
</div>
</section>
</div>
<style>
    .newsletter-section {display:none}
</style>

<script>
    $(document).ready(function() {
        var table = $('table').DataTable({
            "ordering": false // Disable sorting
        });
    });
</script>