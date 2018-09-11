<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

$mobile_sns_icon = '';
if (GML_IS_MOBILE) $sns_mc_icon = '';
else $sns_mc_icon = '_cmt';

if (!$board['bo_use_sns']) return;
?>
<?php if ($list[$i]['wr_twitter_user']) { ?>
<a href="https://www.twitter.com/<?php echo $list[$i]['wr_twitter_user']; ?>" target="_blank"><img src="<?php echo GML_SNS_URL; ?>/icon/twitter<?php echo $sns_mc_icon; ?>.png" alt="<?php e__('Add with Twitter'); ?>"></a>
<?php } ?>
