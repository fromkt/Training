<?php
include_once('_common.php');

if( ! $config['cf_social_login_use'] ){
    alert(__('Social login settings are disabled.'));
    return;
}

if( ! GML_SOCIAL_USE_POPUP ){
    alert(__('New window options are disabled.'));
    return;
}

$provider_name = social_get_request_provider();

if( !$provider_name ){
    alert(__('Service name is not imported.'));
}

if( isset( $_REQUEST["redirect_to_idp"] ) ){
    $content = social_check_login_before();

    $get_login_url = GML_BBS_URL."/login.php?url=".$urlencode;

    if( $content ){
        //팝업으로 뜨웠다면 아래 
        ?>
        <script>
        if( window.opener ){
            (function(){
                var login_url = "<?php echo $get_login_url; ?>";

                window.opener.location.href = login_url+"&provider=<?php echo $provider_name; ?>";
                window.close();
            })();
        }
        </script>
        <?php
    }
} else {
    social_login_session_clear(1);
    social_return_from_provider_page( $provider_name, '', '', '', '' );
}
?>