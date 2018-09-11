<?php
include_once('./_common.php');

if (!$is_member)
    alert(__('Only members can access it.'));

$sql = " delete from {$gml['scrap_table']} where mb_id = '{$member['mb_id']}' and ms_id = '$ms_id' ";
sql_query($sql);

$sql = " update `{$gml['member_table']}` set mb_scrap_cnt = '".get_scrap_totals($member['mb_id'])."' where mb_id = '{$member['mb_id']}' ";
sql_query($sql);

goto_url('./scrap.php?page='.$page);
?>
