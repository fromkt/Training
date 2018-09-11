<?php
// 회원가입축하 메일 (회원님께 발송)
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages
?>

<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title><?php e__('Email to congratulate you on Sign up'); ?></title>
</head>

<body>

<div style="margin:30px auto;width:600px;border:10px solid #f7f7f7">
    <div style="border:1px solid #dedede">
        <h1 style="padding:30px 30px 0;background:#f7f7f7;color:#555;font-size:1.4em">
            <?php e__('Congratulations on your membership.'); ?>
        </h1>
        <span style="display:block;padding:10px 30px 30px;background:#f7f7f7;text-align:right">
            <a href="<?php echo GML_URL; ?>" target="_blank"><?php echo $config['cf_title'] ?></a>
        </span>
        <p style="margin:20px 0 0;padding:30px 30px 50px;min-height:200px;height:auto !important;height:200px;border-bottom:1px solid #eee">
            <b><?php echo sprintf(__('Congratulations on your membership in %s.'), $mb_name); ?><br>
            <?php e__('We will do our best to repay your support.'); ?><br>
            <?php if ($config['cf_use_email_certify']) { ?><?php e__('Click on the <strong>mail authentication</strong> below to complete your membership.'); ?><br><?php } ?>
            <?php e__('Thank you.'); ?>
        </p>

        <?php if ($config['cf_use_email_certify']) { ?>
        <a href="<?php echo $certify_href ?>" target="_blank" style="display:block;padding:30px 0;background:#484848;color:#fff;text-decoration:none;text-align:center"><?php e__('Mail authentication'); ?></a>
        <?php } else { ?>
        <a href="<?php echo GML_URL ?>" target="_blank" style="display:block;padding:30px 0;background:#484848;color:#fff;text-decoration:none;text-align:center"><?php e__('Go to Web site'); ?></a>
        <?php } ?>
    </div>
</div>

</body>
</html>
