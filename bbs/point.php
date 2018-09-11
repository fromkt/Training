<?php
include_once('./_common.php');

if ($is_guest)
    alert_close(__('Only members can access it.'));

$gml['title'] = sprintf(__('Point History for %s'), get_text($member['mb_nick']));
include_once(GML_PATH.'/head.sub.php');

$list = array();

$sql_common = " from {$gml['point_table']} where mb_id = '".escape_trim($member['mb_id'])."' ";
$sql_order = " order by po_id desc ";

$sql = " select count(*) as cnt {$sql_common} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$show_total_point = number_format($member['mb_point']);

$sum_point1 = $sum_point2 = 0;

$sql = " select *
            {$sql_common}
            {$sql_order}
            limit {$from_record}, {$rows} ";
$result = sql_query($sql);
$list = array();
for ($i=0; $row=sql_fetch_array($result); $i++) {
    if ($row['po_point'] > 0) {
        $row['show_point'] = '+' .number_format($row['po_point']);
        $sum_point1 += $row['po_point'];
    } else {
        $row['show_point'] = number_format($row['po_point']);
        $row['point_num_class'] = 'point_num_sbt';
        $sum_point2 += $row['po_point'];
    }

    if($row['po_expired'] == 1) {
        $row['expr_class'] = ' txt_expired';
        $row['show_expired_date'] = __('Expiry').': '. substr(str_replace('-', '', $row['po_expire_date']), 2);
    } else {
        $row['show_expired_date'] = ($row['po_expire_date'] == '9999-12-31') ? '&nbsp;' : $row['po_expire_date'];
    }

    $list[] = $row;
}
if ($sum_point1 > 0)
    $sum_point1 = "+" . number_format($sum_point1);
$sum_point2 = number_format($sum_point2);

if ($i == 0)
    $no_list = '<li class="empty_li">'.__('No Data').'</li>';

$point_paging = get_paging(GML_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'].'?'.$qstr.'&amp;page=');

// load language for l10n
bind_lang_domain( 'default', get_path_lang_dir('skin', $member_skin_path.'/'.GML_LANG_DIR) );

include_once($member_skin_path.'/point.skin.php');

include_once(GML_PATH.'/tail.sub.php');
?>
