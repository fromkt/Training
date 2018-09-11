<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<!-- Start Login { -->
<div id="mb_login" class="mbskin">
    <h1><?php echo $gml['title'] ?></h1>

    <form name="flogin" action="<?php echo $login_action_url ?>" onsubmit="return flogin_submit(this);" method="post" id="flogin">
    <input type="hidden" name="url" value="<?php echo $login_url ?>">

    <div id="login_frm">
        <label for="login_id" class="sound_only"><?php e__('Member Login'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label>
        <input type="text" name="mb_id" id="login_id" placeholder="<?php e__('ID'); ?>" required class="frm_input required" maxLength="20">
        <label for="login_pw" class="sound_only"><?php e__('Password'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label>
        <input type="password" name="mb_password" id="login_pw" placeholder="<?php e__('Password'); ?>" required class="frm_input required" maxLength="20">
        <div class="login_auto">
        	<label for="login_auto_login" id="login_auto_lb"><span class="agree_ck"></span><?php e__('Autologin'); ?></label>
            <input type="checkbox" name="auto_login" id="login_auto_login">
        </div>
		<button type="submit" class="btn_submit"><?php e__('Login'); ?></button>
    </div>

    <section id="login_info">
        <h2><?php e__('Member Login'); ?></h2>
        <span>
        	<a href="./register.php"><?php e__('Register'); ?></a>
            <a href="<?php echo GML_BBS_URL ?>/password_lost.php" target="_blank" id="login_password_lost"><?php e__('Find ID and Password'); ?></a>         
        </span>
    </section>
    
    <?php
    // Display Social login button when using social login
    @include_once(get_social_skin_path().'/social_login.skin.php');
    ?>
    
    </form>
</div>

<?php
get_localize_script('login_skin',
array(
'check_msg1'=>__('If you use auto login, you will not have to enter your member ID and password from now on.'),  // 자동로그인을 사용하시면 다음부터 회원아이디와 비밀번호를 입력하실 필요가 없습니다.
'check_msg2'=>__('Please refrain from using personal information in public places.'),    // 공공장소에서는 개인정보가 유출될 수 있으니 사용을 자제하여 주십시오.
'check_msg3'=>__('Do you want to use auto login?'),    // 자동로그인을 사용하시겠습니까?
),
true);
?>

<script>
$(function(){
    $("#login_auto_login").click(function(){
        if (this.checked) {
            this.checked = confirm( login_skin.check_msg1 + "\n\n" + login_skin.check_msg2 + "\n\n" + login_skin.check_msg3 );
        }
    });
    $("#login_auto_lb").click(function(){
        $(".agree_ck").toggleClass("click_on");
    });
});

function flogin_submit(f)
{
    return true;
}
</script>
<!-- } End Login -->
