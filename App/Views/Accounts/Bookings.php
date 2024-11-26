
<div class="container-fluid">
<div class="row g-3">

<div class="col-md-2">
<?php require "Sidebar.php";
// if (isset($meta['data']->flights)){ $flights_bookings = $meta['data']->flights; }
// if (isset($meta['data']->hotels)){ $hotels_bookings = $meta['data']->hotels; }
// if (isset($meta['data']->tours)){ $tours_bookings = $meta['data']->tours; }
// if (isset($meta['data']->cars)){ $cars_bookings = $meta['data']->cars; }
// if (isset($meta['data']->visa)){ $visa_bookings = $meta['data']->visa; }
if (isset($meta['data'])){ $booking_array = array_merge($meta['data']->flights,$meta['data']->hotels,$meta['data']->tours,$meta['data']->cars);} else { $booking_array = []; }
?>

</div>

<!-- ================================
       START DASHBOARD NAV
================================= -->
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-1.13.8/sl-1.7.0/datatables.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-1.13.8/sl-1.7.0/datatables.min.js"></script>

<!-- ================================
    START DASHBOARD AREA
================================= -->

    <section class="col-md-10">
    <div class="px-0 py-3">

    <div class="bg-white border rounded-2">
    <div class="container-fluid p-4">

    <!-- FLIGHTS -->
    <div class="bg-white" id="flights">
    <div class="form-title-wrap p-0 mb-2">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h3 class="title mb-3"><?=T::bookings?></h3>
                <h3 class="title"><?//=T::flights.' '.T::bookings?></h3>
            </div>
        </div>
    </div>

    <div class="table-responsive">
    <table class="table table-striped table-bordered align-middle text-capitalized" style="width:100%">
    <thead>
        <tr>
        <th>Cient <?=T::name?></th>
        <th><?=T::booking?> <?=T::id?></th>
        <th><?=T::pnr?></th>
        <th width="80px"><?=T::payment?></th>
        <th width="80px"><?=T::booking?></th>
        <th width="100px"><?=T::type?></th>
        <th><?=T::date?></th>

        <th><?=T::price?></th>

        <?php
        // AGENT FEE ONLY FOR AGENTS
        if(isset ($_SESSION['phptravels_client'])){
            if(($_SESSION['phptravels_client']->user_type)=="Agent"){
        ?>
        <th width="110px">Comission</th>
        <?php } } ?>

        <th></th>
        </tr>

        <tr class="filters">
        <th><input type="search" class="form-control" placeholder="Search Name" /></th>
        <th><input type="search" class="form-control" placeholder="Search Booking ID" /></th>
        <th><input type="search" class="form-control" placeholder="Search by PNR" /></th>
        <th><select class="form-select"><option value="">All</option></th>
        <th><select class="form-select"><option value="">All</option></th>
        <th><select class="form-select"><option value="">All</option></th>
        <th><input type="search" class="form-control" placeholder="Search by Date" /></th>
        <th><input type="search" class="form-control" placeholder="Search by Price" /></th>

        <?php
        // AGENT FEE ONLY FOR AGENTS
        if(isset ($_SESSION['phptravels_client'])){
            if(($_SESSION['phptravels_client']->user_type)=="Agent"){
        ?>
        <th><input type="search" class="form-control" placeholder="Comission" /></th>
        <?php } } ?>

        <th></th>

        </tr>

    </thead>
    <tbody>
        <?php
        if(!is_null($booking_array) && !empty($booking_array)){
        foreach($booking_array as &$ma)
        $tmp[] = &$ma->booking_ref_no;
        array_multisort($tmp,SORT_DESC, $booking_array);
        foreach (($booking_array) as $i => $booking){
        ?>
        <tr>
        <td><strong><?=(json_decode($booking->guest)[0]->first_name)?> <?=(json_decode($booking->guest)[0]->last_name)?></strong></td>
        <td><?=$booking->booking_ref_no?></td>
        <td><?=$booking->pnr?></td>
        <td><?=$booking->payment_status?></td>
        <td><?=$booking->booking_status?></td>
        <td><?=$booking->module_type?></td>

        <?php
        $date = new DateTime($booking->booking_date);
        ?>
        <td><?=$date->format("M d Y");?></td>

        <td><strong><?=$booking->currency_markup?> <?=$booking->price_markup?></strong></td>

        <?php
        // AGENT FEE ONLY FOR AGENTS
        if(isset ($_SESSION['phptravels_client'])){
            if(($_SESSION['phptravels_client']->user_type)=="Agent"){

        ?>
        <td>
            <?php if (!empty($booking->agent_fee)){ ?>
                <?=$booking->currency_markup?> <?=$booking->agent_fee?>
            <?php } ?>
        </td>
        <?php } } ?>

        <td class="d-flex justify-content-center align-items-center">
            <a href="<?=root.$booking->module_type?>/invoice/<?=$booking->booking_ref_no?>" target="_blank" class="btn btn-dark text-white d-flex justify-content-center align-items-center gap-2">
                <?=T::invoice?>
                <svg width="18px" height="18px" viewBox="0 0 512 512" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <title>open-external</title>
                    <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <g id="icon" fill="#fff" transform="translate(85.333333, 64.000000)">
                        <path d="M128,63.999444 L128,106.666444 L42.6666667,106.666667 L42.6666667,320 L256,320 L256,234.666444 L298.666,234.666444 L298.666667,362.666667 L4.26325641e-14,362.666667 L4.26325641e-14,64 L128,63.999444 Z M362.666667,1.42108547e-14 L362.666667,170.666667 L320,170.666667 L320,72.835 L143.084945,249.751611 L112.915055,219.581722 L289.83,42.666 L192,42.6666667 L192,1.42108547e-14 L362.666667,1.42108547e-14 Z" id="Combined-Shape">
                        </path>
                    </g>
                    </g>
                </svg>
            </a>
        </td>
        </tr>
        <?php } }?>
    </tbody>
    </table>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>

    <script>
    $(document).ready(function() {
    var table = $('table').DataTable({
        "ordering": false // Disable sorting
    });

    // Apply the individual column filter for text input
    table.columns([0, 1, 2, 3, 6, 7, 8]).every(function() {
        var that = this;

        $('input', this.header()).on('keyup change', function() {
            if (that.search() !== this.value) {
                that
                    .search(this.value)
                    .draw();
            }
        });

        // Listen for the search event to handle "cross" button click
        $('input', this.header()).on('search', function() {
            if (that.search() !== this.value) {
                that
                    .search(this.value)
                    .draw();
            }
        });
    });

    // Apply the individual column filter for select box
    table.columns([3, 4, 5]).every(function() {
        var that = this;
        var column = this;

        // Populate select box with unique values
        var select = $('select', this.header());
        column.data().unique().sort().each(function(d, j) {
            select.append('<option value="' + d + '">' + d + '</option>')
        });

        // Apply filter on select box change
        select.on('change', function() {
            var val = $.fn.dataTable.util.escapeRegex($(this).val());
            that
                .search(val ? '^' + val + '$' : '', true, false)
                .draw();
        });
    });
});

</script>


    </script>

    <style>
    table{ width:100%; }
    #example_filter{ float:right; }
    #example_paginate{ float:right; }
    label { display: inline-flex; margin-bottom: .5rem; margin-top: .5rem; }
    .dataTables_filter input { margin-left: 10px; }
    table { vertical-align: middle !important; }
    .dataTables_filter { display: flex; justify-content: end; align-items: center; }
    .paging_simple_numbers { display: flex; justify-content: end; align-items: center; }
    tr:hover{background-color: rgba(128,137,150,0.1);}
    .newsletter-section {display:none}
    .pagination { color: #fff !important }
    thead input { width: 100%;}
    /* table th:last-child input { display: none; } */
    .page-item a {color: #fff !important}
    .disabled>.page-link, .page-link.disabled {background : rgba(33, 37, 41, 0.75) !important }
    </style>

    <div class="my-5"></div>
    <div class="my-5"></div>