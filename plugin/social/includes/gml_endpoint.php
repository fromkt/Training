<?php
if (!defined('_GNUBOARD_')) exit;
//https://hybridauth.github.io/hybridauth/userguide/tuts/change-hybridauth-endpoint-url.html

class GML_Hybrid_Authentication {

    public static function hybridauth_endpoint() {

        require_once( GML_SOCIAL_LOGIN_PATH.'/Hybrid/Auth.php' );
        require_once( GML_SOCIAL_LOGIN_PATH.'/Hybrid/Endpoint.php' );
        require_once( GML_SOCIAL_LOGIN_PATH.'/includes/gml_endpoint_class.php' );

        if( defined('GML_SOCIAL_LOGIN_START_PARAM') && GML_SOCIAL_LOGIN_START_PARAM !== 'hauth.start' && isset($_REQUEST[GML_SOCIAL_LOGIN_START_PARAM]) ){
            $_REQUEST['hauth_start'] = preg_replace('/[^a-zA-Z0-9\-\._]/i', '', $_REQUEST[GML_SOCIAL_LOGIN_START_PARAM]);
        }

        if( defined('GML_SOCIAL_LOGIN_DONE_PARAM') && GML_SOCIAL_LOGIN_DONE_PARAM !== 'hauth.done' && isset($_REQUEST[GML_SOCIAL_LOGIN_DONE_PARAM]) ){
            $_REQUEST['hauth_done'] = preg_replace('/[^a-zA-Z0-9\-\._]/i', '', $_REQUEST[GML_SOCIAL_LOGIN_DONE_PARAM]);
        }

        /*
        $key = 'hauth.' . $action; // either `hauth_start` or `hauth_done`

        $_REQUEST[ $key ] = $provider; // provider will be something like `facebook` or `google`
        */

        GML_Hybrid_Endpoint::process();
    }

}

?>