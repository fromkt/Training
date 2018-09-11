<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/style.css">', 0);
?>

<div class="lt">
    <a href="<?php echo $see_more_href ?>" class="lt_title"><strong><?php echo $bo_subject ?></strong></a>
    <ul>
    <?php for ($i=0; $i<count($list); $i++) { ?>
        <li>
        <?php if ($list[$i]['icon_secret']) { ?>
            <i class="fa fa-lock" aria-hidden="true"></i>
        <?php } ?>

            <a href="<?php echo $list[$i]['href'] ?>" class="lt_tit">
                <?php echo $list[$i]['show_subject'] ?>
            <?php if ($list[$i]['icon_new']) { ?>
                <span class="new_icon">N</span>
            <?php } ?>
            <?php if ($list[$i]['icon_file']) { ?>
                <i class="fa fa-download" aria-hidden="true"></i>
            <?php } ?>
            <?php if ($list[$i]['icon_link ']) { ?>
                <?php echo $list[$i]['icon_link'] ?>
            <?php } ?>
            <?php if ($list[$i]['icon_hot']) { ?>
                <?php echo $list[$i]['icon_hot'] ?>
            <?php } ?>
            </a>

            <!-- // Writer name, Date            	
            <div class="lt_info">
                <?php echo $list[$i]['name'] ?>
                <span class="lt_date"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $list[$i]['datetime2'] ?></span>
            </div>
            -->
            
            <div class="cnt_cmt_bx">
            	<?php if ($list[$i]['comment_cnt']) { ?>
                    <span class="sound_only"><?php e__('Comment') ?></span><?php echo $list[$i]['comment_cnt']; ?><span class="sound_only"><?php e__('Count') ?></span>
                <?php } ?>  
            </div>
        </li>
    <?php } ?>
    <?php echo $show_no_list // No recent posts ?>
    </ul>
</div>
