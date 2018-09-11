<?php
$sub_menu = "200300";
include_once('./_common.php');

if (!$config['cf_email_use'])
    alert(__('You must check "Enable mail sending" in Preferences to send mail.'));

include_once(GML_LIB_PATH.'/mailer.lib.php');

auth_check($auth[$sub_menu], 'w');

check_demo();

$gml['title'] = __("Test Member's Mail");

$name = get_text($member['mb_name']);
$nick = $member['mb_nick'];
$mb_id = $member['mb_id'];
$email = $member['mb_email'];

$ma = get_mail_content_db($ma_id);

$subject = $ma['ma_subject'];

$content = $ma['ma_content'];
$content = preg_replace("/{NAME}/", $name, $content);
$content = preg_replace("/{NICKNAME}/", $nick, $content);
$content = preg_replace("/{MEMBER_ID}/", $mb_id, $content);
$content = preg_replace("/{EMAIL}/", $email, $content);

$mb_md5 = md5($member['mb_id'].$member['mb_email'].$member['mb_datetime']);

$content = $content . '<p>'.sprintf(__('If you do not wish to receive further information, please %s.'), "[<a href='".GML_BBS_URL."/email_stop.php?mb_id={$mb_id}&amp;mb_md5={$mb_md5}' target='_blank'>".__('Unsubscribe')."</a>]").'</p>';

mailer($config['cf_title'], $member['mb_email'], $member['mb_email'], $subject, $content, 1);

alert(sprintf(__('Do Have Sent a test email to %s. Please check.'), $member['mb_nick'].'('.$member['mb_email'].')'));
?>
