<?php
include_once('./_common.php');
//include_once(GML_CAPTCHA_PATH.'/captcha.lib.php');
include_once(GML_LIB_PATH.'/register.lib.php');

define('ASIDE_DISABLE', 1);

if( ! $config['cf_social_login_use'] ){
    alert(__('Social login settings are disabled.'));
}

if( $is_member ){
    alert(__('Already registered as a member.'), GML_URL);
}

$provider_name = social_get_request_provider();
$user_profile = social_session_exists_check();
if( ! $user_profile ){
    alert(__('Only social login users can access it.'), GML_URL);
}

// 소셜 가입된 내역이 있는지 확인 상수 GML_SOCIAL_DELETE_DAY 관련
$is_exists_social_account = social_before_join_check($url);

$user_nick = social_relace_nick($user_profile->displayName);
$user_email = isset($user_profile->emailVerified) ? $user_profile->emailVerified : $user_profile->email;
$user_id = $user_profile->sid ? preg_replace("/[^0-9a-z_]+/i", "", $user_profile->sid) : get_social_convert_id($user_profile->identifier, $provider_name);

//$is_exists_id = exist_mb_id($user_id);
//$is_exists_name = exist_mb_nick($user_nick, '');
$user_id = exist_mb_id_recursive($user_id);
$user_nick = exist_mb_nick_recursive($user_nick, '');
$is_exists_email = $user_email ? exist_mb_email($user_email, '') : false;

// 불법접근을 막도록 토큰생성
$token = md5(uniqid(rand(), true));
set_session("ss_token", $token);

$gml['title'] = __('Social login').' - '.social_get_provider_service_name($provider_name);

include_once(GML_BBS_PATH.'/_head.php');

$register_action_url = https_url(GML_PLUGIN_DIR.'/'.GML_SOCIAL_LOGIN_DIR, true).'/register_member_update.php';
$login_action_url = GML_HTTPS_BBS_URL."/login_check.php";
$req_nick = !isset($member['mb_nick_date']) || (isset($member['mb_nick_date']) && $member['mb_nick_date'] <= date("Y-m-d", GML_SERVER_TIME - ($config['cf_nick_modify'] * 86400)));
$required = ($w=='') ? 'required' : '';
$readonly = ($w=='u') ? 'readonly' : '';

// load language for l10n
bind_lang_domain( 'default', get_path_lang_dir('skin', get_social_skin_path().'/'.GML_LANG_DIR) );

include_once(get_social_skin_path().'/social_register_member.skin.php');

include_once(GML_BBS_PATH.'/_tail.php');
?>
