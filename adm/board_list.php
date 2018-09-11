<?php
$sub_menu = "300100";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$sql_common = " from {$gml['board_table']} a ";
$sql_search = " where (1) ";

if ($is_admin != "super") {
    $sql_common .= " , {$gml['group_table']} b ";
    $sql_search .= " and (a.gr_id = b.gr_id and b.gr_admin = '{$member['mb_id']}') ";
}

if ($stx) {
    $sql_search .= " and ( ";
    switch ($sfl) {
        case "bo_table" :
            $sql_search .= " ($sfl like '$stx%') ";
            break;
        case "a.gr_id" :
            $sql_search .= " ($sfl = '$stx') ";
            break;
        default :
            $sql_search .= " ($sfl like '%$stx%') ";
            break;
    }
    $sql_search .= " ) ";
}

if (!$sst) {
    $sst  = "a.gr_id, a.bo_table";
    $sod = "asc";
}
$sql_order = " order by $sst $sod ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
$result = sql_query($sql);

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">'.__('All List').'</a>';

$gml['title'] = __('Manage Board');
include_once('./admin.head.php');

$colspan = 15;
?>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt"><?php e__('Number of Boards generated'); ?>  </span><span class="ov_num"> <?php echo number_format($total_count) ?> <?php e__('Count'); ?></span></span>
</div>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">

<label for="sfl" class="sound_only"><?php e__('Search target'); ?></label>
<select name="sfl" id="sfl">
    <option value="bo_table"<?php echo get_selected($_GET['sfl'], "bo_table", true); ?>>TABLE</option>
    <option value="bo_subject"<?php echo get_selected($_GET['sfl'], "bo_subject"); ?>><?php e__('Title'); ?></option>
    <option value="a.gr_id"<?php echo get_selected($_GET['sfl'], "a.gr_id"); ?>><?php e__('Group ID'); ?></option>
