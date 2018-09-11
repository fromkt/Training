<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

if( ! $config['cf_social_login_use']) {     //If you don't use social login
    return;
}

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.GML_JS_URL.'/remodal/remodal.css">', 11);
add_stylesheet('<link rel="stylesheet" href="'.GML_JS_URL.'/remodal/remodal-default-theme.css">', 12);
add_stylesheet('<link rel="stylesheet" href="'.get_social_skin_url().'/style.css">', 13);
add_javascript('<script src="'.GML_JS_URL.'/remodal/remodal.js"></script>', 10);

$email_msg = $is_exists_email ? __('Duplicate email to register.Please enter another e-mail.') : '';
$email_exists_class = $is_exists_email ? ' is_exists_email' : '';
?>

<!-- Start enter / edit member profile { -->
<div class="mbskin" id="register_member">

    <script src="<?php echo GML_JS_URL ?>/jquery.register_form.js"></script>
    
    <!-- Start register form -->
    <form id="fregisterform" name="fregisterform" action="<?php echo $register_action_url; ?>" onsubmit="return fregisterform_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
    <input type="hidden" name="w" value="<?php echo $w; ?>">
    <input type="hidden" name="url" value="<?php echo $urlencode; ?>">
    <input type="hidden" name="mb_name" value="<?php echo $user_nick; ?>" >
    <input type="hidden" name="provider" value="<?php echo $provider_name;?>" >
    <input type="hidden" name="action" value="register">

    <input type="hidden" name="mb_id" value="<?php echo $user_id; ?>" id="reg_mb_id">
    <input type="hidden" name="mb_nick_default" value="<?php echo isset($user_nick)?get_text($user_nick):''; ?>">
    <input type="hidden" name="mb_nick" value="<?php echo isset($user_nick)?get_text($user_nick):''; ?>" id="reg_mb_nick">

    <div class="toggle">
        <div class="toggle-title">
		<span class="right_i"><i></i> <?php e__('Learn more'); ?></span>
		<span class="title-name"><input type="checkbox" name="agree" value="1" id="agree11"> <label for="agree11"><?php e__('Terms and conditions of membership'); ?></label></span>
        </div>
        <div class="toggle-inner">
            <p><?php echo conv_content($config['cf_stipulation'], 0); ?></p>
        </div>
    </div>  <!-- END OF TOGGLE -->
    <div class="toggle">
        <div class="toggle-title">
		<span class="right_i"><i></i> <?php e__('Learn more'); ?></span>
		<span class="title-name"><input type="checkbox" name="agree2" value="1" id="agree21"> <label for="agree21"><?php e__('Privacy Policy Guide'); ?></label></span>
        </div>
        <div class="toggle-inner">
            <p><?php echo conv_content($config['cf_privacy'], 0); ?></p>
        </div>
    </div>  <!-- END OF TOGGLE -->
    <div class="all_agree">
		<span class="title-name"><input type="checkbox" name="chk_all" value="1" id="chk_all"> <label for="chk_all"><strong><?php e__('I accept the terms and conditions.'); ?></strong></label></span>
    </div>

    <div class="sns_tbl tbl_wrap input_profile">
        <table>
        <caption><?php e__('Entering member profile'); ?></caption>
        <tbody>
        <tr>
            <th scope="row"><label for="reg_mb_email">E-mail<strong class="sound_only"><?php e__('Required'); ?></strong></label></th>
            <td>
                <input type="text" name="mb_email" value="<?php echo isset($user_email)?$user_email:''; ?>" id="reg_mb_email" required class="frm_input email required<?php echo $email_exists_class; ?>" size="70" maxlength="100" placeholder="<?php e__('Enter your email.'); ?>" >
                <p class="email_msg"><?php echo $email_msg; ?></p>
            </td>
        </tr>

        </tbody>
        </table>
    </div>

    <div class="btn_confirm">
        <input type="submit" value="<?php e__('Register'); ?>" id="btn_submit" class="btn_submit" accesskey="s">
        <a href="<?php echo GML_URL ?>" class="btn_cancel"><?php e__('Cancel'); ?></a>
    </div>
    </form>
    <!-- End register form -->

    <!-- Link to an existing account -->

    <div class="member_connect">
        <p class="strong"><?php e__('Are you an existing member?'); ?></p>
        <button type="button" class="connect-opener btn-txt" data-remodal-target="modal">
            <?php e__('Link to an existing account'); ?>
            <i class="fa fa-angle-double-right"></i>
        </button>
    </div>

    <div id="sns-link-pnl" class="remodal" data-remodal-id="modal" role="dialog" aria-labelledby="modal1Title" aria-describedby="modal1Desc">
        <button type="button" class="connect-close" data-remodal-action="close">
            <i class="fa fa-close"></i>
            <span class="txt"><?php e__('Close'); ?></span>
        </button>
        <div class="connect-fg">
            <form method="post" action="<?php echo $login_action_url ?>" onsubmit="return flogin_submit(this);">
            <input type="hidden" id="url" name="url" value="<?php echo $login_url ?>">
            <input type="hidden" id="provider" name="provider" value="<?php echo $provider_name ?>">
            <input type="hidden" id="action" name="action" value="social_account_linking">

            <div class="connect-title"><?php e__('Link to an existing account'); ?></div>

            <div class="connect-desc">
                <?php e__('Link SNS ID to existing ID.'); ?><br>
                <?php e__('Then, log in with your SNS ID and you can log in with your existing ID.'); ?>
            </div>

            <div id="login_fs">
                <label for="login_id" class="login_id"><?php e__('ID'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label>
                <span class="lg_id"><input type="text" name="mb_id" id="login_id" class="frm_input required" size="20" maxLength="20" ></span>
                <label for="login_pw" class="login_pw"><?php e__('Password'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label>
                <span class="lg_pw"><input type="password" name="mb_password" id="login_pw" class="frm_input required" size="20" maxLength="20"></span>
                <br>
                <input type="submit" value="<?php e__('Link Account'); ?>" class="login_submit btn_submit">
            </div>

            </form>
        </div>
    </div>

<?php
get_localize_script('social_register_skin',
array(
'agree_msg1'=>__('You must agree to the terms and conditions of your membership before you can sign up.'),  // 회원가입약관의 내용에 동의하셔야 회원가입 하실 수 있습니다.
'agree_msg2'=>__('You must agree to the information handling policy guidelines before you can sign up.'),    // 개인정보처리방침안내의 내용에 동의하셔야 회원가입 하실 수 있습니다.
),
true);
?>
    <script>
    // check submit
    function fregisterform_submit(f)
    {

        if (!f.agree.checked) {
            alert(social_register_skin.agree_msg1);
            f.agree.focus();
            return false;
        }

        if (!f.agree2.checked) {
            alert(social_register_skin.agree_msg2);
            f.agree2.focus();
            return false;
        }

        // check E-mail
        if ((f.w.value == "") || (f.w.value == "u" && f.mb_email.defaultValue != f.mb_email.value)) {
            var msg = reg_mb_email_check();
            if (msg) {
                alert(msg);
                jQuery(".email_msg").html(msg);
                $(".input_profile").show();
                f.reg_mb_email.select();
                return false;
            }
        }

        document.getElementById("btn_submit").disabled = "disabled";

        return true;
    }

    function flogin_submit(f)
    {
        var mb_id = $.trim($(f).find("input[name=mb_id]").val()),
            mb_password = $.trim($(f).find("input[name=mb_password]").val());

        if(!mb_id || !mb_password){
            return false;
        }

        return true;
    }

    jQuery(function($){
        if( jQuery(".toggle .toggle-title").hasClass('active') ){
            jQuery(".toggle .toggle-title.active").closest('.toggle').find('.toggle-inner').show();
        }
        jQuery(".toggle .toggle-title .right_i").click(function(){

            var $parent = $(this).parent();
            
            if( $parent.hasClass('active') ){
                $parent.removeClass("active").closest('.toggle').find('.toggle-inner').slideUp(200);
            } else {
                $parent.addClass("active").closest('.toggle').find('.toggle-inner').slideDown(200);
            }
        });

        if( !$("#reg_mb_email").hasClass("is_exists_email") && $("#reg_mb_email").val() ){
            if($(".input_profile input").length === 1){
                $(".input_profile").hide();
            }
        }

        // All select
        $("input[name=chk_all]").click(function() {
            if ($(this).prop('checked')) {
                $("input[name^=agree]").prop('checked', true);
            } else {
                $("input[name^=agree]").prop("checked", false);
            }
        });
    });
    </script>

</div>
<!-- } End enter / edit member profile -->