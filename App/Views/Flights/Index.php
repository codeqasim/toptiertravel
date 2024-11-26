<section class="feature flights">
<section class="container" style="border-radius:10px;padding:50px 0px">
<div class="container">
<h2 class="text-center text-white mt-5 text-capitalize">

<!-- <svg fill="" width="50" height="50" viewBox="-2.5 0 19 19" xmlns="http://www.w3.org/2000/svg"
class="d-block text-center mx-auto mb-2">
<path
d="M12.382 5.304 10.096 7.59l.006.02L11.838 14a.908.908 0 0 1-.211.794l-.573.573a.339.339 0 0 1-.566-.08l-2.348-4.25-.745-.746-1.97 1.97a3.311 3.311 0 0 1-.75.504l.44 1.447a.875.875 0 0 1-.199.79l-.175.176a.477.477 0 0 1-.672 0l-1.04-1.039-.018-.02-.788-.786-.02-.02-1.038-1.039a.477.477 0 0 1 0-.672l.176-.176a.875.875 0 0 1 .79-.197l1.447.438a3.322 3.322 0 0 1 .504-.75l1.97-1.97-.746-.744-4.25-2.348a.339.339 0 0 1-.08-.566l.573-.573a.909.909 0 0 1 .794-.211l6.39 1.736.02.006 2.286-2.286c.37-.372 1.621-1.02 1.993-.65.37.372-.279 1.622-.65 1.993z" />
</svg> -->

<strong><?=T::flights_searchforbestflights?></strong></h2>

<div style="padding:50px 20" id="fadein">
<div class="p-5 rounded bg-white shadow-lg mt-4">
<?php include "Search.php"; ?>
</div>
</div>
</div>
</section>
</section>

<svg style="position: relative;bottom: 0;left: 0;width: 100%;height: 50px;fill: #fff;z-index: 100;margin-top: -78px;"
class="hero-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 10" preserveAspectRatio="none">
<path d="M0 10 0 0 A 90 59, 0, 0, 0, 100 0 L 100 10 Z"></path>
</svg>

<?php include "Featured.php"; ?>

<style> .featured.py-5 {background:white !important}</style>
<style>
.select2-container--default .select2-selection--single .select2-selection__rendered {
padding: 0px 7px !important;
}
</style>