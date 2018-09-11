<?php
include_once('./_common.php');
include_once(GML_CAPTCHA_PATH.'/captcha.lib.php');

if ($is_guest)
    alert_close(__('Only members can access it.'));

if (!$member['mb_open'] && $is_admin != 'super' && $member['mb_id'] != $mb_id)
    alert_close(__('You cant send a note to someone else unless you open your profile. Profile open settings can be made in Edit Member profile.'));

$content = "";
// Unable to send a MEMO to a member who has leave 탈퇴한 회원에게 쪽지 보낼 수 없음

$mb_id = isset($mb_id) ? $mb_id : '';
$mb_hash = isset($mb_hash) ? $mb_hash : '';
$me_recv_mb_nicks = '';

if ($mb_id || $mb_hash)
{
    $me_recv_mb_id = get_search_string(get_member_by_hash($mb_id, $mb_hash));

    $mb = get_member($me_recv_mb_id);
    $me_recv_mb_nicks = $mb['mb_nick'];

    if (!$mb['mb_id'])
        alert_close(__('Member information does not exist.').'\\n\\n'.__('This member may have left.'));

    if (!$mb['mb_open'] && $is_admin != 'super')
        alert_close(__('This member has not Open Profile'));

    // 4.00.15
    $row = sql_fetch(" select me_memo from {$gml['memo_table']} where me_id = '{$me_id}' and (me_recv_mb_id = '{$member['mb_id']}' or me_send_mb_id = '{$member['mb_id']}') ");
    if ($row['me_memo'])
    {
        $content = "\n\n\n".' >'
                         ."\n".' >'
                         ."\n".' >'.str_replace("\n", "\n> ", get_text($row['me_memo'], 0))
                         ."\n".' >'
                         .' >';

    }
}

$gml['title'] = __('Send Memo');
include_once(GML_PATH.'/head.sub.php');

$memo_action_url = GML_HTTPS_BBS_URL."/memo_form_update.php";

// load language for l10n
bind_lang_domain( 'default', get_path_lang_dir('skin', $member_skin_path.'/'.GML_LANG_DIR) );

include_once($member_skin_path.'/memo_form.skin.php');

include_once(GML_PATH.'/tail.sub.php');
?>
