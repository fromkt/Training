<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages
$data_path = '../'.GML_DATA_DIR;

if (!$title) $title = sprintf(__('Install %s'), GML_VERSION);
?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?php echo $title; ?></title>
<link rel="stylesheet" href="install.css">
<link rel="stylesheet" href="../js/font-awesome/css/font-awesome.min.css">
</head>
<body>

<div id="ins_bar">
    <span id="bar_img"><img src="./img/logo.png" alt="gnuboard logo"></span>
    <span id="bar_txt">INSTALLATION</span>
</div>

<?php
// 파일이 존재한다면 설치할 수 없다.
$dbconfig_file = $data_path.'/'.GML_DBCONFIG_FILE;
if (file_exists($dbconfig_file)) {
?>
<h1><?php echo sprintf(__('The program %s is already installed.'), GML_VERSION); ?></h1>

<div class="ins_inner">
    <p><?php e__('The program is already installed.'); ?><br /><?php e__('To perform a new installation, please delete and refresh the DB config file.'); ?></p>
</div>
<?php
    exit;
}
?>

<?php
$exists_data_dir = true;
// data 디렉토리가 있는가?
if (!is_dir($data_path))
{
?>
<h1><?php echo sprintf(__('Please check the information below to install %s.'), GML_VERSION); ?></h1>

<div class="ins_inner">
    <p>
        <?php echo sprintf(__('Please create the %s directory down in the root directory.'), GML_DATA_DIR); ?><br />
        <?php echo '('.__('The root directory is where the "common.php" file resides.').')'; ?><br /><br />
        $> mkdir <?php echo GML_DATA_DIR ?><br /><br />
        <?php e__('IF Window OS, please create a "data" folder.'); ?><br /><br />
        <?php e__('Please refresh your browser after executing the above command.'); ?>
    </p>
</div>
<?php
    $exists_data_dir = false;
}
?>

<?php
$write_data_dir = true;
// data 디렉토리에 파일 생성 가능한지 검사.
if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
    $sapi_type = php_sapi_name();
    if (substr($sapi_type, 0, 3) == 'cgi') {
        if (!(is_readable($data_path) && is_executable($data_path)))
        {
        ?>
        <div class="ins_inner">
            <p>
                <?php echo sprintf(__('Please change the %s directory permission to %s.'), GML_DATA_DIR, '705'); ?><br /><br />
                $> chmod 705 <?php echo GML_DATA_DIR ?> <?php e__('OR'); ?> chmod uo+rx <?php echo GML_DATA_DIR ?><br /><br />
                <?php e__('Please refresh your browser after executing the above command.'); ?>
            </p>
        </div>
        <?php
            $write_data_dir = false;
        }
    } else {
        if (!(is_readable($data_path) && is_writeable($data_path) && is_executable($data_path)))
        {
        ?>
        <div class="ins_inner">
            <p>
                <?php echo sprintf(__('Please change the %s directory permission to %s.'), GML_DATA_DIR, '707'); ?><br /><br />
                $> chmod 707 <?php echo GML_DATA_DIR ?> <?php e__('OR'); ?> chmod uo+rwx <?php echo GML_DATA_DIR ?><br /><br />
                <?php e__('Please refresh your browser after executing the above command.'); ?>
            </p>
        </div>
        <?php
            $write_data_dir = false;
        }
    }
} else {
    
    if ( !(is_readable($data_path) && is_writeable($data_path)) ) { ?>
        <div class="ins_inner">
            <p>
                <?php echo sprintf(__('Have not write permissions in the %s folder.'), GML_DATA_DIR); ?>
                <br><br>
                <?php e__('Please change the %s directory a write permission, then refresh your browser.'); ?>
            </p>
        </div>
    <?php
    $write_data_dir = false;
    }
}
?>