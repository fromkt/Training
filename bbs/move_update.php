<?php
include_once('./_common.php');

// 게시판 관리자 이상 복사, 이동 가능
if ($is_admin != 'board' && $is_admin != 'group' && $is_admin != 'super')
    alert_close(__('Board Admin or higher access is available.'));

if ($sw != 'move' && $sw != 'copy')
    alert(sprintf(__('%s parameter is wrong'), 'sw'));

if(!count($_POST['chk_bo_table']))
    alert(sprintf(__('Please select at least one bulletin board for %s.'), $act), $url);

// 원본 파일 디렉토리
$src_dir = GML_DATA_PATH.'/file/'.$bo_table;

$save = array();
$save_count_write = 0;
$save_count_comment = 0;
$cnt = 0;

$wr_id_list = preg_replace('/[^0-9\,]/', '', $_POST['wr_id_list']);

$sql = " select distinct wr_num from $write_table where wr_id in ({$wr_id_list}) order by wr_id ";
$result = sql_query($sql);
while ($row = sql_fetch_array($result))
{
    $wr_num = $row['wr_num'];
    for ($i=0; $i<count($_POST['chk_bo_table']); $i++)
    {
        $move_bo_table = preg_replace('/[^a-z0-9_]/i', '', $_POST['chk_bo_table'][$i]);

        // 취약점 18-0075 참고
        $sql = "select * from {$gml['board_table']} where bo_table = '".sql_real_escape_string($move_bo_table)."' ";
        $move_board = sql_fetch($sql);
        // 존재하지 않다면
        if( !$move_board['bo_table'] ) continue;

        $move_write_table = $gml['write_prefix'] . $move_bo_table;

        $src_dir = GML_DATA_PATH.'/file/'.$bo_table; // 원본 디렉토리
        $dst_dir = GML_DATA_PATH.'/file/'.$move_bo_table; // 복사본 디렉토리

        $count_write = 0;
        $count_comment = 0;

        $next_wr_num = get_next_num($move_write_table);

        $sql2 = " select * from $write_table where wr_num = '$wr_num' order by wr_parent, wr_is_comment, wr_comment desc, wr_id ";
        $result2 = sql_query($sql2);
        while ($row2 = sql_fetch_array($result2))
        {
            $nick = cut_str($member['mb_nick'], $config['cf_cut_name']);
            if (!$row2['wr_is_comment'] && $config['cf_use_copy_log']) {
                if(strstr($row2['wr_option'], 'html')) {
                    $log_tag1 = '<div class="content_'.$sw.'">';
                    $log_tag2 = '</div>';
                } else {
                    $log_tag1 = "\n";
                    $log_tag2 = '';
                }

                $row2['wr_content'] .= "\n".$log_tag1.'['.sprintf(__('This post was %s from %s by %s'), ($sw == 'copy' ? __('copied') : __('moved')),  GML_TIME_YMDHIS.' '.$board['bo_subject'], $nick).']'.$log_tag2;
            }

            // The Good or Bad Count of Post
            $wr_good = $wr_nogood = 0;
            if ($sw == 'move' && $i == 0) {
                $wr_good = $row2['wr_good'];
                $wr_nogood = $row2['wr_nogood'];
            }

            $sql = " insert into $move_write_table
                        set wr_num = '$next_wr_num',
                             wr_reply = '{$row2['wr_reply']}',
                             wr_is_comment = '{$row2['wr_is_comment']}',
                             wr_comment = '{$row2['wr_comment']}',
                             wr_comment_reply = '{$row2['wr_comment_reply']}',
                             ca_name = '".addslashes($row2['ca_name'])."',
                             wr_option = '{$row2['wr_option']}',
                             wr_subject = '".addslashes($row2['wr_subject'])."',
                             wr_content = '".addslashes($row2['wr_content'])."',
                             wr_link1 = '".addslashes($row2['wr_link1'])."',
                             wr_link2 = '".addslashes($row2['wr_link2'])."',
                             wr_link1_hit = '{$row2['wr_link1_hit']}',
                             wr_link2_hit = '{$row2['wr_link2_hit']}',
                             wr_hit = '{$row2['wr_hit']}',
                             wr_good = '{$wr_good}',
                             wr_nogood = '{$wr_nogood}',
                             mb_id = '{$row2['mb_id']}',
                             wr_password = '{$row2['wr_password']}',
                             wr_name = '".addslashes($row2['wr_name'])."',
                             wr_email = '".addslashes($row2['wr_email'])."',
                             wr_homepage = '".addslashes($row2['wr_homepage'])."',
                             wr_datetime = '{$row2['wr_datetime']}',
                             wr_file = '{$row2['wr_file']}',
                             wr_last = '{$row2['wr_last']}',
                             wr_ip = '{$row2['wr_ip']}',
                             wr_1 = '".addslashes($row2['wr_1'])."',
                             wr_2 = '".addslashes($row2['wr_2'])."',
                             wr_3 = '".addslashes($row2['wr_3'])."',
                             wr_4 = '".addslashes($row2['wr_4'])."',
                             wr_5 = '".addslashes($row2['wr_5'])."',
                             wr_6 = '".addslashes($row2['wr_6'])."',
                             wr_7 = '".addslashes($row2['wr_7'])."',
                             wr_8 = '".addslashes($row2['wr_8'])."',
                             wr_9 = '".addslashes($row2['wr_9'])."',
                             wr_10 = '".addslashes($row2['wr_10'])."' ";
            sql_query($sql);

            $insert_id = sql_insert_id();

            // IF is not Comment
            if (!$row2['wr_is_comment'])
            {
                $save_parent = $insert_id;

                $sql3 = " select * from {$gml['board_file_table']} where bo_table = '$bo_table' and wr_id = '{$row2['wr_id']}' order by bf_no ";
                $result3 = sql_query($sql3);
                for ($k=0; $row3 = sql_fetch_array($result3); $k++)
                {
                    if ($row3['bf_file'])
                    {
                        // 원본파일을 복사하고 퍼미션을 변경
                        // 제이프로님 코드제안 적용
                        $copy_file_name = ($bo_table !== $move_bo_table) ? $row3['bf_file'] : $row2['wr_id'].'_copy_'.$insert_id.'_'.$row3['bf_file'];
                        @copy($src_dir.'/'.$row3['bf_file'], $dst_dir.'/'.$copy_file_name);
                        @chmod($dst_dir/$row3['bf_file'], GML_FILE_PERMISSION);
                    }

                    $sql = " insert into {$gml['board_file_table']}
                                set bo_table = '$move_bo_table',
                                     wr_id = '$insert_id',
                                     bf_no = '{$row3['bf_no']}',
                                     bf_source = '".addslashes($row3['bf_source'])."',
                                     bf_file = '$copy_file_name',
                                     bf_download = '{$row3['bf_download']}',
                                     bf_content = '".addslashes($row3['bf_content'])."',
                                     bf_filesize = '{$row3['bf_filesize']}',
                                     bf_width = '{$row3['bf_width']}',
                                     bf_height = '{$row3['bf_height']}',
                                     bf_type = '{$row3['bf_type']}',
                                     bf_datetime = '{$row3['bf_datetime']}' ";
                    sql_query($sql);

                    if ($sw == 'move' && $row3['bf_file'])
                        $save[$cnt]['bf_file'][$k] = $src_dir.'/'.$row3['bf_file'];
                }

                $count_write++;

                if ($sw == 'move' && $i == 0)
                {
                    // Move Scrap 스크랩 이동
                    sql_query(" update {$gml['scrap_table']} set bo_table = '$move_bo_table', wr_id = '$save_parent' where bo_table = '$bo_table' and wr_id = '{$row2['wr_id']}' ");

                    // Move new post 최신글 이동
                    sql_query(" update {$gml['board_new_table']} set bo_table = '$move_bo_table', wr_id = '$save_parent', wr_parent = '$save_parent' where bo_table = '$bo_table' and wr_id = '{$row2['wr_id']}' ");

                    // Move Good or Bad 추천데이터 이동
                    sql_query(" update {$gml['board_good_table']} set bo_table = '$move_bo_table', wr_id = '$save_parent' where bo_table = '$bo_table' and wr_id = '{$row2['wr_id']}' ");
                }
            }
            else
            {
                $count_comment++;

                if ($sw == 'move')
                {
                    // Move new post 최신글 이동
                    sql_query(" update {$gml['board_new_table']} set bo_table = '$move_bo_table', wr_id = '$insert_id', wr_parent = '$save_parent' where bo_table = '$bo_table' and wr_id = '{$row2['wr_id']}' ");
                }
            }

            sql_query(" update $move_write_table set wr_parent = '$save_parent' where wr_id = '$insert_id' ");

            if ($sw == 'move')
                $save[$cnt]['wr_id'] = $row2['wr_parent'];

            $cnt++;
        }

        sql_query(" update {$gml['board_table']} set bo_count_write = bo_count_write + '$count_write' where bo_table = '$move_bo_table' ");
        sql_query(" update {$gml['board_table']} set bo_count_comment = bo_count_comment + '$count_comment' where bo_table = '$move_bo_table' ");

        delete_cache_latest($move_bo_table);
    }

    $save_count_write += $count_write;
    $save_count_comment += $count_comment;
}

