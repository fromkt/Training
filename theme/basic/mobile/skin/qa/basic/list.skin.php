<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

for ($i=0; $i<count($list); $i++) {
    $list[$i]['qa_status_class'] = $list[$i]['qa_status'] ? 'txt_done' : 'txt_rdy';
    $list[$i]['qa_status_icon'] = $list[$i]['qa_status'] ? __('Answer completed') : __('Answer waiting');
}

if ($i == 0) {
    $no_list = '<li class="empty_list">'.__('No Qa posts found.').'</li>';
}

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$qa_skin_url.'/style.css">', 0);
?>

<div id="bo_list">
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

	<div class="bo_option">
		<button class="sch_tog"><i class="fa fa-search" aria-hidden="true"></i><span class="sound_only"><?php e__('Search'); ?>"></span></button>
		<!-- Start QA button { -->
	    <?php if ($admin_href || $write_href) { ?>
	    <ul class="btn_bo_user">
	        <?php if ($admin_href) { ?><li><a href="<?php echo $admin_href ?>" class="btn_admin btn"><?php e__('Admin'); ?></a></li><?php } ?>
	        <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02 btn"><?php e__('Write'); ?></a></li><?php } ?>
	    </ul>
	    <?php } ?>
	    <!-- } End QA button -->
	</div>

    <!-- Start Search post { -->
	<fieldset id="bo_sch">
	    <legend><?php e__('Search Posts'); ?></legend>
	    <form name="fsearch" method="get">
	    <input type="hidden" name="sca" value="<?php echo $sca ?>">
	    <label for="stx" class="sound_only"><?php e__('Search'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label>
	    <input type="text" name="stx" value="<?php echo $stx ?>" required id="stx" class="sch_input" size="15" maxlength="15">
	    <button type="submit" value="<?php e__('Search'); ?>" class="sch_btn"><i class="fa fa-search" aria-hidden="true"></i><span class="sound_only"><?php e__('Search'); ?></span></button>
	    </form>
	</fieldset>
	<script>
		$(document).ready(function(){
			$(".sch_tog").click(function(){
				$("#bo_sch").toggle();
			});
		});
	</script>
	<!-- } End Search post -->

    <div id="bo_list_total" class="sound_only">
        <span><?php echo sprintf(n__('%s total', '%s totals', $show_total_count), $show_total_count); ?></span>
        , <?php echo sprintf(n__('%s page', '%s pages', $page), $page); ?>
    </div>

    <form name="fqalist" id="fqalist" action="./qadelete.php" onsubmit="return fqalist_submit(this);" method="post">
    <input type="hidden" name="stx" value="<?php echo $stx; ?>">
    <input type="hidden" name="sca" value="<?php echo $sca; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">

    <?php if ($is_admin) { ?>
    <div class="list_chk all_chk">
        <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);">
        <label for="chkall"><span class="sound_only"><?php e__('All current page posts'); ?> </span><?php e__('Select All'); ?></label>
    </div>
    <?php } ?>

    <div class="list_03">
        <ul>
            <?php
            for ($i=0; $i<count($list); $i++) {
            ?>
            <li class="bo_li<?php if ($is_admin) echo ' bo_adm'; ?>">
                <div class="li_title">
                	<span class="li_stat <?php echo $list[$i]['qa_status_class'] ?>"><?php echo $list[$i]['qa_status_icon'] ?></span>

                    <strong><?php echo $list[$i]['category']; ?></strong>
                    <a href="<?php echo $list[$i]['view_href']; ?>" class="li_sbj">
                    	<?php if ($is_admin) { ?>
		                <span class="bo_chk li_chk">
		                    <label for="chk_qa_id_<?php echo $i ?>"><span class="sound_only"><?php echo $list[$i]['subject']; ?></span></label>
		                    <input type="checkbox" name="chk_qa_id[]" value="<?php echo $list[$i]['qa_id'] ?>" id="chk_qa_id_<?php echo $i ?>">
		                </span>
		                <?php } ?>
                        <?php echo $list[$i]['subject']; ?> <span><i class="fa fa-download" aria-hidden="true"></i></span>
                    </a>
                </div>
                <div class="li_info">
                    <span><?php echo $list[$i]['name']; ?></span>
                    <span><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $list[$i]['date']; ?></span>
                </div>
            </li>
            <?php
            }
            ?>

            <?php echo $no_list // No Qa posts found ?>
        </ul>
    </div>

    <div class="btn_top">
        <ul class="btn_bo_adm">
            <?php if ($is_admin) { ?><li><button type="submit" name="btn_submit" onclick="document.pressed='delete_selection'" class="btn btn_b01"><?php e__('Delete Selection'); ?></button></li><?php } ?>
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
