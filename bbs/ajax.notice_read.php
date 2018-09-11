<?php
include_once('./_common.php');

$sql = " update {$gml['notice_table']}
                set no_read_datetime = '". GML_TIME_YMDHIS.
            "' where no_id = {$_POST['chk_no_id']}";
sql_query($sql);

// view unreaded notice count
$sql = " select count(*) as cnt from {$gml['notice_table']} where rel_mb_id = '{$member['mb_id']}' and no_read_datetime = '0000-00-00 00:00:00' ";
$row = sql_fetch($sql);
$unread_cnt = $row['cnt'];

// update notifications count in the member table
$sql = " update {$gml['member_table']}
            set mb_notice_cnt = {$unread_cnt}
            where mb_id = '{$member['mb_id']}' ";
sql_query($sql);

?>