delete_cache_latest($bo_table);

if ($sw == 'move')
{
    for ($i=0; $i<count($save); $i++)
    {
        for ($k=0; $k<count($save[$i]['bf_file']); $k++)
            @unlink($save[$i]['bf_file'][$k]);

        sql_query(" delete from $write_table where wr_parent = '{$save[$i]['wr_id']}' ");
        sql_query(" delete from {$gml['board_new_table']} where bo_table = '$bo_table' and wr_id = '{$save[$i]['wr_id']}' ");
        sql_query(" delete from {$gml['board_file_table']} where bo_table = '$bo_table' and wr_id = '{$save[$i]['wr_id']}' ");
    }
    sql_query(" update {$gml['board_table']} set bo_count_write = bo_count_write - '$save_count_write', bo_count_comment = bo_count_comment - '$save_count_comment' where bo_table = '$bo_table' ");
}

$msg = sprintf(__('Has %s this post to the selected bulletin board.'), $act);
$opener_href  = get_pretty_url($bo_table,'','&amp;page='.$page.'&amp;'.$qstr);
$opener_href1 = str_replace('&amp;', '&', $opener_href);
$back_msg = __('Back');

start_event('bbs_move_update', $bo_table, $chk_bo_table, $wr_id_list, $opener_href);

echo <<<HEREDOC
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<script>
alert("$msg");
opener.document.location.href = "$opener_href1";
window.close();
</script>
<noscript>
<p>
    "$msg"
</p>
<a href="$opener_href">$back_msg</a>
</noscript>
HEREDOC;
?>
