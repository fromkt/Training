<?php
include_once('./_common.php');

if (!$is_member)
    alert(__('Only members can access it.'));

$delete_token = get_session('ss_memo_delete_token');
set_session('ss_memo_delete_token', '');

if (!($token && $delete_token == $token))
    alert(__('Unable to delete due to token error.'));

$me_id = (int)$_REQUEST['me_id'];

$sql = " select * from {$gml['memo_table']} where me_id = '{$me_id}' ";
$row = sql_fetch($sql);

$sql = " delete from {$gml['memo_table']}
            where me_id = '{$me_id}'
            and (me_recv_mb_id = '{$member['mb_id']}' or me_send_mb_id = '{$member['mb_id']}') ";
sql_query($sql);

if (!$row['me_read_datetime'][0]) // Before you take a memo
{
    $sql = " update {$gml['member_table']}
                set mb_memo_call = ''
                where mb_id = '{$row['me_recv_mb_id']}'
                and mb_memo_call = '{$row['me_send_mb_id']}' ";
    sql_query($sql);

    $sql = " update `{$gml['member_table']}` set mb_memo_cnt = '".get_memo_not_read($member['mb_id'])."' where mb_id = '{$member['mb_id']}' ";
    sql_query($sql);
}

start_event('memo_delete', $me_id, $row);

goto_url('./memo.php?kind='.$kind);
?>
