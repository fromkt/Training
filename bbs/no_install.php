<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

$lang = get_initialize_lang(true);
bind_lang_domain('default', get_path_lang_dir('default'));
bind_lang_domain('default', get_path_lang_dir('install'));
?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title><?php e__('Error!'); ?> <?php echo GML_VERSION ?> <?php e__('Install'); ?></title>
<link rel="stylesheet" href="install/install.css">
<script src="<?php echo GML_JS_URL ?>/jquery-1.12.4.min.js"></script>
<script src="<?php echo GML_JS_URL ?>/jquery-migrate-1.4.1.min.js"></script>
</head>
<body>

<div id="ins_bar">
    <span id="bar_img"><img src="<?php echo GML_URL; ?>/install/img/logo.png" alt="gnuboard logo"></span>
    <span id="bar_txt">Message</span>
</div>
<h1><?php e__('Install GNUBOARD M first.'); ?></h1>
<div class="ins_inner">
    <p><?php e__('The following files could not be found.'); ?></p>
    <ul>
        <li><strong><?php echo GML_DATA_DIR.'/'.GML_DBCONFIG_FILE ?></strong></li>
    </ul>
    <p><?php e__('Please install the GNUBOARD and run again.'); ?></p>
    <div>
    <h3><?php e__('Select Language'); ?></h3>
        <?php echo get_lang_select_html('var_lang', $lang, 'class="install_select_lang"'); ?>
    </div>
    <div class="inner_btn">
        <a href="<?php echo GML_URL; ?>/install/"><?php echo GML_VERSION ?> <?php e__('Install'); ?></a>
    </div>
</div>
<div id="ins_ft">
    <strong><?php e__('GNUBOARD M'); ?></strong>
    <p>GPL! OPEN SOURCE GNUBOARD</p>
</div>

<script>
jQuery(function($){
    var install_url = "<?php echo $_SERVER['PHP_SELF']; ?>";
    $("select.install_select_lang").change(function () {
        location.replace(install_url + "?lang="+ this.value);
    });
});
</script>

</body>
</html>