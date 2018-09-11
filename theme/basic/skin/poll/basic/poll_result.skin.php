<?php
if (!defined("_GNUBOARD_")) exit; // Unable to access direct pages

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$poll_skin_url.'/style.css">', 0);
?>

<!-- Start poll result { -->
<div id="poll_result" class="new_win">
    <h1 id="win_title"><?php echo $gml['title'] ?></h1>
    <span class="poll_all"><?php echo sprintf(n__('A total of %s vote', 'A total of %s votes', $nf_total_po_cnt), $nf_total_po_cnt); ?></span>
    <div class="new_win_con">
        <!-- Start poll Results Graph { -->
        <section id="poll_result_list">
            <h2><?php echo $po_subject ?> <span class="sound_only"><?php e__('Result'); ?></span></h2>
            <ol>
            <?php for ($i=1; $i<=count($list); $i++) {  ?>
                <li>
                    <span><?php echo $list[$i]['content'] ?></span>
                    <span class="poll_percent"><?php echo number_format($list[$i]['rate'], 1) ?> %</span>
                    <div class="poll_result_graph">
                        <span style="width:<?php echo number_format($list[$i]['rate'], 1) ?>%"><strong class="poll_cnt"><?php echo $list[$i]['cnt'] ?> <?php echo n__('vote', 'votes', $list[$i]['cnt']); ?></strong></span>
                    </div>
                </li>
            <?php }  ?>
            </ol>
        </section>
        <!-- } End poll Results Graph -->

        <!-- Start a poll { -->
        <?php if ($is_etc) {  ?>
        <section id="poll_result_cmt">
            <h2><?php e__('Other opinion on this poll'); ?></h2>

            <?php for ($i=0; $i<count($list2); $i++) {  ?>
            <article>
                <header>
                    <h2><?php echo $list2[$i]['pc_name']; ?><span class="sound_only"><?php e__("'s opinion"); ?></span></h2>
                    <?php echo $list2[$i]['name'] ?>
                    <span class="poll_datetime"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $list2[$i]['datetime'] ?></span>
                </header>
                <p>
                    <?php echo $list2[$i]['idea']; ?>
                </p>
            	<span class="poll_cmt_del"><?php if ($list2[$i]['del']) { echo $list2[$i]['del']."<i class=\"fa fa-trash-o\" aria-hidden=\"true\"></i><span class=\"sound_only\">".__('Delete')."</span></a>"; }  ?></span>
            </article>
            <?php }  ?>
        </section>
        <?php }  ?>
        <!-- } End a poll -->
		
		<!-- Start other opinion { -->
        <?php if ($member['mb_level'] >= $po['po_level']) {  ?>
        <form name="fpollresult" action="./poll_etc_update.php" onsubmit="return fpollresult_submit(this);" method="post" autocomplete="off">
        <input type="hidden" name="po_id" value="<?php echo $po_id ?>">
        <input type="hidden" name="w" value="">
        <input type="hidden" name="skin_dir" value="<?php echo urlencode($skin_dir); ?>">
        <?php if ($is_member) {  ?><input type="hidden" name="pc_name" value="<?php echo $show_mb_nick ?>"><?php }  ?>

        <div class="poll_result_wcmt">
        	<h3><span>기타의견</span> <?php echo $po_etc ?></h3>
            <div id="poll_result_wcmt">
            	<div>
					<label for="pc_idea" class="sound_only"><?php e__('Opinion'); ?><strong><?php e__('Required'); ?></strong></label>
                	<textarea id="pc_idea" name="pc_idea" required class="frm_input full_input required" size="47" maxlength="100" placeholder="의견을 입력해 주세요."></textarea>
				</div>
            
            <?php if ($is_guest) {  ?>
				<div class="poll_result_guest">
                	<label for="pc_name" class="sound_only"><?php e__('Name'); ?><strong><?php e__('Required'); ?></strong></label>
                	<input type="text" name="pc_name" id="pc_name" required class="frm_input required" size="20" placeholder="<?php e__('Name'); ?>">
				</div>
			<?php }  ?>
                <button type="submit" class="full_btn_submit"><?php e__('Enter Opinion'); ?></button>
            </div>
        </div>
        <?php echo $captcha_html ?>
        </form>
        <?php }  ?>
        <!-- } End other opinion -->
        
        <!-- Start view other poll { -->
        <aside id="poll_result_oth">
            <h2><?php e__('See other poll results'); ?></h2>
            <ul>
                <?php for ($i=0; $i<count($list3); $i++) {  ?>
                <li><a href="./poll_result.php?po_id=<?php echo $list3[$i]['po_id'] ?>&amp;skin_dir=<?php echo urlencode($skin_dir); ?>"> <?php echo $list3[$i]['subject'] ?> </a><span><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $list3[$i]['date'] ?></span></li>
                <?php }  ?>
            </ul>
        </aside>
        <!-- } End view other poll -->

        <div class="win_btn">
            <button type="button" onclick="window.close();" class="btn_close"><?php e__('Close window'); ?></button>
        </div>
    </div>
</div>

<?php
get_localize_script('poll_result_skin',
array(
'poll_delete_msg'=>__('Are you sure you want to delete the other opinion?'),  // 해당 기타의견을 삭제하시겠습니까?
),
true);
?>
<script>
jQuery(function() {
    $(".poll_delete").click(function() {
        if(!confirm(poll_result_skin.poll_delete_msg))
            return false;
    });
});

function fpollresult_submit(f)
{
    <?php if ($is_guest) { echo chk_captcha_js(); }  ?>

    return true;
}
</script>
<!-- } End poll result -->
