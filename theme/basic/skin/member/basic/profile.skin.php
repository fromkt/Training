<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

$member_profile_img = get_member_profile_img($mb['mb_id']);
$show_point = number_format($mb['mb_point']);
$show_register_date = ($member['mb_level'] >= $mb['mb_level']) ?  substr($mb['mb_datetime'],0,10) ." (".number_format($mb_reg_after)." ".__('Days').")" : __('Unknown');
$show_last_login_date = ($member['mb_level'] >= $mb['mb_level']) ? $mb['mb_today_login'] : __('Unknown');

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<!-- Start Profile { -->
<div id="profile" class="new_win">
    <h1 id="win_title"><?php echo sprintf(__("%s's profile"), $mb_nick); ?></h1>
	<ul>
		<li class="profile_name">
			<span class="my_profile_img profile_cnt">
				<?php echo $member_profile_img ?>
	        </span>
	        <?php echo $mb_nick ?>
        </li>
        <li>
        	<span class="profile_cnt"><?php e__('Member Level'); ?><strong><?php echo $mb['mb_level'] ?></strong></span>
            <span class="profile_cnt"><?php e__('Point'); ?><strong><?php echo $show_point ?></strong></span>
		</li>
		<li class="profile_connec">
			<span class="profile_cnt"><?php e__('Register Date'); ?><strong><?php echo $show_register_date ?></strong></span>
			<span class="profile_cnt"><?php e__('Last login date'); ?><strong><?php echo $show_last_login_date ?></strong></span>
		</li>


        <?php if ($mb_homepage) { ?>
        <li>
            <span class="profile_cnt"><?php e__('Homepage'); ?><strong><a href="<?php echo $mb_homepage ?>" target="_blank"><?php echo $mb_homepage ?></strong></span>
        </li>
        <?php } ?>

        <li class="greeting">
        	<h2><?php e__('Profile'); ?></h2>
            <p><?php echo $mb_profile ?></p>
        </li>
    </div>
	<div class="win_btn">
        <button type="button" onclick="window.close();" class="btn_b01 btn_close"><?php e__('Close Window'); ?></button>
    </div>
</div>
<!-- } End Profile -->
