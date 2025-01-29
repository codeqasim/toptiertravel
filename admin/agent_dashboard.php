<?php
require_once '_config.php';
auth_check();
$title = "Agent Dashboard";
include "_header.php";

$user_id = $USER_SESSION->backend_user_id;
// print_r($user_id);

?>

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
                                                    <span class="fw-bold text-fixed-white ">Hello Demo Agent</span>
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
                                            <div class="card-item-title mb-2">
                                                <label class="main-content-label fs-13 fw-bold mb-1">Total Sales
                                                    Revenue</label>
                                                <span class="d-block fs-12 mb-0 text-muted">Previous month vs this
                                                    months</span>
                                            </div>
                                            <div class="card-item-body">
                                                <div class="card-item-stat">
                                                    <h4 class="fw-bold">$5,900.00</h4>
                                                    <small><b class="text-success">55%</b> higher</small>
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
                                            <div class="card-item-title mb-2">
                                                <label class="main-content-label fs-13 fw-bold mb-1">Total
                                                Commission</label>
                                                <span class="d-block fs-12 mb-0 text-muted">Total
                                                Commission You Earned</span>
                                            </div>
                                            <div class="card-item-body">
                                                <div class="card-item-stat">
                                                    <h4 class="fw-bold">15</h4>
                                                    <small><b class="text-success">5%</b> Increased</small>
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
                                            <span class="d-block fs-12 mt-2 mb-0 text-muted"> UX UI & Backend
                                                Developement. </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-6 my-auto">
                                                <h6 class="mb-3 fs-14 fw-normal">UPCOMIG COMMISSION</h6>
                                                <div class="text-start">
                                                    <h3 class="fw-bold me-3 mb-2 text-primary">$5,240</h3>
                                                    <p class="fs-13 my-auto text-muted">May 28 - June 01 (2018)</p>
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
                                            <span class="d-block fs-12 mt-2 mb-0 text-muted">Here are your three top clients. </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mt-1">
                                            <div class="col-5">
                                                <span class="">Qasim H</span>
                                            </div>
                                            <div class="col-3 my-auto">
                                                <div class="progress ht-6 my-auto progress-animate">
                                                    <div class="progress-bar ht-6 wd-80p" role="progressbar"
                                                        aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="d-flex">
                                                    <span class="fs-13"><i
                                                            class="text-success fe fe-arrow-up"></i><b>24.75%</b></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-4">
                                            <div class="col-5">
                                                <span class="">Usama Malik</span>
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
                                                <span class="">M Ahtisham</span>
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
                                            <div class="dropdown">
                                            <button style = "margin-left:12px !important;"
                                                    class="btn text-white dropdown-toggle bg-black me-2"
                                                    type="button"
                                                    id="dropdownMenuButton"
                                                    data-bs-toggle="dropdown"
                                                    aria-expanded="false"
                                                >
                                                SEE ALL
                                                RESERVATIONS </button>
                                            
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                                                <li><a class="dropdown-item" href="#">Action 1</a></li>
                                                <li><a class="dropdown-item" href="#">Action 2</a></li>
                                                <li><a class="dropdown-item" href="#">Action 3</a></li>
                                            </ul>
                                            </div>
                                            </div>
                                            <div class="ms-auto d-flex flex-wrap gap-2">
                                                <div class="contact-search3 me-3 ">
                                                    <button type="button" class="btn border-0"><i class="fe fe-search fw-semibold text-muted" aria-hidden="true"></i></button>
                                                    <input type="text" class="form-control h-6" id="typehead1" placeholder="Search here..." autocomplete="off">
                                                </div>
                                                <div class="ms-auto d-flex dropdown">
                                                    <a href="javascript:void(0);" class="btn dropdown-toggle btn-sm btn-wave waves-effect waves-light btn-primary d-inline-flex align-items-center" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-equalizer-line me-1"></i>Sort by</a>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                        <li><a class="dropdown-item" href="javascript:void(0);">Task</a></li>
                                                        <li><a class="dropdown-item" href="javascript:void(0);">Team</a></li>
                                                        <li><a class="dropdown-item" href="javascript:void(0);">Status</a></li>
                                                        <li class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item" href="javascript:void(0);"><i class="fa fa-cog me-2"></i>Settings</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
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
                                                    <tr>
                                                        <td class="fw-medium">
                                                            <div class="form-check">
                                                                <label class="form-check-label">Qasim Hussain</label>
                                                            </div>
                                                        </td>
                                                        <td class="text-nowrap">
                                                            wynn Las vegas
                                                        </td>
                                                        <td class="text-center">Jan 25 - Jan 29<i class=""></i></td>
                                                        <td class="text-primary">High</td>
                                                        <td><span
                                                                class="badge bg-pill rounded-pill bg-primary-transparent">Completed</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-medium">
                                                            <div class="form-check">
                                                                <label class="form-check-label">Usama Malik</label>
                                                            </div>
                                                        </td>
                                                        <td class="text-nowrap">
                                                            wynn Las vegas
                                                        </td>
                                                        <td class="text-center">Jan 25 - Jan 29<i class=""></i></td>
                                                        <td class="text-secondary">Normal</td>
                                                        <td><span
                                                                class="badge bg-pill rounded-pill bg-warning-transparent">Pending</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-medium">
                                                            <div class="form-check">
                                                                <label class="form-check-label">Malik Ahtisham</label>
                                                            </div>
                                                        </td>
                                                        <td class="text-nowrap">
                                                            wynn Las vegas
                                                        </td>
                                                        <td class="text-center">Jan 25 - Jan 29<i class=""></i></td>
                                                        <td class="text-warning">Low</td>
                                                        <td><span
                                                                class="badge bg-pill rounded-pill bg-primary-transparent">Completed</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-medium">
                                                            <div class="form-check">
                                                                <label class="form-check-label">Shahzar Ahmad</label>
                                                            </div>
                                                        </td>
                                                        <td class="text-nowrap">
                                                            wynn Las vegas
                                                        </td>
                                                        <td class="text-center">Jan 25 - Jan 29<i class=""></i></td>
                                                        <td class="text-primary">High</td>
                                                        <td><span
                                                                class="badge bg-pill rounded-pill bg-danger-transparent">Rejected</span>
                                                        </td>
                                                    </tr>
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
                                    <tr>
                                        <!-- <td class="">
                                            <div class="main-img-user avatar-md">
                                                <img alt="avatar" class="rounded-circle me-3"
                                                    src="../assets/images/faces/5.jpg">
                                            </div>
                                        </td> -->
                                        <td>
                                            <div class="d-flex align-middle ms-3">
                                                <div class="d-inline-block">
                                                    <h6 class="mb-1">Flicker</h6>
                                                    <p class="mb-0 fs-13 text-muted">App improvement</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-block">
                                                <h6 class="mb-2 fs-15 fw-semibold">$45.234
                                                </h6>
                                                <p class="mb-0 tx-11 text-muted">12 Jan 2020</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <!-- <td class="wd-5p">
                                            <div class="main-img-user avatar-md">
                                                <img alt="avatar" class="rounded-circle me-3"
                                                    src="../assets/images/faces/6.jpg">
                                            </div>
                                        </td> -->
                                        <td>
                                            <div class="d-flex align-middle ms-3">
                                                <div class="d-inline-block">
                                                    <h6 class="mb-1">Intoxica</h6>
                                                    <p class="mb-0 fs-13 text-muted">Milestone</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-block">
                                                <h6 class="mb-2 fs-15 fw-semibold">$23.452
                                                </h6>
                                                <p class="mb-0 tx-11 text-muted">23 Jan 2020</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <!-- <td class="wd-5p">
                                            <div class="main-img-user avatar-md">
                                                <img alt="avatar" class="rounded-circle me-3"
                                                    src="../assets/images/faces/7.jpg">
                                            </div>
                                        </td> -->
                                        <td>
                                            <div class="d-flex align-middle ms-3">
                                                <div class="d-inline-block">
                                                    <h6 class="mb-1">Digiwatt</h6>
                                                    <p class="mb-0 fs-13 text-muted">Sales executive</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-block">
                                                <h6 class="mb-2 fs-15 fw-semibold">$78.001
                                                </h6>
                                                <p class="mb-0 tx-11 text-muted">4 Apr 2020</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <!-- <td class="wd-5p">
                                            <div class="main-img-user avatar-md">
                                                <img alt="avatar" class="rounded-circle me-3"
                                                    src="../assets/images/faces/8.jpg">
                                            </div>
                                        </td> -->
                                        <td>
                                            <div class="d-flex align-middle ms-3">
                                                <div class="d-inline-block">
                                                    <h6 class="mb-1">Flicker</h6>
                                                    <p class="mb-0 fs-13 text-muted">Milestone2</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-block">
                                                <h6 class="mb-2 fs-15 fw-semibold">$37.285
                                                </h6>
                                                <p class="mb-0 tx-11 text-muted">4 Apr 2020</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <!-- <td class="wd-5p pb-0">
                                            <div class="main-img-user avatar-md">
                                                <img alt="avatar" class="rounded-circle me-3"
                                                    src="../assets/images/faces/4.jpg">
                                            </div>
                                        </td> -->
                                        <td class="pb-0">
                                            <div class="d-flex align-middle ms-3">
                                                <div class="d-inline-block">
                                                    <h6 class="mb-1">Flicker</h6>
                                                    <p class="mb-0 fs-13 text-muted">App improvement</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pb-0">
                                            <div class="d-inline-block">
                                                <h6 class="mb-2 fs-15 fw-semibold">$25.341
                                                </h6>
                                                <p class="mb-0 tx-11 text-muted">4 Apr 2020</p>
                                            </div>
                                        </td>
                                    </tr>
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
                                    <div class="card-header  border-bottom-0 pb-0">
                                        <div>
                                            <div class="d-flex">
                                                <label class="main-content-label my-auto pt-2">Top Destinations</label>
                                            </div>
                                            <span class="d-block fs-12 mt-2 mb-0 text-muted">Here are your three top clients.</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mt-1">
                                            <div class="col-5">
                                                <span class="">Las Vages</span>
                                            </div>
                                            <div class="col-3 my-auto">
                                                <div class="progress ht-6 my-auto progress-animate">
                                                    <div class="progress-bar ht-6 wd-80p" role="progressbar"
                                                        aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="d-flex">
                                                    <span class="fs-13"><i
                                                            class="text-success fe fe-arrow-up"></i><b>24.75%</b></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-4">
                                            <div class="col-5">
                                                <span class="">Miami</span>
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
                                                <span class="">New York City</span>
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



<?php include "_footer.php" ?>