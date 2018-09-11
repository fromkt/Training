<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<!-- Start accept the terms and conditions of Sign up { -->
<div class="mbskin">

    <?php
    // Display Social login button when using social login
    @include_once(get_social_skin_path().'/social_register.skin.php');
    ?>

    <form name="fregister" id="fregister" action="<?php echo $register_action_url ?>" onsubmit="return fregister_submit(this);" method="POST" autocomplete="off">

    <section id="fregister_term">
        <h2><?php e__('Privacy Policy Guide') ?></h2>
        <textarea readonly><?php echo get_text($config['cf_stipulation']) ?></textarea>
        <fieldset class="fregister_agree li_chk">
        	<label for="agree11"><?php e__('I agree to the terms and conditions of membership.'); ?></label>
            <input type="checkbox" name="agree" value="1" id="agree11">
        </fieldset>
    </section>

    <section id="fregister_private">
        <h2><?php e__('Privacy Policy Guide'); ?></h2>
        <div class="tbl_head01 tbl_wrap">
            <table class="fp_thead">
	                <caption><?php e__('Privacy Policy Guide'); ?></caption>
                	<tr>
                		<th rowspan="4" class="th_num"><?php e__('Number'); ?></th>
                	</tr>
                	<tr>
                		<td><?php e__('Purpose'); ?></td>
                	</tr>
                	<tr>
                		<td><?php e__('Article'); ?></td>
                	</tr>
                	<tr>
                		<td><?php e__('Retention period'); ?></td>
                	</tr>
				</table>
				
				<table>
					<tr>
						<th rowspan="4" class="th_num">1</th>
					</tr>
					<tr>
                		<td><?php e__('Identify user and verify identity'); ?></td>
                	</tr>
                	<tr>
                		<td><?php e__('ID, name and password'); ?></td>
                	</tr>
                	<tr>
                		<td><?php e__('Until member withdrawal'); ?></td>
                	</tr>
				</table>
				
	            <table>
	            	<tr>
						<th rowspan="4" class="th_num">2</th>
					</tr>
					<tr>
	                    <td><?php e__('Notice of use of customer service'); ?>, <?php e__('Identify users to respond to CS'); ?></td>
					</tr>
					<tr>
	                    <td><?php e__('Contacts (e-mail, mobile phone number)'); ?></td>
	                </tr>
	                <tr>
	                    <td><?php e__('Until member withdrawal'); ?></td>
	                </tr>
	            </table>
        </div>
        <fieldset class="fregister_agree li_chk">
        	<label for="agree21"><?php e__('I agree with the information handling policy guidelines.'); ?></label>
            <input type="checkbox" name="agree2" value="1" id="agree21">
       </fieldset>
    </section>

	<div id="fregister_chkall" class="all_chk">
        <input type="checkbox" name="chk_all" value="1" id="chkall">
        <label for="chkall"><?php e__('Select All'); ?></label>
    </div>

    <button type="submit" class="btn_submit"><?php e__('Sign Up'); ?></button>
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
