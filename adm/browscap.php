<?php
$sub_menu = "100510";
include_once('./_common.php');

if(!(version_compare(phpversion(), '5.3.0', '>=') && defined('GML_BROWSCAP_USE') && GML_BROWSCAP_USE))
    alert(__('PHP Version is low and not available.'), GML_ADMIN_URL);

if ($is_admin != 'super')
    alert(__('Only the Super administrator can access it.'));

$gml['title'] = 'Browscap Update';
include_once('./admin.head.php');
?>

<div id="processing">
    <p><?php e__('To update your Browscap information, please click the update button below.'); ?></p>
    <button type="button" id="run_update"><?php e__('Update'); ?></button>
</div>

<?php
get_localize_script('j_browscap',
array(
'processing_msg'=>__('Browscap Information is being updated'),  // Browscap 정보를 업데이트 중입니다.
'success_msg'=>__('Successfully updated Browscap information.'),    // Browscap 정보를 업데이트 했습니다.
),
true);
?>
<script>
$(function() {
    $("#run_update").on("click", function() {
        $("#processing").html('<div class="update_processing"></div><p>' + j_browscap.processing_msg + '</p>');

        $.ajax({
            url: "./browscap_update.php",
            async: true,
            cache: false,
            dataType: "html",
            success: function(data) {
                if(data != "") {
                    alert(data);
                    return false;
                }

                $("#processing").html("<div class='check_processing'></div><p>" + j_browscap.success_msg + "</p>");
            }
        });
    });
});
</script>

<?php
include_once('./admin.tail.php');
?>