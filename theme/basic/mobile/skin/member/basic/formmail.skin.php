<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<!-- Start Formmail { -->
<div id="formmail" class="new_win">
    <h1 id="win_title"><?php echo sprintf(__('Send an email to %s'), $name); ?></h1>

    <form name="fformmail" action="./formmail_send.php" onsubmit="return fformmail_submit(this);" method="post" enctype="multipart/form-data" style="margin:0px;">
    <input type="hidden" name="to" value="<?php echo $email ?>">
    <input type="hidden" name="attach" value="2">
    <?php if ($is_member) { // IF Member  ?>
    <input type="hidden" name="fnick" value="<?php echo get_text($member['mb_nick']) ?>">
    <input type="hidden" name="fmail" value="<?php echo $member['mb_email'] ?>">
    <?php }  ?>

    <div class="form_01 new_win_con">
        <h2 class="sound_only"><?php e__('Writing mail'); ?></h2>
        <ul>
            <?php if (!$is_member) {  ?>
            <li>
                <label for="fnick" class="sound_only"><?php e__('Name'); ?><strong><?php e__('Required'); ?></strong></label>
                <input type="text" name="fnick" id="fnick" required class="frm_input full_input required" placeholder="<?php e__('Name'); ?>">
            </li>
            <li>
                <label for="fmail" class="sound_only"><?php e__('E-mail'); ?><strong><?php e__('Required'); ?></strong></label>
                <input type="text" name="fmail"  id="fmail" required class="frm_input full_input required" placeholder="<?php e__('E-mail'); ?>">
            </li>
            <?php }  ?>
            <li>
                <label for="subject" class="sound_only"><?php e__('Subject'); ?><strong><?php e__('Required'); ?></strong></label>
                <input type="text" name="subject" id="subject" required class="frm_input full_input required"  placeholder="<?php e__('Subject'); ?>">
            </li>
            <li>
                <span class="sound_only"><?php e__('Type'); ?></span>
                <input type="radio" name="type" value="0" id="type_text" checked> <label for="type_text">TEXT</label>
                <input type="radio" name="type" value="1" id="type_html"> <label for="type_html">HTML</label>
                <input type="radio" name="type" value="2" id="type_both"> <label for="type_both">TEXT+HTML</label>
            </li>
            <li>
                <label for="content" class="sound_only"><?php e__('Content'); ?><strong><?php e__('Required'); ?></strong></label>
                <textarea name="content" id="content" required class="required"></textarea>
            </li>
            <li class="formmail_flie">
                <div class="file_wr">
                    <label for="file1" class="lb_icon"><i class="fa fa-download" aria-hidden="true"></i><span class="sound_only"> <?php e__('The attached file'); ?> 1</span></label>
                    <input type="file" name="file1"  id="file1"  class="frm_file">
               </div>
               <div class="frm_info"><?php e__('Please check if the file is attached after you send it.'); ?></div>
                
            </li>
            <li class="formmail_flie">
                <div class="file_wr">
                    <label for="file2" class="lb_icon"><i class="fa fa-download" aria-hidden="true"></i><span class="sound_only"> <?php e__('The attached file'); ?> 2</span></label>
                    <input type="file" name="file2" id="file2" class="frm_file">
                </div>
            </li>
            <li>
                <span class="sound_only"><?php e__('Captcha'); ?></span>
                <?php echo captcha_html(); ?>
            </li>
        </ul>
        <div class="win_btn">
            <input type="submit" value="<?php e__('Send Mail'); ?>" id="btn_submit" class="btn_submit">
            <button type="button" onclick="window.close();" class="btn_close"><?php e__('Close window'); ?></button>
        </div>
    </div>


    </form>
</div>

<?php
get_localize_script('formmail_skin',
array(
'confirm_msg1'=>__('The attachment takes a long time to transfer.'),  // 첨부파일의 용량이 큰경우 전송시간이 오래 걸립니다.
'confirm_msg2'=>__('Do not close or refresh the window before sending mail is complete.'),    // 메일보내기가 완료되기 전에 창을 닫거나 새로고침 하지 마십시오.
),
true);
?>
<script>
with (document.fformmail) {
    if (typeof fname != "undefined")
        fname.focus();
    else if (typeof subject != "undefined")
        subject.focus();
}

function fformmail_submit(f)
{
    <?php echo chk_captcha_js();  ?>

    if (f.file1.value || f.file2.value) {
        // 4.00.11
        if (!confirm( formmail_skin.confirm_msg1 + "\n\n" + formmail_skin.confirm_msg2 ))
            return false;
    }

    document.getElementById('btn_submit').disabled = true;

    return true;
}
</script>
<!-- } End Formmail -->