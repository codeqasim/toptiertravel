<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include "_config.php";
    if (isset($_POST['update_service'])){

        $filteredArray = array_filter($_REQUEST, function($value) {
            return $value !== 'update_service'  && !empty($value);
        });
        foreach ($filteredArray as $key => $value) {
            if (is_array($value)) {
                
                // Check if record exists first
                $existing = GET('our_services_translations', [
                    "service_id" => $filteredArray['service_id'], 
                    "language_id" => $key
                ]);
                
                $params = array(
                    "title" => $value[0],
                    "description" => $value[1],
                    "button_text" => $value[2],
                );
                
                if (!empty($existing)) {
                    // Update existing record
                    $data = $db->update('our_services_translations',$params, [ "service_id" => $filteredArray['service_id'] , "language_id"=> $key]);
                } else {
                    // Insert new record
                    $params['service_id'] = $filteredArray['service_id'];
                    $params['language_id'] = $key;
                    $data = $db->insert('our_services_translations', $params);
                }
            }
        }
        ALERT_MSG('updated');
        REDIRECT('./our_services.php');
        exit;
    }
}
?>

<div class="page_head">
    <div class="panel-heading">
        <div class="float-start">
            <p class="m-0 page_title"><?=T::our_services?> <?=T::translations?></p>
        </div>
        <div class="float-end">
            <a href="javascript:window.history.back();" data-toggle="tooltip" data-placement="top" title="Previous Page"
               class="loading_effect btn btn-warning"><?=T::back?></a>
        </div>
    </div>
</div>

<form action="translations_our_services.php" method="post">

    <?php
    $params = array( "id"=> $_GET['service']);
    $param = array('status'=>1);
    $languages = GET('languages',$param);
    $services = GET('our_services',$params);

    ?>

    <div class="container mt-4 mb-5">
        <div class="card">
            <div class="card-header">
            <?=str_replace('-',' ',$services[0]->slug);?>
            </div>
            <div class="card-body p-4">

                <?php foreach($languages as $lang => $i){
                    if($i->default != 1){
                        $params = array( "service_id"=> $_GET['service'],"language_id"=>$i->id);
                        $trans_result = GET('our_services_translations',$params);
                        $trans = !empty($trans_result) ? $trans_result[0] : null;
                    ?>
                    <div class="card mt-3">
                        <div class="card-header">
                            <img src="./assets/img/flags/<?=strtolower($i->country_id)?>.svg"style="max-width:20px;">
                            <strong class="mx-2"><?=$i->name?></strong>
                        </div>
                        <div class="card-body">

                            <div class="form-floating mb-2">
                                <input class="form-control"  name="<?=strtolower($i->id)?>[]" value="<?=$trans->title ?? ""?>" />
                                <label for=""><?=T::title?></label>
                            </div>

                            <div class="form-floating mb-2">
                                <textarea class="form-control" placeholder="" id="" style="height: 100px" name="<?=strtolower($i->id)?>[]"><?=$trans->description ?? ""?></textarea>
                                <label for=""><?=T::description?></label>
                            </div>

                            <div class="form-floating mb-2">
                                <input class="form-control"  name="<?=strtolower($i->id)?>[]" value="<?=$trans->button_text ?? ""?>" />
                                <label for=""><?=T::button_text?></label>
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
    <input type="hidden" name="update_service" value="update_service">
    <input type="hidden" name="service_id" value="<?=$_GET['service']?>">
</form>

<div class="mt-5" style="margin-bottom:100px;"></div>