</select>
<label for="stx" class="sound_only"><?php e__('Search term'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="required frm_input">
<input type="submit" value="<?php e__('Search'); ?>" class="btn_submit">

</form>


<form name="fboardlist" id="fboardlist" action="./board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="<?php echo $token ?>">

<div class="tbl_head01 tbl_head0 tbl_wrap">
    <table>
    <caption><?php echo $gml['title']; ?> <?php e__('List'); ?></caption>
    <thead>
    <tr>
        <th scope="col" rowspan="2">
            <label for="chkall" class="sound_only"><?php e__('All Boards'); ?></label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col" colspan="2"><?php echo subject_sort_link('bo_subject') ?><?php e__('Subject'); ?></a></th>
        <th scope="col"><?php echo subject_sort_link('bo_skin', '', 'desc') ?><?php e__('Skin'); ?></a></th>
        <th scope="col"><?php ep__('Read P', 'Read Point'); ?><span class="sound_only"><?php e__('Point'); ?></span></th>
        <th scope="col"><?php ep__('Comment P', 'Comments Point'); ?><span class="sound_only"><?php e__('Point'); ?></span></th>
        <th scope="col"><?php echo subject_sort_link('bo_use_sns') ?><?php e__('Use SNS'); ?></a></th>
        <th scope="col"><?php echo subject_sort_link('bo_order') ?><?php e__('Output sort'); ?></a></th>
        <th scope="col" rowspan="2"><?php e__('Edit'); ?></th>
    </tr>
    <tr>
        <th scope="col"><?php echo subject_sort_link('a.gr_id') ?><?php e__('Group'); ?></a></th>
        <th scope="col"><?php echo subject_sort_link('bo_table') ?>TABLE</a></th>
        <th scope="col"><?php echo subject_sort_link('bo_mobile_skin', '', 'desc') ?><?php e__('Mobile Skin'); ?></span></a></th>
        <th scope="col"><?php ep__('Write P', 'Write Point'); ?><span class="sound_only"><?php e__('Point'); ?></span></th>
        <th scope="col"><?php ep__('Down P', 'Download Point'); ?><span class="sound_only"><?php e__('Point'); ?></span></th>
        <th scope="col"><?php echo subject_sort_link('bo_use_search') ?><?php e__('Use search'); ?></a></th>
        <th scope="col"><?php e__('Browser_device'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $one_update = '<a href="./board_form.php?w=u&amp;bo_table='.$row['bo_table'].'&amp;'.$qstr.'" class="btn_03">'.__('Edit').'</a>';
        $one_copy = '<a href="./board_copy.php?bo_table='.$row['bo_table'].'" class="board_copy btn_04" target="win_board_copy">'.__('Copy').'</a>';

        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>">

        <td class="td_chk" rowspan="2">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['bo_subject']) ?></label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
        </td>
        <td colspan="2">
            <label for="bo_subject_<?php echo $i; ?>" class="sound_only"><?php e__('Board subject'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label>
            <input type="text" name="bo_subject[<?php echo $i ?>]" value="<?php echo get_text($row['bo_subject']) ?>" id="bo_subject_<?php echo $i ?>" required class="required frm_input bo_subject full_input" size="10">
        </td>

        <td class="td_select">
            <label for="bo_skin_<?php echo $i; ?>" class="sound_only"><?php e__('Skin'); ?></label>
            <?php echo get_skin_select('board', 'bo_skin_'.$i, "bo_skin[$i]", $row['bo_skin']); ?>
        </td>


        <td class="td_numsmall">
            <label for="bo_read_point_<?php echo $i; ?>" class="sound_only"><?php e__('Read Point'); ?></label>
            <input type="text" name="bo_read_point[<?php echo $i ?>]" value="<?php echo $row['bo_read_point'] ?>" id="bo_read_point_<?php echo $i; ?>" class="frm_input" size="2">
        </td>

        <td class="td_numsmall">
            <label for="bo_comment_point_<?php echo $i; ?>" class="sound_only"><?php e__('Comment Point'); ?></label>
            <input type="text" name="bo_comment_point[<?php echo $i ?>]" value="<?php echo $row['bo_comment_point'] ?>" id="bo_comment_point_<?php echo $i; ?>" class="frm_input" size="2">
        </td>

        <td class="td_chk">
            <label for="bo_use_sns_<?php echo $i; ?>" class="sound_only"><?php e__('Use SNS'); ?></label>
            <input type="checkbox" name="bo_use_sns[<?php echo $i ?>]" value="1" id="bo_use_sns_<?php echo $i ?>" <?php echo $row['bo_use_sns']?"checked":"" ?>>
        </td>

        <td class="td_chk">
            <label for="bo_order_<?php echo $i; ?>" class="sound_only"><?php e__('Output sort'); ?></label>
            <input type="text" name="bo_order[<?php echo $i ?>]" value="<?php echo $row['bo_order'] ?>" id="bo_order_<?php echo $i ?>" class="frm_input" size="2">
        </td>

        <td class="td_mng td_mng_s" rowspan="2">
            <?php echo $one_update ?><br>
            <?php echo $one_copy ?>
        </td>
    </tr>



    <tr class="<?php echo $bg; ?>">
        <td>
            <?php if ($is_admin == 'super'){ ?>
                <?php echo get_group_select("gr_ids[$i]", $row['gr_id']) ?>
            <?php }else{ ?>
                <input type="hidden" name="gr_ids[<?php echo $i ?>]" value="<?php echo $row['gr_id'] ?>"><?php echo $row['gr_subject'] ?>
            <?php } ?>
        </td>

        <td>
            <input type="hidden" name="board_table[<?php echo $i ?>]" value="<?php echo $row['bo_table'] ?>">
            <a href="<?php echo get_pretty_url($row['bo_table']) ?>"><?php echo $row['bo_table'] ?></a>
        </td>

        <td>
            <label for="bo_mobile_skin_<?php echo $i; ?>" class="sound_only"><?php e__('Mobile Skin'); ?></label>
            <?php echo get_mobile_skin_select('board', 'bo_mobile_skin_'.$i, "bo_mobile_skin[$i]", $row['bo_mobile_skin']); ?>
        </td>


        <td class="td_numsmall">
            <label for="bo_write_point_<?php echo $i; ?>" class="sound_only"><?php e__('Point Write'); ?></label>
            <input type="text" name="bo_write_point[<?php echo $i ?>]" value="<?php echo $row['bo_write_point'] ?>" id="bo_write_point_<?php echo $i; ?>" class="frm_input" size="2">
        </td>

        <td class="td_numsmall">
            <label for="bo_download_point_<?php echo $i; ?>" class="sound_only"><?php e__('Point Download'); ?></label>
            <input type="text" name="bo_download_point[<?php echo $i ?>]" value="<?php echo $row['bo_download_point'] ?>" id="bo_download_point_<?php echo $i; ?>" class="frm_input" size="2">
        </td>

        <td class="td_chk">
            <label for="bo_use_search_<?php echo $i; ?>" class="sound_only"><?php e__('Use search'); ?></label>
            <input type="checkbox" name="bo_use_search[<?php echo $i ?>]" value="1" id="bo_use_search_<?php echo $i ?>" <?php echo $row['bo_use_search']?"checked":"" ?>>
        </td>

        <td class="td_mngsmall">
            <label for="bo_device_<?php echo $i; ?>" class="sound_only"><?php e__('Browser_device'); ?></label>
            <select name="bo_device[<?php echo $i ?>]" id="bo_device_<?php echo $i ?>">
                <option value="both"<?php echo get_selected($row['bo_device'], 'both', true); ?>><?php e__('All'); ?></option>
                <option value="pc"<?php echo get_selected($row['bo_device'], 'pc'); ?>><?php e__('PC'); ?></option>
                <option value="mobile"<?php echo get_selected($row['bo_device'], 'mobile'); ?>><?php e__('MOBILE'); ?></option>
            </select>
        </td>

    </tr>
    <?php
    }
    if ($i == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">'.__('No Data').'</td></tr>';
    ?>
    </tbody>
    </table>
</div>


<div class="btn_fixed_top">
    <button type="submit" name="act_button" value="modify_selection" title="<?php e__('Modify Selection'); ?>" onclick="document.pressed=this.title" class="btn btn_02"><?php e__('Modify Selection'); ?></button>
    <?php if ($is_admin == 'super') { ?>
    <button type="submit" name="act_button" value="delete_selection" title="<?php e__('Delete Selection'); ?>" onclick="document.pressed=this.title" class="btn btn_02"><?php e__('Delete Selection'); ?></button>
    <?php } ?>
    <?php if ($is_admin == 'super') { ?>
    <a href="./board_form.php" id="bo_add" class="btn btn_01"><?php e__('Add Board'); ?></a>
    <?php } ?>
</div>

</form>

<?php echo get_paging(GML_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'].'?'.$qstr.'&amp;page='); ?>

<?php
get_localize_script('board_list',
array(
'check_msg'=>__('Please select at least one item to %s.'),  // %s 하실 항목을 하나 이상 선택하세요.
'delete_msg'=>__('Are you sure you want to delete it?'),    // 정말 삭제하시겠습니까?
'delete_pressed' => __('Delete selected'),  //선택삭제
),
true);
?>
<script>
function fboardlist_submit(f)
{
    if (!is_checked("chk[]")) {
        alert( js_sprintf(board_list.check_msg, document.pressed) );
        return false;
    }

    if(document.pressed == board_list.delete_pressed ) {
        if(!confirm( board_list.delete_msg )) {
            return false;
        }
    }

    return true;
}

jQuery(function($){
    $(".board_copy").click(function(){
        window.open(this.href, "win_board_copy", "left=100,top=100,width=400,height=400");
        return false;
    });
});
</script>

<?php
include_once('./admin.tail.php');
?>
