<?php
if (!defined("_GNUBOARD_")) exit; // Unable to access direct pages

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$outlogin_skin_url.'/style.css">', 0);
?>

<!-- Start outlogin form { -->
<aside id="ol_before" class="ol">
    <h2><?php e__('Member Login'); ?></h2>
    <form name="foutlogin" action="<?php echo $outlogin_action_url ?>" onsubmit="return fhead_submit(this);" method="post" autocomplete="off">
    <fieldset>
    	<div class="ol_login_input">
        	<input type="hidden" name="url" value="<?php echo $outlogin_url ?>">
        	<input type="text" name="mb_id" id="ol_id" placeholder="<?php e__('Member ID'); ?>" required maxlength="20">
        	<input type="password" id="ol_pw" name="mb_password" placeholder="<?php e__('Password'); ?>" required  maxlength="20">
        </div>

        <button type="submit" id="ol_submit" value="<?php e__('Login'); ?>" class="btn_submit"><?php e__('Login'); ?></button>

        <div class="ol_before_btn">
            <a href="<?php echo GML_BBS_URL ?>/register.php"><b><?php e__('Register'); ?></b></a>
            <a href="<?php echo GML_BBS_URL ?>/password_lost.php" id="ol_password_lost"><?php e__('Find Account'); ?></a>
        </div>

        <div id="ol_svc">
        	<label for="auto_login" id="auto_login_label" class="ol_auto_ck"><?php e__('Auto_login'); ?></label>
            <input type="checkbox" id="auto_login" name="auto_login" value="1">

        </div>
    </fieldset>

    <?php
    // Display Social login button when using social login
    @include_once(get_social_skin_path().'/social_outlogin.skin.1.php');
    ?>
    </form>
</aside>

<?php
get_localize_script('outlogin_skin1',
array(
'check_msg1'=>__('If you use auto login, you will not have to enter your member ID and password from now on.'),  // 자동로그인을 사용하시면 다음부터 회원아이디와 비밀번호를 입력하실 필요가 없습니다.
'check_msg2'=>__('Please refrain from using personal information in public places.'),    // 공공장소에서는 개인정보가 유출될 수 있으니 사용을 자제하여 주십시오.
'check_msg3'=>__('Do you want to use auto login?'),    // 자동로그인을 사용하시겠습니까?
),
true);
?>
<script>
<?php if (!GML_IS_MOBILE) { ?>
$omi = $('#ol_id');
$omp = $('#ol_pw');
$omp.css('display','inline-block').css('width',104);
$omi_label = $('#ol_idlabel');
$omi_label.addClass('ol_idlabel');
$omp_label = $('#ol_pwlabel');
$omp_label.addClass('ol_pwlabel');
$omi.focus(function() {
    $omi_label.css('visibility','hidden');
});
$omp.focus(function() {
    $omp_label.css('visibility','hidden');
});
$omi.blur(function() {
    $this = $(this);
    if($this.attr('id') == "ol_id" && $this.attr('value') == "") $omi_label.css('visibility','visible');
});
$omp.blur(function() {
    $this = $(this);
    if($this.attr('id') == "ol_pw" && $this.attr('value') == "") $omp_label.css('visibility','visible');
});
<?php } ?>

$(function($) {
    $("#auto_login").click(function(){
        if ($(this).is(":checked")) {
            if(!confirm(outlogin_skin1.check_msg1 + "\n\n" + outlogin_skin1.check_msg2 + "\n\n" + outlogin_skin1.check_msg3))
                $(".ol_auto_ck").removeClass("click_on");
                return false;
        	}
    });

    $("#auto_login_label").click(function(){
        $(".ol_auto_ck").toggleClass("click_on");
    });
});

function fhead_submit(f)
{
    return true;
}
</script>
<!-- } End outlogin form -->
