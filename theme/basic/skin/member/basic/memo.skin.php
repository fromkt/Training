<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<!-- Start Memo List { -->
<div id="memo_list" class="new_win">
    <h1 id="win_title"><?php echo $gml['title'] ?></h1>
    <div class="new_win_con">
        <ul class="win_ul">
            <li class="<?php echo ($kind == 'recv') ? 'selected' : '' ?>"><a href="./memo.php?kind=recv"><?php e__('Received Memo'); ?></a></li>
            <li class="<?php echo ($kind == 'send') ? 'selected' : '' ?>"><a href="./memo.php?kind=send"><?php e__('Sent Memo'); ?></a></li>
            <li><a href="./memo_form.php"><?php e__('Write Memo'); ?></a></li>
        </ul>
        <div class="win_total">
            <span>
                <?php echo ($kind == 'recv') ? sprintf(n__('%s total memo received', '%s totals memo received', $total_count), $total_count) : sprintf(n__('%s total memo sent', '%s totals memo sent', $total_count), $total_count) ; ?>
            </span>
        </div>
        <div class="list_02">
            <ul>
	            <?php for ($i=0; $i<count($list); $i++) {  ?>
	            <li>
                    <?php if (!$list[$i]['is_read']) { ?>
	            	<span class="memo_view_ico"><i class="fa fa-envelope-open-o" aria-hidden="true"></i><b class="sound_only"><?php e__('Readed Memo') ?></b></span>
                    <?php } else { ?>
                    <span class="memo_view memo_view_ico"><i class="fa fa-envelope-o" aria-hidden="true"></i><b class="sound_only"><?php echo e__('Unreaded Memo') ?></b></span>
                    <?php } ?>
	                <span class="memo_name"><a href="<?php echo $list[$i]['view_href'] ?>"><?php echo $list[$i]['mb_nick'] ?></a></span>
	                <span class="memo_datetime"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $list[$i]['send_datetime'] ?> - <?php echo $list[$i]['read_datetime'] ?> <a href="<?php echo $list[$i]['del_href'] ?>" onclick="del(this.href); return false;" class="memo_del"><i class="fa fa-trash-o" aria-hidden="true"></i> <span class="sound_only"><?php e__('Delete'); ?></span></a></span>
	            </li>
	            <?php } ?>
	            <?php if ($i==0) { echo '<li class="empty_table">'.__('No Memos.').'</li>'; }  ?>
            </ul>
        </div>

        <!-- Page -->
        <?php echo $write_pages; ?>

        <p class="win_desc">
            <?php echo sprintf(__('The maximum number of days to keep Memos is <strong>%s</strong> days.'), $config['cf_memo_del']); ?>
        </p>

        <div class="win_btn">
            <button type="button" onclick="window.close();" class="btn_close"><?php e__('Close Window'); ?></button>
        </div>
    </div>
</div>
<!-- } End Memo List -->
