<?php
$sub_menu = "200300";
include_once('./_common.php');
include_once(GML_EDITOR_LIB);

auth_check($auth[$sub_menu], 'r');

$html_title = __('Member mail');

if ($w == 'u') {
    $html_title .= __('Edit');
    $readonly = ' readonly';

    $sql = " select * from {$gml['mail_table']} where ma_id = '{$ma_id}' ";
    $ma = sql_fetch($sql);
    if (!$ma['ma_id'])
        alert(__('No data'));
} else {
    $html_title .= __('Write');
}

$gml['title'] = $html_title;
include_once('./admin.head.php');
?>
<div class="local_desc01 local_desc">
<p><?php e__('If you insert it into the content, such as {name}, {nickname}, {member ID}, {email}, the mail will be converted to match the content and sent.'); ?></p>
</div>

<form name="fmailform" id="fmailform" action="./mail_update.php" onsubmit="return fmailform_check(this);" method="post">
<input type="hidden" name="w" value="<?php echo $w ?>" id="w">
<input type="hidden" name="ma_id" value="<?php echo $ma['ma_id'] ?>" id="ma_id">
<input type="hidden" name="token" value="" id="token">

<div class="frm_wr">
    <ul class="frm_ul">
        <li>
            <label for="ma_subject" class="lb_block"><?php e__('Mail Title'); ?><strong class="sound_only"><?php e__('required'); ?></strong></label>
            <input type="text" name="ma_subject" value="<?php echo $ma['ma_subject'] ?>" id="ma_subject" required class="required frm_input frm_input_full" size="100">
        </li>
        <li>
            <label for="ma_content" class="lb_block"><?php e__('Mail Contents'); ?><strong class="sound_only"><?php e__('required'); ?></strong></label>
            <?php echo editor_html("ma_content", get_text($ma['ma_content'], 0)); ?>
        </li>
    </ul>
</div>


<div class="btn_fixed_top">
    <input type="submit" class="btn_submit btn" accesskey="s" value="<?php e__('Save'); ?>">
</div>
</form>

<?php
get_localize_script('fmail_form',
array(
'title_msg'=>__('Enter a title.'),  // 제목을 입력하세요.
'contents_msg'=>__('Enter contents.'),    // 내용을 입력하세요.
),
true);
?>
<script>
function fmailform_check(f)
{
    errmsg = "";
    errfld = "";

    check_field(f.ma_subject, fmail_form.title_msg);
    //check_field(f.ma_content, fmail_form.contents_msg);

    if (errmsg != "") {
        alert(errmsg);
        errfld.focus();
        return false;
    }

    <?php echo get_editor_js("ma_content"); ?>
    <?php echo chk_editor_js("ma_content"); ?>

    return true;
}

document.fmailform.ma_subject.focus();
</script>

<?php
include_once('./admin.tail.php');
?>
