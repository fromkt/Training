<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

if(!$is_admin)
    alert(__('You do not have access.'), GML_URL);

// load language for l10n
bind_lang_domain( 'default', get_path_lang_dir('skin', $board_skin_path.'/'.GML_LANG_DIR) );

@include_once($board_skin_path.'/delete_all.head.skin.php');

$count_write = 0;
$count_comment = 0;

$tmp_array = array();
if ($wr_id) // Delete per item
    $tmp_array[0] = $wr_id;
else // Batch deletion
    $tmp_array = $_POST['chk_wr_id'];

$chk_count = count($tmp_array);

if($chk_count > (GML_IS_MOBILE ? $board['bo_mobile_page_rows'] : $board['bo_page_rows']))
    alert(__('Please use the correct method.'));

// 사용자 코드 실행
@include_once($board_skin_path.'/delete_all.skin.php');

// 거꾸로 읽는 이유는 답변글부터 삭제가 되어야 하기 때문임
for ($i=$chk_count-1; $i>=0; $i--)
{
    $write = sql_fetch(" select * from $write_table where wr_id = '$tmp_array[$i]' ");

    if ($is_admin == 'super') // IF Super Admin
        ;
    else if ($is_admin == 'group') // IF Group Admin
    {
        $mb = get_member($write['mb_id']);
        if ($member['mb_id'] == $group['gr_admin']) // 자신이 관리하는 그룹인가?
        {
            if ($member['mb_level'] >= $mb['mb_level']) // 자신의 레벨이 크거나 같다면 통과
                ;
            else
                continue;
        }
        else
            continue;
    }
    else if ($is_admin == 'board') // IF Board Admin
    {
        $mb = get_member($write['mb_id']);
        if ($member['mb_id'] == $board['bo_admin']) // 자신이 관리하는 게시판인가?
            if ($member['mb_level'] >= $mb['mb_level']) // 자신의 레벨이 크거나 같다면 통과
                ;
            else
                continue;
        else
            continue;
    }
    else if ($member['mb_id'] && $member['mb_id'] == $write['mb_id']) // 자신의 글이라면
    {
        ;
    }
    else if ($wr_password && !$write['mb_id'] && check_password($wr_password, $write['wr_password'])) // 비밀번호가 같다면
    {
        ;
    }
    else
        continue;   // 나머지는 삭제 불가

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
    if ($row['cnt'])
            continue;

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
                // Delete file
                @unlink(GML_DATA_PATH.'/file/'.$bo_table.'/'.str_replace('../', '',$row2['bf_file']));

                // Delete thumbnail
                if(preg_match("/\.({$config['cf_image_extension']})$/i", $row2['bf_file'])) {
                    delete_board_thumbnail($bo_table, $row2['bf_file']);
                }
            }

            // Delete Editor Thumbnails
            delete_editor_thumbnail($row['wr_content']);

            // Delete File Table Row
            sql_query(" delete from {$gml['board_file_table']} where bo_table = '$bo_table' and wr_id = '{$row['wr_id']}' ");

            // CKEDITOR Upload images sources
            if( $config['cf_editor'] == "ckeditor4" ) {
                include_once(GML_EDITOR_LIB);
                if( class_exists('EditorImage') ) {
                    // 게시글 삭제 시 업로드 이미지 삭제
                    $eImg = new EditorImage();
                    $eImg->chk_delete($bo_table, $row['wr_id']);
                }
            }

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
    $board['bo_notice'] = $bo_notice;
}

// Decrease number of posts
if ($count_write > 0 || $count_comment > 0)
    sql_query(" update {$gml['board_table']} set bo_count_write = bo_count_write - '$count_write', bo_count_comment = bo_count_comment - '$count_comment' where bo_table = '$bo_table' ");

@include_once($board_skin_path.'/delete_all.tail.skin.php');

delete_cache_latest($bo_table);

start_event('bbs_delete_all', $tmp_array, $board);

goto_url(get_pretty_url($bo_table, '', '&amp;page='.$page.$qstr));
?>