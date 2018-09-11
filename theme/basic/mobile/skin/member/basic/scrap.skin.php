<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<div id="scrap" class="new_win">
    <h1 id="win_title"><?php echo $gml['title'] ?></h1>
	<div class="new_win_con">
	    <ul id="scrap_ul">
	        <?php for ($i=0; $i<count($list); $i++) { ?>
	        <li>
	        	<div class="scrap_left">
	        		<a href="<?php echo $list[$i]['opener_href_wr_id'] ?>" target="_blank" class="scrap_tit" onclick="opener.document.location.href='<?php echo $list[$i]['opener_href_wr_id'] ?>'; return false;"><?php echo $list[$i]['subject'] ?></a>
	            	<a href="<?php echo $list[$i]['opener_href'] ?>" target="_blank" class="scrap_cate" onclick="opener.document.location.href='<?php echo $list[$i]['opener_href'] ?>'; return false;"><?php echo $list[$i]['bo_subject'] ?></a>
	        		<span class="scrap_datetime"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $list[$i]['ms_datetime'] ?></span>
	        	</div>
	        	<a href="<?php echo $list[$i]['del_href']; ?>" onclick="del(this.href); return false;" class="scrap_del"><i class="fa fa-trash-o" aria-hidden="true"></i><span class="sound_only"><?php e__('Delete'); ?></span></a>
	        </li>
	        <?php } ?>
	        <?php if ($i == 0) echo "<li class=\"empty_list\">".__('No Data')."</li>"; ?>
	    </ul>
	</div>
    <?php echo get_paging($config['cf_mobile_pages'], $page, $total_page, "?$qstr&amp;page="); ?>

    <div class="win_btn">
        <button type="button" onclick="window.close();" class="btn_close"><?php e__('Close Window'); ?></button>
    </div>
</div>
