<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

$colspan = 6;
if ($is_admin) $colspan++;

for ($i=0; $i<count($list); $i++) {
    $list[$i]['qa_status_class'] = $list[$i]['qa_status'] ? 'txt_done' : 'txt_rdy';
    $list[$i]['qa_status_icon'] = $list[$i]['qa_status'] ? ''.__('Answer completed') : ''.__('Answer waiting');
}

if ($i == 0) {
    $no_list = '<tr><td colspan="'.$colspan.'" class="empty_table">'.__('No Qa posts found.').'</td></tr>';
}

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$qa_skin_url.'/style.css">', 0);
?>

<div id="bo_list">

	<!-- Start QA info { -->
	<div id="bo_list_total">
        <span><?php echo sprintf(n__('%s total', '%s totals', $show_total_count), $show_total_count); ?></span>
        , <?php echo sprintf(n__('%s page', '%s pages', $page), $page); ?>
    </div>
    <!-- } End QA info -->

	<?php if ($category_option) { ?>
    <!-- Start Category { -->
    <nav id="bo_cate">
        <h2><?php echo $qaconfig['qa_title'] ?> <?php e__('Category'); ?></h2>
        <ul id="bo_cate_ul">
            <?php echo $category_option ?>
        </ul>
    </nav>
    <!-- } End Category -->
    <?php } ?>

	<!-- Start QA button { -->
    <div id="bo_btn_top">
    	<!-- Start Search post { -->
	    <fieldset id="bo_sch">
	        <legend><?php e__('Search Posts'); ?></legend>

	        <form name="fsearch" method="get">
	        <input type="hidden" name="sca" value="<?php echo $sca ?>">
	        <label for="stx" class="sound_only"><?php e__('Search'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label>
	        <input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required  class="sch_input" size="25" maxlength="15">
	        <button type="submit" value="<?php e__('Search'); ?>" class="sch_btn"><i class="fa fa-search" aria-hidden="true"></i><span class="sound_only"><?php e__('Search'); ?></span></button>
	        </form>
	    </fieldset>
	    <!-- } End Search post -->

        <?php if ($admin_href || $write_href) { ?>
        <ul class="btn_bo_user">
            <?php if ($admin_href) { ?><li><a href="<?php echo $admin_href ?>" class="btn_admin btn"><?php e__('Admin'); ?></a></li><?php } ?>
            <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02 btn"><?php e__('Write'); ?></a></li><?php } ?>
        </ul>
        <?php } ?>
    </div>
    <!-- } End QA button -->

    <form name="fqalist" id="fqalist" action="./qadelete.php" onsubmit="return fqalist_submit(this);" method="post">
    <input type="hidden" name="stx" value="<?php echo $stx; ?>">
    <input type="hidden" name="sca" value="<?php echo $sca; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">

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
            <th scope="col"><?php e__('Category'); ?></th>
            <th scope="col"><?php e__('Subject'); ?></th>
            <th scope="col"><?php e__('Writer'); ?></th>
            <th scope="col"><?php e__('Date'); ?></th>
            <th scope="col"><?php e__('State'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        for ($i=0; $i<count($list); $i++) {
        ?>
        <tr>
            <?php if ($is_admin) { ?>
            <td class="td_chk li_chk">
                <label for="chk_qa_id_<?php echo $i ?>" class="sound_only"><?php echo $list[$i]['subject']; ?></label>
                <input type="checkbox" name="chk_qa_id[]" value="<?php echo $list[$i]['qa_id'] ?>" id="chk_qa_id_<?php echo $i ?>">
            </td>
            <?php } ?>
            <td class="td_num"><?php echo $list[$i]['num']; ?></td>
            <td class="td_cate"><span><?php echo $list[$i]['category']; ?></span></td>
            <td class="td_subject">
                <a href="<?php echo $list[$i]['view_href']; ?>" class="bo_tit">
                    <?php echo $list[$i]['subject']; ?>
                    <?php if ($list[$i]['icon_file']) { ?>
                        <i class="fa fa-download" aria-hidden="true"></i>
                    <?php } ?>
                </a>
            </td>
            <td class="td_name"><?php echo $list[$i]['name']; ?></td>
            <td class="td_date"><?php echo $list[$i]['date']; ?></td>
            <td class="td_stat"><span class="<?php echo $list[$i]['qa_status_class'] ?>"><?php echo $list[$i]['qa_status_icon'] ?></span></td>
        </tr>
        <?php
        }
        ?>

        <?php echo $no_list; // No Qa posts found ?>
        </tbody>
        </table>
    </div>

    <div class="bo_fx">
        <ul class="btn_bo_user_btm">
            <?php if ($is_admin) { ?>
            <li><button type="submit" name="btn_submit" value="delete_selection" onclick="document.pressed=this.value" class="btn btn_b01"><?php e__('Delete Selection'); ?></button></li>
            <?php } ?>
            <?php if ($list_href) { ?><li><a href="<?php echo $list_href ?>" class="btn_b01 btn"><?php e__('List'); ?></a></li><?php } ?>
            <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02 btn"><?php e__('Write'); ?></a></li><?php } ?>
        </ul>
    </div>
    </form>
</div>

<?php if($is_admin) { ?>
<noscript>
<p><?php e__('If you are not using JavaScript, please be careful because you can delete the selection immediately without a separate verification process.'); ?></p>
</noscript>
<?php } ?>

<!-- Pagination -->
<?php echo $list_pages;  ?>
<!-- } End Board List -->
