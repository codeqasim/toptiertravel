<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include "_config.php";
    if (isset($_POST['update_testimonial'])){

        $filteredArray = array_filter($_REQUEST, function($value) {
            return $value !== 'update_testimonial'  && !empty($value);
        });
        foreach ($filteredArray as $key => $value) {
            if (is_array($value)) {
                // Check if record exists first
                $existing = GET('testimonials_translations', [
                    "testimonial_id" => $filteredArray['testimonial_id'], 
                    "language_id"    => $key
                ]);

                $params = array(
                    "name"        => $value[0],
                    "country"     => $value[1],
                    "title"       => $value[2],
                    "description" => $value[3],
                );

                if (!empty($existing)) {
                    // Update existing record
                    $db->update('testimonials_translations', $params, [
                        "testimonial_id" => $filteredArray['testimonial_id'],
                        "language_id"    => $key
                    ]);
                } else {
                    // Insert new record
                    $params['testimonial_id'] = $filteredArray['testimonial_id'];
                    $params['language_id']    = $key;
                    $db->insert('testimonials_translations', $params);
                }
            }
        }
        ALERT_MSG('updated');
        REDIRECT('./testimonials.php');
        exit;
    }
}
?>

<div class="page_head">
    <div class="panel-heading">
        <div class="float-start">
            <p class="m-0 page_title"><?=T::testimonials?> <?=T::translations?></p>
        </div>
        <div class="float-end">
            <a href="javascript:window.history.back();" data-toggle="tooltip" data-placement="top" title="Previous Page"
               class="loading_effect btn btn-warning"><?=T::back?></a>
        </div>
    </div>
</div>

<form action="translations_testimonials.php" method="post">

    <?php
    $params = array( "id"=> $_GET['testimonial']);
    $param = array('status'=>1);
    $languages = GET('languages',$param);
    $testimonials = GET('testimonials',$params);
    ?>

    <div class="container mt-4 mb-5">
        <div class="card">
            <div class="card-header">
                <?=$testimonials[0]->name?>
            </div>
            <div class="card-body p-4">

                <?php foreach($languages as $lang => $i){
                    if($i->default != 1){
                        $params = array( "testimonial_id"=> $_GET['testimonial'],"language_id"=>$i->id);
                        $trans_result = GET('testimonials_translations',$params);
                        $trans = !empty($trans_result) ? $trans_result[0] : null;
                    ?>
                    <div class="card mt-3">
                        <div class="card-header">
                            <img src="./assets/img/flags/<?=strtolower($i->country_id)?>.svg"style="max-width:20px;">
                            <strong class="mx-2"><?=$i->name?></strong>
                        </div>
                        <div class="card-body">

                            <div class="form-floating mb-2">
                                <input class="form-control"  name="<?=strtolower($i->id)?>[]" value="<?=$trans->name ?? ""?>" />
                                <label for=""><?=T::name?></label>
                            </div>

                            <div class="form-floating mb-2">
                                <input class="form-control"  name="<?=strtolower($i->id)?>[]" value="<?=$trans->country ?? ""?>" />
                                <label for=""><?=T::country?></label>
                            </div>

                            <div class="form-floating mb-2">
                                <input class="form-control"  name="<?=strtolower($i->id)?>[]" value="<?=$trans->title ?? ""?>" />
                                <label for=""><?=T::title?></label>
                            </div>

                            <div class="form-floating mb-2">
                                <textarea class="form-control" placeholder="" id="" style="height: 100px" name="<?=strtolower($i->id)?>[]"><?=$trans->description ?? ""?></textarea>
                                <label for=""><?=T::description?></label>
                            </div>

                        </div>
                    </div>
                <?php }
                }?>

            </div>

            <div class="card-footer text-muted" style="position: fixed; bottom: 0; width: 100%;background: #e9ecef;">
                <div class="mx-4 my-3">
                    <button type="submit" class="btn btn-primary mdc-ripple-upgraded"> <?=T::submit?></button>
                </div>
            </div>

        </div>

    </div>
    <input type="hidden" name="update_testimonial" value="update_testimonial">
    <input type="hidden" name="testimonial_id" value="<?=$_GET['testimonial']?>">
</form>

<div class="mt-5" style="margin-bottom:100px;"></div>