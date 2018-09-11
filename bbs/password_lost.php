<?php
include_once('./_common.php');
include_once(GML_CAPTCHA_PATH.'/captcha.lib.php');

if ($is_member) {
    alert(__('You are already logged in.'));
}

$gml['title'] = __('Find Account information');
include_once(GML_PATH.'/head.sub.php');

$action_url = GML_HTTPS_BBS_URL."/password_lost2.php";

// load language for l10n
bind_lang_domain( 'default', get_path_lang_dir('skin', $member_skin_path.'/'.GML_LANG_DIR) );

include_once($member_skin_path.'/password_lost.skin.php');

include_once(GML_PATH.'/tail.sub.php');
?>