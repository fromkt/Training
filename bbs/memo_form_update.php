<?php
include_once('./_common.php');
include_once(GML_CAPTCHA_PATH.'/captcha.lib.php');

if ($is_guest)
    alert(__('Only members can access it.'));

if (!chk_captcha()) {
    alert(__('The Captcha certification is invalid.'));
}

$recv_list = explode(',', trim($_POST['me_recv_mb_nicks']));
$str_nick_list = '';
$msg = '';
$error_list  = array();
$member_list = array();

start_event('memo_form_update_before', $recv_list);

for ($i=0; $i<count($recv_list); $i++) {

    $recv_nick = preg_replace('/\s+/', '', get_search_string($recv_list[$i]));

    $sql = " select mb_id, mb_nick, mb_open, mb_leave_date, mb_intercept_date from {$gml['member_table']} where mb_nick = '".sql_real_escape_string($recv_nick)."' ";
    
    $row = sql_fetch($sql);

    if ($row) {
        if ($is_admin || ($row['mb_open'] && (!$row['mb_leave_date'] || !$row['mb_intercept_date']))) {
            $member_list['id'][]   = $row['mb_id'];
            $member_list['nick'][] = $row['mb_nick'];
        } else {
            $error_list[]   = $recv_nick;
        }
    }
    /*
    // 관리자가 아니면서
    // 가입된 회원이 아니거나 정보공개를 하지 않았거나 탈퇴한 회원이거나 차단된 회원에게 쪽지를 보내는것은 에러
    if ((!$row['mb_id'] || !$row['mb_open'] || $row['mb_leave_date'] || $row['mb_intercept_date']) && !$is_admin) {
        $error_list[]   = $recv_list[$i];
    } else {
        $member_list['id'][]   = $row['mb_id'];
        $member_list['nick'][] = $row['mb_nick'];
    }
    */
}

$error_msg = implode(",", $error_list);

if ($error_msg && !$is_admin)
    alert(sprintf(__('Member Nickname "%s" is a member that does not exist (or is not opened), is a member Nickname that has been excluded, or has been blocked from access.'), $error_msg).'\\n'.__('Did not send your Memo.'));

if (!$is_admin) {
    if (isset($member_list['id']) && is_array($member_list['id'])) {
        $point = (int)$config['cf_memo_send_point'] * count($member_list['id']);
        if ($point) {
            if ($member['mb_point'] - $point < 0) {
                alert( sprintf(__('Your point ( %s points) is not enough to send a Memo.'), number_format($member['mb_point'])) );
            }
        }
    }
}

if(isset($member_list['id']) && is_array($member_list['id'])){
    for ($i=0; $i<count($member_list['id']); $i++) {

        $recv_mb_id   = $member_list['id'][$i];
        $recv_mb_nick = get_text($member_list['nick'][$i]);

        // 받는 회원 쪽지 INSERT
        $sql = " insert into {$gml['memo_table']} ( me_recv_mb_id, me_send_mb_id, me_send_datetime, me_memo, me_type, me_send_ip ) values ( '$recv_mb_id', '{$member['mb_id']}', '".GML_TIME_YMDHIS."', '{$_POST['me_memo']}' , 'recv', '".get_real_client_ip()."' ) ";

        sql_query($sql);

        if( $me_id = sql_insert_id() ){

            // 보내는 회원 쪽지 INSERT
            $sql = " insert into {$gml['memo_table']} ( me_recv_mb_id, me_send_mb_id, me_send_datetime, me_memo, me_send_id, me_type , me_send_ip ) values ( '$recv_mb_id', '{$member['mb_id']}', '".GML_TIME_YMDHIS."', '{$_POST['me_memo']}', '$me_id', 'send', '".get_real_client_ip()."' ) ";
            sql_query($sql);

        }

        // 실시간 쪽지 알림 기능
        $sql = " update {$gml['member_table']} set mb_memo_call = '{$member['mb_id']}', mb_memo_cnt = '".get_memo_not_read($recv_mb_id)."' where mb_id = '$recv_mb_id' ";
        sql_query($sql);

        // 알림
        add_notice("memo", $member['mb_id'], $recv_mb_id, "", $me_id, $me_id);

        if (!$is_admin) {
            insert_point($member['mb_id'], (int)$config['cf_memo_send_point'] * (-1), sprintf(__('Send a memo to %s'), $recv_mb_nick), '@memo', $recv_mb_id, $me_id);
        }
    }
}

if ($member_list) {

    $redirect_url = GML_HTTP_BBS_URL."/memo.php?kind=send";
    $str_nick_list = implode(',', $member_list['nick']);

    start_event('memo_form_update_after', $member_list, $str_nick_list, $redirect_url);

    alert(sprintf(__('You have sent a Memo to %s.'), $str_nick_list), $redirect_url, false);
} else {

    $redirect_url = GML_HTTP_BBS_URL."/memo_form.php";

    start_event('memo_form_update_failed', $member_list, $redirect_url);

    alert(__('The member did not exist, so did not send a Memo.'), $redirect_url, false);
}
?>
