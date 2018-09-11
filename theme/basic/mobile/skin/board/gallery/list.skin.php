<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages
include_once(GML_LIB_PATH.'/thumbnail.lib.php');

$btn_top_class = isset($view) ? 'view_is_list btn_top' : 'btn_top top';

$board_mobile_subject = $board['bo_mobile_subject'] ? $board['bo_mobile_subject'] : $board['bo_subject'];

for ($i=0; $i<count($list); $i++) {
    if ($wr_id == $list[$i]['wr_id']) {
        $list[$i]['subject_sound_only'] = '<span class="bo_current">'.__('Reading').'</span>';
        $list[$i]['gall_li_class'] = 'gall_now';
    } else {
        $list[$i]['subject_sound_only'] =  $list[$i]['num'];
    }

    if ($list[$i]['is_notice']) { // notice
        $list[$i]['img_content'] = '<strong style="width:'. $board['bo_mobile_gallery_width']. 'px;height:'. $board['bo_mobile_gallery_height']. 'px">'.__('Notice').'</strong>';
    } else {
        $thumb = get_list_thumbnail($board['bo_table'], $list[$i]['wr_id'], $board['bo_mobile_gallery_width'], $board['bo_mobile_gallery_height']);

        if($thumb['src']) {
            $img_content = '<img src="'.$thumb['src'].'" alt="'.$thumb['alt'].'" width="'.$board['bo_mobile_gallery_width'].'" height="'.$board['bo_mobile_gallery_height'].'">';
        } else {
            $img_content = '<span class="no-img">no image</span>';
        }

        $list[$i]['img_content'] = $img_content;
    }

    if ($list[$i]['comment_cnt']) {
        $list[$i]['show_comment_cnt'] = '<span class="sound_only">'.__('Comments').'</span>'. $list[$i]['comment_cnt']. '<span class="sound_only">'.__('Count').'</span>';
    }

    $list[$i]['icon_new'] = isset($list[$i]['icon_new']) ? $list[$i]['icon_new'] : '';
    $list[$i]['icon_hot'] = isset($list[$i]['icon_hot']) ? $list[$i]['icon_hot'] : '';
}

if (count($list) == 0) {
    $no_list = '<li class=\"empty_list\">'.__('No posts found.').'</li>';
}

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
?>

<script src="<?php echo GML_JS_URL; ?>/jquery.fancylist.js"></script>

