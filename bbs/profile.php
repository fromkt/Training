<?php
include_once('./_common.php');

if (!$member['mb_id'])
    alert_close(__('Only members can access it.'));

if (!$member['mb_open'] && $is_admin != 'super' && $member['mb_id'] != $mb_id)
    alert_close(__('If you dont open your profile, you cant look up other member profile.').'\\n\\n'.__('Profile open settings can be made in Edit Member Profile.'));

$mb_id = isset($mb_id) ? $mb_id : '';
$mb_hash = isset($mb_hash) ? $mb_hash : '';

$mb = get_member(get_search_string(get_member_by_hash($mb_id, $mb_hash)));

if (!$mb['mb_id'])
    alert_close(__('Member information does not exist.').'\\n\\n'.__('This member may have left.'));

if (!$mb['mb_open'] && $is_admin != 'super' && $member['mb_id'] != $mb_id)
    alert_close(__('This member has not Open Profile'));

$gml['title'] = sprintf(__('Introduction of %s'), $mb['mb_nick']);
include_once(GML_PATH.'/head.sub.php');

$mb_nick = get_sideview($mb['mb_id'], get_text($mb['mb_nick']), $mb['mb_email'], $mb['mb_homepage'], $mb['mb_open']);

// 회원가입후 몇일째인지? + 1 은 당일을 포함한다는 뜻
$sql = " select (TO_DAYS('".GML_TIME_YMDHIS."') - TO_DAYS('{$mb['mb_datetime']}') + 1) as days ";
$row = sql_fetch($sql);
$mb_reg_after = $row['days'];

$mb_homepage = set_http(get_text(clean_xss_tags($mb['mb_homepage'])));
$mb_profile = $mb['mb_profile'] ? conv_content($mb['mb_profile'],0) : __('There is no introduction.');

// load language for l10n
bind_lang_domain( 'default', get_path_lang_dir('skin', $member_skin_path.'/'.GML_LANG_DIR) );

include_once($member_skin_path.'/profile.skin.php');

include_once(GML_PATH.'/tail.sub.php');
?>
