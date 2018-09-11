<?php
include_once('./_common.php');
include_once(GML_CAPTCHA_PATH.'/captcha.lib.php');
include_once(GML_LIB_PATH.'/mailer.lib.php');

if (!$config['cf_email_use'])
    alert(__('Admin must check "Enable mail sending" in Preferences to send mail.').'\\n\\n'.__('Please contact your administrator.'));

if (!$is_member && $config['cf_formmail_is_member'])
    alert_close(__('Only members can use it.'));

$email_enc = new str_encrypt();
$to = $email_enc->decrypt($to);

if (!chk_captcha()) {
    alert(__('The Captcha certification is invalid.'));
}

if (!preg_match("/([0-9a-zA-Z_-]+)@([0-9a-zA-Z_-]+)\.([0-9a-zA-Z_-]+)/", $to)){
    alert_close('The e-mail address does not match the format, so can not send the mail.');
}

$file = array();
for ($i=1; $i<=$attach; $i++) {
    if ($_FILES['file'.$i]['name'])
        $file[] = attach_file($_FILES['file'.$i]['name'], $_FILES['file'.$i]['tmp_name']);
}

$content = stripslashes($content);
if ($type == 2) {
    $type = 1;
    $content = str_replace("\n", "<br>", $content);
}

// html 이면
if ($type) {
    $current_url = GML_URL;
    $mail_content = '<!doctype html><html lang="ko"><head><meta charset="utf-8"><title>'.__('Send Mail').'</title><link rel="stylesheet" href="'.$current_url.'/style.css"></head><body>'.$content.'</body></html>';
}
else
    $mail_content = $content;

mailer($fnick, $fmail, $to, $subject, $mail_content, $type, $file);

// Delete Temp Attachments 임시 첨부파일 삭제
if(!empty($file)) {
    foreach($file as $f) {
        @unlink($f['path']);
    }
}

$html_title = __('Sending mail');
include_once(GML_PATH.'/head.sub.php');

alert_close(__('Mail has been sent successfully.'));

include_once(GML_PATH.'/tail.sub.php');
?>