<!-- Start Board List { -->
<div id="bo_gall">

    <?php if ($is_category) { ?>
    <nav id="bo_cate">
        <h2><?php echo $board_mobile_subject ?> <?php e__('Category'); ?></h2>
        <ul id="bo_cate_ul">
            <?php echo $category_option ?>
        </ul>
    </nav>
    <?php } ?>
    
    <?php if ($rss_href || $write_href || $admin_href) { ?>
	<div class="bo_option">
		<button class="sch_tog"><i class="fa fa-search" aria-hidden="true"></i><span class="sound_only"><?php e__('Search box'); ?></span></button>
		<ul class="btn_top2">
		    <?php if ($rss_href) { ?><li><a href="<?php echo $rss_href ?>" class="btn_b01"><i class="fa fa-rss" aria-hidden="true"></i><span class="sound_only">RSS</span></a></li><?php } ?>
		    <?php if ($admin_href) { ?><li><a href="<?php echo $admin_href ?>" class="btn_admin"><?php e__('Admin'); ?></a></li><?php } ?>
		    <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02"><?php e__('Write'); ?></a></li><?php } ?>
		</ul>
	</div>
	<?php } ?>
	
	<!-- Board Search Start { -->
	<fieldset id="bo_sch">
	    <legend><?php e__('Search for posts'); ?></legend>
	
	    <form name="fsearch" method="get">
	    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
	    <input type="hidden" name="sca" value="<?php echo $sca ?>">
	    <input type="hidden" name="sop" value="and">
	    <label for="sfl" class="sound_only"><?php e__('Search target'); ?></label>
	    <select name="sfl">
	        <option value="wr_subject"<?php echo get_selected($sfl, 'wr_subject', true); ?>><?php e__('Subject'); ?></option>
	        <option value="wr_content"<?php echo get_selected($sfl, 'wr_content'); ?>><?php e__('Content'); ?></option>
	        <option value="wr_subject||wr_content"<?php echo get_selected($sfl, 'wr_subject||wr_content'); ?>><?php e__('Subject+Content'); ?></option>
	        <option value="wr_name,1"<?php echo get_selected($sfl, 'wr_name,1'); ?>><?php e__('Writer') ?></option>
	        <option value="wr_name,0"<?php echo get_selected($sfl, 'wr_name,0'); ?>><?php e__('Commenter') ?></option>
	    </select>
	    <input name="stx" value="<?php echo $stx ?>" placeholder="<?php e__('Search term'); ?>(<?php e__('Required'); ?>)" required id="stx" class="sch_input" size="15" maxlength="20">
	    <button type="submit" value="<?php e__('Search'); ?>" class="sch_btn"><i class="fa fa-search" aria-hidden="true"></i> <span class="sound_only"><?php e__('Search'); ?></span></button>
	    </form>
	</fieldset>
	<script>
		$(document).ready(function(){
			$(".sch_tog").click(function(){
				$("#bo_sch").toggle();
			});
		});
	</script>
	<!-- } Board Search End -->

    <div class="sound_only">
        <span><?php echo sprintf(n__('%s total', '%s totals', $show_total_count), $show_total_count); ?></span>
        , <?php echo sprintf(n__('%s page', '%s pages', $page), $page); ?>
    </div>

    <form name="fboardlist"  id="fboardlist" action="<?php echo GML_BBS_URL ?>/board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="sw" value="">

    <h2><?php e__('List of images') ?></h2>

    <?php if ($is_admin) { ?>
    <div id="gall_allchk" class="list_chk all_chk">
        <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);">
		<label for="chkall"><span class="sound_only"><?php e__('All current page posts'); ?></span><?php e__('Select All'); ?></label>
	</div>
    <?php } ?>

    <ul id="gall_ul">
        <?php for ($i=0; $i<count($list); $i++) {
        ?>
        <li class="gall_li <?php echo $list[$i]['gall_li_class'] ?>">
            <div class="gall_li_wr">

                <?php if ($is_admin) { ?>
                <span class="gall_li_chk bo_chk li_chk">
                    <label for="chk_wr_id_<?php echo $i ?>"><span class="sound_only"><?php echo $list[$i]['subject'] ?></span></label>
                    <input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
                </span>
                <?php } ?>
                
                <span class="sound_only">
                    <?php echo $list[$i]['subject_sound_only'] ?>
                </span>
				
				<div class="gall_img_area">
                	<a href="<?php echo $list[$i]['href'] ?>" class="gall_img"><?php echo $list[$i]['img_content'] ?></a>
                	<div class="gall_recomm">
						<?php if ($is_good) { ?><span class="reco reco_good"><span class="sound_only"><?php e__('Good') ?></span><strong><i class="fa fa-thumbs-o-up" aria-hidden="true"></i> <?php echo $list[$i]['wr_good'] ?></strong></span><?php } ?>
                        <?php if ($is_nogood) { ?><span class="reco reco_nogood"><span class="sound_only"><?php e__('Bad') ?></span><strong><i class="fa fa-thumbs-o-down" aria-hidden="true"></i> <?php echo $list[$i]['wr_nogood'] ?></strong></span><?php } ?>  
					</div>
                </div>

                <div class="gall_text_href">
                    <?php if ($is_category && $list[$i]['ca_name']) { ?>
                    <a href="<?php echo $list[$i]['ca_name_href'] ?>" class="bo_cate_link"><?php echo $list[$i]['ca_name'] ?></a>
                    <?php } ?>
                    <a href="<?php echo $list[$i]['href'] ?>" class="gall_li_tit">
                        <?php echo $list[$i]['subject'] ?>
                        <?php echo $list[$i]['show_comment_cnt'] ?>
                    </a>
                    <?php if ($list[$i]['icon_new']) { ?>
		            <span class="new_icon">N<span class="sound_only"><?php e__('New'); ?></span></span>
			        <?php } ?>
                    <?php echo $list[$i]['icon_hot']; ?>
                    
                    <div class="gall_writer">
                    	<span class="sound_only"><?php e__('Writer') ?> </span><?php echo $list[$i]['name'] ?>
                    </div>
                    <div class="gall_info">
                        <span class="sound_only"><?php e__('hits') ?> </span><strong><i class="fa fa-eye" aria-hidden="true"></i> <?php echo $list[$i]['wr_hit'] ?></strong>
                        <span class="sound_only"><?php e__('Date') ?> </span><span class="date"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $list[$i]['datetime2'] ?></span>
                    </div>
                </div>
            </div>
        </li>
        <?php } ?>
        <?php echo $no_list // No posts ?>
    </ul>

    <?php if ($list_href || $is_admin || $write_href) { ?>
    <div class="btn_top">
        <ul class="btn_bo_adm">
            <?php if ($list_href) { ?>
            <li class="btn_align_right"><a href="<?php echo $list_href ?>" class="btn_b02 btn"> <?php e__('List'); ?></a></li>
            <?php } ?> 
            <?php if ($is_admin) { ?>
            <li><button type="submit" name="btn_submit" value="delete_selection" onclick="document.pressed=this.value" class="btn btn_b01"><?php e__('Delete Selection'); ?></button></li>
            <li><button type="submit" name="btn_submit" value="copy_selection" onclick="document.pressed=this.value" class="btn btn_b01"><?php e__('Copy Selection'); ?></button></li>
            <li><button type="submit" name="btn_submit" value="move_selection" onclick="document.pressed=this.value" class="btn btn_b01"><?php e__('Move Selection'); ?></button></li>
            <?php } ?>
            <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02 btn"><?php e__('Write'); ?></a></li><?php } ?>
        </ul>
    </div>
    <?php } ?>
    </form>
</div>


<?php if($is_admin) { ?>
<noscript>
<p><?php e__('If you are not using JavaScript, please be careful because you can delete the selection immediately without a separate verification process.') ?></p>
</noscript>
<?php } ?>

<!-- Pagination -->
<?php echo $write_pages; ?>

<!-- } End Board List -->
