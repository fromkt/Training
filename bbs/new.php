<?php
include_once('./_common.php');

$gml['title'] = __('New Post');
include_once('./_head.php');

$sql_common = " from {$gml['board_new_table']} a, {$gml['board_table']} b, {$gml['group_table']} c where a.bo_table = b.bo_table and b.gr_id = c.gr_id and b.bo_use_search = 1 ";

$gr_id = isset($_GET['gr_id']) ? substr(preg_replace('#[^a-z0-9_]#i', '', $_GET['gr_id']), 0, 10) : '';
if ($gr_id) {
    $sql_common .= " and b.gr_id = '$gr_id' ";
}

$view = isset($_GET['view']) ? $_GET['view'] : "";

if ($view == "w")
    $sql_common .= " and a.wr_id = a.wr_parent ";
else if ($view == "c")
    $sql_common .= " and a.wr_id <> a.wr_parent ";
else
    $view = '';

if(isset($mb_nick) && $mb_nick){    // IF non - member
    $mb_nick = get_search_string(strip_tags($mb_nick));

    $sql = "select mb_id from `{$gml['member_table']}` where mb_nick = '".sql_real_escape_string($mb_nick)."' ";
    $mb = sql_fetch($sql);

    if( $mb['mb_id'] ){
        $mb_hash = get_string_encrypt($mb['mb_id']);
    } else {
        $sql_common .= " and 1 = 0 ";
    }
}

$mb_id = isset($mb_id) ? get_search_string(strip_tags($mb_id)) : '';
$mb_hash = isset($mb_hash) ? $mb_hash : '';
$mb_nick = isset($mb_nick) ? $mb_nick : '';

if ($is_admin && $mb_id) {
    $sql_common .= " and a.mb_id = '".$mb_id."' ";
} else if ($mb_hash){
    $sql_common .= " and a.mb_id = '".get_string_check_decrypt($mb_hash, 'mb_id')."' ";

    if ( ! $mb_nick ){
        $mb = get_member( get_string_check_decrypt($mb_hash, 'mb_id') );
        $mb_nick = $mb['mb_nick'];
    }
}

$sql_order = " order by a.bn_id desc ";

$sql = " select count(*) as cnt {$sql_common} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = GML_IS_MOBILE ? $config['cf_mobile_page_rows'] : $config['cf_new_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$group_select = '<label for="gr_id" class="sound_only">그룹</label><select name="gr_id" id="gr_id"><option value="">'.__('All Group');
$sql = " select gr_id, gr_subject from {$gml['group_table']} order by gr_id ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++) {
    $group_select .= "<option value=\"".$row['gr_id']."\">".$row['gr_subject'];
}
$group_select .= '</select>';

$list = array();
$sql = " select a.*, b.bo_subject, b.bo_mobile_subject, c.gr_subject, c.gr_id {$sql_common} {$sql_order} limit {$from_record}, {$rows} ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++) {
    $tmp_write_table = $gml['write_prefix'].$row['bo_table'];

    if ($row['wr_id'] == $row['wr_parent']) {

        // IS Post
        $comment = "";
        $comment_class = ['if_board', 'fa-list-ul'];
        $comment_link = "";
        $row2 = sql_fetch(" select * from {$tmp_write_table} where wr_id = '{$row['wr_id']}' ");
        $list[$i] = $row2;

        $name = get_sideview($row2['mb_id'], get_text(cut_str($row2['wr_name'], $config['cf_cut_name'])), $row2['wr_email'], $row2['wr_homepage']);
        // 당일인 경우 시간으로 표시함
        $datetime = substr($row2['wr_datetime'],0,10);
        $datetime2 = $row2['wr_datetime'];
        if ($datetime == GML_TIME_YMD) {
            $datetime2 = substr($datetime2,11,5);
        } else {
            $datetime2 = substr($datetime2,5,5);
        }

    } else {

        // IS Comment
        $comment = p__('[C]', 'IS Comment').' ';
        $comment_class = ['if_comment', 'fa-commenting-o'];
        $comment_link = '#c_'.$row['wr_id'];
        $row2 = sql_fetch(" select * from {$tmp_write_table} where wr_id = '{$row['wr_parent']}' ");
        $row3 = sql_fetch(" select mb_id, wr_name, wr_email, wr_homepage, wr_datetime from {$tmp_write_table} where wr_id = '{$row['wr_id']}' ");
        $list[$i] = $row2;
        $list[$i]['wr_id'] = $row['wr_id'];
        $list[$i]['mb_id'] = $row3['mb_id'];
        $list[$i]['wr_name'] = $row3['wr_name'];
        $list[$i]['wr_email'] = $row3['wr_email'];
        $list[$i]['wr_homepage'] = $row3['wr_homepage'];

        $name = get_sideview($row3['mb_id'], get_text(cut_str($row3['wr_name'], $config['cf_cut_name'])), $row3['wr_email'], $row3['wr_homepage']);
        // 당일인 경우 시간으로 표시함
        $datetime = substr($row3['wr_datetime'],0,10);
        $datetime2 = $row3['wr_datetime'];
        if ($datetime == GML_TIME_YMD) {
            $datetime2 = substr($datetime2,11,5);
        } else {
            $datetime2 = substr($datetime2,5,5);
        }

    }

    $list[$i]['gr_id'] = $row['gr_id'];
    $list[$i]['bo_table'] = $row['bo_table'];
    $list[$i]['name'] = $name;
    $list[$i]['comment_class'] = $comment_class;
    $list[$i]['href'] = get_pretty_url($row['bo_table'], $row['wr_parent'], $comment_link);
    $list[$i]['datetime'] = $datetime;
    $list[$i]['datetime2'] = $datetime2;

    $list[$i]['gr_subject'] = $row['gr_subject'];
    $list[$i]['bo_subject'] = ((GML_IS_MOBILE && $row['bo_mobile_subject']) ? get_board_gettext_titles($row['bo_mobile_subject']) : get_board_gettext_titles($row['bo_subject']));
    $list[$i]['wr_subject'] = $row2['wr_subject'];
}

$write_pages = get_paging(GML_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "?gr_id=$gr_id&amp;view=$view&amp;mb_id=$mb_id&amp;mb_hash=$mb_hash&amp;page=");

// load language for l10n
bind_lang_domain( 'default', get_path_lang_dir('skin', $new_skin_path.'/'.GML_LANG_DIR) );

include_once($new_skin_path.'/new.skin.php');

include_once('./_tail.php');
?>
