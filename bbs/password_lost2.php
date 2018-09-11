<?php
include_once('./_common.php');
include_once(GML_CAPTCHA_PATH.'/captcha.lib.php');
include_once(GML_LIB_PATH.'/mailer.lib.php');

if ($is_member) {
    alert_close(__('You are already logged in.'), GML_URL);
}

if (!chk_captcha()) {
    alert(__('The Captcha certification is invalid.'));
}

$email = trim($_POST['mb_email']);

if (!$email)
    alert_close(__('Email address is empty.'));

$sql = " select count(*) as cnt from {$gml['member_table']} where mb_email = '$email' ";
$row = sql_fetch($sql);
if ($row['cnt'] > 1)
    alert(__('More than one identical email address exists.').'\\n\\n'.__('Please contact your admin.'));

$sql = " select mb_no, mb_id, mb_name, mb_nick, mb_email, mb_datetime from {$gml['member_table']} where mb_email = '$email' ";
$mb = sql_fetch($sql);
if (!$mb['mb_id'])
    alert(__('This member does not exist.'));
else if (is_admin($mb['mb_id']))
    alert(__('You can not access administrator ID.'));

// 임시비밀번호 발급
$change_password = rand(100000, 999999);
$mb_lost_certify = get_encrypt_string($change_password);

// 어떠한 회원정보도 포함되지 않은 일회용 난수를 생성하여 인증에 사용
$mb_nonce = md5(pack('V*', rand(), rand(), rand(), rand()));

// 임시비밀번호와 난수를 mb_lost_certify 필드에 저장
$sql = " update {$gml['member_table']} set mb_lost_certify = '$mb_nonce $mb_lost_certify' where mb_id = '{$mb['mb_id']}' ";
sql_query($sql);

// 인증 링크 생성
$href = GML_BBS_URL.'/password_lost_certify.php?mb_no='.$mb['mb_no'].'&amp;mb_nonce='.$mb_nonce;

$subject = "[".$config['cf_title']."] ".__('This is the information for the requested member.');

$content = "";

$content .= '<div style="margin:30px auto;width:600px;border:10px solid #f7f7f7">';
$content .= '<div style="border:1px solid #dedede">';
$content .= '<h1 style="padding:30px 30px 0;background:#f7f7f7;color:#555;font-size:1.4em">';
$content .= __('Information about members');
$content .= '</h1>';
$content .= '<span style="display:block;padding:10px 30px 30px;background:#f7f7f7;text-align:right">';
$content .= '<a href="'.GML_URL.'" target="_blank">'.$config['cf_title'].'</a>';
$content .= '</span>';
$content .= '<p style="margin:20px 0 0;padding:30px 30px 30px;border-bottom:1px solid #eee;line-height:1.7em">';
$content .= sprintf(__('%s member requested to find member information in %s.'), addslashes($mb['mb_name']).' ('.addslashes($mb['mb_nick']).')', GML_TIME_YMDHIS).'<br>';
$content .= __('Since our site does not even have an admin knowledge of your password, we will inform you by creating a new password instead of giving you your password.').'<br>';
$content .= __('Please check the password below and <span style="color:#ff3061">click the <strong>Change Password</strong> link</span>.');
$content .= __('When the authentication message that the password has been changed is displayed, enter the member ID and changed password on the website and log in.').'<br>';
$content .= __('Please change the password from the Modify Information menu after login.');
$content .= '</p>';
$content .= '<p style="margin:0;padding:30px 30px 30px;border-bottom:1px solid #eee;line-height:1.7em">';
$content .= '<span style="display:inline-block;width:100px">'.__('Member ID').'</span> '.$mb['mb_id'].'<br>';
$content .= '<span style="display:inline-block;width:100px">'.__('Password to be changed').'</span> <strong style="color:#ff3061">'.$change_password.'</strong>';
$content .= '</p>';
$content .= '<a href="'.$href.'" target="_blank" style="display:block;padding:30px 0;background:#484848;color:#fff;text-decoration:none;text-align:center">'.__('Change Password').'</a>';
$content .= '</div>';
$content .= '</div>';

mailer($config['cf_admin_email_name'], $config['cf_admin_email'], $mb['mb_email'], $subject, $content, 1);

alert_close($email.' '.__('mail address has been sent to authenticate the member ID and password.').'\\n\\n'.__('Please check your mail.'));
?>
