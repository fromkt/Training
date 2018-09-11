<?php
$sub_menu = "200200";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$sql_common = " from {$gml['point_table']} ";

$sql_search = " where (1) ";

if ($stx) {
    $sql_search .= " and ( ";
    switch ($sfl) {
        case 'mb_id' :
            $sql_search .= " ({$sfl} = '{$stx}') ";
            break;
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

$mb = array();
if ($sfl == 'mb_id' && $stx)
    $mb = get_member($stx);

$gml['title'] = __('Point Manage');
include_once ('./admin.head.php');

$colspan = 9;

$po_expire_term = '';
if($config['cf_point_term'] > 0) {
    $po_expire_term = $config['cf_point_term'];
}

if (strstr($sfl, "mb_id"))
    $mb_id = $stx;
else
    $mb_id = "";
?>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>

        <span class="btn_ov01"><span class="ov_txt"><?php e__('All'); ?> </span><span class="ov_num"> <?php echo number_format($total_count) ?> <?php e__('Count'); ?></span></span>

    <?php
    if (isset($mb['mb_id']) && $mb['mb_id']) {
        echo '&nbsp;<span class="btn_ov01"><span class="ov_txt">' . sprintf(__('Total points of %s'), $mb['mb_id']) .'</span><span class="ov_num"> ' . number_format($mb['mb_point']) .' '.__('Point').'</span></span>';
    } else {
        $row2 = sql_fetch(" select sum(po_point) as sum_point from {$gml['point_table']} ");
        echo '&nbsp;<span class="btn_ov01"><span class="ov_txt">'.__('Total points').'</span><span class="ov_num">'.number_format($row2['sum_point']).' '.__('Point').'</span></span>';
    }
    ?>
</div>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
<label for="sfl" class="sound_only"><?php e__('Search target'); ?></label>
<select name="sfl" id="sfl">
    <option value="mb_id"<?php echo get_selected($_GET['sfl'], "mb_id"); ?>><?php e__('Member ID'); ?></option>
    <option value="po_content"<?php echo get_selected($_GET['sfl'], "po_content"); ?>><?php e__('Content'); ?></option>
</select>
<label for="stx" class="sound_only"><?php e__('Search term'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="required frm_input">
<input type="submit" class="btn_submit" value="<?php e__('Search'); ?>">
</form>

<form name="fpointlist" id="fpointlist" method="post" action="./point_list_delete.php" onsubmit="return fpointlist_submit(this);">
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
        <th scope="col" rowspan="2">
            <label for="chkall" class="sound_only"><?php e__('Total point history'); ?></label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col"><?php echo subject_sort_link('mb_id') ?><?php e__('Member ID'); ?></a></th>
        <th scope="col" class="m_no"><?php e__('Name'); ?></th>
        <th scope="col"><?php echo subject_sort_link('po_point') ?><?php e__('Point'); ?></a></th>
        <th scope="col"><?php echo subject_sort_link('po_datetime') ?><?php e__('Date'); ?></a></th>
    </tr>
    <tr>
        <th scope="col"><?php echo subject_sort_link('po_content') ?><?php e__('History'); ?></a></th>
        <th scope="col" class="m_no"><?php e__('Nickname'); ?></th>
        <th scope="col"><?php e__('Point Sum'); ?></th>
        <th scope="col"><?php e__('Expiration date'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        if ($i==0 || ($row2['mb_id'] != $row['mb_id'])) {
            $sql2 = " select mb_id, mb_name, mb_nick, mb_email, mb_homepage, mb_point from {$gml['member_table']} where mb_id = '{$row['mb_id']}' ";
            $row2 = sql_fetch($sql2);
        }

        $mb_nick = get_sideview($row['mb_id'], $row2['mb_nick'], $row2['mb_email'], $row2['mb_homepage']);

        $link1 = $link2 = '';
        if (!preg_match("/^\@/", $row['po_rel_table']) && $row['po_rel_table']) {
            $link1 = '<a href="'.get_pretty_url($row['po_rel_table'], $row['po_rel_id']).'" target="_blank">';
            $link2 = '</a>';
        }

        $expr = '';
        if($row['po_expired'] == 1)
            $expr = ' txt_expired';

        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>">
        <td class="td_chk" rowspan="2">
            <input type="hidden" name="mb_id[<?php echo $i ?>]" value="<?php echo $row['mb_id'] ?>" id="mb_id_<?php echo $i ?>">
            <input type="hidden" name="po_id[<?php echo $i ?>]" value="<?php echo $row['po_id'] ?>" id="po_id_<?php echo $i ?>">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo $row['po_content'] ?> <?php e__('History'); ?></label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
        </td>
        <td class="td_mbid"><a href="?sfl=mb_id&amp;stx=<?php echo $row['mb_id'] ?>"><?php echo $row['mb_id'] ?></a></td>
        <td class="td_mbname m_no"><?php echo get_text($row2['mb_name']); ?></td>
        <td class="td_num td_pt"><?php echo number_format($row['po_point']) ?></td>
        <td class="td_datetime"><?php echo $row['po_datetime'] ?></td>
    </tr>
    <tr class="<?php echo $bg; ?>">
        <td class="td_pt_log td_left"><?php echo $link1 ?><?php echo $row['po_content'] ?><?php echo $link2 ?></td>
        <td class="td_name sv_use m_no"><div><?php echo $mb_nick ?></div></td>
        <td class="td_num td_pt"><?php echo number_format($row['po_mb_point']) ?></td>
        <td class="td_date<?php echo $expr; ?>">
            <?php if ($row['po_expired'] == 1) { ?>
            <?php e__('expired'); ?><?php echo substr(str_replace('-', '', $row['po_expire_date']), 2); ?>
            <?php } else echo $row['po_expire_date'] == '9999-12-31' ? '&nbsp;' : $row['po_expire_date']; ?>
        </td>
    </tr>
    <?php
    }

    if ($i == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">'.__('No data').'</td></tr>';
    ?>
    </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <input type="submit" name="act_button" value="<?php e__('Delete Selection'); ?>" onclick="document.pressed=this.value" class="btn btn_02">
</div>

</form>

<?php echo get_paging(GML_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>

<section id="point_mng" class="panel">
    <h2 class="h2_frm"><?php e__('Set individual member point fluctuation'); ?></h2>

    <form name="fpointlist2" method="post" id="fpointlist2" action="./point_update.php" autocomplete="off">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="token" value="<?php echo $token ?>">

    <div class="panel_con">
        <ul class="frm_ul">
            <li class="li_50">
                <label for="mb_id" class="lb_block"><?php e__('Member ID'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label>
                <input type="text" name="mb_id" value="<?php echo $mb_id ?>" id="mb_id" class="required frm_input frm_input_full" required>
            </li>
            <li class="li_50 bd_0">
                <label for="po_point" class="lb_block"><?php e__('Point'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label>
                <input type="text" name="po_point" id="po_point" required class="required frm_input frm_input_full">
            </li>
            <li class="li_clear">
                <label for="po_content" class="lb_block"><?php e__('Point Histoty'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label>
                <input type="text" name="po_content" id="po_content" required class="required frm_input frm_input_full" size="80">
            </li>

            <?php if($config['cf_point_term'] > 0) { ?>
            <li>
                <label for="po_expire_term" class="lb_block"><?php e__('Point Expire Term'); ?></label>
                <input type="text" name="po_expire_term" value="<?php echo $po_expire_term; ?>" id="po_expire_term" class="frm_input" size="5"> <?php e__('Day'); ?>
            </li>
            <?php } ?>
        </ul>
    </div>

    <div class="panel_btn">
        <input type="submit" value="<?php e__('Apply'); ?>" class="btn_submit btn">
    </div>

    </form>

</section>

<?php
get_localize_script('fpointlist',
array(
'check_msg'=>__('Please select at least one item to %s.'),  // %s 하실 항목을 하나 이상 선택하세요.
'delete_msg'=>__('Are you sure you want to delete it?'),    // 정말 삭제하시겠습니까?
'delete_pressed' => __('Delete selected'),  //선택삭제
),
true);
?>
<script>
function fpointlist_submit(f)
{
    if (!is_checked("chk[]")) {
        alert( js_sprintf(fpointlist.check_msg, document.pressed) );
        return false;
    }

    if(document.pressed == fpointlist.delete_pressed ) {
        if(!confirm( fpointlist.delete_msg )) {
            return false;
        }
    }

    return true;
}
</script>

<?php
include_once ('./admin.tail.php');
?>
