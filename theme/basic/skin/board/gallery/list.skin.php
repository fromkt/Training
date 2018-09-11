<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages
include_once(GML_LIB_PATH.'/thumbnail.lib.php');

$li_class = array();
for ($i=0; $i<count($list); $i++) {
    $classes = array();

    $classes[] = 'gall_li';
    $classes[] = 'col-gn-'.$bo_gallery_cols;

    if( $i && ($i % $bo_gallery_cols == 0) ){
        $classes[] = 'box_clear';
    }

    if( $wr_id && $wr_id == $list[$i]['wr_id'] ){
        $classes[] = 'gall_now';
    }

    $li_class[$i] = implode(' ', $classes);

    if ($list[$i]['is_notice']) {   // notice
        $list[$i]['img_content'] = '<span class="is_notice">'.__('Notice').'</span>';
    } else {
        $thumb = get_list_thumbnail($board['bo_table'], $list[$i]['wr_id'], $board['bo_gallery_width'], $board['bo_gallery_height'], false, true);

        if($thumb['src']) {
            $img_content = '<img src="'.$thumb['src'].'" alt="'.$thumb['alt'].'" >';
        } else {
            $img_content = '<span class="no_image">no image</span>';
        }
        $list[$i]['img_content'] = $img_content;
    }

    if ($wr_id == $list[$i]['wr_id']) {
        $list[$i]['subject_sound_only'] = "<span class=\"bo_current\">".__('Reading')."</span>";
    } else {
        $list[$i]['subject_sound_only'] = $list[$i]['num'];
    }


    if ($list[$i]['comment_cnt']) {
        $list[$i]['show_comment_cnt'] = '<span class="sound_only">'.__('Comment').'</span><span class="cnt_cmt">'. $list[$i]['wr_comment']. '</span>';
    }

    $list[$i]['icon_new'] = isset($list[$i]['icon_new']) ? rtrim($list[$i]['icon_new']) : "";
    $list[$i]['icon_hot'] = isset($list[$i]['icon_hot']) ? rtrim($list[$i]['icon_hot']) : "";
    $list[$i]['icon_secret'] = isset($list[$i]['icon_secret']) ? rtrim($list[$i]['icon_secret']) : "";
}

if (count($list) == 0) {
    $no_list = '<li class="empty_list">'.__('No posts found.').'</li>';
}

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
?>

<!-- Start board page information { -->
<div id="bo_list_total">
    <span><?php echo sprintf(n__('%s total', '%s totals', $show_total_count), $show_total_count); ?></span>
    , <?php echo sprintf(n__('%s page', '%s pages', $page), $page); ?>
</div>
<!-- } End board page information -->

