<?php
// 회원가입 메일 (관리자 메일로 발송)
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages
?>

<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title><?php e__('Register Notification Email'); ?></title>
</head>

<body>

<div style="margin:30px auto;width:600px;border:10px solid #f7f7f7">
    <div style="border:1px solid #dedede">
        <h1 style="padding:30px 30px 0;background:#f7f7f7;color:#555;font-size:1.4em">
            <?php e__('Register Notification Email'); ?>
        </h1>
        <span style="display:block;padding:10px 30px 30px;background:#f7f7f7;text-align:right">
            <a href="<?php echo GML_URL ?>" target="_blank"><?php echo $config['cf_title'] ?></a>
        </span>
        <p style="margin:20px 0 0;padding:30px 30px 50px;min-height:200px;height:auto !important;height:200px;border-bottom:1px solid #eee">
            <b><?php echo sprintf(__('%s has registered.'), $mb_name); ?><br>
            <?php e__('Member ID'); ?> : <b><?php echo $mb_id ?></b><br>
            <?php e__('Member Name'); ?> : <?php echo $mb_name ?><br>
            <?php e__('Member Nickname'); ?> : <?php echo $mb_nick ?><br>
            <?php e__('Recommendation ID'); ?> : <?php echo $mb_recommend ?>
        </p>
        <a href="<?php echo GML_ADMIN_URL ?>/member_form.php?w=u&amp;mb_id=<?php echo $mb_id ?>" style="display:block;padding:30px 0;background:#484848;color:#fff;text-decoration:none;text-align:center"><?php e__('Confirm membership information in Admin'); ?></a>
    </div>
</div>

</body>
</html>
