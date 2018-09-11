<?php
include_once('./_common.php');
include_once(GML_CAPTCHA_PATH.'/captcha.lib.php');

if (!$config['cf_email_use'])
    alert_close(__('Admin must check "Enable mail sending" in Preferences to send mail.').'\\n\\n'.__('Please contact your administrator.'));

if (!$is_member && $config['cf_formmail_is_member'])
    alert_close(__('Only members can use it.'));

$mb_id = isset($mb_id) ? $mb_id : '';
$mb_hash = isset($mb_hash) ? $mb_hash : '';

$mb_id = get_search_string(get_member_by_hash($mb_id, $mb_hash));

if ($is_member && !$member['mb_open'] && $is_admin != "super" && $member['mb_id'] != $mb_id)
    alert_close(__('You cant send an email to someone else unless you open your profile.').'\\n\\n'.__('Profile Open settings can be made in Modify Member Edit.'));

if ($mb_id)
{
    $mb = get_member($mb_id);
    if (!$mb['mb_id'])
        alert_close(__('Member information does not exist.').'\\n\\n'.__('This member may have left.'));

    if (!$mb['mb_open'] && $is_admin != "super")
        alert_close(__('This member has not Open Profile'));
}

$sendmail_count = (int)get_session('ss_sendmail_count') + 1;
if ($sendmail_count > 3)
    alert_close(__('You can only send a certain number of emails after a single connection.').'\\n\\n'.__('Please login or log in again to continue sending mail.'));

$gml['title'] = __('Writing mail');
include_once(GML_PATH.'/head.sub.php');

$email_dec = get_string_decrypt($email);

$email = get_email_address($email_dec);
if(!$email)
    alert_close(__('The email is not valid.'));

$email = get_string_encrypt($email);

if (!$name)
    $name = $email;
else
    $name = get_text(stripslashes($name), true);

if (!isset($type))
    $type = 0;

$type_checked[0] = $type_checked[1] = $type_checked[2] = "";
$type_checked[$type] = 'checked';

// load language for l10n
bind_lang_domain( 'default', get_path_lang_dir('skin', $member_skin_path.'/'.GML_LANG_DIR) );

include_once($member_skin_path.'/formmail.skin.php');

include_once(GML_PATH.'/tail.sub.php');
?>
