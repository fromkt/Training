<?php
if (!defined("_GNUBOARD_")) exit; // Unable to access direct pages

for ($i=0; $i<count($list); $i++) {
    $list[$i]['board_url'] = get_pretty_url($list[$i]['bo_table']);
    $list[$i]['bo_subject'] = cut_str($list[$i]['bo_subject'], 20);
    $list[$i]['wr_subject'] = get_text(cut_str($list[$i]['wr_subject'], 80));
}

if ($i == 0) $no_list = '<li class="empty_table">'.__('No posts found.').'</li>';

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$new_skin_url.'/style.css">', 0);
?>

<!-- Start search new posts { -->
<fieldset id="new_sch">
    <legend><?php e__('Search Details'); ?></legend>
    <form name="fnew" method="get">
    <?php echo $group_select ?>
    <label for="view" class="sound_only"><?php e__('Search target'); ?></label>
    <select name="view" id="view" onchange="select_change()">
        <option value=""><?php e__('All posts'); ?></option>
        <option value="w"><?php e__('post'); ?></option>
        <option value="c"><?php e__('Comment'); ?></option>
    </select>
    <input type="text" name="mb_id" value="<?php echo $mb_id ?>" id="mb_id" placeholder="<?php e__('search terms'); ?>(<?php e__('Required'); ?>)" required class="frm_input">
    <button type="submit" value="<?php e__('Search'); ?>" class="btn_sch_submit"><i class="fa fa-search" aria-hidden="true"></i> <?php e__('search'); ?></button>
    </form>
    <script>
    /* In the Select box, automatic movement release.
    function select_change()
    {
        document.fnew.submit();
    }
    */
    document.getElementById("gr_id").value = "<?php echo $gr_id ?>";
    document.getElementById("view").value = "<?php echo $view ?>";
    </script>
</fieldset>
<!-- } End search new posts -->

<!-- Start list new posts { -->
<div class="list_03" id="new_list">
    <ul>
    <?php for ($i=0; $i<count($list); $i++) { ?>
    <li>
    	<a href="./new.php?gr_id=<?php echo $list[$i]['gr_id'] ?>" class="new_group"><?php echo $list[$i]['gr_subject'] ?></a>
    	<a href="<?php echo $list[$i]['board_url'] ?>" class="new_board"><?php echo $list[$i]['bo_subject'] ?></a>
        <a href="<?php echo $list[$i]['href'] ?>" class="new_tit">
        	<span class="new_li_status <?php echo $list[$i]['comment_class'][0] ?>"><i class="fa <?php echo $list[$i]['comment_class'][1] ?>" aria-hidden="true"></i></span><?php echo $list[$i]['wr_subject'] ?></a></td>
        <div class="newli_info">
        	<span class="sound_only"><?php e__('Writer') ?></span><span class="new_guest"><?php echo $list[$i]['name'] ?></span>
        	<span class="sound_only"><?php e__('data') ?></span><span class="new_date"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $list[$i]['datetime2'] ?></span>
        	<span class="sound_only"><?php e__('view') ?></span><span class="new_view"><i class="fa fa-eye" aria-hidden="true"></i> <?php echo $list[$i]['wr_hit'] ?></span>
        	<span class="sound_only"><?php e__('Comment') ?></span><span class="new_cmt"><i class="fa fa-commenting-o" aria-hidden="true"></i> <?php echo $list[$i]['wr_comment']; ?></span>
        </div>
    </li>
    <?php } ?>

    <?php echo $no_list // No posts found. ?>
    </ul>
</div>

<?php echo $write_pages ?>
<!-- } End list new posts -->
