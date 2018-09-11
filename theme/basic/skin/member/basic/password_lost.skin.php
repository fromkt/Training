<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<!-- Start Find member info { -->
<div id="find_info" class="new_win">
    <h1 id="win_title"><?php e__('Find member info'); ?></h1>
    <div class="new_win_con">
        <form name="fpasswordlost" action="<?php echo $action_url ?>" onsubmit="return fpasswordlost_submit(this);" method="post" autocomplete="off">
        <fieldset id="info_fs">
            <p>
                <?php e__('Please enter the registered email address when signing up.'); ?><br>
                <?php e__('We will send you ID and password information by email.'); ?>
            </p>
            <label for="mb_email" class="sound_only"><?php e__('E-mail address'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label>
            <input type="text" name="mb_email" id="mb_email" required class="required frm_input full_input email" size="30" placeholder="<?php e__('E-mail address'); ?>">
        </fieldset>
        <?php echo captcha_html();  ?>
        <div class="win_btn">
	    	<input type="submit" value="<?php e__('Confirm'); ?>" class="btn_submit">
	    	<button type="button" onclick="window.close();" class="btn_close"><?php e__('Close Window'); ?></button>
		</div>
        </form>
    </div>
</div>

<script>
function fpasswordlost_submit(f)
{
    <?php echo chk_captcha_js();  ?>

    return true;
}

jQuery(function() {
    var sw = screen.width;
    var sh = screen.height;
    var cw = document.body.clientWidth;
    var ch = document.body.clientHeight;
    var top  = sh / 2 - ch / 2 - 100;
    var left = sw / 2 - cw / 2;
    moveTo(left, top);
});
</script>
<!-- } End Find member info -->