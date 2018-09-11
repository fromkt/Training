<?php
$sub_menu = "300300";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

// 체크된 자료 삭제
if (isset($_POST['chk']) && is_array($_POST['chk'])) {
    for ($i=0; $i<count($_POST['chk']); $i++) {
        $pp_id = (int) $_POST['chk'][$i];

        sql_query(" delete from {$gml['popular_table']} where pp_id = '$pp_id' ", true);
    }
}

$sql_common = " from {$gml['popular_table']} a ";
$sql_search = " where (1) ";

if ($stx) {
    $sql_search .= " and ( ";
    switch ($sfl) {
        case "pp_word" :
            $sql_search .= " ({$sfl} like '{$stx}%') ";
            break;
        case "pp_date" :
            $sql_search .= " ({$sfl} = '{$stx}') ";
            break;
        default :
            $sql_search .= " ({$sfl} like '%{$stx}%') ";
            break;
    }
    $sql_search .= " ) ";
}

if (!$sst) {
    $sst  = "pp_id";
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
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select *
            {$sql_common}
            {$sql_search}
            {$sql_order}
            limit {$from_record}, {$rows} ";
$result = sql_query($sql);

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">'.__('All list').'</a>';

$gml['title'] = __('Manage top search');
include_once('./admin.head.php');

$colspan = 4;
?>

<script>
var list_update_php = '';
var list_delete_php = 'popular_list.php';
</script>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
     <span class="btn_ov01"><span class="ov_txt"><?php e__('Totals'); ?></span><span class="ov_num"> <?php echo number_format($total_count) ?> <?php e__('Count'); ?></span></span> 

</div>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
<div class="sch_last">
    <label for="sfl" class="sound_only"><?php e__('Search target'); ?></label>
    <select name="sfl" id="sfl">
        <option value="pp_word"<?php echo get_selected($_GET['sfl'], "pp_word"); ?>><?php e__('Search term'); ?></option>
        <option value="pp_date"<?php echo get_selected($_GET['sfl'], "pp_date"); ?>><?php e__('Date'); ?></option>
    </select>
    <label for="stx" class="sound_only"><?php e__('Search term'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label>
    <input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="required frm_input">
    <input type="submit" value="<?php e__('Search'); ?>" class="btn_submit">
</div>
</form>

<form name="fpopularlist" id="fpopularlist" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="<?php echo $token ?>">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $gml['title']; ?> <?php e__('List'); ?></caption>
    <thead>
    <tr>
        <th scope="col">
            <label for="chkall" class="sound_only"><?php e__('All current page popular search keywords'); ?></label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col"><?php echo subject_sort_link('pp_word') ?><?php e__('Search term'); ?></a></th>
        <th scope="col"><?php e__('Date'); ?></th>
        <th scope="col"><?php e__('IP'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {

        $word = get_text($row['pp_word']);
        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>">
        <td class="td_chk">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo $word ?></label>
            <input type="checkbox" name="chk[]" value="<?php echo $row['pp_id'] ?>" id="chk_<?php echo $i ?>">
        </td>
        <td class="td_left"><a href="<?php echo $_SERVER['SCRIPT_NAME'] ?>?sfl=pp_word&amp;stx=<?php echo $word ?>"><?php echo $word ?></a></td>
        <td><?php echo $row['pp_date'] ?></td>
        <td><?php echo $row['pp_ip'] ?></td>
    </tr>

    <?php
    }

    if ($i == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">'.__('No Data').'</td></tr>';
    ?>
    </tbody>
    </table>

</div>

<?php if ($is_admin == 'super'){ ?>
<div class="btn_fixed_top">
    <button type="submit" class="btn btn_02"><?php e__('Delete Selection'); ?></button>
</div>
<?php } ?>

</form>

<?php echo get_paging(GML_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>

<?php
get_localize_script('fpopularlist',
array(
'delete_msg'=>__('Are you sure you want to delete?'),  // 정말 삭제하시겠습니까?
'check_msg'=>__('Select at least one item to delete.'),    // 선택삭제 하실 항목을 하나 이상 선택하세요.
),
true);
?>
<script>
jQuery(function($) {
    $('#fpopularlist').submit(function() {
        if(confirm( fpopularlist.delete_msg )) {
            if (!is_checked("chk[]")) {
                alert( fpopularlist.check_msg );
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
include_once('./admin.tail.php');
?>
