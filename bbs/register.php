<?php
include_once('./_common.php');

// 로그인중인 경우 회원가입 할 수 없습니다.
if ($is_member) {
    goto_url(GML_URL);
}

define("_DONT_WRAP_IN_CONTAINER_", true);

// 세션을 지웁니다.
set_session("ss_mb_reg", "");

$gml['title'] = __('Terms and conditions of Register');
include_once('./_head.php');

$register_action_url = GML_BBS_URL.'/register_form.php';

// import the 'social login' language file.
bind_lang_domain( 'default', get_path_lang_dir('skin', get_social_skin_path().'/'.GML_LANG_DIR) );
// load language for l10n
bind_lang_domain( 'default', get_path_lang_dir('skin', $member_skin_path.'/'.GML_LANG_DIR) );

include_once($member_skin_path.'/register.skin.php');

include_once('./_tail.php');
?>
