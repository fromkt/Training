<?php
global $lo_location;
global $lo_url;

include_once('./_common.php');

if($error) {
    $gml['title'] = __('Error notification page');
} else {
    $gml['title'] = __('Result information page');
}
include_once(GML_PATH.'/head.sub.php');

$msg = isset($msg) ? strip_tags($msg) : '';
$msg2 = str_replace("\\n", "<br>", $msg);

$url = clean_xss_tags($url);
if (!$url) $url = clean_xss_tags($_SERVER['HTTP_REFERER']);

$url = preg_replace("/[\<\>\'\"\\\'\\\"\(\)]/", "", $url);

// url 체크
check_url_host($url, $msg);

if($error) {
    $header2 = __('The following items have errors :');
} else {
    $header2 = __('Please check the following information.');
}
?>

<script>
alert("<?php echo $msg; ?>");
//document.location.href = "<?php echo $url; ?>";
<?php if ($url) { ?>
document.location.replace("<?php echo str_replace('&amp;', '&', $url); ?>");
<?php } else { ?>
//alert('history.back();');
history.back();
<?php } ?>
</script>

<noscript>
<div id="validation_check">
    <h1><?php echo $header2 ?></h1>
    <p class="cbg">
        <?php echo $msg2 ?>
    </p>
    <?php if($post) { ?>
    <form method="post" action="<?php echo $url ?>">
    <?php
    foreach($_POST as $key => $value) {
        if(strlen($value) < 1)
            continue;

        if(preg_match("/pass|pwd|capt|url/", $key))
            continue;
    ?>
    <input type="hidden" name="<?php echo $key ?>" value="<?php echo $value ?>">
    <?php
    }
    ?>
    <input type="submit" value="<?php e__('Back'); ?>">
    </form>
    <?php } else { ?>
    <div class="btn_confirm">
        <a href="<?php echo $url ?>"><?php e__('Back'); ?></a>
    </div>
    <?php } ?>

</div>
</noscript>

<?php
include_once(GML_PATH.'/tail.sub.php');
?>