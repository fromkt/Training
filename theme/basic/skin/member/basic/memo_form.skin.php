<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<!-- Start Send Memo { -->
<div id="memo_write" class="new_win">
    <h1 id="win_title"><?php e__('Send Memo'); ?></h1>
    <div class="new_win_con">
        <ul class="win_ul">
            <li><a href="./memo.php?kind=recv"><?php e__('Received Memo'); ?></a></li>
            <li><a href="./memo.php?kind=send"><?php e__('Send Memo'); ?></a></li>
            <li class="selected"><a href="./memo_form.php"><?php e__('Write Memo'); ?></a></li>
        </ul>

        <form name="fmemoform" action="<?php echo $memo_action_url; ?>" onsubmit="return fmemoform_submit(this);" method="post" autocomplete="off">
        <div class="form_01">
            <h2 class="sound_only"><?php e__('Write Memo'); ?></h2>
            <ul>
                <li>
                    <label for="me_recv_mb_nicks" class="sound_only"><?php e__('Receiving Member ID'); ?><strong><?php e__('Required'); ?></strong></label>

                    <input type="text" name="me_recv_mb_nicks" value="<?php echo $me_recv_mb_nicks; ?>" id="me_recv_mb_nicks" required class="frm_input full_input required" size="47" placeholder="<?php e__('Enter Member Nickname'); ?>">
                    <span class="frm_info">* <?php e__('Separate multiple members by comma (,).'); ?></span>
                    <?php if ($config['cf_memo_send_point']) { ?>
                    <span class="frm_info">
                    <?php echo sprintf(__('When sending a note, deduct %s points per member.'), number_format($config['cf_memo_send_point'])); ?></span>
                    <?php } ?>
                </li>
                <li>
                    <label for="me_memo" class="sound_only"><?php e__('Content'); ?></label>
                    <textarea name="me_memo" id="me_memo" required class="required"><?php echo $content ?></textarea>
                </li>
                <li>
                    <span class="sound_only"><?php e__('Captcha'); ?></span>
                    <?php echo captcha_html(); ?>
                </li>
            </ul>
        </div>

        <div class="win_btn">
        	<button type="button" onclick="window.close();" class="btn_close"><?php e__('Close Window'); ?></button>
            <button type="submit" id="btn_submit" class="btn_submit"><?php e__('Send'); ?></button>
        </div>
    </div>
    </form>
</div>

<script>
function fmemoform_submit(f)
{
    <?php echo chk_captcha_js();  ?>

    return true;
}
</script>
<!-- } End Send Memo -->
