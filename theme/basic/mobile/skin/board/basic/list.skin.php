<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

$btn_top_class = isset($view) ? 'view_is_list btn_top' : 'btn_top top';

$board_mobile_subject = $board['bo_mobile_subject'] ? $board['bo_mobile_subject'] : $board['bo_subject'];

$colspan = 2;
if ($is_admin) $colspan++;

for ($i=0; $i<count($list); $i++) {
    $list[$i]['notice_class'] = $list[$i]['is_notice'] ? "bo_notice" : '';
    $list[$i]['icon_new'] = isset($list[$i]['icon_new']) ? $list[$i]['icon_new'] : '';
    $list[$i]['icon_hot'] = isset($list[$i]['icon_hot']) ? $list[$i]['icon_hot'] : '';
    $list[$i]['icon_file'] = isset($list[$i]['icon_file']) ? $list[$i]['icon_file'] : '';
    $list[$i]['icon_link'] = isset($list[$i]['icon_link']) ? $list[$i]['icon_link'] : '';
    $list[$i]['icon_secret'] = isset($list[$i]['icon_secret']) ? $list[$i]['icon_secret'] : '';

    if ($list[$i]['comment_cnt']) {
        $list[$i]['comment_cnt_html'] = '<span class="sound_only">'.__('Comments').'</span><i class="fa fa-commenting-o" aria-hidden="true"></i>'. $list[$i]['comment_cnt']. '<span class="sound_only">'.__('Count').'</span>';
    }
}

if (count($list) == 0) {
    $no_list = '<li class=\"empty_table\">'.__('No posts found.').'</li>';
}

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
?>

<!-- Start Board List { -->
<div id="bo_list">

    <?php if ($is_category) { ?>
    <nav id="bo_cate">
        <h2><?php echo $board_mobile_subject ?> <?php e__('Category'); ?></h2>
        <ul id="bo_cate_ul">
            <?php echo $category_option ?>
        </ul>
    </nav>
    <?php } ?>
	
	<div class="bo_option">
		<button class="sch_tog"><i class="fa fa-search" aria-hidden="true"></i><span class="sound_only"><?php e__('Search'); ?></span></button>
		<?php if ($rss_href || $write_href || $admin_href) { ?>
		<ul class="btn_top2">
		    <?php if ($rss_href) { ?><li><a href="<?php echo $rss_href ?>" class="btn_b01"><i class="fa fa-rss" aria-hidden="true"></i><span class="sound_only">RSS</span></a></li><?php } ?>
		    <?php if ($admin_href) { ?><li><a href="<?php echo $admin_href ?>" class="btn_admin"><?php e__('Admin'); ?></a></li><?php } ?>
		    <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02"><?php e__('Write'); ?></a></li><?php } ?>
		</ul>
		<?php } ?>
	</div>

	<!-- Board Search Start { -->
	<fieldset id="bo_sch">
	    <legend><?php e__('Search for posts'); ?></legend>
	
	    <form name="fsearch" method="get">
	    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
	    <input type="hidden" name="sca" value="<?php echo $sca ?>">
	    <input type="hidden" name="sop" value="and">
	    <label for="sfl" class="sound_only"><?php e__('Search target'); ?></label>
	    <select name="sfl" id="sfl">
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
	
	<div id="bo_list_total">
        <span><?php echo sprintf(n__('%s total', '%s totals', $show_total_count), $show_total_count); ?></span>
        , <?php echo sprintf(n__('%s page', '%s pages', $page), $page); ?>
    </div>

    <form name="fboardlist" id="fboardlist" action="<?php echo GML_BBS_URL ?>/board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="sw" value="">

    <div class="list_03">
        <?php if ($is_admin) { ?>
        <div class="list_chk all_chk">
            <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);">
            <label for="chkall"><span class="sound_only"><?php e__('All current page posts'); ?> </span><?php e__('Select All'); ?></label>
        </div>
        <?php } ?>
        <ul>
            <?php
            for ($i=0; $i<count($list); $i++) {
            ?>
            <li class="<?php echo $list[$i]['notice_class'] ?>">

                <div class="bo_subject">
					
					<?php if ($list[$i]['is_notice']) { ?>
                        <strong class="notice_icon"><?php e__('Notice') ?></strong>
                    <?php } ?>
                    <?php if ($is_category && $list[$i]['ca_name']) { ?>
                    <a href="<?php echo $list[$i]['ca_name_href'] ?>" class="bo_cate_link"><?php echo $list[$i]['ca_name'] ?></a>
                    <?php } ?>

                    <a href="<?php echo $list[$i]['href'] ?>" class="bo_subject">
                    	<?php if ($is_admin) { ?>
		                <span class="bo_chk li_chk">
		                    <label for="chk_wr_id_<?php echo $i ?>"><span class="sound_only"><?php echo $list[$i]['subject'] ?></span></label>
		                    <input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
		                </span>
		                <?php } ?>
                        <?php echo $list[$i]['icon_reply']; ?>
                        
                        <?php echo $list[$i]['icon_secret'] ?>

                        <?php echo $list[$i]['subject'] ?>

                        <?php if ($list[$i]['icon_new']) { ?>
			            <span class="new_icon">N<span class="sound_only"><?php e__('New'); ?></span></span>
				        <?php } ?>
                        <?php echo $list[$i]['icon_hot'] ?>
                        <?php echo $list[$i]['icon_file'] ?>
                        <?php echo $list[$i]['icon_link'] ?>
                    </a>
                </div>
                <div class="bo_info">
                    <span class="sound_only"><?php e__('Writer') ?></span><span class="bo_guest"><?php echo $list[$i]['name'] ?></span>
                    <span class="sound_only"><?php e__('view') ?></span><span class="bo_view"><i class="fa fa-eye" aria-hidden="true"></i> <?php echo $list[$i]['wr_hit'] ?></span>
                    <span class="sound_only"><?php e__('data') ?></span><span class="bo_date"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $list[$i]['datetime2'] ?></span>
                	<span class="sound_only"><?php e__('Comment') ?></span><span class="bo_cmt"><i class="fa fa-commenting-o" aria-hidden="true"></i> <?php echo $list[$i]['wr_comment']; ?></span>
                </div>

            </li><?php } ?>
            <?php echo $no_list // No posts ?>
        </ul>
    </div>

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
