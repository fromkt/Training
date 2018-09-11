<?php
include_once('./_common.php');
include_once(GML_CAPTCHA_PATH.'/captcha.lib.php');

$po = get_poll_db($po_id);
if (!$po['po_id'])
    alert(__('No poll found.'));

if ($member['mb_level'] < $po['po_level'])
    alert(sprintf(__('Only members with Level %s or higher can view the result.'), $po['po_level']));

$gml['title'] = __('Poll Results');

$po_subject = $po['po_subject'];

$max = 1;
$total_po_cnt = 0;
for ($i=1; $i<=9; $i++) {
    $poll = $po['po_poll'.$i];
    if ($poll == '') break;

    $count = $po['po_cnt'.$i];
    $total_po_cnt += $count;

    if ($count > $max)
        $max = $count;
}
$nf_total_po_cnt = number_format($total_po_cnt);

$list = array();

for ($i=1; $i<=9; $i++) {
    $poll = $po['po_poll'.$i];
    if ($poll == '') { break; }

    $list[$i]['content'] = $poll;
    $list[$i]['cnt'] = $po['po_cnt'.$i];
    if ($total_po_cnt > 0)
        $list[$i]['rate'] = ($list[$i]['cnt'] / $total_po_cnt) * 100;

    $bar = (int)($list[$i]['cnt'] / $max * 100);

    $list[$i]['bar'] = $bar;
    $list[$i]['num'] = $i;
}

$list2 = array();

// List etc opinions
$sql = " select a.*, b.mb_open
           from {$gml['poll_etc_table']} a
           left join {$gml['member_table']} b on (a.mb_id = b.mb_id)
          where po_id = '{$po_id}' order by pc_id desc ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++) {
    $list2[$i]['pc_name']  = get_text($row['pc_name']);
    $list2[$i]['name']     = get_sideview($row['mb_id'], get_text(cut_str($row['pc_name'],10)), '', '', $row['mb_open']);
    $list2[$i]['idea']     = get_text(cut_str($row['pc_idea'], 255));
    $list2[$i]['datetime'] = $row['pc_datetime'];

    $list2[$i]['del'] = '';
    if ($is_admin == 'super' || ($row['mb_id'] == $member['mb_id'] && $row['mb_id']))
        $list2[$i]['del'] = '<a href="'.GML_BBS_URL.'/poll_etc_update.php?w=d&amp;pc_id='.$row['pc_id'].'&amp;po_id='.$po_id.'&amp;skin_dir='.$skin_dir.'" class="poll_delete">';
}

// 기타의견 입력
$is_etc = false;
if ($po['po_etc']) {
    $is_etc = true;
    $po_etc = $po['po_etc'];
    if ($member['mb_id'])
        $name = '<b>'.$member['mb_nick'].'</b> <input type="hidden" name="pc_name" value="'.$member['mb_nick'].'">';
    else
        $name = '<input type="text" name="pc_name" size="10" class="input" required>';
}

$list3 = array();

// 다른투표
$sql = " select po_id, po_subject, po_date from {$gml['poll_table']} order by po_id desc ";
$result = sql_query($sql);
for ($i=0; $row2=sql_fetch_array($result); $i++) {
    $list3[$i]['po_id'] = $row2['po_id'];
    $list3[$i]['date'] = substr($row2['po_date'],2,8);
    $list3[$i]['subject'] = cut_str($row2['po_subject'],60,"…");
}

if (GML_IS_MOBILE) {
    $poll_skin_path = GML_THEME_MOBILE_PATH.'/'.GML_SKIN_DIR.'/poll/'.$skin_dir;
    if(!is_dir($poll_skin_path))
        $poll_skin_path = GML_THEME_PATH.'/'.GML_SKIN_DIR.'/poll/'.$skin_dir;
    $poll_skin_url = str_replace(GML_PATH, GML_URL, $poll_skin_path);
} else {
    $poll_skin_path = GML_THEME_PATH.'/'.GML_SKIN_DIR.'/poll/'.$skin_dir;
    $poll_skin_url = str_replace(GML_PATH, GML_URL, $poll_skin_path);
}

include_once(GML_PATH.'/head.sub.php');

$show_mb_nick = get_text(cut_str($member['mb_nick'],255));
$captcha_html = captcha_html();

if (!file_exists($poll_skin_path.'/poll_result.skin.php')) die('skin error');

// load language for l10n
bind_lang_domain( 'default', get_path_lang_dir('skin', $poll_skin_path.'/'.GML_LANG_DIR) );

include_once ($poll_skin_path.'/poll_result.skin.php');

include_once(GML_PATH.'/tail.sub.php');
?>
