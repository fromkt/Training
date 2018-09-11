<?php
include_once('./_common.php');
$count = count($_POST['chk_no_id']);

if(!$count) {
    alert(sprintf(__('Please select at least one item to %s.'), $_POST['btn_submit']));
}

switch ($_POST['btn_submit']) {
    case 'delete_selection':
        foreach($_POST['chk_no_id'] as $no_id) {
            sql_query(" delete from {$gml['notice_table']} where no_id = {$no_id} ");
        }
        break;
    case 'mark_as_read':
        foreach($_POST['chk_no_id'] as $no_id) {
            $sql = " update {$gml['notice_table']}
                            set no_read_datetime = '". GML_TIME_YMDHIS.
                        "' where no_id = {$no_id}";
            sql_query($sql);
        }
        break;
    default:
        alert(__('Please use the correct method.'));
        break;
}

// get unreaded notice count
$sql = " select count(*) as cnt from {$gml['notice_table']} where rel_mb_id = '{$member['mb_id']}' and no_read_datetime = '0000-00-00 00:00:00' ";
$row = sql_fetch($sql);
$unread_cnt = $row['cnt'];

// update notice count to member table
$sql = " update {$gml['member_table']}
            set mb_notice_cnt = {$unread_cnt}
            where mb_id = '{$member['mb_id']}' ";
sql_query($sql);

$qstrs = $page.$qstr;
if($qstrs) {
    $qstrs = "?$qstrs";
}

goto_url(GML_HTTP_BBS_URL.'/notice.php'.$qstrs);
?>
