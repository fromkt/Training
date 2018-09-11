<?php
include_once('./_common.php');

$html_title = __('Link').' &gt; '.conv_subject($write['wr_subject'], 255);

if (!($bo_table && $wr_id && $no))
    alert_close(__('The parameters is wrong.'));

// Prevent SQL Injection
$row = sql_fetch(" select count(*) as cnt from {$gml['write_prefix']}{$bo_table} ", FALSE);
if (!$row['cnt'])
    alert_close(__('This Board does not exist.'));

if (!$write['wr_link'.$no])
    alert_close(__('No link found.'));

$ss_name = 'ss_link_'.$bo_table.'_'.$wr_id.'_'.$no;
if (empty($_SESSION[$ss_name]))
{
    $sql = " update {$gml['write_prefix']}{$bo_table} set wr_link{$no}_hit = wr_link{$no}_hit + 1 where wr_id = '{$wr_id}' ";
    sql_query($sql);

    set_session($ss_name, true);
}

goto_url(set_http($write['wr_link'.$no]));
?>