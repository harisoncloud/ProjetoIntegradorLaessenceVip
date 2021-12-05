
<link rel=stylesheet href="view/stylesheet/magic360gallery.css">
<link rel=stylesheet href="view/stylesheet/mt-form-font.css">

<?php
if (version_compare(VERSION, '2', '<')){
?>

<script>
    var oldJQuery = jQuery;
</script>
<script src="https://code.jquery.com/jquery-1.12.4.min.js"
        integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="
        crossorigin="anonymous"></script>

<script>
    var jQueryMagic360 = jQuery.noConflict();
    jQuery = oldJQuery;
</script>
<?php }else{ ?>
<script>
    var jQueryMagic360 = jQuery;
</script>
<?php } ?>


<script src="view/javascript/magic360/magic360_gallery.js"></script>

<input type="hidden" name="token" value="<?php echo $token; ?>" />
<input type="hidden" name="ocVersion" value="<?php echo $ocVersion; ?>" />

<div id="content">
    <div class="ajax-overlay">
        <span class="ajax-loading-icon"></span>
    </div>
    <div id="buttons-container" class="buttons-container">
        <div class="mt-button mt-upload-container mt-border-r-4px fileinput-button">
            <span><?php echo $text_upload; ?></span>
            <input id="filesToUpload" type="file" name="files[]" multiple="multiple" accept="image/*" size="1">
        </div>
        <div class="mt-button mt-border-r-4px mt-upload-container mt-delete-all">
            <span><?php echo $text_delete_all; ?></span>
        </div>
    </div>
    <div class="mt-settings-form magictoolboxContent">
        <fieldset class="mt-border-r-4px">
            <legend>Multi-row spin options</legend>
            <div>
                <label for="default-message">Multi-row spin</label>
                <span>
                    <input type="checkbox" id="magic360-multi-rows" name="magic360-multi-rows" />
                </span>
            </div>
            <div style="clear: both;"></div>
            <div>
                <label for="default-message">Number of images on X-axis</label>
                <span>
                    <input type="text" id="magic360-columns" name="magic360-columns" size="10" disabled="disabled" value="0" />
                </span>
                <div class="mt-button mt-border-r-4px mt-update-columns">
                    <span><?php echo $text_save; ?></span>
                </div>
            </div>
            <div style="clear: both;"></div>
        </fieldset>
        <fieldset class="mt-border-r-4px">
            <legend><?php echo $text_images_block; ?></legend>
            <table id="magic360_images" cellspacing="0" cellpadding="0" class="mt-table">
            <thead>
                <tr>
                    <th><?php echo $text_number; ?></th>
                    <th><?php echo $text_image; ?></th>
                    <th><?php echo $text_delete; ?></th>
                </tr>
            </thead>
            <tbody class='show-content' data-images=""></tbody>
            </table>
        </fieldset>
    </div>
</div>