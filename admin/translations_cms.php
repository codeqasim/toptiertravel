<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    include "_config.php";

    if (isset($_POST['update_cms'])) {

        // Filter out empty values and remove update button
        $filteredArray = array_filter($_REQUEST, function($value) {
            return $value !== 'update_cms' && !empty($value);
        });

        foreach ($filteredArray as $key => $value) {
            if (is_array($value)) {
                
                // $key = language_id, $filteredArray['page_id'] = page_id
                $page_id = $filteredArray['page_id'];
                $language_id = $key;

                // Extract title and description
                $post_title = $value[0] ?? '';
                $post_desc  = $value[1] ?? '';

                // Prepare params
                $params = [
                    "page_id"     => $page_id,
                    "language_id" => $language_id,
                    "post_title"  => $post_title,
                    "post_desc"   => $post_desc,
                ];
                
                // Check if row exists already
                $exists = $db->get('cms_translations', '*', [
                    "page_id"     => $page_id,
                    "language_id" => $language_id
                ]);

                if ($exists) {
                    // Update if exists
                    $db->update('cms_translations', $params, [
                        "page_id"     => $page_id,
                        "language_id" => $language_id
                    ]);
                } else {
                    // Insert if not exists
                    $db->insert('cms_translations', $params);
                }
            }
        }

        ALERT_MSG('updated');
        REDIRECT('cms.php');
        exit;
    }
}
?>


<div class="page_head">
    <div class="panel-heading">
        <div class="float-start">
            <p class="m-0 page_title"><?=T::cms?> <?=T::translations?></p>
        </div>
        <div class="float-end">
            <a href="javascript:window.history.back();" data-toggle="tooltip" data-placement="top" title="Previous Page"
               class="loading_effect btn btn-warning"><?=T::back?></a>
        </div>
    </div>
</div>

<form action="translations_cms.php" method="post">

    <?php
    $params = array( "id"=> $_GET['cms']);
    $param = array('status'=>1);
    $languages = GET('languages',$param);
    $cms = cms_get('cms',$params);
    ?>

    <div class="container mt-4 mb-5">
        <div class="card">
            <div class="card-header">
            <?=str_replace('-',' ',$cms[0]['slug_url']);?>
            </div>
            <div class="card-body p-4">

                <?php foreach($languages as $lang => $i){
                    if($i->default != 1){
                        $params = array( "page_id"=> $_GET['cms'],"language_id"=>$i->id);
                        $trans = GET('cms_translations',$params)[0];
                    ?>
                    <div class="card mt-3">
                        <div class="card-header">
                            <img src="./assets/img/flags/<?=strtolower($i->country_id)?>.svg"style="max-width:20px;">
                            <strong class="mx-2"><?=$i->name?></strong>
                        </div>
                        <div class="card-body">

                            <div class="form-floating mb-2">
                                <input class="form-control"  name="<?=strtolower($i->id)?>[]" value="<?=$trans->post_title ?? ""?>" />
                                <label for=""><?=T::name?></label>
                            </div>

                            <div class="form-floating mb-2">
                                <textarea class="form-control" placeholder="" id="" style="height: 100px" name="<?=strtolower($i->id)?>[]"><?=$trans->post_desc ?? ""?></textarea>
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
    <input type="hidden" name="update_cms" value="update_cms">
    <input type="hidden" name="page_id" value="<?=$_GET['cms']?>">
</form>

<div class="mt-5" style="margin-bottom:100px;"></div>