<!-- Start Board List { -->
<div id="bo_gall" style="width:<?php echo $width; ?>">
	<?php if ($is_category) { ?>
    <nav id="bo_cate">
        <h2><?php echo $board['bo_subject'] ?> <?php e__('Category'); ?></h2>
        <ul id="bo_cate_ul">
            <?php echo $category_option ?>
        </ul>
    </nav>
    <?php } ?>

	<!-- Start board page buttons, Search { -->
    <div id="bo_btn_top">

    	<!-- Board Search Start { -->
	    <fieldset id="bo_sch">
	        <legend><?php e__('Search for posts'); ?></legend>
	        <form name="fsearch" method="get">
	        <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
	        <input type="hidden" name="sca" value="<?php echo $sca ?>">
	        <input type="hidden" name="sop" value="and">
	        <label for="sfl" class="sound_only"><?php e__('Search target'); ?></label>
	        <select name="sfl" id="sfl">
	            <?php echo get_board_sfl_select_options($sfl); ?>
	        </select>
	        <label for="stx" class="sound_only"><?php e__('Search term'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label>
	        <input type="text" name="stx" value="<?php echo $stx ?>" required id="stx" class="sch_input" size="25" maxlength="20" placeholder="<?php e__('Enter search term'); ?>">
	        <button type="submit" value="<?php e__('Search'); ?>" class="sch_btn"><i class="fa fa-search" aria-hidden="true"></i><span class="sound_only"><?php e__('Search'); ?></span></button>
	        </form>
	    </fieldset>
	    <!-- } Board Search End -->

        <?php if ($rss_href || $admin_href || $write_href) { ?>
        <ul id="gall_allchk" class="btn_bo_user">
        	<?php if ($is_admin) { ?>
		    <li class="all_chk">
		        <label for="chkall"><span class="sound_only"><?php e__('All current page posts'); ?></span><?php e__('All check'); ?></label>
		        <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);">
		    </li>
		    <?php } ?>
            <?php if ($rss_href) { ?><li><a href="<?php echo $rss_href ?>" class="btn_b01 btn"><i class="fa fa-rss" aria-hidden="true"></i><span class="sound_only">RSS</span></a></li><?php } ?>
            <?php if ($admin_href) { ?><li><a href="<?php echo $admin_href ?>" class="btn_admin btn"><?php e__('Admin'); ?></a></li><?php } ?>
            <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02 btn"><?php e__('Write'); ?></a></li><?php } ?>
        </ul>
        <?php } ?>
    </div>
    <!-- } End board page buttons, Search -->

    <form name="fboardlist"  id="fboardlist" action="<?php echo GML_BBS_URL ?>/board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="sw" value="">

    <ul id="gall_ul" class="gall_row">
        <?php
        for ($i=0; $i<count($list); $i++) {
         ?>
        <li class="<?php echo $li_class[$i] ?>">
            <div class="gall_box">
                <div class="gall_chk li_chk">
                <?php if ($is_admin) { ?>
                <label for="chk_wr_id_<?php echo $i ?>" class="sound_only"><?php echo $list[$i]['subject'] ?></label>
                <input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
                <?php } ?>
                <span class="sound_only"><?php echo $list[$i]['subject_sound_only'] ?></span>
                </div>
                <div class="gall_con">
                    <div class="gall_img_area">
                        <a href="<?php echo $list[$i]['href'] ?>" class="gall_img"><?php echo $list[$i]['img_content'] ?></a>
                        <div class="gall_recomm">
	                        <?php if ($is_good) { ?><span class="reco reco_good"><span class="sound_only"><?php e__('Good'); ?></span><i class="fa fa-thumbs-o-up" aria-hidden="true"></i> <?php echo $list[$i]['wr_good'] ?></span><?php } ?>
	                        <?php if ($is_nogood) { ?><span class="reco reco_nogood"><span class="sound_only"><?php e__('Bad'); ?></span><i class="fa fa-thumbs-o-down" aria-hidden="true"></i> <?php echo $list[$i]['wr_nogood'] ?></span><?php } ?>
	                    </div>
                    </div>
                    <div class="gall_text_href">
                        <?php
                        if ($is_category && $list[$i]['ca_name']) {
                         ?>
                        <a href="<?php echo $list[$i]['ca_name_href'] ?>" class="bo_cate_link"><?php echo $list[$i]['ca_name'] ?></a>
                        <?php } ?>
                        <a href="<?php echo $list[$i]['href'] ?>" class="bo_tit">
                        	<?php echo $list[$i]['icon_secret'] ?>
                            <?php echo $list[$i]['subject'] ?>
                            <?php echo $list[$i]['show_comment_cnt'] ?>
                            <?php echo $list[$i]['icon_hot'] ?>
                            <?php if ($list[$i]['icon_new']) { ?>
				            <span class="new_icon">N<span class="sound_only"><?php e__('New'); ?></span></span>
					        <?php } ?>
                        </a>
                    </div>
                    <div class="gall_info">
                        <span class="sound_only"><?php e__('Writer'); ?></span><?php echo $list[$i]['name'] ?>
                        <span class="align_right">
                        	<span class="sound_only"><?php e__('Date'); ?></span>
                        	<i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $list[$i]['datetime2'] ?>
                        	<span class="sound_only"><?php e__('Hit'); ?></span>
                        	<i class="fa fa-eye" aria-hidden="true"></i> <?php echo $list[$i]['wr_hit'] ?>
                        </span>
                    </div>
                </div>
            </div>
        </li>
        <?php } ?>
        <?php echo $no_list; // No posts ?>
    </ul>

     <?php if ($list_href || $is_admin || $write_href) { ?>
    <div class="bo_fx">
        <?php if ($list_href || $write_href) { ?>
        <ul class="btn_bo_user_btm">
            <?php if ($is_admin) { ?>
            <li><button type="submit" name="btn_submit" value="delete_selection" onclick="document.pressed=this.value" class="btn btn_b01"><?php e__('Delete Selection'); ?></button></li>
            <li><button type="submit" name="btn_submit" value="copy_selection" onclick="document.pressed=this.value" class="btn btn_b01"><?php e__('Copy Selection'); ?></button></li>
            <li><button type="submit" name="btn_submit" value="move_selection" onclick="document.pressed=this.value" class="btn btn_b01"><?php e__('Move Selection'); ?></button></li>
            <?php } ?>
            <?php if ($list_href) { ?><li class="btn_align_right"><a href="<?php echo $list_href ?>" class="btn_b01 btn"><?php e__('List'); ?></a></li><?php } ?>
            <?php if ($write_href) { ?><li class="btn_align_right"><a href="<?php echo $write_href ?>" class="btn_b02 btn"><?php e__('Write'); ?></a></li><?php } ?>
        </ul>
        <?php } ?>
    </div>
    <?php } ?>
    </form>
</div>

<?php if($is_admin) { ?>
<noscript>
<p><?php e__('If you are not using JavaScript, please be careful because you can delete the selection immediately without a separate verification process.'); ?></p>
</noscript>
<?php } ?>

<!-- Pagination -->
<?php echo $write_pages;  ?>

<!-- } End Board List -->
