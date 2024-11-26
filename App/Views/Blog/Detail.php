<!-- ================================
   START CARD AREA
   ================================= -->
   <section class="card-area section--padding mt-3">
   <div class="container">
      <div class="row py-3 g-5">
         <div class="col-md-6">
            <?php if (isset($meta['data']->data[0]->post_img)) { ?>
            <img class="w-100 rounded-2" src="<?=root?>/uploads/blog/<?=$meta['data']->data[0]->post_img?>" alt="blog-img">
            <?php } ?>
         </div>
         <div class="col-md-6 d-flex align-items-center">
            <div>
               <h1>
                  <strong><?=$meta['data']->data[0]->post_title?></strong>
               </h1>
               <?php
                  $date = $meta['data']->data[0]->created_at;
                  $newDate = date("d-m-Y", strtotime($date));
                  ?>
               <hr>
               <div class="d-flex align-items-center justify-content-between">
                  <p class="card-title font-size-28 text-end d-flex algin-items-center justify-content-end gap-2">
                     <svg class="mt-1" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                     </svg>
                     <small><?=T::date?> <?=$newDate?></small>
                  </p>
               </div>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-lg-12">
            <div class="card-item blog-card blog-card-layout-2 blog-single-card mb-5">
               <div class="card-body">
                  <div class="section-block"></div>
                  <?= html_entity_decode($meta['data']->data[0]->post_desc) ?>
               </div>
            </div>
            <!-- end card-item -->
         </div>
         <!-- end col-lg-8 -->
      </div>
      <!-- end row -->
   </div>
   <!-- end container -->
</section>
<!-- end card-area -->
<!-- ================================
   END CARD AREA
   ================================= -->
<div class="section-block"></div>