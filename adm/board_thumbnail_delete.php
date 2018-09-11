<?php
$sub_menu = '300100';
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

if(!$board['bo_table'])
    alert(__('This bulletin board does not exist.'));

$gml['title'] = sprintf(__('Delete thumbnail - board %s'), $board['bo_subject']);
include_once('./admin.head.php');
?>

<div class="local_desc02 local_desc">
    <p>
        <?php e__('Do not stop the program from running until you receive a completion message.'); ?>
    </p>
</div>

<?php
$dir = GML_DATA_PATH.'/file/'.$bo_table;

$cnt = 0;
if(is_dir($dir)) {
    echo '<ul>';
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

    echo '<li>'.__('Completed').'</li></ul>'.PHP_EOL;
    echo '<div class="local_desc01 local_desc"><p><strong>'.sprintf(__('%s thumbnails have been deleted.'), $cnt).'</strong></p></div>'.PHP_EOL;
} else {
    echo '<p>'.__('Attachment directory does not exist.').'</p>';
}
?>

<div class="btn_confirm01 btn_confirm"><a href="./board_form.php?w=u&amp;bo_table=<?php echo $bo_table; ?>&amp;<?php echo $qstr; ?>"><?php e__('Return to Modify Bulletin'); ?></a></div>

<?php
include_once('./admin.tail.php');
?>