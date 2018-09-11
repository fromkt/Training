<?php
$sub_menu = '100910';
include_once('./_common.php');

if ($is_admin != 'super')
    alert(__('Only the Super administrator can access it.'), GML_URL);

$gml['title'] = __('Delete Captcha File Batch');
include_once('./admin.head.php');
?>

<div class="local_desc02 local_desc">
    <p>
        <?php e__('Do not stop the program from running until you receive a completion message.'); ?>
    </p>
</div>

<?php
flush();

if (!$dir=@opendir(GML_DATA_PATH.'/cache')) {
    echo '<p>'.__('Failed to open Captcha directory.').'</p>';
}

$cnt=0;
echo '<ul class="del_ul">'.PHP_EOL;
$files = glob(GML_DATA_PATH.'/cache/?captcha-*');
if (is_array($files)) {
    $before_time  = GML_SERVER_TIME - 3600; // 한시간전
    foreach ($files as $gcaptcha_file) {
        $modification_time = filemtime($gcaptcha_file); // 파일접근시간

        if ($modification_time > $before_time) continue;

        $cnt++;
        unlink($gcaptcha_file);
        echo '<li>'.$gcaptcha_file.'</li>'.PHP_EOL;

        flush();

        if ($cnt%10==0) 
            echo PHP_EOL;
    }
}

echo '<li>'.__('Completed').'</li></ul>'.PHP_EOL;
echo '<div class="local_desc01 local_desc"><p><strong>'.sprintf(__('%s captchafile data has been deleted.'), $cnt).'</strong><br>'.__('You may complete the program.').'</p></div>'.PHP_EOL;
?>

<?php
include_once('./admin.tail.php');
?>