<?php
$sub_menu = '100900';
include_once('./_common.php');

if ($is_admin != 'super')
    alert(__('Only the Super administrator can access it.'), GML_URL);

$gml['title'] = __('Delete Cache File Batch');
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
    echo '<p>'.__('Failed to open Cache directory.').'</p>';
}

$cnt=0;

echo '<ul class="del_ul">'.PHP_EOL;
$files = glob(GML_DATA_PATH.'/cache/latest-*');
if (is_array($files)) {
    foreach ($files as $cache_file) {
        $cnt++;
        unlink($cache_file);
        echo '<li>'.$cache_file.'</li>'.PHP_EOL;

        flush();

        if ($cnt%10==0) 
            echo PHP_EOL;
    }
}

gml_delete_all_cache();

echo '<li>'.__('Completed').'</li></ul>'.PHP_EOL;
echo '<div class="local_desc01 local_desc"><p><strong>'.sprintf(__('%s cachefile data has been deleted.'), $cnt).'</strong><br>'.__('You may complete the program.').'</p></div>'.PHP_EOL;
?>

<?php
include_once('./admin.tail.php');
?>