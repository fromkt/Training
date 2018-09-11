<?php
$sub_menu = "100520";
include_once('./_common.php');

if(!(version_compare(phpversion(), '5.3.0', '>=') && defined('GML_BROWSCAP_USE') && GML_BROWSCAP_USE))
    alert(__('PHP Version is low and not available.'), GML_ADMIN_URL);

if ($is_admin != 'super')
    alert(__('Only the Super administrator can access it.'));

$rows = preg_replace('#[^0-9]#', '', $_GET['rows']);
if(!$rows)
    $rows = 100;

$gml['title'] = __('Convert Visit Log');
include_once('./admin.head.php');
?>

<div id="processing">
    <p><?php e__('To convert your connection log information to Browscap information, click the update button below.'); ?></p>
    <button type="button" id="run_update"><?php e__('Update'); ?></button>
</div>

<?php
get_localize_script('browscap_convert',
array(
'processing_msg'=>__('Converting to Browscap information.'),  // Browscap 정보로 변환 중입니다.
),
true);
?>
<script>
$(function() {
    $(document).on("click", "#run_update", function() {
        $("#processing").html('<div class="update_processing"></div><p>' + browscap_convert.processing_msg + '</p>');

        $.ajax({
            method: "GET",
            url: "./browscap_converter.php",
            data: { rows: "<?php echo $rows; ?>" },
            async: true,
            cache: false,
            dataType: "html",
            success: function(data) {
                $("#processing").html(data);
            }
        });
    });
});
</script>

<?php
include_once('./admin.tail.php');
?>