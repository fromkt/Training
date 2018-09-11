<?php
include_once('./_common.php');
include_once(GML_CAPTCHA_PATH.'/captcha.lib.php');

define("_DONT_WRAP_IN_CONTAINER_", true);

$gml['title'] = __('Mail authentication mail address change');
include_once('./_head.php');

$mb_id = substr(clean_xss_tags($_GET['mb_id']), 0, 20);
$mb = get_member($mb_id);

if (substr($mb['mb_email_certify'],0,1)!=0) {
    alert(__('This is a member who has already signed up to mail.'), GML_URL);
}

$ckey = trim($_GET['ckey']);
$key  = md5($mb['mb_ip'].$mb['mb_datetime']);

if(!$ckey || $ckey != $key)
    alert(__('Please use the correct method.'), GML_URL);
?>

<p class="rg_em_p"><?php e__('You can change the mail address of the member information if you do not have a mail authentication.'); ?></p>

<form method="post" name="fregister_email" action="<?php echo GML_HTTPS_BBS_URL.'/register_email_update.php'; ?>" onsubmit="return fregister_email_submit(this);">
<input type="hidden" name="mb_id" value="<?php echo $mb_id; ?>">

<div class="tbl_frm01 tbl_frm rg_em">
    <table>
    <caption><?php e__('Enter site information'); ?></caption>
    <tr>
        <th scope="row"><label for="reg_mb_email"><?php e__('E-mail'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label></th>
        <td><input type="text" name="mb_email" id="reg_mb_email" required class="frm_input email required" size="30" maxlength="100" value="<?php echo $mb['mb_email']; ?>"></td>
    </tr>
    <tr>
        <th scope="row"><?php e__('Captcha'); ?></th>
        <td><?php echo captcha_html(); ?></td>
    </tr>
    </table>
</div>

<div class="btn_confirm">
    <input type="submit" id="btn_submit" class="btn_submit" value="<?php e__('Change authentication mail'); ?>">
    <a href="<?php echo GML_URL ?>" class="btn_cancel"><?php e__('Cancel'); ?></a>
</div>

</form>

<script>
function fregister_email_submit(f)
{
    <?php echo chk_captcha_js();  ?>

    return true;
}
</script>
<?php
include_once('./_tail.php');
?>
