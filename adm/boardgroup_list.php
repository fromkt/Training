<?php
$sub_menu = "300200";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

if (!isset($group['gr_device'])) {
    // 게시판 그룹 사용 필드 추가
    // both : pc, mobile 둘다 사용
    // pc : pc 전용 사용
    // mobile : mobile 전용 사용
    // none : 사용 안함
    sql_query(" ALTER TABLE  `{$gml['board_group_table']}` ADD  `gr_device` ENUM(  'both',  'pc',  'mobile' ) NOT NULL DEFAULT  'both' AFTER  `gr_subject` ", false);
}

$sql_common = " from {$gml['group_table']} ";

$sql_search = " where (1) ";
if ($is_admin != 'super')
    $sql_search .= " and (gr_admin = '{$member['mb_id']}') ";

if ($stx) {
    $sql_search .= " and ( ";
    switch ($sfl) {
        case "gr_id" :
        case "gr_admin" :
            $sql_search .= " ({$sfl} = '{$stx}') ";
            break;
        default :
            $sql_search .= " ({$sfl} like '%{$stx}%') ";
            break;
    }
    $sql_search .= " ) ";
}

if ($sst)
    $sql_order = " order by {$sst} {$sod} ";
else
    $sql_order = " order by gr_id asc ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
$result = sql_query($sql);

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">'.__('All').'</a>';

$gml['title'] = __('Manage bbs Groups');
include_once('./admin.head.php');

$colspan = 10;
?>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt"><?php e__('Total group'); ?></span><span class="ov_num"> <?php echo number_format($total_count) ?> <?php e__('Count'); ?></span></span>
</div>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
<label for="sfl" class="sound_only"><?php e__('Search target'); ?></label>
<select name="sfl" id="sfl">
    <option value="gr_subject"<?php echo get_selected($_GET['sfl'], "gr_subject"); ?>><?php e__('Title'); ?></option>
    <option value="gr_id"<?php echo get_selected($_GET['sfl'], "gr_id"); ?>>ID</option>
    <option value="gr_admin"<?php echo get_selected($_GET['sfl'], "gr_admin"); ?>><?php e__('Group Admin'); ?></option>
