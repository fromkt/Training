<?php
include_once('./_common.php');

//print_r2($_POST); exit;

if ($is_admin != 'super')
    alert(__('Access is only available to Super Admin'));

$board = array();
$save_bo_table = array();

for($i=0;$i<count($_POST['chk_bn_id']);$i++)
{
    // 실제 번호를 넘김
    $k = $_POST['chk_bn_id'][$i];

    $bo_table = $_POST['bo_table'][$k];
    $wr_id    = $_POST['wr_id'][$k];

    $save_bo_table[] = $bo_table;

    $write_table = $gml['write_prefix'].$bo_table;

    if ($board['bo_table'] != $bo_table)
        $board = get_board_db($bo_table, true);

    $write = get_write($write_table, $wr_id, true);
    if (!$write) continue;

    // Delete Post
    if ($write['wr_is_comment']==0)
    {
        $len = strlen($write['wr_reply']);
        if ($len < 0) $len = 0;
        $reply = substr($write['wr_reply'], 0, $len);

        // 나라오름님 수정 : 원글과 코멘트수가 정상적으로 업데이트 되지 않는 오류를 잡아 주셨습니다.
        $sql = " select wr_id, mb_id, wr_is_comment from $write_table where wr_parent = '{$write['wr_id']}' order by wr_id ";
        $result = sql_query($sql);
        while ($row = sql_fetch_array($result))
        {
            // 원글이라면
            if (!$row['wr_is_comment'])
            {
                if (!delete_point($row['mb_id'], $bo_table, $row['wr_id'], 'write'))
                    insert_point($row['mb_id'], $board['bo_write_point'] * (-1), "{$board['bo_subject']} {$row['wr_id']} ".__('Delete Post'));

                // 업로드된 파일이 있다면 파일삭제
                $sql2 = " select * from {$gml['board_file_table']} where bo_table = '$bo_table' and wr_id = '{$row['wr_id']}' ";
                $result2 = sql_query($sql2);
                while ($row2 = sql_fetch_array($result2))
                    @unlink(GML_DATA_PATH.'/file/'.$bo_table.'/'.$row2['bf_file']);

                // 파일테이블 행 삭제
                sql_query(" delete from {$gml['board_file_table']} where bo_table = '$bo_table' and wr_id = '{$row['wr_id']}' ");

                $count_write++;
            }
            else
            {
                // 코멘트 포인트 삭제
                if (!delete_point($row['mb_id'], $bo_table, $row['wr_id'], 'comment'))
                    insert_point($row['mb_id'], $board['bo_comment_point'] * (-1), "{$board['bo_subject']} {$write['wr_id']}-{$row['wr_id']} ".__('Delete Comment'));

                $count_comment++;
            }
        }

        if ($pressed === 'delete_selection_content') {
            // Remove content and replace user
            sql_query(" update $write_table set wr_subject =  '".GML_TIME_YMDHIS." - ".__('Delete to the request of the own User ☆')."', wr_content = '', wr_name='".__('Delete own User request ☆')."' where wr_id = '{$write['wr_id']}' ");
        } else {
            // Delete Post
            sql_query(" delete from $write_table where wr_parent = '{$write['wr_id']}' ");
        }

        // Delete new post
        sql_query(" delete from {$gml['board_new_table']} where bo_table = '$bo_table' and wr_parent = '{$write['wr_id']}' ");

        // Delete scrap
        sql_query(" delete from {$gml['scrap_table']} where bo_table = '$bo_table' and wr_id = '{$write['wr_id']}' ");

        // Remove notice
        $notice_array = explode(",", trim($board['bo_notice']));
        $bo_notice = "";
        $lf = '';
        for ($k=0; $k<count($notice_array); $k++) {
            if ((int)$write['wr_id'] != (int)$notice_array[$k])
                $bo_notice .= $nl.$notice_array[$k];

            if($bo_notice)
                $lf = ',';
        }
        $bo_notice = trim($bo_notice);
        sql_query(" update {$gml['board_table']} set bo_notice = '$bo_notice' where bo_table = '$bo_table' ");

        if ($pressed === 'delete_selection') {
            // 글숫자 감소
            if ($count_write > 0 || $count_comment > 0) {
                sql_query(" update {$gml['board_table']} set bo_count_write = bo_count_write - '$count_write', bo_count_comment = bo_count_comment - '$count_comment' where bo_table = '$bo_table' ");
            }
        }
    }
    else // Delete Comment
    {
        //--------------------------------------------------------------------
        // 코멘트 삭제시 답변 코멘트 까지 삭제되지는 않음
        //--------------------------------------------------------------------
        //print_r2($write);

        $comment_id = $wr_id;

        $len = strlen($write['wr_comment_reply']);
        if ($len < 0) $len = 0;
        $comment_reply = substr($write['wr_comment_reply'], 0, $len);

        // Delete Comment point
        if (!delete_point($write['mb_id'], $bo_table, $comment_id, 'comment')) {
            insert_point($write['mb_id'], $board['bo_comment_point'] * (-1), "{$board['bo_subject']} {$write[wr_parent]}-{$comment_id} ".__('Delete Comment'));
        }

        // Delete Comment
        sql_query(" delete from $write_table where wr_id = '$comment_id' ");

        // 코멘트가 삭제되므로 해당 게시물에 대한 최근 시간을 다시 얻는다.
        $sql = " select max(wr_datetime) as wr_last from $write_table where wr_parent = '{$write['wr_parent']}' ";
        $row = sql_fetch($sql);

        // 원글의 코멘트 숫자를 감소
        sql_query(" update $write_table set wr_comment = wr_comment - 1, wr_last = '$row[wr_last]' where wr_id = '{$write['wr_parent']}' ");

        // 코멘트 숫자 감소
        sql_query(" update {$gml['board_table']} set bo_count_comment = bo_count_comment - 1 where bo_table = '$bo_table' ");

        // delete new post
        sql_query(" delete from {$gml['board_new_table']} where bo_table = '$bo_table' and wr_id = '$comment_id' ");
    }
}

$save_bo_table = array_unique($save_bo_table);
foreach ($save_bo_table as $key=>$value) {
    delete_cache_latest($value);
}

start_event('bbs_new_delete', $chk_bn_id, $save_bo_table);

goto_url("new.php?sfl=$sfl&stx=$stx&page=$page");
?>