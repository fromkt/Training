<?php
include_once('./_common.php');

include_once(GML_PATH.'/head.sub.php');

if (!$is_member)
{
    $href = './login.php?'.$qstr.'&amp;url='.urlencode(get_pretty_url($bo_table, $wr_id));
    echo '<script> alert(\''.__('Only members can access it.').'\'); top.location.href = \''.str_replace('&amp;', '&', $href).'\'; </script>';
    exit;
}

// 게시글 존재하는지
if(!$write['wr_id'])
    alert_close(__('The post you are trying to scrap does not exist.'));

$sql = " select count(*) as cnt from {$gml['scrap_table']}
            where mb_id = '{$member['mb_id']}'
            and bo_table = '$bo_table'
            and wr_id = '$wr_id' ";
$row = sql_fetch($sql);
if ($row['cnt'])
{
    echo '
    <script>
    if (confirm(\''.__('You already been Scrap.')."\n\n".__('Do you want to confirm the scrap now?').'\'))
        document.location.href = \'./scrap.php\';
    else
        window.close();
    </script>
    <noscript>
    <p>'.__('You already been Scrap.').'</p>
    <a href="./scrap.php">'.__('Confirm Scrap').'</a>
    <a href="'.get_pretty_url($bo_table, $wr_id).'">'.__('Back').'</a>
    </noscript>';
    exit;
}

$wr_content = trim($_POST['wr_content']);

// 덧글이 넘어오고 코멘트를 쓸 권한이 있다면
if ($wr_content && ($member['mb_level'] >= $board['bo_comment_level']))
{
    $wr = get_write($write_table, $wr_id);
    // 원글이 존재한다면
    if ($wr['wr_id'])
    {

        // 세션의 시간 검사
        // 4.00.15 - 댓글 수정시 연속 게시물 등록 메시지로 인한 오류 수정
        if ($w == 'c' && $_SESSION['ss_datetime'] >= (GML_SERVER_TIME - $config['cf_delay_sec']) && !$is_admin)
            alert(__('The post can not be writed in succession too soon.'));

        set_session('ss_datetime', GML_SERVER_TIME);

        $mb_id = $member['mb_id'];
        $wr_name = addslashes(clean_xss_tags($board['bo_use_name'] ? $member['mb_name'] : $member['mb_nick']));
        $wr_password = $member['mb_password'];
        $wr_email = addslashes($member['mb_email']);
        $wr_homepage = addslashes(clean_xss_tags($member['mb_homepage']));

        $sql = " select max(wr_comment) as max_comment from $write_table
                    where wr_parent = '$wr_id' and wr_is_comment = '1' ";
        $row = sql_fetch($sql);
        $row['max_comment'] += 1;

        $sql = " insert into $write_table
                    set ca_name = '{$wr['ca_name']}',
                         wr_option = '',
                         wr_num = '{$wr['wr_num']}',
                         wr_reply = '',
                         wr_parent = '$wr_id',
                         wr_is_comment = '1',
                         wr_comment = '{$row['max_comment']}',
                         wr_content = '$wr_content',
                         mb_id = '$mb_id',
                         wr_password = '$wr_password',
                         wr_name = '$wr_name',
                         wr_email = '$wr_email',
                         wr_homepage = '$wr_homepage',
                         wr_datetime = '".GML_TIME_YMDHIS."',
                         wr_ip = '{$_SERVER['REMOTE_ADDR']}' ";
        sql_query($sql);

        $comment_id = sql_insert_id();

        // 원글에 코멘트수 증가
        sql_query(" update $write_table set wr_comment = wr_comment + 1 where wr_id = '$wr_id' ");

        // 새글 INSERT
        sql_query(" insert into {$gml['board_new_table']} ( bo_table, wr_id, wr_parent, bn_datetime, mb_id ) values ( '$bo_table', '$comment_id', '$wr_id', '".GML_TIME_YMDHIS."', '{$member['mb_id']}' ) ");

        // 코멘트 1 증가
        sql_query(" update {$gml['board_table']}  set bo_count_comment = bo_count_comment + 1 where bo_table = '$bo_table' ");

        // 포인트 부여
        insert_point($member['mb_id'], $board['bo_comment_point'], "{$board['bo_subject']} {$wr_id}-{$comment_id} ".__('Comment with Scrap'), $bo_table, $comment_id, 'comment');
    }
}

$sql = " insert into {$gml['scrap_table']} ( mb_id, bo_table, wr_id, ms_datetime ) values ( '{$member['mb_id']}', '$bo_table', '$wr_id', '".GML_TIME_YMDHIS."' ) ";
sql_query($sql);

$sql = " update `{$gml['member_table']}` set mb_scrap_cnt = '".get_scrap_totals($member['mb_id'])."' where mb_id = '{$member['mb_id']}' ";
sql_query($sql);

delete_cache_latest($bo_table);

echo '
<script>
    if (confirm("'.__('Scraped this post.').'\\n\\n'.__('Do you want to confirm the scrap now?').'"))
        document.location.href = "./scrap.php";
    else
        window.close();
</script>
<noscript>
<p>'.__('Scraped this post.').'</p>
<a href="./scrap.php">'.__('Confirm Scrap').'</a>
</noscript>
';
?>