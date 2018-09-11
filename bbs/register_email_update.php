<?php
include_once('./_common.php');
include_once(GML_CAPTCHA_PATH.'/captcha.lib.php');
include_once(GML_LIB_PATH.'/mailer.lib.php');

$mb_id = substr(clean_xss_tags($_POST['mb_id']), 0, 20);
$mb_email = get_email_address(trim($_POST['mb_email']));

if(!$mb_id || !$mb_email)
    alert(__('Please use the correct method.'), GML_URL);

$sql = " select mb_name from {$gml['member_table']} where mb_id = '{$mb_id}' and substring(mb_email_certify, 1, 1) = '0' ";
$mb = sql_fetch($sql);
if (!$mb) {
    alert(__('This is a member who has already signed up to mail.'), GML_URL);
}

if (!chk_captcha()) {
    alert(__('The Captcha certification is invalid.'));
}

$sql = " select count(*) as cnt from {$gml['member_table']} where mb_id <> '{$mb_id}' and mb_email = '$mb_email' ";
$row = sql_fetch($sql);
if ($row['cnt']) {
    alert(sprintf(__('Mail address %s already exists.'), $mb_email).'\\n\\n'.__('Please enter other e-mail address.'));
}

// 인증메일 발송
$subject = sprintf(__('This is the %s authentication email.'), '['.$config['cf_title'].']');

$mb_name = $mb['mb_name'];

// 어떠한 회원정보도 포함되지 않은 일회용 난수를 생성하여 인증에 사용
$mb_md5 = md5(pack('V*', rand(), rand(), rand(), rand()));

sql_query(" update {$gml['member_table']} set mb_email_certify2 = '$mb_md5' where mb_id = '$mb_id' ");

$certify_href = GML_BBS_URL.'/email_certify.php?mb_id='.$mb_id.'&amp;mb_md5='.$mb_md5;

ob_start();
include_once ('./register_form_update_mail3.php');
$content = ob_get_contents();
ob_end_clean();

mailer($config['cf_admin_email_name'], $config['cf_admin_email'], $mb_email, $subject, $content, 1);

$sql = " update {$gml['member_table']} set mb_email = '$mb_email' where mb_id = '$mb_id' ";
sql_query($sql);

alert(sprintf(__('Send you the authentication email again in %s mail.'), $mb_email)."\\n\\n".sprintf(__('Please check the %s mail.'), $mb_email), GML_URL);
?>