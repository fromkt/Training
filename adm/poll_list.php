<?php
$sub_menu = "200900";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$sql_common = " from {$gml['poll_table']} ";

$sql_search = " where (1) ";
if ($stx) {
    $sql_search .= " and ( ";
    switch ($sfl) {
        default :
            $sql_search .= " ({$sfl} like '%{$stx}%') ";
            break;
    }
    $sql_search .= " ) ";
}

if (!$sst) {
    $sst  = "po_id";
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

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">'.__('All List').'</a>';

$gml['title'] = __('Poll Manage');
include_once('./admin.head.php');

$colspan = 7;
?>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt"><?php e__('Number of votes'); ?> </span><span class="ov_num"> <?php echo number_format($total_count) ?> <?php e__('Count'); ?></span></span>
</div>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
<div class="sch_last">
    <label for="sfl" class="sound_only"><?php e__('Search target'); ?></label>
    <select name="sfl" id="sfl">
        <option value="po_subject"<?php echo get_selected($_GET['sfl'], "po_subject"); ?>><?php e__('Title'); ?></option>
    </select>
    <label for="stx" class="sound_only"><?php e__('Search term'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label>
    <input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="required frm_input">
    <input type="submit" class="btn_submit" value="<?php e__('Search'); ?>">
</div>
</form>


<form name="fpolllist" id="fpolllist" action="./poll_delete.php" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $gml['title']; ?> <?php e__('List'); ?></caption>
    <thead>
    <tr>
        <th scope="col">
            <label for="chkall" class="sound_only"><?php _('All Current List'); ?></label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col"><?php ep__('Num', 'Number'); ?></th>
        <th scope="col"><?php e__('Title'); ?></th>
        <th scope="col"><?php e__('Voting authority'); ?></th>
        <th scope="col"><?php e__('Number of votes'); ?></th>
        <th scope="col"><?php e__('Other opinions'); ?></th>
        <th scope="col"><?php e__('Edit'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $sql2 = " select sum(po_cnt1+po_cnt2+po_cnt3+po_cnt4+po_cnt5+po_cnt6+po_cnt7+po_cnt8+po_cnt9) as sum_po_cnt from {$gml['poll_table']} where po_id = '{$row['po_id']}' ";
        $row2 = sql_fetch($sql2);
        $po_etc = ($row['po_etc']) ? __('Enable') : __('Disable');

        $s_mod = '<a href="./poll_form.php?'.$qstr.'&amp;w=u&amp;po_id='.$row['po_id'].'" class="btn_03">'.__('Edit').'</a>';

        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>">
        <td class="td_chk">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo cut_str(get_text($row['po_subject']),70) ?> <?php e__('Topic'); ?></label>
            <input type="checkbox" name="chk[]" value="<?php echo $row['po_id'] ?>" id="chk_<?php echo $i ?>">
        </td>
        <td class="td_num"><?php echo $row['po_id'] ?></td>
        <td class="td_left"><?php echo cut_str(get_text($row['po_subject']),70) ?></td>
        <td class="td_num"><?php echo $row['po_level'] ?></td>
        <td class="td_num"><?php echo $row2['sum_po_cnt'] ?></td>
        <td class="td_etc"><?php echo $po_etc ?></td>
        <td class="td_mng td_mng_s"><?php echo $s_mod ?></td>
    </tr>

    <?php
    }

    if ($i==0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">'.__('No Data').'</td></tr>';
    ?>
    </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <input type="submit" value="<?php e__('Delete Selection'); ?>" class="btn btn_02">
    <a href="./poll_form.php" id="poll_add" class="btn btn_01"><?php e__('Add poll'); ?></a>
</div>
</form>

<?php echo get_paging(GML_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>

<?php
get_localize_script('poll_list',
array(
'delete_msg'=>__('Are you sure you want to delete?'),  // 정말 삭제하시겠습니까?
'check_msg'=>__('Select at least one item to delete.'),    // 선택삭제 하실 항목을 하나 이상 선택하세요.
),
true);
?>
<script>
jQuery(function($) {
    $('#fpolllist').submit(function() {
        if(confirm( poll_list.delete_msg )) {
            if (!is_checked("chk[]")) {
                alert( poll_list.check_msg );
                return false;
            }

            return true;
        } else {
            return false;
        }
    });
});
</script>

<?php
include_once ('./admin.tail.php');
?>