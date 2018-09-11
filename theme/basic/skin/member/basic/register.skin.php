<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<a href="<?php echo GML_URL ?>" class="register_logo"><img src="<?php echo GML_IMG_URL ?>/logo.png" alt="<?php echo $config['cf_title']; ?>"></a>

<!-- Start accept the terms and conditions of Sign up { -->
<div class="register">
	<!-- Start register step { -->
	<div id="register_step">
		<ol>
			<li class="step1 step">
				<span class="sound_only">1.</span>
				<span class="step_tit"><?php e__('Terms of Membership'); ?></span>
			</li>
			<li class="step2">
				<span class="sound_only">2.</span>
				<span class="step_tit"><?php e__('Sign Up'); ?></span>
			</li>
			<li class="step3">
				<span class="sound_only">3.</span>
				<span class="step_tit"><?php ep__('Complete', 'Sign Up Complete'); ?></span>
			</li>
		</ol>
	</div>
	<!-- } End register step -->

	<form  name="fregister" id="fregister" action="<?php echo $register_action_url ?>" onsubmit="return fregister_submit(this);" method="POST" autocomplete="off">

	<p><?php e__('You must agree to the terms and conditions of membership and the information processing policy guidelines before you can sign up.'); ?></p>

    <?php
    // Display Social login button when using social login
    @include_once(get_social_skin_path().'/social_register.skin.php');
    ?>

    <section id="fregister_term">
        <h2><?php e__('Terms of Membership'); ?></h2>
        <textarea readonly><?php echo get_text($config['cf_stipulation']) ?></textarea>
        <fieldset class="fregister_agree li_chk">
            <label for="agree11"><?php e__('I agree to the terms and conditions of membership.'); ?></label>
            <input type="checkbox" name="agree" value="1" id="agree11">
        </fieldset>
    </section>

    <section id="fregister_private">
        <h2><?php e__('Privacy Policy Guide'); ?></h2>
        <div>
            <table>
                <caption><?php e__('Privacy Policy Guide'); ?></caption>
                <thead>
                <tr>
                    <th><?php e__('Purpose'); ?></th>
                    <th><?php e__('Article'); ?></th>
                    <th><?php e__('Retention period'); ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?php e__('Identify user and verify identity'); ?></td>
                    <td><?php e__('ID, name and password'); ?></td>
                    <td><?php e__('Until member withdrawal'); ?></td>
                </tr>
                <tr>
                    <td><?php e__('Notice of use of customer service'); ?>,<br><?php e__('Identify users to respond to CS'); ?></td>
                    <td><?php e__('Contacts (e-mail, mobile phone number)'); ?></td>
                    <td><?php e__('Until member withdrawal'); ?></td>
                </tr>
                </tbody>
            </table>
        </div>

        <fieldset class="fregister_agree li_chk">
            <label for="agree21"><?php e__('I agree with the information handling policy guidelines.'); ?></label>
            <input type="checkbox" name="agree2" value="1" id="agree21">
        </fieldset>
    </section>

    <div id="fregister_chkall" class="all_chk">
        <label for="chk_all"><?php e__('Select All'); ?></label>
        <input type="checkbox" name="chk_all"  value="1"  id="chk_all">
    </div>

    <div class="btn_confirm">
		<button type="submit" class="btn_submit"><?php e__('Sign Up'); ?></button>
    </div>

    </form>

<?php
get_localize_script('register_skin',
array(
'agree_msg1'=>__('You must agree to the terms and conditions of your membership before you can sign up.'),  // 회원가입약관의 내용에 동의하셔야 회원가입 하실 수 있습니다.
'agree_msg2'=>__('You must agree to the information handling policy guidelines before you can sign up.'),    // 개인정보처리방침안내의 내용에 동의하셔야 회원가입 하실 수 있습니다.
),
true);
?>
    <script>
    function fregister_submit(f)
    {
        if (!f.agree.checked) {
            alert( register_skin.agree_msg1 );
            f.agree.focus();
            return false;
        }

        if (!f.agree2.checked) {
            alert( register_skin.agree_msg2 );
            f.agree2.focus();
            return false;
        }

        return true;
    }
	</script>
</div>
<!-- } End accept the terms and conditions of Sign up -->
