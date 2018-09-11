<?php
include_once('./_common.php');

if (!$is_member)
    alert_close(__('Only members can access it.'));

$gml['title'] = sprintf(__('%s Scrap'), get_text($member['mb_nick']));
include_once(GML_PATH.'/head.sub.php');

$sql_common = " from {$gml['scrap_table']} where mb_id = '{$member['mb_id']}' ";
$sql_order = " order by ms_id desc ";

$sql = " select count(*) as cnt $sql_common ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$list = array();

$sql = " select *
            $sql_common
            $sql_order
            limit $from_record, $rows ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++) {

    $list[$i] = $row;

    // 순차적인 번호 (순번)
    $num = $total_count - ($page - 1) * $rows - $i;

    // 게시판 제목
    $sql2 = " select bo_subject from {$gml['board_table']} where bo_table = '{$row['bo_table']}' ";
    $row2 = sql_fetch($sql2);
    if (!$row2['bo_subject']) $row2['bo_subject'] = __('[No board]');

    // 게시물 제목
    $tmp_write_table = $gml['write_prefix'] . $row['bo_table'];
    $sql3 = " select wr_subject from $tmp_write_table where wr_id = '{$row['wr_id']}' ";
    $row3 = sql_fetch($sql3, FALSE);
    $subject = get_text(cut_str($row3['wr_subject'], 100));
    if (!$row3['wr_subject'])
        $row3['wr_subject'] = __('[No post]');

    $list[$i]['num'] = $num;
    $list[$i]['opener_href'] = get_pretty_url($row['bo_table']);
    $list[$i]['opener_href_wr_id'] = get_pretty_url($row['bo_table'], $row['wr_id']);
    $list[$i]['bo_subject'] = $row2['bo_subject'];
    $list[$i]['subject'] = $subject;
    $list[$i]['del_href'] = './scrap_delete.php?ms_id='.$row['ms_id'].'&amp;page='.$page;
}

// load language for l10n
bind_lang_domain( 'default', get_path_lang_dir('skin', $member_skin_path.'/'.GML_LANG_DIR) );

include_once($member_skin_path.'/scrap.skin.php');

include_once(GML_PATH.'/tail.sub.php');
?>
