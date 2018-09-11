<?php
// Delete Comment
include_once('./_common.php');

$comment_id = (int) $comment_id;

$delete_comment_token = get_session('ss_delete_comment_'.$comment_id.'_token');
set_session('ss_delete_comment_'.$comment_id.'_token', '');

if (!($token && $delete_comment_token == $token))
    alert(__('Unable to delete due to token error.'));

// load language for l10n
bind_lang_domain( 'default', get_path_lang_dir('skin', $board_skin_path.'/'.GML_LANG_DIR) );

@include_once($board_skin_path.'/delete_comment.head.skin.php');

$write = get_write($write_table, $comment_id);
if (!$write['wr_id'] || !$write['wr_is_comment'])
    alert(__('No comments have been registered or this is not a comment.'));

if ($is_admin == 'super') // IF Super Admin
    ;
else if ($is_admin == 'group') { // IF Group Admin
    $mb = get_member($write['mb_id']);
    if ($member['mb_id'] === $group['gr_admin']) { // 자신이 관리하는 그룹인가?
        if ($member['mb_level'] >= $mb['mb_level']) // 자신의 레벨이 크거나 같다면 통과
            ;
        else
            alert(__('Can not delete as this is a comment from the member above the group admin authority.'));
    } else
        alert(__('You can not delete a comment because it is not a bulletin board for the group you manage.'));
} else if ($is_admin === 'board') { // IF Board Admin
    $mb = get_member($write['mb_id']);
    if ($member['mb_id'] === $board['bo_admin']) { // 자신이 관리하는 게시판인가?
        if ($member['mb_level'] >= $mb['mb_level']) // 자신의 레벨이 크거나 같다면 통과
            ;
        else
            alert(__('This is a comment made by a member above the board admin authority and can not be deleted.'));
    } else
        alert(__('You can not delete comments because they are not your own bulletin board.'));
} else if ($member['mb_id']) {
    if ($member['mb_id'] !== $write['mb_id'])
        alert(__('Can not delete because it is not your own post.'));
} else {
    if (!check_password($wr_password, $write['wr_password']))
        alert(__('The password is incorrect.'));
}

$len = strlen($write['wr_comment_reply']);
if ($len < 0) $len = 0;
$comment_reply = substr($write['wr_comment_reply'], 0, $len);

$sql = " select count(*) as cnt from {$write_table}
            where wr_comment_reply like '{$comment_reply}%'
            and wr_id <> '{$comment_id}'
            and wr_parent = '{$write[wr_parent]}'
            and wr_comment = '{$write[wr_comment]}'
            and wr_is_comment = 1 ";
$row = sql_fetch($sql);
if ($row['cnt'] && !$is_admin)
    alert(__('You can not delete an answer comment associated with this comment because it exists.'));

// Delete Comment Points
if (!delete_point($write['mb_id'], $bo_table, $comment_id, 'comment'))
    insert_point($write['mb_id'], $board['bo_comment_point'] * (-1), "{$board['bo_subject']} {$write['wr_parent']}-{$comment_id} ".__('Delete Comment'));

// Delete Comment
sql_query(" delete from {$write_table} where wr_id = '{$comment_id}' ");

// Obtain the most recent time for the post again because the comment is deleted.
$sql = " select max(wr_datetime) as wr_last from {$write_table} where wr_parent = '{$write['wr_parent']}' ";
$row = sql_fetch($sql);

// Reduce the number of original comments
sql_query(" update {$write_table} set wr_comment = wr_comment - 1, wr_last = '{$row['wr_last']}' where wr_id = '{$write['wr_parent']}' ");

// Update board comment count
sql_query(" update {$gml['board_table']} set bo_count_comment = bo_count_comment - 1 where bo_table = '{$bo_table}' ");

// Delete new post
sql_query(" delete from {$gml['board_new_table']} where bo_table = '{$bo_table}' and wr_id = '{$comment_id}' ");

// Run skin code
@include_once($board_skin_path.'/delete_comment.skin.php');
@include_once($board_skin_path.'/delete_comment.tail.skin.php');

delete_cache_latest($bo_table);

start_event('bbs_delete_comment', $comment_id, $board);

goto_url(get_pretty_url($bo_table, $write['wr_parent'], '&amp;page='.$page. $qstr));
?>
