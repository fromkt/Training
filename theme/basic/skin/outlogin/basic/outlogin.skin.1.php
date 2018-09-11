<?php
if (!defined("_GNUBOARD_")) exit; // Unable to access direct pages

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$outlogin_skin_url.'/style.css">', 0);
?>

<!-- Start outlogin form { -->
<section id="ol_before" class="ol">
    <h2><?php e__('Member Login'); ?></h2>
    <form name="foutlogin" action="<?php echo $outlogin_action_url ?>" onsubmit="return fhead_submit(this);" method="post" autocomplete="off">
    <fieldset>
        <div class="ol_wr">
            <input type="hidden" name="url" value="<?php echo $outlogin_url ?>">
            <label for="ol_id" id="ol_idlabel" class="sound_only"><?php e__('Member ID'); ?><strong><?php e__('Required'); ?></strong></label>
            <input type="text" id="ol_id" name="mb_id" required maxlength="20" placeholder="<?php e__('ID'); ?>">
            <label for="ol_pw" id="ol_pwlabel" class="sound_only"><?php e__('Password'); ?><strong><?php e__('Required'); ?></strong></label>
            <input type="password" name="mb_password" id="ol_pw" required maxlength="20" placeholder="<?php e__('Password'); ?>">
            <button type="submit" id="ol_submit" class="btn_b02"><?php e__('Login'); ?></button>
        </div>
        <div class="ol_auto_wr">
            <div id="ol_auto">
                <label for="auto_login" id="auto_login_label"><span class="agree_ck"></span><?php e__('Auto_login'); ?></label>
                <input type="checkbox" name="auto_login" value="1" id="auto_login">
            </div>
            <div id="ol_svc">
                <a href="<?php echo GML_BBS_URL ?>/register.php" class="border-right"><b><?php e__('Register'); ?></b></a>
                <a href="<?php echo GML_BBS_URL ?>/password_lost.php" id="ol_password_lost"><?php e__('Find Account'); ?></a>
            </div>
        </div>
    </fieldset>
    </form>
</section>

<?php
// Display Social login button when using social login
@include_once(get_social_skin_path().'/social_outlogin.skin.1.php');
?>

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
$omi = $('#ol_id');
$omp = $('#ol_pw');
$omi_label = $('#ol_idlabel');
$omi_label.addClass('ol_idlabel');
$omp_label = $('#ol_pwlabel');
$omp_label.addClass('ol_pwlabel');

jQuery(function($) {

    $("#auto_login").click(function(){
        if ($(this).is(":checked")) {
            if(!confirm(outlogin_skin1.check_msg1 + "\n\n" + outlogin_skin1.check_msg2 + "\n\n" + outlogin_skin1.check_msg3)) {
                $(".agree_ck").removeClass("click_on");
                return false;
            }
        }
    });

    $("#auto_login_label").click(function(){
        $(".agree_ck").toggleClass("click_on");
    });
});

function fhead_submit(f)
{
    return true;
}

</script>
<!-- } End outlogin form -->
