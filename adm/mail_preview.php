<?php
$sub_menu = "200300";
include_once('./_common.php');
include_once(GML_LIB_PATH.'/mailer.lib.php');

auth_check($auth[$sub_menu], 'r');

$se = sql_fetch("select ma_subject, ma_content from {$gml['mail_table']} where ma_id = '{$ma_id}' ");

$subject = $se['ma_subject'];
$content = conv_content($se['ma_content'], 1) . "<hr size=0><p><span style='font-size:9pt;'>".sprintf(__('If you do not wish to receive further information, please %s.'), "[<a href='".GML_BBS_URL."/email_stop.php?mb_id=***&amp;mb_md5=***' target='_blank'>".__('Unsubscribe')."</a>]")."</span></p>";
?>

<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title><?php echo GML_VERSION ?> <?php e__('Send mail test'); ?></title>
</head>

<body>

<h1><?php echo $subject; ?></h1>

<p>
    <?php echo $content; ?>
</p>

<p>
    <strong><?php e__('Attention!'); ?></strong> <?php e__('The design shown on this screen may differ from the design when the actual content is sent.'); ?>
</p>

</body>
</html>