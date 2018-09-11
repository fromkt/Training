<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

$colspan = 5;

if ($is_admin) $colspan++;
if ($is_good) $colspan++;
if ($is_nogood) $colspan++;
if ($is_category) $colspan++;

$hit_sort_link = subject_sort_link('wr_hit', $qstr2, 1);
$good_sort_link = subject_sort_link('wr_good', $qstr2, 1);
$nogood_sort_link = subject_sort_link('wr_nogood', $qstr2, 1);
$datetime_sort_link = subject_sort_link('wr_datetime', $qstr2, 1);

foreach($list as $i => $v) {
    if ($v['is_notice']) {  // is notice
        $list[$i]['subject_head'] = '<strong class="notice_icon">'.__('Notice').'</strong>';
        $list[$i]['notice_class'] = "bo_notice";
    } else if ($wr_id == $v['wr_id']) {
        $list[$i]['subject_head'] = "<span class=\"bo_current\">".__('Reading')."</span>";
    } else {
        $list[$i]['subject_head'] = $v['num'];
    }

    $list[$i]['td_style'] = "padding-left:". ($list[$i]['reply'] ? (strlen($list[$i]['wr_reply'])*10) : '0'). "px";
    $list[$i]['icon_secret'] = isset($list[$i]['icon_secret']) ? rtrim($list[$i]['icon_secret']) : '';
}

if (count($list) == 0) {
    $no_list = '<tr><td colspan="'.$colspan.'" class="empty_table">'.__('No posts found.').'</td></tr>';
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
<div id="bo_list" style="width:<?php echo $width; ?>">
	<!-- Start Board Category { -->
    <?php if ($is_category) { ?>
    <nav id="bo_cate">
        <h2><?php echo get_board_gettext_titles($board['bo_subject']); ?> <?php e__('Category'); ?></h2>
        <ul id="bo_cate_ul">
            <?php echo $category_option ?>
        </ul>
    </nav>
    <?php } ?>
    <!-- } End Board Category -->

    <!-- Start board page information and buttons { -->
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
        <ul class="btn_bo_user">
            <?php if ($rss_href) { ?><li><a href="<?php echo $rss_href ?>" class="btn_b01 btn"><i class="fa fa-rss" aria-hidden="true"></i><span class="sound_only">RSS</span></a></li><?php } ?>
            <?php if ($admin_href) { ?><li><a href="<?php echo $admin_href ?>" class="btn_admin btn"><?php e__('Admin'); ?></a></li><?php } ?>
            <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02 btn"><?php e__('Write'); ?></a></li><?php } ?>
        </ul>
        <?php } ?>
    </div>
    <!-- } End board page information and buttons -->

    <form name="fboardlist" id="fboardlist" action="<?php echo GML_BBS_URL ?>/board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="sca" value="<?php echo $sca ?>">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="sw" value="">

    <div class="tbl_head01 tbl_wrap">
        <table>
        <caption><?php echo $board['bo_subject'] ?> <?php e__('List'); ?></caption>
        <thead>
        <tr>
            <?php if ($is_admin) { ?>
            <th scope="col" class="td_chk_all all_chk">
                <label for="chkall" class="sound_only"><?php e__('All current page posts'); ?></label>
                <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);">
            </th>
            <?php } ?>
            <th scope="col"><?php ep__('Num', 'Number'); ?></th>
            <?php if ($is_category) { ?>
            <th scope="col"><?php e__('Category'); ?></th>
            <?php } ?>
            <th scope="col"><?php e__('Subject'); ?></th>
            <th scope="col"><?php e__('Writer'); ?></th>
            <th scope="col"><?php echo $hit_sort_link ?><?php e__('hits'); ?></a></th>
            <?php if ($is_good) { ?><th scope="col"><?php echo $good_sort_link ?><?php e__('Good'); ?></a></th><?php } ?>
            <?php if ($is_nogood) { ?><th scope="col"><?php echo $nogood_sort_link ?><?php e__('Bad'); ?></a></th><?php } ?>
            <th scope="col"><?php echo $datetime_sort_link ?><?php e__('Date'); ?></a></th>
        </tr>
        </thead>
        <tbody>
        <?php for ($i=0; $i<count($list); $i++) { ?>
        <tr class="<?php echo $list[$i]['notice_class'] ?>">
            <?php if ($is_admin) { ?>
            <td class="td_chk li_chk">
                <label for="chk_wr_id_<?php echo $i ?>" class="sound_only"><?php echo $list[$i]['subject'] ?></label>
                <input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
            </td>
            <?php } ?>
            <td class="td_num2"><?php echo $list[$i]['subject_head'] ?></td>
            
            <?php if ($is_category) { ?>
			<td class="td_cate">
				<?php if ($is_category && $list[$i]['ca_name']) { ?>
                <a href="<?php echo $list[$i]['ca_name_href'] ?>" class="bo_cate_link"><?php echo $list[$i]['ca_name'] ?></a>
                <?php } ?>
			</td>
            <?php } ?>

            <td class="td_subject" style="<?php echo $list[$i]['td_style'] ?>">
                <div class="bo_tit">

                    <a href="<?php echo $list[$i]['href'] ?>">
                        <?php echo $list[$i]['icon_reply'] ?>
                        <?php echo $list[$i]['icon_secret'] ?>
                        <?php echo $list[$i]['subject'] ?>
                    </a>
                    <?php
                    // if ($list[$i]['file']['count']) { echo '<'.$list[$i]['file']['count'].'>'; }
                    if (isset($list[$i]['icon_file'])) echo rtrim($list[$i]['icon_file']);
                    if (isset($list[$i]['icon_link'])) echo rtrim($list[$i]['icon_link']);
                    if (isset($list[$i]['icon_hot'])) echo rtrim($list[$i]['icon_hot']);
                    ?>
                    <?php if ($list[$i]['icon_new']) { ?>
		            <span class="new_icon">N<span class="sound_only"><?php e__('New'); ?></span></span>
			        <?php } ?>
                    <?php if ($list[$i]['comment_cnt']) { ?><span class="sound_only"><?php e__('Comments'); ?></span><span class="cnt_cmt"><?php echo $list[$i]['wr_comment']; ?></span><span class="sound_only"><?php e__('Count'); ?></span><?php } ?>
                </div>

            </td>
            <td class="td_name sv_use"><?php echo $list[$i]['name'] ?></td>
            <td class="td_num"><i class="fa fa-eye" aria-hidden="true"></i> <?php echo $list[$i]['wr_hit'] ?></td>
            <?php if ($is_good) { ?><td class="td_num"><?php echo $list[$i]['wr_good'] ?></td><?php } ?>
            <?php if ($is_nogood) { ?><td class="td_num"><?php echo $list[$i]['wr_nogood'] ?></td><?php } ?>
            <td class="td_datetime"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $list[$i]['datetime2'] ?></td>
        </tr>
        <?php } ?>
        <?php echo $no_list; // No posts ?>
        </tbody>
        </table>
    </div>

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
