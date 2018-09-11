<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

$have_to_keep_nickname = isset($member['mb_nick_date']) && $member['mb_nick_date'] > date("Y-m-d", GML_SERVER_TIME - ($config['cf_nick_modify'] * 86400));

$mb_open_date_editable = isset($member['mb_open_date']) && $member['mb_open_date'] <= date("Y-m-d", GML_SERVER_TIME - ($config['cf_open_modify'] * 86400)) || empty($member['mb_open_date']);

$mb_open_date_fix_deadline = date("Y-m-d", isset($member['mb_open_date']) ? strtotime("{$member['mb_open_date']} 00:00:00")+$config['cf_open_modify']*86400:GML_SERVER_TIME+$config['cf_open_modify']*86400);

$member_icon_usable = $config['cf_use_member_icon'] && $member['mb_level'] >= $config['cf_icon_level'];

$exist_member_icon_path = ($w == 'u') && file_exists($mb_icon_path);

$member_image_usable = ($member['mb_level'] >= $config['cf_icon_level']) && $config['cf_member_img_size'] && $config['cf_member_img_width'] && $config['cf_member_img_height'];
$exist_member_image_path = ($w == 'u') && file_exists($mb_img_path);

$config['cf_nick_modify'] = (int)$config['cf_nick_modify'];
$config['cf_open_modify'] = (int)$config['cf_open_modify'];

if ($config['cf_use_email_certify']) {
    $email_cert_info = ($w == 'u') ? __('You must authenticate again if you change your e-mail address.') : __('Sign up for membership only after checking the contents sent through e-mail.');
}

$submit_btn_text = ($w=='') ? __('Sign up') : __('Edit profile');

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<a href="<?php echo GML_URL ?>" class="register_logo"><img src="<?php echo GML_IMG_URL ?>/logo.png" alt="<?php echo $config['cf_title']; ?>"></a>

