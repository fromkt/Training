<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

// Run only when you are a Super admin
if($config['cf_admin'] != $member['mb_id'] || $is_admin != 'super')
    return;

// Comparison of execution days
if(isset($config['cf_optimize_date']) && $config['cf_optimize_date'] >= GML_TIME_YMD)
    return;

// Delete user logs older than the set date
if($config['cf_visit_del'] > 0) {
    $tmp_before_date = date("Y-m-d", GML_SERVER_TIME - ($config['cf_visit_del'] * 86400));
    $sql = " delete from {$gml['visit_table']} where vi_date < '$tmp_before_date' ";
    sql_query($sql);
    sql_query(" OPTIMIZE TABLE `{$gml['visit_table']}`, `{$gml['visit_sum_table']}` ");
}

// Delete popular search terms that are older than the set date
if($config['cf_popular_del'] > 0) {
    $tmp_before_date = date("Y-m-d", GML_SERVER_TIME - ($config['cf_popular_del'] * 86400));
    $sql = " delete from {$gml['popular_table']} where pp_date < '$tmp_before_date' ";
    sql_query($sql);
    sql_query(" OPTIMIZE TABLE `{$gml['popular_table']}` ");
}

// Delete recent post past setting date
if($config['cf_new_del'] > 0) {
    $sql = " delete from {$gml['board_new_table']} where (TO_DAYS('".GML_TIME_YMDHIS."') - TO_DAYS(bn_datetime)) > '{$config['cf_new_del']}' ";
    sql_query($sql);
    sql_query(" OPTIMIZE TABLE `{$gml['board_new_table']}` ");
}

// Delete a note past the set date
if($config['cf_memo_del'] > 0) {
    $sql = " delete from {$gml['memo_table']} where (TO_DAYS('".GML_TIME_YMDHIS."') - TO_DAYS(me_send_datetime)) > '{$config['cf_memo_del']}' ";
    sql_query($sql);
    sql_query(" OPTIMIZE TABLE `{$gml['memo_table']}` ");
}

// Automatically delete withdrawal members
if($config['cf_leave_day'] > 0) {
    $sql = " select mb_id from {$gml['member_table']}
                where (TO_DAYS('".GML_TIME_YMDHIS."') - TO_DAYS(mb_leave_date)) > '{$config['cf_leave_day']}'
                  and mb_memo not regexp '^[0-9]{8}.*삭제함' ";
    $result = sql_query($sql);
    while ($row=sql_fetch_array($result))
    {
        // Delete Member Data
        member_delete($row['mb_id']);
    }
}

//Delete Voice Capta File
$captcha_mp3 = glob(GML_PATH.'/data/cache/kcaptcha-*.mp3');
if($captcha_mp3 && is_array($captcha_mp3)) {
    foreach ($captcha_mp3 as $file) {
        if (filemtime($file) + 86400 < GML_SERVER_TIME) {
            @unlink($file);
        }
    }
}

// Record execution date
if(isset($config['cf_optimize_date'])) {
    sql_query(" update {$gml['config_table']} set cf_optimize_date = '".GML_TIME_YMD."' ");
}
?>