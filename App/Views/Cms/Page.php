<div class="container my-5">
<div class="card">
  <h5 class="card-header py-3"><strong><small><?=($meta['data']->data[0]->page_name)?></small></strong></h5>
  <div class="card-body p-4 py-3">
    <!-- <h5 class="card-title">Special title treatment</h5> -->

    <?php

    $link = $_SERVER['REQUEST_URI'];
    $link_array = explode('/',$link);
    $page = end($link_array);
    if ($page =="contact-us" OR $page =="contact" ){
      include "Contact.php";

    } else {

    ?>

    <p class="card-text"><?=(html_entity_decode($meta['data']->data[0]->content))?></p>
    <?php } ?>
   </div>
</div>
</div>