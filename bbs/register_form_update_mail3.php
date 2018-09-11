<?php
// E-mail 수정시 인증 메일 (회원님께 발송)
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages
?>

<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title><?php e__('Member authentication mail'); ?></title>
</head>

<body>

<div style="margin:30px auto;width:600px;border:10px solid #f7f7f7">
    <div style="border:1px solid #dedede">
        <h1 style="padding:30px 30px 0;background:#f7f7f7;color:#555;font-size:1.4em">
            <?php e__('This is your membership email.'); ?>
        </h1>
        <span style="display:block;padding:10px 30px 30px;background:#f7f7f7;text-align:right">
            <a href="<?php echo GML_URL ?>" target="_blank"><?php echo $config['cf_title'] ?></a>
        </span>
        <p style="margin:20px 0 0;padding:30px 30px 50px;min-height:200px;height:auto !important;height:200px;border-bottom:1px solid #eee">
            <?php if($w == 'u') { ?>
            <?php echo sprintf(__('The e-mail address of %s has been changed.'), '<b>'.$mb_name.'</b>'); ?><br><br>
            <?php } ?>

            <?php e__('Click on the address below to complete your certification.'); ?><br>
            <a href="<?php echo $certify_href ?>" target="_blank"><b><?php echo $certify_href ?></b></a><br><br>

            <?php e__('We will do our best to repay your support.'); ?><br>
            <?php e__('Thank you.'); ?>
        </p>
        <a href="<?php echo GML_BBS_URL ?>/login.php" target="_blank" style="display:block;padding:30px 0;background:#484848;color:#fff;text-decoration:none;text-align:center"><?php echo $config['cf_title'] ?> <?php e__('Login'); ?></a>
    </div>
</div>

</body>
</html>
