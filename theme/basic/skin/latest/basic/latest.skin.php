<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/style.css">', 0);
?>

<div class="lt">
    <h2 class="lat_title"><a href="<?php echo $see_more_href ?>"><?php echo $bo_subject; ?></a></h2>
    <ul class="lt_ul">
    <?php for ($i=0; $i<count($list); $i++) { ?>
        <li>
            <?php echo $list[$i]['img_thumbnail'] ?>
	        <?php if ($list[$i]['icon_secret']) { ?>
	            <i class="fa fa-lock" aria-hidden="true"></i><span class="sound_only"><?php e__('Secret'); ?></span>
	        <?php } ?>

            <a href="<?php echo $list[$i]['href'] ?>" class="lt_tit"><?php echo $list[$i]['show_subject'] ?></a>

            <?php if ($list[$i]['icon_new']) { ?>
            <span class="new_icon">N<span class="sound_only"><?php e__('New'); ?></span></span>
	        <?php } ?>

	        <?php if ($list[$i]['icon_hot']) { ?>
	            <span class="hot_icon">H<span class="sound_only"><?php e__('Hot'); ?></span></span>
	        <?php } ?>
	        <ul class="lct_info">
				<li class="lt_nick"><span class="sound_only"><?php e__('Writer'); ?></span><?php echo $list[$i]['name']; ?></li>
				<li class="lt_date"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $list[$i]['datetime2']; ?></li>
				<?php if ($list[$i]['comment_cnt']) { ?>
		        <li class="lt_comnt"><i class="fa fa-commenting-o" aria-hidden="true"></i> <?php echo $list[$i]['comment_cnt'] ?></li>
		        <?php } ?>
			</ul>
        </li>
    <?php }  ?>
    <?php echo $show_no_list; // No recent posts ?>
    </ul>
    <a href="<?php echo $see_more_href ?>" class="lt_more"><span class="sound_only"> <?php echo $bo_subject ?></span><?php e__('More'); ?></a>
</div>
