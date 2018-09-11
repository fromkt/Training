<?php
$sub_menu = '100920';
include_once('./_common.php');

if ($is_admin != 'super')
    alert(__('Only the Super administrator can access it.'), GML_URL);

$gml['title'] = __('Delete Thumbnail File Batch');
include_once('./admin.head.php');
?>

<div class="local_desc02 local_desc">
    <p>
        <?php e__('Do not stop the program from running until you receive a completion message.'); ?>
    </p>
</div>

<?php
$directory = array();
$dl = array('file', 'editor');

if( defined('GML_YOUNGCART_VER') ){
    $dl[] = 'item';
}

foreach($dl as $val) {
    if($handle = opendir(GML_DATA_PATH.'/'.$val)) {
        while(false !== ($entry = readdir($handle))) {
            if($entry == '.' || $entry == '..')
                continue;

            $path = GML_DATA_PATH.'/'.$val.'/'.$entry;

            if(is_dir($path))
                $directory[] = $path;
        }
    }
}

flush();

if (empty($directory)) {
    echo '<p>'.__('Failed to open Thumbnail directory.').'</p>';
}

$cnt=0;
echo '<ul class="del_ul">'.PHP_EOL;

foreach($directory as $dir) {
    $files = glob($dir.'/thumb-*');
    if (is_array($files)) {
        foreach($files as $thumbnail) {
            $cnt++;
            @unlink($thumbnail);

            echo '<li>'.$thumbnail.'</li>'.PHP_EOL;

            flush();

            if ($cnt%10==0)
                echo PHP_EOL;
        }
    }
}

echo '<li>'.__('Completed').'</li></ul>'.PHP_EOL;
echo '<div class="local_desc01 local_desc"><p><strong>'.sprintf(__('%s Thumbnail file data has been deleted.'), $cnt).'</strong><br>'.__('You may complete the program.').'</p></div>'.PHP_EOL;
?>

<?php
include_once('./admin.tail.php');
?>