</select>
<label for="stx" class="sound_only"><?php e__('Search term'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label>
<input type="text" name="stx" id="stx" value="<?php echo $stx ?>" required class="required frm_input">
<input type="submit" value="<?php e__('Search'); ?>" class="btn_submit">
</form>


<form name="fboardgrouplist" id="fboardgrouplist" action="./boardgroup_list_update.php" onsubmit="return fboardgrouplist_submit(this);" method="post">
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
            <label for="chkall" class="sound_only"><?php e__('Group All'); ?></label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col"><?php echo subject_sort_link('gr_id') ?><?php e__('Group ID'); ?></a></th>
        <th scope="col"><?php echo subject_sort_link('gr_subject') ?><?php e__('Subject'); ?></a></th>
        <th scope="col"><?php echo subject_sort_link('gr_admin') ?><?php e__('Group Admin'); ?></a></th>
        <th scope="col"><?php e__('Board'); ?></th>
        <th scope="col"><?php ep__('Access Usage', 'Only permitted members are allowed to use.'); ?></th>
        <th scope="col"><?php e__('Access members'); ?></th>
        <th scope="col"><?php echo subject_sort_link('gr_order') ?><?php e__('Output sort'); ?></a></th>
        <th scope="col"><?php e__('Browser_device'); ?></th>
        <th scope="col"><?php e__('Edit'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
        // 접근회원수
        $sql1 = " select count(*) as cnt from {$gml['group_member_table']} where gr_id = '{$row['gr_id']}' ";
        $row1 = sql_fetch($sql1);

        // 게시판수
        $sql2 = " select count(*) as cnt from {$gml['board_table']} where gr_id = '{$row['gr_id']}' ";
        $row2 = sql_fetch($sql2);

        $s_upd = '<a href="./boardgroup_form.php?'.$qstr.'&amp;w=u&amp;gr_id='.$row['gr_id'].'" class="btn_03">'.__('Edit').'</a>';

        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>">
        <td class="td_chk">
            <input type="hidden" name="group_id[<?php echo $i ?>]" value="<?php echo $row['gr_id'] ?>">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo $row['gr_subject'] ?> <?php e__('Group'); ?></label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
        </td>
        <td class="td_left"><a href="<?php echo get_pretty_url('group', 'gr_id='.$row['gr_id']) ?>"><?php echo $row['gr_id'] ?></a></td>
        <td class="td_input">
            <label for="gr_subject_<?php echo $i; ?>" class="sound_only"><?php e__('Group Subject'); ?></label>
            <input type="text" name="gr_subject[<?php echo $i ?>]" value="<?php echo get_text($row['gr_subject']) ?>" id="gr_subject_<?php echo $i ?>" class="tbl_input">
        </td>
        <td class="td_mng td_input">
        <?php if ($is_admin == 'super'){ ?>
            <label for="gr_admin_<?php echo $i; ?>" class="sound_only"><?php e__('Group Admin'); ?></label>
            <input type="text" name="gr_admin[<?php echo $i ?>]" value="<?php echo $row['gr_admin'] ?>" id="gr_admin_<?php echo $i ?>" class="tbl_input" size="10" maxlength="20">
        <?php }else{ ?>
            <input type="hidden" name="gr_admin[<?php echo $i ?>]" value="<?php echo $row['gr_admin'] ?>"><?php echo $row['gr_admin'] ?>
        <?php } ?>
        </td>
        <td class="td_num"><a href="./board_list.php?sfl=a.gr_id&amp;stx=<?php echo $row['gr_id'] ?>"><?php echo $row2['cnt'] ?></a></td>
        <td class="td_numsmall">
             <label for="gr_use_access_<?php echo $i; ?>" class="sound_only"><?php e__('Access Member Usage'); ?></label>
            <input type="checkbox" name="gr_use_access[<?php echo $i ?>]" <?php echo $row['gr_use_access']?'checked':'' ?> value="1" id="gr_use_access_<?php echo $i ?>">
        </td>
        <td class="td_num"><a href="./boardgroupmember_list.php?gr_id=<?php echo $row['gr_id'] ?>"><?php echo $row1['cnt'] ?></a></td>
        <td class="td_numsmall">
            <label for="gr_order_<?php echo $i; ?>" class="sound_only"><?php e__('Main Menu Output Order'); ?></label>
            <input type="text" name="gr_order[<?php echo $i ?>]" value="<?php echo $row['gr_order'] ?>" id="gr_order_<?php echo $i ?>" class="tbl_input" size="2">
        </td>
        <td class="td_mng">
            <label for="gr_device_<?php echo $i; ?>" class="sound_only"><?php e__('Browser_device'); ?></label>
            <select name="gr_device[<?php echo $i ?>]" id="gr_device_<?php echo $i ?>">
                <option value="both"<?php echo get_selected($row['gr_device'], 'both'); ?>><?php e__('All'); ?></option>
                <option value="pc"<?php echo get_selected($row['gr_device'], 'pc'); ?>><?php e__('PC'); ?></option>
                <option value="mobile"<?php echo get_selected($row['gr_device'], 'mobile'); ?>><?php e__('MOBILE'); ?></option>
            </select>
        </td>
        <td class="td_mng td_mng_s"><?php echo $s_upd; ?></td>
    </tr>

    <?php
        }
    if ($i == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">'.__('No Data').'</td></tr>';
    ?>
    </table>
</div>

<div class="btn_fixed_top">
    <button type="submit" name="act_button" onclick="document.pressed=this.title" title="<?php e__('Modify Selection'); ?>" value="modify_selection" class="btn btn_02"><?php e__('Modify Selection'); ?></button>
    <button type="submit" name="act_button" onclick="document.pressed=this.title" title="<?php e__('Delete Selection'); ?>" value="delete_selection" class="btn btn_02"><?php e__('Delete Selection'); ?></button>
    <a href="./boardgroup_form.php" class="btn btn_01"><?php e__('Add Group'); ?></a>
</div>
</form>

<div class="local_desc01 local_desc">
    <p>
        <?php e__('If you set the option to use access, only members designated by the administrator can access the group.'); ?><br>
        <?php e__('The Enable Access option applies to all bulletins in the group.'); ?>
    </p>
</div>

<?php
$pagelist = get_paging(GML_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'].'?'.$qstr.'&amp;page=');
echo $pagelist;
?>

<?php
get_localize_script('fboardgrouplist',
array(
'check_msg'=>__('Please select at least one item to %s.'),  // %s 하실 항목을 하나 이상 선택하세요.
'delete_msg'=>__('Are you sure you want to delete it?'),    // 정말 삭제하시겠습니까?
'delete_pressed' => __('Delete selected'),  //선택삭제
),
true);
?>
<script>
function fboardgrouplist_submit(f)
{
    if (!is_checked("chk[]")) {
        alert( js_sprintf(fboardgrouplist.check_msg, document.pressed) );
        return false;
    }

    if(document.pressed == fboardgrouplist.delete_pressed ) {
        if(!confirm( fboardgrouplist.delete_msg )) {
            return false;
        }
    }

    return true;
}
</script>

<?php
include_once('./admin.tail.php');
?>
