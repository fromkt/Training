<?php
$sub_menu = "300200";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$gr = get_group($gr_id);
if (!$gr['gr_id']) {
    alert(__('This group does not exist.'));
}

$sql_common = " from {$gml['group_member_table']} a
                         left outer join {$gml['member_table']} b on (a.mb_id = b.mb_id) ";

$sql_search = " where gr_id = '{$gr_id}' ";
// 회원아이디로 검색되지 않던 오류를 수정
if (isset($stx) && $stx) {
    $sql_search .= " and ( ";
    switch ($sfl) {
        default :
            $sql_search .= " ($sfl like '%$stx%') ";
            break;
    }
    $sql_search .= " ) ";
}

if (!$sst) {
    $sst  = "gml_datetime";
    $sod = "desc";
}
$sql_order = " order by {$sst} {$sod} ";

$sql = " select count(*) as cnt
            {$sql_common}
            {$sql_search}
            {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select *
            {$sql_common}
            {$sql_search}
            {$sql_order}
            limit {$from_record}, {$rows} ";
$result = sql_query($sql);

$gml['title'] = sprintf(__('%s group accessible members (group ID : %s)'), $gr['gr_subject'], $gr['gr_id']);
include_once('./admin.head.php');

$colspan = 7;
?>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
<input type="hidden" name="gr_id" value="<?php echo $gr_id ?>">
<label for="sfl" class="sound_only"><?php e__('Search target'); ?></label>
<select name="sfl" id="sfl">
    <option value="a.mb_id"<?php echo get_selected($_GET['sfl'], "a.mb_id") ?>><?php e__('Member ID'); ?></option>
</select>
<label for="stx" class="sound_only"><?php e__('Search terms'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="required frm_input">
<input type="submit" value="<?php e__('Search'); ?>" class="btn_submit">
</form>

<form name="fboardgroupmember" id="fboardgroupmember" action="./boardgroupmember_update.php" onsubmit="return fboardgroupmember_submit(this);" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="<?php echo $token ?>">
<input type="hidden" name="gr_id" value="<?php echo $gr_id ?>">
<input type="hidden" name="w" value="ld">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $gml['title']; ?> <?php e__('List'); ?></caption>
    <thead>
    <tr>
        <th scope="col">
            <label for="chkall" class="sound_only"><?php e__('All accessible members'); ?></label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col"><?php e__('Group'); ?></th>
        <th scope="col"><?php echo subject_sort_link('b.mb_id', 'gr_id='.$gr_id) ?><?php e__('Member ID'); ?></a></th>
        <th scope="col"><?php echo subject_sort_link('b.mb_name', 'gr_id='.$gr_id) ?><?php e__('Name'); ?></a></th>
        <th scope="col"><?php echo subject_sort_link('b.mb_nick', 'gr_id='.$gr_id) ?><?php e__('Nickname'); ?></a></th>
        <th scope="col"><?php echo subject_sort_link('b.mb_today_login', 'gr_id='.$gr_id) ?><?php ep__('Last login', 'Last Login Date'); ?></a></th>
        <th scope="col"><?php echo subject_sort_link('a.gml_datetime', 'gr_id='.$gr_id) ?><?php e__('Processing date'); ?></a></th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
        // 접근가능한 그룹수
        $sql2 = " select count(*) as cnt from {$gml['group_member_table']} where mb_id = '{$row['mb_id']}' ";
        $row2 = sql_fetch($sql2);
        $group = "";
        if ($row2['cnt'])
            $group = '<a href="./boardgroupmember_form.php?mb_id='.$row['mb_id'].'">'.$row2['cnt'].'</a>';

        $mb_nick = get_sideview($row['mb_id'], $row['mb_nick'], $row['mb_email'], $row['mb_homepage']);

        $bg = 'bg'.($i%2);
    ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_chk">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo $row['mb_nick'] ?> <?php e__('Member'); ?></label>
            <input type="checkbox" name="chk[]" value="<?php echo $row['gml_id'] ?>" id="chk_<?php echo $i ?>">
        </td>
        <td class="td_grid"><?php echo $group ?></td>
        <td class="td_mbid"><?php echo $row['mb_id'] ?></td>
        <td class="td_mbname"><?php echo get_text($row['mb_name']); ?></td>
        <td class="td_name sv_use"><?php echo $mb_nick ?></td>
        <td class="td_datetime"><?php echo substr($row['mb_today_login'],2,8) ?></td>
        <td class="td_datetime"><?php echo $row['gml_datetime'] ?></td>
    </tr>
    <?php
    }

    if ($i == 0)
    {
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">'.__('No Data').'</td></tr>';
    }
    ?>
    </tbody>
    </table>
</div>

<div class="btn_list01 btn_list">
    <input type="submit" name="" value="<?php e__('Delete Selection'); ?>">
</div>
</form>

<?php echo get_paging(GML_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;gr_id=$gr_id&page="); ?>

<?php
get_localize_script('boardgroupmember_list',
array(
'check_msg'=>__('Select at least one item to delete.'),  // 선택삭제 하실 항목을 하나 이상 선택하세요.
),
true);
?>
<script>
function fboardgroupmember_submit(f)
{
    if (!is_checked("chk[]")) {
        alert(boardgroupmember_list.check_msg);
        return false;
    }

    return true;
}
</script>

<?php
include_once('./admin.tail.php');
?>
