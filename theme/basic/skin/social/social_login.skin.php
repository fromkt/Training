<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

if( ! $config['cf_social_login_use']) {     //If you don't use social login
    return;
}

$social_pop_once = false;

$self_url = GML_BBS_URL."/login.php";

//새창을 사용한다면
if( GML_SOCIAL_USE_POPUP ) {
    $self_url = GML_SOCIAL_LOGIN_URL.'/popup.php';
}

add_stylesheet('<link rel="stylesheet" href="'.get_social_skin_url().'/style.css">', 10);
?>

<div id="sns_login" class="login-sns sns-wrap-32 sns-wrap-over">
    <h3>소셜계정으로 로그인</h3>
    <div class="sns-wrap">
        <?php if( social_service_check('facebook') ) { ?>
        <a href="<?php echo $self_url;?>?provider=facebook&amp;url=<?php echo $urlencode;?>" class="sns-icon social_link sns-facebook" title="<?php e__('Facebook'); ?>">
            <span class="ico"></span>
            <span class="txt"><?php e__('Facebook'); ?><i> <?php e__('Login'); ?></i></span>
        </a>
        <?php }     //end if ?>
        <?php if( social_service_check('google') ) { ?>
        <a href="<?php echo $self_url;?>?provider=google&amp;url=<?php echo $urlencode;?>" class="sns-icon social_link sns-google" title="<?php e__('Google'); ?>">
            <span class="ico"></span>
            <span class="txt"><?php e__('Google'); ?><i> <?php e__('Login'); ?></i></span>
        </a>
        <?php }     //end if ?>
        <?php if( social_service_check('twitter') ) { ?>
        <a href="<?php echo $self_url;?>?provider=twitter&amp;url=<?php echo $urlencode;?>" class="sns-icon social_link sns-twitter" title="<?php e__('Twitter'); ?>">
            <span class="ico"></span>
            <span class="txt"><?php e__('Twitter'); ?><i> <?php e__('Login'); ?></i></span>
        </a>
        <?php }     //end if ?>
        <?php if( social_service_check('github') ) { ?>
        <a href="<?php echo $self_url;?>?provider=github&amp;url=<?php echo $urlencode;?>" class="sns-icon social_link sns-github" title="<?php e__('Github'); ?>">
            <span class="ico"></span>
            <span class="txt"><?php e__('Github'); ?><i> <?php e__('Login'); ?></i></span>
        </a>
        <?php }     //end if ?>
        <?php if( social_service_check('naver') ) { ?>
        <a href="<?php echo $self_url;?>?provider=naver&amp;url=<?php echo $urlencode;?>" class="sns-icon social_link sns-naver" title="<?php e__('Naver'); ?>">
            <span class="ico"></span>
            <span class="txt"><?php e__('Naver'); ?><i> <?php e__('Login'); ?></i></span>
        </a>
        <?php }     //end if ?>
        <?php if( social_service_check('kakao') ) { ?>
        <a href="<?php echo $self_url;?>?provider=kakao&amp;url=<?php echo $urlencode;?>" class="sns-icon social_link sns-kakao" title="<?php e__('Kakao'); ?>">
            <span class="ico"></span>
            <span class="txt"><?php e__('Kakao'); ?><i> <?php e__('Login'); ?></i></span>
        </a>
        <?php }     //end if ?>

        <?php if( GML_SOCIAL_USE_POPUP && !$social_pop_once ){
        $social_pop_once = true;

        get_localize_script('social_login_skin',
        array(
        'check_msg'=>__('Pop-up Blocker is blocked in your browser. Please activate pop-up and try again.'),  // 브라우저에서 팝업이 차단되어 있습니다. 팝업 활성화 후 다시 시도해 주세요.
        ),
        true);

        ?>
        <script>
            jQuery(function($){
                $(".sns-wrap").on("click", "a.social_link", function(e){
                    e.preventDefault();

                    var pop_url = $(this).attr("href");
                    var newWin = window.open(
                        pop_url,
                        "social_sing_on",
                        "location=0,status=0,scrollbars=0,width=600,height=500"
                    );

                    if(!newWin || newWin.closed || typeof newWin.closed=='undefined')
                         alert(social_login_skin.check_msg);

                    return false;
                });
            });
        </script>
        <?php } ?>

    </div>
</div>
