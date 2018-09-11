<?php
include_once('./_common.php');
include_once(GML_PATH.'/head.sub.php');

$url1 = clean_xss_tags($url1);
$url2 = clean_xss_tags($url2);
$url3 = clean_xss_tags($url3);

// Check url
check_url_host($url1);
check_url_host($url2);
check_url_host($url3);
?>

<script>
var conf = "<?php echo strip_tags($msg); ?>";
if (confirm(conf)) {
    document.location.replace("<?php echo $url1; ?>");
} else {
    document.location.replace("<?php echo $url2; ?>");
}
</script>

<noscript>
<article id="confirm_check">
<header>
    <hgroup>
        <h1><?php echo $header; ?></h1> <!-- Doing Work content -->
        <h2><?php e__('Please confirm the following contents.'); ?></h2>
    </hgroup>
</header>
<p>
    <?php echo $msg; ?>
</p>

<a href="<?php echo $url1; ?>"><?php e__('Confirm'); ?></a>
<a href="<?php echo $url2; ?>"><?php e__('Cancel'); ?></a><br><br>
<a href="<?php echo $url3; ?>"><?php e__('Back'); ?></a>
</article>
</noscript>

<?php
include_once(GML_PATH.'/tail.sub.php');
?>