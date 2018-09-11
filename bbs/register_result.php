<?php
include_once('./_common.php');

if (isset($_SESSION['ss_mb_reg']))
    $mb = get_member($_SESSION['ss_mb_reg']);

// 회원정보가 없다면 초기 페이지로 이동
if (!$mb['mb_id'])
    goto_url(GML_URL);

define("_DONT_WRAP_IN_CONTAINER_", true);

$gml['title'] = __('Membership complete');
include_once('./_head.php');

// load language for l10n
bind_lang_domain( 'default', get_path_lang_dir('skin', $member_skin_path.'/'.GML_LANG_DIR) );

include_once($member_skin_path.'/register_result.skin.php');
include_once('./_tail.php');
?>
