<?php
include_once('./_common.php');

if ($is_guest)
    alert_close(__('Only members can use it.'));

set_session('ss_memo_delete_token', $token = uniqid(time()));

$gml['title'] = __('My Memo');
include_once(GML_PATH.'/head.sub.php');

$kind = $kind ? clean_xss_tags(strip_tags($kind)) : 'recv';

if ($kind == 'recv'){
    $unkind = 'send';
} else if ($kind == 'send') {
    $unkind = 'recv';
} else {
    alert(sprintf(__('%s value is wrong'), $kind));
}

$sql = " select count(*) as cnt from {$gml['memo_table']} where me_{$kind}_mb_id = '{$member['mb_id']}' and me_type = '$kind' ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$total_page  = ceil($total_count / $config['cf_page_rows']);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ((int) $page - 1) * $config['cf_page_rows']; // 시작 열을 구함

if ($kind == 'recv')
{
    $kind_title = __('Received');
    $recv_img = 'on';
    $send_img = 'off';
}
else
{
    $kind_title = __('Sent');
    $recv_img = 'off';
    $send_img = 'on';
}

$list = array();

$sql = " select a.*, b.mb_id, b.mb_nick, b.mb_email, b.mb_homepage
            from {$gml['memo_table']} a
            left join {$gml['member_table']} b on (a.me_{$unkind}_mb_id = b.mb_id)
            where a.me_{$kind}_mb_id = '{$member['mb_id']}' and a.me_type = '$kind'
            order by a.me_id desc limit $from_record, {$config['cf_page_rows']} ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++)
{
    $list[$i] = $row;

    $mb_id = $row["me_{$unkind}_mb_id"];

    if ($row['mb_nick'])
        $mb_nick = $row['mb_nick'];
    else
        $mb_nick = __('Unknown');

    $name = get_sideview($row['mb_id'], $row['mb_nick'], $row['mb_email'], $row['mb_homepage']);

    if (substr($row['me_read_datetime'],0,1) == 0)
        $read_datetime = __('Not yet read');
    else
        $read_datetime = substr($row['me_read_datetime'],2,14);

    $send_datetime = substr($row['me_send_datetime'],2,14);

    $list[$i]['name'] = $name;
    $list[$i]['send_datetime'] = $send_datetime;
    $list[$i]['read_datetime'] = $read_datetime;
    $list[$i]['is_read'] = (substr($list[$i]['read_datetime'],0,1) == 0);
    $list[$i]['view_href'] = './memo_view.php?me_id='.$row['me_id'].'&amp;kind='.$kind.'&amp;page='.$page;
    $list[$i]['del_href'] = './memo_delete.php?me_id='.$row['me_id'].'&amp;token='.$token.'&amp;kind='.$kind;
}

$write_pages = get_paging(GML_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "./memo.php?kind=$kind".$qstr."&amp;page=");

// load language for l10n
bind_lang_domain( 'default', get_path_lang_dir('skin', $member_skin_path.'/'.GML_LANG_DIR) );

include_once($member_skin_path.'/memo.skin.php');

include_once(GML_PATH.'/tail.sub.php');
?>
