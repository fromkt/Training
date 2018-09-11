<?php
include_once('./_common.php');

// create notice table
if(!sql_query(" DESC {$gml['notice_table']} ", false)) {
    sql_query(" CREATE TABLE IF NOT EXISTS `{$gml['notice_table']}` (
        `no_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `no_case` varchar(50) NOT NULL DEFAULT '',
        `mb_id` varchar(20) NOT NULL DEFAULT '0',
        `rel_mb_id` varchar(20) NOT NULL DEFAULT '0',
        `bo_table` varchar(20) NOT NULL DEFAULT '',
        `wr_id` int(11) NOT NULL DEFAULT '0',
        `rel_wr_id` int(11) NOT NULL DEFAULT '0',
        `no_notice_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
        `no_read_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY (`no_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8; ", false);
}

// add a column of notice retention period to the config table
if(!isset($config['cf_notice_del'])) {
    sql_query(" ALTER TABLE `{$gml['config_table']}`
                ADD `cf_notice_del` int(11) NOT NULL DEFAULT '0' AFTER `cf_popular_del`"
    , true);

    $sql = " update {$gml['config_table']} set cf_notice_del = 60 ";
    sql_query($sql, false);

    $config['cf_notice_del'] = 60;
}

// add a column of notice count to the member table
if(!isset($member['mb_notice_cnt'])) {
    sql_query(" ALTER TABLE `{$gml['member_table']}`
                ADD `mb_notice_cnt` int(11) NOT NULL DEFAULT '0' AFTER `mb_memo_call`
              ", true);
}

$gml['title'] = __('Alarm');

// readed/unreaded notice
$sql_search = "";
if($read == 'y') {
    $sql_search = " and no_read_datetime > '0000-00-00 00:00:00' ";
} else if($read == 'n'){
    $sql_search = " and no_read_datetime = '0000-00-00 00:00:00' ";
}

// total notice count
$sql = " select count(*) as cnt from {$gml['notice_table']} where rel_mb_id = '{$member['mb_id']}' ";
$sql .= $sql_search;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

if ($page < 1) { $page = 1; } // If there is no page, the first page
$page_rows = GML_IS_MOBILE ? $config['cf_mobile_page_rows'] : $config['cf_page_rows'];
$total_page  = ceil($total_count / $page_rows);  // calculate the entire page
$from_record = ($page - 1) * $page_rows;

// Notice list
$sql = " select * from {$gml['notice_table']} where rel_mb_id = '{$member['mb_id']}' ";
$sql_common = " order by no_id desc limit {$from_record}, {$page_rows} ";
$sql .= $sql_search. $sql_common;
$result = sql_query($sql);

$list = array();
for($i=0; $row=sql_fetch_array($result); $i++) {
    $list[$i]['no_id'] = $row['no_id'];

    $is_read = true;
    if(date($row['no_notice_datetime']) - date($row['no_read_datetime']) > 0) {
        $is_read = false;
    }
    if($is_read) {
        $list[$i]['read_class'] = "";
        $list[$i]['status'] = __('Read');
    } else {
        $list[$i]['read_class'] = "read_arm";
        $list[$i]['status'] = __('Unread');
    }
    // create notice message
    $list[$i]['subject'] = get_notice_subject($row);

    // Show notification time as relative time (ex - 2 hours ago)
    $list[$i]['notice_datetime'] = time2str($row['no_notice_datetime']);
}

$write_pages = get_paging(GML_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "?{$_SERVER['QUERY_STRING']}&amp;page=");

// show current tab selection
function notice_btn_on_class($read, $value) {
    if($read == $value) {
        return 'class="arm_btn_on"';
    } else {
        return '';
    }
}

// load language for l10n
bind_lang_domain( 'default', get_path_lang_dir('skin', $member_skin_path.'/'.GML_LANG_DIR) );

include_once('./_head.php');
include_once($member_skin_path. "/notice.skin.php");
include_once('./_tail.php');
?>