<div class="register">
	<?php if(!$is_member) {?>
	<!-- Start register step { -->
	<div id="register_step">
		<ol>
			<li class="step step1">
				<span class="sound_only">1.</span>
				<span class="step_tit"><?php e__('Terms of Membership'); ?></span>
			</li>
			<li class="step step2">
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
	<?php } ?>

	<!-- Start enter / modify member info { -->
	<script src="<?php echo GML_JS_URL ?>/jquery.register_form.js"></script>

	<form id="fregisterform" name="fregisterform" action="<?php echo $register_action_url ?>" onsubmit="return fregisterform_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="w" value="<?php echo $w ?>">
	<input type="hidden" name="url" value="<?php echo $urlencode ?>">
	<input type="hidden" name="agree" value="<?php echo $agree ?>">
	<input type="hidden" name="agree2" value="<?php echo $agree2 ?>">
	<input type="hidden" name="cert_type" value="<?php echo $member['mb_certify']; ?>">
	<input type="hidden" name="cert_no" value="">
	<?php if (isset($member['mb_sex'])) {  ?><input type="hidden" name="mb_sex" value="<?php echo $member['mb_sex'] ?>"><?php }  ?>
	<?php if ($have_to_keep_nickname) { // If the date of nickname modification is not over  ?>
	<input type="hidden" name="mb_nick_default" value="<?php echo get_text($member['mb_nick']) ?>">
	<input type="hidden" name="mb_nick" value="<?php echo get_text($member['mb_nick']) ?>">
	<?php }  ?>
	<div id="register_form" class="form_01">
	    <div>
	        <h2><?php e__('Enter ID and Password'); ?></h2>
	        <ul>
	            <li>
	                <label for="reg_mb_id" class="sound_only"><?php e__('ID'); ?><strong><?php e__('Required'); ?></strong></label>
	                <input type="text" name="mb_id" value="<?php echo $member['mb_id'] ?>" id="reg_mb_id" <?php echo $required ?> <?php echo $readonly ?> class="frm_input full_input <?php echo $required ?> <?php echo $readonly ?>" minlength="3" maxlength="20" placeholder="<?php e__('ID'); ?>">
	                <span id="msg_mb_id"></span>
	                <span class="frm_info"><?php e__('You can enter only alphabet, numbers, and _. Please enter at least 3 characters.'); ?></span>
	            </li>
	            <li>
	                <label for="reg_mb_password" class="sound_only"><?php e__('Password'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label>
	                <input type="password" name="mb_password" id="reg_mb_password" <?php echo $required ?> class="frm_input full_input frm_no_border <?php echo $required ?>" minlength="3" maxlength="20" placeholder="<?php e__('Password'); ?>">

	                <label for="reg_mb_password_re" class="sound_only"><?php e__('Confirm Password'); ?><strong><?php e__('Required'); ?></strong></label>
	                <input type="password" name="mb_password_re" id="reg_mb_password_re" <?php echo $required ?> class="frm_input full_input <?php echo $required ?>" minlength="3" maxlength="20" placeholder="<?php e__('Confirm Password'); ?>">
	            </li>
	        </ul>
	    </div>

	    <div class="tbl_frm01 tbl_wrap">
	        <h2><?php e__('Enter member profile'); ?></h2>
	        <ul>
	            <li>
	                <label for="reg_mb_name" class="sound_only"><?php e__('Name'); ?><strong><?php e__('Required'); ?></strong></label>
	                <input type="text" id="reg_mb_name" name="mb_name" value="<?php echo get_text($member['mb_name']) ?>" <?php echo $required ?> <?php echo $readonly; ?> class="frm_input full_input <?php echo $required ?> <?php echo $readonly ?>" size="10" placeholder="<?php e__('Name'); ?>">
	            </li>
	            <?php if ($req_nick) {  ?>
	            <li>
	                <label for="reg_mb_nick" class="sound_only"><?php e__('Nickname'); ?><strong><?php e__('Required'); ?></strong></label>
	                <input type="hidden" name="mb_nick_default" value="<?php echo isset_variable($member['mb_nick']) ?>">
	                <input type="text" name="mb_nick" value="<?php echo isset_variable($member['mb_nick']) ?>" id="reg_mb_nick" required class="frm_input required nospace full_input" size="10" maxlength="20" placeholder="<?php e__('Nickname'); ?>">
	                <span id="msg_mb_nick"></span>
	                <span class="frm_info">*
	                    <?php e__('Enter only letters and numbers without spaces'); ?><br>
	                    <?php echo sprintf(__('If you change your nickname, you can not change it within the next %s days.'), $config['cf_nick_modify']); ?>
	                </span>
	            </li>
	            <?php }  ?>

	            <li>
	                <label for="reg_mb_email" class="sound_only"><?php e__('E-mail'); ?><strong><?php e__('Required'); ?></strong></label>
	                <?php if ($config['cf_use_email_certify']) {  ?>
	                <span class="frm_info">* <?php echo $email_cert_info ?></span>
	                <?php }  ?>
	                <input type="hidden" name="old_email" value="<?php echo $member['mb_email'] ?>">
	                <input type="text" name="mb_email" value="<?php echo isset_variable($member['mb_email']) ?>" id="reg_mb_email" required class="frm_input email full_input required" size="70" maxlength="100" placeholder="E-mail">
	            </li>

	            <?php if ($config['cf_use_homepage']) {  ?>
	            <li>
	                <label for="reg_mb_homepage" class="sound_only"><?php e__('Homepage'); ?><?php if ($config['cf_req_homepage']){ ?><strong><?php e__('Required'); ?></strong><?php } ?></label>
	                <input type="text" name="mb_homepage" value="<?php echo get_text($member['mb_homepage']) ?>" id="reg_mb_homepage" <?php echo element_required($config['cf_req_homepage']) ?> class="frm_input full_input <?php echo element_required($config['cf_req_homepage']) ?>" size="70" maxlength="255" placeholder="<?php e__('Homepage'); ?>">
	            </li>
	            <?php }  ?>

	            <li>
	            <?php if ($config['cf_use_tel']) {  ?>

	                <label for="reg_mb_tel" class="sound_only"><?php e__('Phone Number'); ?><?php if ($config['cf_req_tel']) { ?><strong><?php e__('Required'); ?></strong><?php } ?></label>
	                <input type="text" name="mb_tel" value="<?php echo get_text($member['mb_tel']) ?>" id="reg_mb_tel" <?php echo element_required($config['cf_req_tel']) ?> class="frm_input half_input <?php echo element_required($config['cf_req_tel']) ?>" maxlength="20" placeholder="<?php e__('Phone Number'); ?>">
	            <?php }  ?>

	            <?php if ($config['cf_use_hp'] || $config['cf_cert_hp']) {  ?>
	                <label for="reg_mb_hp" class="sound_only"><?php e__('Mobile Number'); ?><?php if ($config['cf_req_hp']) { ?><strong><?php e__('Required'); ?></strong><?php } ?></label>

	                <input type="text" name="mb_hp" value="<?php echo get_text($member['mb_hp']) ?>" id="reg_mb_hp" <?php echo element_required($config['cf_req_hp']) ?> class="frm_input right_input half_input <?php echo element_required($config['cf_req_hp']) ?>" maxlength="20" placeholder="<?php e__('Mobile Number'); ?>">
	                <?php if ($config['cf_cert_use'] && $config['cf_cert_hp']) { ?>
	                <input type="hidden" name="old_mb_hp" value="<?php echo get_text($member['mb_hp']) ?>">
	                <?php } ?>
	            <?php }  ?>
	            </li>


	            <?php if ($config['cf_use_addr']) { ?>
	            <li>
	                <?php if ($config['cf_req_addr']) { ?><strong class="sound_only"><?php e__('Required'); ?></strong><?php }  ?>
                    <?php echo get_form_address($member, array(
                    'mb_country'=>'class="frm_input"',
                    'mb_zip'=>'class="frm_input half_input2 '.element_required($config['cf_req_addr']).'" size="5" maxlength="6"',
                    'mb_addr1'=>'class="frm_input frm_address full_input '.element_required($config['cf_req_addr']).'" size="50"',
                    'mb_addr2'=>'class="frm_input frm_address full_input" size="50"',
                    'mb_addr3'=>'class="frm_input frm_address full_input" size="50"'
                    ),
                    array(
                    'mb_country'=>'reg_mb_country',
                    'mb_zip'=>'reg_mb_zip',
                    'mb_addr1'=>'reg_mb_addr1',
                    'mb_addr2'=>'reg_mb_addr2',
                    'mb_addr3'=>'reg_mb_addr3'
                    ),
                    $config['cf_req_addr']); ?>
	            </li>
	            <?php }  ?>
	        </ul>
	    </div>

	    <div class="tbl_frm01 tbl_wrap">
	        <h2><?php e__('Etc profile'); ?></h2>
	        <ul class="personal_setting">
	            <?php if ($config['cf_use_signature']) {  ?>
	            <li>
	                <label for="reg_mb_signature" class="sound_only"><?php e__('Signature'); ?><?php if ($config['cf_req_signature']){ ?><strong><?php e__('Required'); ?></strong><?php } ?></label>
	                <textarea name="mb_signature" id="reg_mb_signature" <?php echo element_required($config['cf_req_signature']) ?> class="<?php echo element_required($config['cf_req_signature']) ?>" placeholder="<?php e__('Signature'); ?>"><?php echo $member['mb_signature'] ?></textarea>
	            </li>
	            <?php }  ?>

	            <?php if ($config['cf_use_profile']) {  ?>
	            <li>
	                <label for="reg_mb_profile" class="sound_only"><?php e__('Introduce Myself'); ?></label>
	                <textarea name="mb_profile" id="reg_mb_profile" <?php echo element_required($config['cf_req_profile']) ?> class="<?php echo element_required($config['cf_req_profile']) ?>" placeholder="<?php e__('Introduce Myself'); ?>"><?php echo $member['mb_profile'] ?></textarea>
	            </li>
	            <?php }  ?>

	            <?php if ($member_icon_usable) {  ?>
	            <li class="mem_pic">
	            	<div>
		                <label for="reg_mb_icon" class="frm_label"><?php e__('Member icon'); ?></label>
		                <input type="file" name="mb_icon" id="reg_mb_icon">
	                </div>

	                <span class="frm_info">*
	                    <?php echo sprintf(__('Image size should be %s pixels width and %s pixels height.'), $config['cf_member_icon_width'], $config['cf_member_icon_height']); ?><br>
	                    <?php echo sprintf(__('Only gif, jpg, png files are allowed. Only %s bytes or less are registered.'), number_format($config['cf_member_icon_size'])); ?>
	                </span>

	                <?php if ($exist_member_icon_path) {  ?>
	                <img src="<?php echo $mb_icon_url ?>" alt="<?php e__('Member icon'); ?>">
	                <input type="checkbox" name="del_mb_icon" value="1" id="del_mb_icon">
	                <label for="del_mb_icon"><?php e__('Delete'); ?></label>
	                <?php }  ?>
	            </li>
	            <?php }  ?>

	            <?php if ($member_image_usable) {  ?>
	            <li class="mem_pic">
	            	<div>
	                	<label for="reg_mb_img" class="frm_label"><?php e__('Member image'); ?></label>
	                	<input type="file" name="mb_img" id="reg_mb_img">
					</div>

	                <span class="frm_info">
	                    <?php echo sprintf(__('Image size should be %s pixels width and %s pixels height.'), $config['cf_member_img_width'], $config['cf_member_img_height']); ?><br>
	                    <?php echo sprintf(__('Only gif, jpg, png files are allowed. Only %s bytes or less are registered.'), number_format($config['cf_member_img_size'])); ?>
	                </span>

	                <?php if ($exist_member_image_path) {  ?>
	                <img src="<?php echo $mb_img_url ?>" alt="<?php e__('Member image'); ?>">
	                <input type="checkbox" name="del_mb_img" value="1" id="del_mb_img">
	                <label for="del_mb_img"><?php e__('Delete'); ?></label>
	                <?php }  ?>
	            </li>
	            <?php } ?>

	            <li class="frm_bar">
	                <label for="reg_mb_mailling" class="frm_label mailling"><span class="sound_only"><?php e__('Mailing service'); ?></span><span class="frm_check frm_check1"></span></label>
	                <input type="checkbox" name="mb_mailling" value="1" id="reg_mb_mailling" <?php echo ($w=='' || $member['mb_mailling']) ? 'checked' : ''; ?>>
	                <?php e__("I'll get an information mail."); ?>
	            </li>

	            <?php if ($config['cf_use_hp']) {  ?>
	            <li class="frm_bar">
	                <label for="reg_mb_sms" class="frm_label frm_sms"><span class="sound_only"><?php e__('SMS receiving status'); ?></span><span class="frm_check frm_check2"></span></label>
	                <input type="checkbox" name="mb_sms" value="1" id="reg_mb_sms" <?php echo ($w=='' || $member['mb_sms']) ? 'checked' : ''; ?>>
	                <?php e__("I'll get a message on my phone."); ?>
	            </li>
	            <?php }  ?>

	            <?php if ($mb_open_date_editable) { // Can be modified if information disclosure is past the date of modification  ?>
	            <li>
	            	<div class="frm_bar">
	                	<label for="reg_mb_open" class="frm_label info_open"><span class="sound_only"><?php e__('Open Profile'); ?></span><span class="frm_check frm_check3"></span></label>
	                	<input type="hidden" name="mb_open_default" value="<?php echo $member['mb_open'] ?>">
	                	<input type="checkbox" name="mb_open" value="1" <?php echo ($w=='' || $member['mb_open']) ? 'checked' : ''; ?> id="reg_mb_open">
	                	<?php e__('Let others see my information.'); ?>
	                </div>
	                <span class="frm_info">
                    	* <?php echo sprintf(__('If you change the Open Profile, you will not be able to change it within the next %s days.'), $config['cf_open_modify']); ?>
                	</span>
	            </li>
	            <?php } else {  ?>
	            <li>
	            	<div class="frm_bar">
	                	<?php e__('Open Profile'); ?><input type="hidden" name="mb_open" value="<?php echo $member['mb_open'] ?>">
	                </div>
	                <span class="frm_info">
	                    <?php echo sprintf(__('Open Profile must not changed until %s, within %s days after modification.'), $mb_open_date_fix_deadline, $config['cf_open_modify']); ?><br>
	                    <?php e__('This is to prevent you from receiving a note after you send it due to frequent information disclosure corrections.'); ?>
	                </span>
	            </li>
	            <?php }  ?>

                <?php
                    // Display social_login account
                    if( $w == 'u' && function_exists('social_member_provider_manage') )
                        social_member_provider_manage();
                ?>

	            <?php if ($w == "" && $config['cf_use_recommend']) {  ?>
	            <li>
	                <label for="reg_mb_recommend" class="sound_only"><?php e__('Recommendation ID'); ?></label>
	                <input type="text" name="mb_recommend" id="reg_mb_recommend" class="frm_input full_input" placeholder="<?php e__('Recommendation ID'); ?>">
	            </li>
	            <?php }  ?>

	            <li class="is_captcha_use">
	                <?php e__('Captcha'); ?>
	                <?php echo captcha_html(); ?>
	            </li>
	        </ul>
	        <script>
            // checkbox
            $(document).ready(function(){
			    $(".mailling").click(function(){
			        $(".frm_check1").toggleClass("click_off");
			    });
			    $(".frm_sms").click(function(){
			        $(".frm_check2").toggleClass("click_off");
			    });
			    $(".info_open").click(function(){
			        $(".frm_check3").toggleClass("click_off");
			    });
			});
            </script>
	    </div>

	</div>
	<div class="btn_confirm">
	    <a href="<?php echo GML_URL ?>" class="btn_cancel"><?php e__('Cancel'); ?></a>
	    <button type="submit" id="btn_submit" class="btn_submit" accesskey="s"><?php echo $submit_btn_text ?></button>
	</div>
	</form>
</div>

<?php
get_localize_script('register_form_skin',
array(
'phone_cert_msg'=>__('Please set the mobile phone identification settings in the basic configuration.'),  // 기본환경설정에서 휴대폰 본인확인 설정을 해주십시오
'password_check_msg'=>__('Please enter at least 3 characters in your password.'),    // 비밀번호를 3글자 이상 입력하십시오.
'password_not_same_msg' => __('The password is not the same.'),  // 비밀번호가 같지 않습니다.
'enter_name_msg' => __('Please enter a name.'),  // 이름을 입력하십시오.
'confirm_cert_msg' => __('You need to confirm your identity to sign up for membership.'),  // 회원가입을 위해서는 본인확인을 해주셔야 합니다.
'member_icon_msg' => __('Member icon is not an image file.'),  // 회원아이콘이 이미지 파일이 아닙니다.
'member_image_msg' => __('Member image is not an image file.'),  // 회원이미지가 이미지 파일이 아닙니다.
'recommend_not_msg' => __('You can not recommend yourself.'),  // 본인을 추천할 수 없습니다.
),
true);
?>
<script>
jQuery(function() {
    $("#reg_zip_find").css("display", "inline-block");
});

// submit form check
function fregisterform_submit(f)
{
    // Member ID check
    if (f.w.value == "") {
        var msg = reg_mb_id_check();
        if (msg) {
            alert(msg);
            f.mb_id.select();
            return false;
        }
    }

    if (f.w.value == "") {
        if (f.mb_password.value.length < 3) {
            alert( register_form_skin.password_check_msg );
            f.mb_password.focus();
            return false;
        }
    }

    if (f.mb_password.value != f.mb_password_re.value) {
        alert( register_form_skin.password_not_same_msg );
        f.mb_password_re.focus();
        return false;
    }

    if (f.mb_password.value.length > 0) {
        if (f.mb_password_re.value.length < 3) {
            alert( register_form_skin.password_check_msg );
            f.mb_password_re.focus();
            return false;
        }
    }

    // check name
    if (f.w.value=="") {
        if (f.mb_name.value.length < 1) {
            alert( register_form_skin.enter_name_msg );
            f.mb_name.focus();
            return false;
        }
    }

    <?php if($w == '' && $config['cf_cert_use'] && $config['cf_cert_req']) { ?>
    // check authentication
    if(f.cert_no.value=="") {
        alert( register_form_skin.confirm_cert_msg );
        return false;
    }
    <?php } ?>

    // check nickname
    if ((f.w.value == "") || (f.w.value == "u" && f.mb_nick.defaultValue != f.mb_nick.value)) {
        var msg = reg_mb_nick_check();
        if (msg) {
            alert(msg);
            f.reg_mb_nick.select();
            return false;
        }
    }

    // check E-mail
    if ((f.w.value == "") || (f.w.value == "u" && f.mb_email.defaultValue != f.mb_email.value)) {
        var msg = reg_mb_email_check();
        if (msg) {
            alert(msg);
            f.reg_mb_email.select();
            return false;
        }
    }

    <?php if (($config['cf_use_hp'] || $config['cf_cert_hp']) && $config['cf_req_hp']) {  ?>
    // Check phone number
    var msg = reg_mb_hp_check();
    if (msg) {
        alert(msg);
        f.reg_mb_hp.select();
        return false;
    }
    <?php } ?>

    if (typeof f.mb_icon != "undefined") {
        if (f.mb_icon.value) {
            if (!f.mb_icon.value.toLowerCase().match(/.(gif|jpe?g|png)$/i)) {
                alert( register_form_skin.member_icon_msg );
                f.mb_icon.focus();
                return false;
            }
        }
    }

    if (typeof f.mb_img != "undefined") {
        if (f.mb_img.value) {
            if (!f.mb_img.value.toLowerCase().match(/.(gif|jpe?g|png)$/i)) {
                alert( register_form_skin.member_image_msg );
                f.mb_img.focus();
                return false;
            }
        }
    }

    if (typeof(f.mb_recommend) != "undefined" && f.mb_recommend.value) {
        if (f.mb_id.value == f.mb_recommend.value) {
            alert( register_form_skin.recommend_not_msg );
            f.mb_recommend.focus();
            return false;
        }

        var msg = reg_mb_recommend_check();
        if (msg) {
            alert(msg);
            f.mb_recommend.select();
            return false;
        }
    }

    <?php echo chk_captcha_js();  ?>

    document.getElementById("btn_submit").disabled = "disabled";

    return true;
}
</script>
<!-- } End enter / modify member info -->
