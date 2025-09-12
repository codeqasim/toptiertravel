<script src="./assets/js/ck.js"></script>

<form action="./cms.php" method="POST">

<div class="container mt-4 mb-4">
<div class="card">
<div class="card-body p-5">

<div class="row mb-1">

<div class="col">
    <div class="row form-group mb-3">
        <label class="col-md-1 control-label text-left pt-2"><?=T::status?></label>
        <div class="col-md-8">
        <div class="form-check form-switch">
        <label class="form-check-label" for=""></label>
        <input <?php if(isset($cms)){ if ($cms['status'] == 1){ echo "checked";} }?> style="margin-top:10px;width: 40px; height: 20px;cursor:pointer" class="form-check-input" id="checkedbox" type="checkbox" name="status" value="">
        </div>
        </div>
    </div>
</div>
</div>

<div class="form-floating mb-3">
<input required type="text" placeholder="name" name="page_name" id="name" value="<?php if (isset($_GET['page'])){echo $cms['page_name'];}?>" class="form-control slugger-source">
<label for="name"><?=T::page_name?></label>
</div>

<div class="row">
<div class="col-md-8">

<div class="input-group mb-3">
<span class="input-group-text" id="basic-addon3" style="height:59px"><?=$settings->site_url?>/</span>
<div class="form-floating mb-3">
<input required type="text" placeholder="slug_url" name="slug_url" id="slug_url" value="<?php if (isset($_GET['page'])){echo $cms['slug_url'];}?>" class="form-control slugger-target">
<label for="slug_url"><?=T::slug_url?></label>
</div>

</div>

</div>

<div class="col-md-4">
<div class="form-floating mb-3">
<select required class="form-select" name="menu_id">
<?php foreach($cms_menu as $c){ ?>
    <option value="<?=$c->id?>"><?=$c->name?></option>
<?php } ?>
</select>
<label for=""><?=T::menu_name?></label>
</div>
</div>
</div>

<!-- CATEGORY FIELD SECTION - Shows when specific menus are selected -->
<div class="row" id="category-section" style="display: none;">
<div class="col-md-6">
<div class="form-floating mb-3">
<select class="form-select" name="category" id="category">
    <option value="explore" <?php if (isset($_GET['page']) && isset($cms['category']) && $cms['category'] == 'explore'){ echo 'selected';} ?>>Explore</option>
    <option value="support" <?php if (isset($_GET['page']) && isset($cms['category']) && $cms['category'] == 'support'){ echo 'selected';} ?>>Support</option>
    <option value="company" <?php if (isset($_GET['page']) && isset($cms['category']) && $cms['category'] == 'company'){ echo 'selected';} ?>>Company</option>
    <option value="downloads" <?php if (isset($_GET['page']) && isset($cms['category']) && $cms['category'] == 'downloads'){ echo 'selected';} ?>>Downloads</option>
</select>
<label for="category">Category</label>
</div>
</div>
</div>

<script>
$("[name='menu_id']").val(<?php if (isset($_GET['page'])){echo $cms['menu_id'];}?>)

// Show/hide category section based on menu selection
$(document).ready(function() {
    // Check initial state when editing existing page
    checkMenuSelection();
    
    // Listen for menu dropdown changes
    $("[name='menu_id']").change(function() {
        checkMenuSelection();
    });
    
    function checkMenuSelection() {
        var selectedMenuId = $("[name='menu_id']").val();
        var selectedMenuText = $("[name='menu_id'] option:selected").text().toLowerCase().trim();
        
        console.log("Selected menu text: '" + selectedMenuText + "'"); // Debug line
        console.log("Selected menu ID: " + selectedMenuId); // Debug line
        
        // Show category section ONLY when footer menu is selected
        if (selectedMenuText === 'footer') {
            $('#category-section').show();
            $('#category').prop('required', true);
            // Don't auto-select any category - let user choose
        } else {
            $('#category-section').hide();
            $('#category').prop('required', false);
            $('#category').val(''); // Clear category value when hidden
        }
    }
});
</script>

<textarea name="content" class="editor" cols="30" rows="10" class=""><?php if (isset($_GET['page'])){echo  $cms['content'];}?></textarea>
  
</div>

<div class="card-footer">
    <div class="mx-4 my-3">
        <button type="submit" class="btn btn-primary"> <?=T::submit?></button>
    </div>
</div>

</div>
</div>


<script>
ClassicEditor.create(document.querySelector(".editor"), {

    language: 'es',
    toolbar: {
        items: [
            "heading",
            "|",
            "bold",
            "italic",
            "link",
            "bulletedList",
            "numberedList",
            "|",
            "indent",
            "outdent",
            "|",
            "imageUpload",
            "blockQuote",
            "mediaEmbed",
            "undo",
            "redo",
        ],
    },
    language: "en",
    image: {
        toolbar: ["imageTextAlternative", "imageStyle:full", "imageStyle:side"],
    },
    licenseKey: "",
})
    .then((editor) => {
        window.editor = editor;
    })
    .catch((error) => {
        console.error("Oops, something went wrong!");
        console.error(
            "Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:"
        );
        // console.warn("Build id: ref2goguw78q-8ghiwfe1qu83");
        console.error(error);
    });
</script>

<script>

let source = $('.slugger-source');
    let target = $('.slugger-target');

    //When the user is typing in the name field.
    source.keyup( function(){
        transformStringToSlug(source , target)
    });

    //When the user is typing in the target field
    target.keyup( function(){
        transformStringToSlug(target , target)
    });

    //Actually perform the sluggify
    function transformStringToSlug(the_source , the_target){

        string = the_source.val();

        //Remove any special chars, ignoring any spaces or hyphens
        var slug = string.replace(/[^a-zA-Z0-9\ \-]/g, "");

        //Replace any spaces with hyphens
        slug = slug.split(' ').join('-');

        //Chuck it back into lowercase
        slug = slug.toLowerCase();

        //Valiate out any double hyphens
        slug = slug.split('--').join('-');

        var lastChar = slug.substring(slug.length -1, slug.length);
        if ( lastChar == '-'){
            slug = slug.substring(0 , slug.length -1 );
        }

        //Dump it back to the destination input
        the_target.val( slug );
    }

</script>

<style>
.page_head{position: relative;}
</style>

<?php if (isset($_GET['addpage'])) { ?>
    <input type="hidden" name="add_new">
<?php } if (isset($_GET['page'])) { ?>
    <input type="hidden" name="update">
    <input type="hidden" name="page_id" value="<?=$_GET['page']?>">
<?php } ?>

<input type="hidden" name="form_token" value="<?=$_SESSION["form_token"]?>">
</form>