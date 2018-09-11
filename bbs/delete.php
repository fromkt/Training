<?php
include_once('./_common.php');

$delete_token = get_session('ss_delete_token');
set_session('ss_delete_token', '');

if (!($token && $delete_token == $token))
    alert(__('Unable to delete due to token error.'));

//$wr = sql_fetch(" select * from $write_table where wr_id = '$wr_id' ");

// load language for l10n
bind_lang_domain( 'default', get_path_lang_dir('skin', $board_skin_path.'/'.GML_LANG_DIR) );

@include_once($board_skin_path.'/delete.head.skin.php');

if ($is_admin == 'super') // IF Super Admin
    ;
else if ($is_admin == 'group') { // IF Group Admin
    $mb = get_member($write['mb_id']);
    if ($member['mb_id'] != $group['gr_admin']) // 자신이 관리하는 그룹인가?
        alert(__('You can not delete it because it is not a bulletin board for the group you manage.'));
    else if ($member['mb_level'] < $mb['mb_level']) // 자신의 레벨이 크거나 같다면 통과
        alert(__('You can not delete an post written by a member with a privilege higher than own writer.'));
} else if ($is_admin == 'board') { // IF Board Admin
    $mb = get_member($write['mb_id']);
    if ($member['mb_id'] != $board['bo_admin']) // 자신이 관리하는 게시판인가?
        alert(__('You can not delete a bulletin because it is not your managed.'));
    else if ($member['mb_level'] < $mb['mb_level']) // 자신의 레벨이 크거나 같다면 통과
        alert(__('You can not delete an post written by a member with a privilege higher than own writer.'));
} else if ($member['mb_id']) {
    if ($member['mb_id'] !== $write['mb_id'])
        alert(__('Can not delete because it is not your own post.'));
} else {
    if ($write['mb_id'])
        alert(__('Please login and delete it.'), './login.php?url='.urlencode(get_pretty_url($bo_table, $wr_id)));
    else if (!check_password($wr_password, $write['wr_password']))
        alert(__('Password is incorrect and can not be deleted.'));
}

$len = strlen($write['wr_reply']);
if ($len < 0) $len = 0;
$reply = substr($write['wr_reply'], 0, $len);

// 원글만 구한다.
$sql = " select count(*) as cnt from $write_table
            where wr_reply like '$reply%'
            and wr_id <> '{$write['wr_id']}'
            and wr_num = '{$write['wr_num']}'
            and wr_is_comment = 0 ";
$row = sql_fetch($sql);
if ($row['cnt'] && !$is_admin)
    alert(__('The answer to this post exists and can not be deleted.').'\\n\\n'.__('Please delete the answer first.'));

// 코멘트 달린 원글의 삭제 여부
$sql = " select count(*) as cnt from $write_table
            where wr_parent = '$wr_id'
            and mb_id <> '{$member['mb_id']}'
            and wr_is_comment = 1 ";
$row = sql_fetch($sql);
if ($row['cnt'] >= $board['bo_count_delete'] && !$is_admin)
    alert(__('You can not delete any comments related to this post because they exist.').'\\n\\n'.sprintf(__('You can not delete a post with %s or more comments.'), $board['bo_count_delete']));


// Run Skin file Code
@include_once($board_skin_path.'/delete.skin.php');


$sql = " select wr_id, mb_id, wr_is_comment, wr_content from $write_table where wr_parent = '{$write['wr_id']}' order by wr_id ";
$result = sql_query($sql);
while ($row = sql_fetch_array($result))
{
    // 원글이라면
    if (!$row['wr_is_comment'])
    {
        // 원글 포인트 삭제
        if (!delete_point($row['mb_id'], $bo_table, $row['wr_id'], 'write'))
            insert_point($row['mb_id'], $board['bo_write_point'] * (-1), "{$board['bo_subject']} {$row['wr_id']} ".__('Delete Post'));

        // Delete files if they are uploaded
        $sql2 = " select * from {$gml['board_file_table']} where bo_table = '$bo_table' and wr_id = '{$row['wr_id']}' ";
        $result2 = sql_query($sql2);
        while ($row2 = sql_fetch_array($result2)) {
            @unlink(GML_DATA_PATH.'/file/'.$bo_table.'/'.str_replace('../', '', $row2['bf_file']));
            // Delete thumbnail
            if(preg_match("/\.({$config['cf_image_extension']})$/i", $row2['bf_file'])) {
                delete_board_thumbnail($bo_table, $row2['bf_file']);
            }
        }

        // Delete Editor Thumbnails
        delete_editor_thumbnail($row['wr_content']);

        // Delete File Table Row
        sql_query(" delete from {$gml['board_file_table']} where bo_table = '$bo_table' and wr_id = '{$row['wr_id']}' ");

        $count_write++;
    }
    else
    {
        // Delete Comment Points
        if (!delete_point($row['mb_id'], $bo_table, $row['wr_id'], 'comment'))
            insert_point($row['mb_id'], $board['bo_comment_point'] * (-1), "{$board['bo_subject']} {$write['wr_id']}-{$row['wr_id']} ".__('Delete Comment'));

        $count_comment++;
    }
}

// Deleting a post
sql_query(" delete from $write_table where wr_parent = '{$write['wr_id']}' ");

// Delete a new post
sql_query(" delete from {$gml['board_new_table']} where bo_table = '$bo_table' and wr_parent = '{$write['wr_id']}' ");

// Delete Scrap
sql_query(" delete from {$gml['scrap_table']} where bo_table = '$bo_table' and wr_id = '{$write['wr_id']}' ");

// Remove Notice
$bo_notice = board_notice($board['bo_notice'], $write['wr_id']);
sql_query(" update {$gml['board_table']} set bo_notice = '$bo_notice' where bo_table = '$bo_table' ");

// Decrease number of posts
if ($count_write > 0 || $count_comment > 0)
    sql_query(" update {$gml['board_table']} set bo_count_write = bo_count_write - '$count_write', bo_count_comment = bo_count_comment - '$count_comment' where bo_table = '$bo_table' ");

// CKEDITOR Upload images sources
if( $config['cf_editor'] == "ckeditor4" ) {
    include_once(GML_EDITOR_LIB);
    if( class_exists('EditorImage') ) {
        // 게시글 삭제 시 업로드 이미지 삭제
        $eImg = new EditorImage();
        $eImg->chk_delete($bo_table, $write['wr_id']);
    }
}

@include_once($board_skin_path.'/delete.tail.skin.php');

delete_cache_latest($bo_table);

start_event('bbs_delete', $write, $board);

goto_url(get_pretty_url($bo_table, '', '&amp;page='.$page.$qstr));
?>
