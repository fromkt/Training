<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

if( ! $config['cf_social_login_use']) {     // If you don't use social login
    return;
}

$socials = social_get_provider_service_name('', 'all');

$session_id = session_id();

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.get_social_skin_url().'/style.css">', 10);
?>

<li>
    <label class="frm_label"><?php e__('Manage SNS login'); ?></label>
    <div class="reg-form sns-wrap-reg">
        <div class="sns-wrap">

        <?php foreach( $socials as $social=>$provider_name ){

            if( !option_array_checked($social, $config['cf_social_servicelist'])) {
                continue;
            }

            $social_nonce = social_nonce_create($social, $session_id);
            $add_class='';
            $title='';
            if( in_array($social, $my_provides) ){

                $link_href = GML_SOCIAL_LOGIN_URL.'/unlink.php?provider='.$social.'&amp;social_nonce='.$social_nonce;

                $title = sprintf(__('%s Disconnect.'), $provider_name);
            } else {
                $add_class = ' sns-icon-not';

                $link_href = $self_url.'?provider='.$social.'&amp;mylink=1&amp;url='.$urlencode;

                $title = sprintf(__('Link %s account.'), $provider_name);

            }
        ?>

        <a href="<?php echo $link_href; ?>" id="sns-<?php echo $social; ?>" class="sns-icon social_link sns-<?php echo $social; ?><?php echo $add_class; ?>" title="<?php echo $title; ?>" data-provider="<?php echo $social; ?>" ><span class="ico"></span><span class="txt"><?php echo $provider_name; ?> <?php e__('Login'); ?></span></a>

        <?php }     //end foreach ?>

        </div>
    </div>
</li>

<?php
get_localize_script('social_profile_skin',
array(
'disconnect_msg'=>__('%s Disconnect.'),  // 연결을 해제합니다.
'linked_msg'=>__('Linked Account.'),    // 연결 되었습니다.
'really_msg'=>__('Are you sure you want to disconnect this account?'),   // 정말 이 계정 연결을 해제하시겠습니까?
'invalid_msg'=>__('Invalid request! provider value not found.'),   // 잘못된 요청! provider 값이 없습니다.
'link_account_msg'=>__('Link %s account.'),                 // 계정을 연결 합니다.
'check_msg'=>__('Pop-up Blocker is blocked in your browser. Please activate pop-up and try again.'),  // 브라우저에서 팝업이 차단되어 있습니다. 팝업 활성화 후 다시 시도해 주세요.
),
true);
?>

<script>
function social_get_nonce(provider){
    var socials = [];

    <?php foreach( $socials as $social=>$v ){ ?>
        socials["<?php echo $social; ?>"] = "<?php echo social_nonce_create($social, $session_id); ?>";
    <?php } ?>

    return (typeof socials[provider] != 'undefined') ? socials[provider] : '';
}

function social_link_fn(provider){

    provider = provider.toLowerCase();

    var $icon = jQuery("#sns-"+provider);

    if( $icon.length ){

        var social_url = "<?php echo GML_SOCIAL_LOGIN_URL; ?>",
            link_href = social_url+"/unlink.php?provider="+provider+"&social_nonce="+social_get_nonce(provider),
            atitle = js_sprintf(social_profile_skin.disconnect_msg, provider);

        $icon.attr({"href":link_href, "title":atitle}).removeClass("sns-icon-not");

        alert( social_profile_skin.linked_msg );

        return true;
    }

    return false;
}

jQuery(function($){

    var social_img_path = "<?php echo GML_SOCIAL_LOGIN_URL; ?>",
        self_url = "<?php echo $self_url; ?>",
        urlencode = "<?php echo $urlencode; ?>";

    $(".sns-wrap").on("click", ".social_link", function(e){
        e.preventDefault();

        var othis = $(this);

        if( ! othis.hasClass('sns-icon-not') ){     // disconnect social accounts

            if (!confirm( social_profile_skin.really_msg )) {
                return false;
            }

            var ajax_url = "<?php echo GML_SOCIAL_LOGIN_URL.'/unlink.php' ?>",
                mb_id = '',
                provider = $(this).attr("data-provider");

            if( ! provider ){
                alert( social_profile_skin.invalid_msg );
                return false;
            }

            $.ajax({
                url: ajax_url,
                type: 'POST',
                data: {
                    'provider': provider,
                    'mb_id': mb_id,
                    'nonce' : social_get_nonce(provider)
                },
                dataType: 'json',
                cache : false,
                async: false,
                success: function(data, textStatus) {
                    if (data.error) {
                        alert(data.error);
                        return false;
                    } else {
                        var atitle = js_sprintf(social_profile_skin.link_account_msg, provider),
                            link_href = self_url+"?provider="+provider+"&mylink=1&url="+urlencode;

                        othis.attr({"href":link_href, "title":atitle}).addClass("sns-icon-not");
                    }
                },
                error: function(data) {
                    try { console.log(data) } catch (e) { alert(data.error) };
                }
            });

        } else {        // Link social accounts

            var pop_url = $(this).attr("href");
            var is_popup = "<?php echo GML_SOCIAL_USE_POPUP; ?>";

            if( is_popup ){
                var newWin = window.open(
                    pop_url,
                    "social_sing_on",
                    "location=0,status=0,scrollbars=0,width=600,height=500"
                );

                if(!newWin || newWin.closed || typeof newWin.closed=='undefined')
                     alert( social_profile_skin.check_msg );

            } else {
                location.replace(pop_url);
            }

        }
        return false;
    });
});
</script>
