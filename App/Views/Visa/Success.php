<!-- ================================
    START BREADCRUMB AREA
================================= -->

<section class="breadcrumb-area visa" style="min-height:150px">
    <div class="breadcrumb-wrap">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 mt-5">
                    <div class="breadcrumb-content">
                        <div class="section-heading">
                            <h2 class="sec__title_list text-center my-5 text-white mt-2 d-flex justify-content-center align-items-center gap-3">
                               <strong><?=T::submitted?></strong>
                               <svg class="" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>
                            </h2>
                        </div>
                    </div><!-- end breadcrumb-content -->
                </div><!-- end breadcrumb-content -->
            </div><!-- end row -->
        </div><!-- end container -->
    </div><!-- end breadcrumb-wrap -->
</section><!-- end breadcrumb-area -->
<!-- ================================
    END BREADCRUMB AREA
================================= -->

<!-- ================================
    START BOOKING AREA
================================= -->
<div class="">
<div class="container mt-5 mb-5">
<div class="">
    <!-- form-title-wrap -->
    <div class="">
        <div class="contact-form-action py-5">
            <div class="panel-primary my-5">
                <div class="card-body my-5 text-center col-md-8 mx-auto">
                     <h2><?=T::thankyou?></h2>
                    <p><?=T::visatext2?> <strong><?=($meta['data']->res_code) ?></strong></p>
                    <a class="btn btn-outline-primary" href="<?=root?>">

                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>

                    <?=T::home?></a>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>