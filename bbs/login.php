<?php
include_once('./_common.php');

if( function_exists('social_check_login_before') ){
    $social_login_html = social_check_login_before();
}

$gml['title'] = __('Login');
include_once('./_head.sub.php');

$url = $_GET['url'];

// Check url
check_url_host($url);

// If already logged in
if ($is_member) {
    if ($url)
        goto_url($url);
    else
        goto_url(GML_URL);
}

$login_url        = login_url($url);
$login_action_url = GML_HTTPS_BBS_URL."/login_check.php";

// 로그인 스킨이 없는 경우 관리자 페이지 접속이 안되는 것을 막기 위하여 기본 스킨으로 대체
$login_file = $member_skin_path.'/login.skin.php';
if (!file_exists($login_file))
    $member_skin_path   = GML_SKIN_PATH.'/member/basic';

// import the 'social login' language file.
bind_lang_domain( 'default', get_path_lang_dir('skin', get_social_skin_path().'/'.GML_LANG_DIR) );
// load language for l10n
bind_lang_domain( 'default', get_path_lang_dir('skin', $member_skin_path.'/'.GML_LANG_DIR) );

include_once($member_skin_path.'/login.skin.php');

start_event('member_login_tail', $login_url, $login_action_url, $member_skin_path, $url);

include_once('./_tail.sub.php');
?>
