<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<a href="<?php echo GML_URL ?>" class="register_logo"><img src="<?php echo GML_IMG_URL ?>/logo.png" alt="<?php echo $config['cf_title']; ?>"></a>

<!-- Start register result { -->
<div id="reg_result" class="register">
    <h2><?php e__('<strong>Sign up</strong> is complete.');?></h2>

    <!-- Start register step { -->
	<div id="register_step">
		<ol>
			<li class="step step1">
				<span class="sound_only">1.</span>
				<span class="step_tit">회원가입약관</span>
			</li>
			<li class="step step2">
				<span class="sound_only">2.</span>
				<span class="step_tit">회원가입</span>
			</li>
			<li class="step step3">
				<span class="sound_only">3.</span>
				<span class="step_tit">회원가입완료</span>
			</li>
		</ol>
	</div>
	<!-- } End register step -->

	<div class="reg_result_inner">
	    <p class="reg_result_p">
	        <strong><?php echo sprintf(__("Congratulations on %s's Sign up."), get_text($mb['mb_name'])); ?></strong><br>
	    </p>

	    <?php if (is_use_email_certify()) {  ?>
	    <p>
	        <?php e__('A verification email has been sent to the email address entered at the time of membership registration.'); ?><br>
	        <?php e__('After checking the sent authentication mail, you can use the site smoothly.'); ?>
	    </p>
	    <br>
	    <div id="result_email">
	        <span><?php e__('ID'); ?></span>
	        <strong><?php echo $mb['mb_id'] ?></strong><br>
	        <span><?php e__('E-mail address'); ?></span>
	        <strong><?php echo $mb['mb_email'] ?></strong>
	    </div>
	    <p>
	        <?php e__('If you have entered an e-mail address incorrectly, please contact your site administrator.'); ?>
	    </p>
	    <?php }  ?>

	    <p>
	        <?php e__('Please be assured that your password is stored in an encryption code that no one knows about.'); ?><br>
	        <?php e__('If you lose your ID and password, you can find them using the email address you entered when Sign up.'); ?>
	    </p>

	    <p>
	        <?php e__('You can leave membership at any time and your information is deleted after a certain period of time.'); ?><br>
	        <?php e__('Thank you.'); ?>
	    </p>
	</div>
	<a href="<?php echo GML_URL ?>/" class="btn_submit"><?php e__('Go to Main'); ?></a>

</div>
<!-- } End register result -->
