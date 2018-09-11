<?php
$sub_menu = '300900';
include_once('./_common.php');

if( $config['cf_editor'] == "ckeditor4" ) {
    include_once(GML_EDITOR_LIB);
}
if( !class_exists('EditorImage') ) {
    exit;
}

$eImg = new EditorImage;
$tbl_name   = $eImg->get_tblName();

auth_check($auth[$sub_menu], "r");

// 체크된 자료 삭제
if (isset($_POST['chk']) && is_array($_POST['chk'])) {
    sql_query(" delete from {$tbl_name} where ei_id in ('".implode("','", array_map('intval', $_POST['chk']))."') ", true);
}

$sql_common = " from {$tbl_name} a ";
$sql_search = " where (1) ";
$qstr = "";

if ($stx) {
    $sql_search .= " and ( ";
    switch ($sfl) {
        case "ei_ip" :
            $sql_search .= " {$sfl} like '{$stx}%' ";
            break;
        case "mb_id" :
            $sql_search .= " {$sfl} = '{$stx}' ";
            break;
        default :
            $sql_search .= " {$sfl} like '%{$stx}%' ";
            break;
    }
    $sql_search .= " ) ";
    $qstr .= "&{$sfl}={$stx}";
}

if(!empty($fr_date) && !empty($to_date)) {

    if (! preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date) ) $fr_date = GML_TIME_YMD;
    if (! preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date) ) $to_date = GML_TIME_YMD;

    $sql_search .= " and ei_datetime between '{$fr_date} 00:00:00' and '{$to_date} 23:59:59' ";
    $qstr .= "&fr_date={$fr_date}&to_date={$to_date}";
}

if( $sst && ! in_array($sst, array('ei_size', 'ei_name_original', 'mb_id')) ){
    $sst = '';
}

if( $sod && ! in_array($sod, array('desc', 'asc')) ){
    $sod = '';
}

if (!$sst) {
    $sst  = "ei_id";
    $sod = "desc";
}
$sql_order = " order by {$sst} {$sod} ";
$qstr .= "&sst={$sst}&sod={$sod}";

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

$gml['title'] = __('Editor Images');
include_once('./admin.head.php');
include_once(GML_PLUGIN_PATH.'/jquery-ui/datepicker.php');

$colspan = 4;

$tbl_sql = "select * from {$gml['board_table']}";
$tbl_res = sql_query($tbl_sql);
$arr_tbl = array();
$arr_tbl['bbs/qa'] = __('1:1 Contact');
while( $row = sql_fetch_array($tbl_res) ) {
    $arr_tbl[$row['bo_table']] = $row['bo_subject'];
}
?>

<script>
var list_update_php = '';
var list_delete_php = 'editorimglist.php';
</script>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
     <span class="btn_ov01"><span class="ov_txt"><?php e__('Totals'); ?></span><span class="ov_num"> <?php echo number_format($total_count) ?> <?php e__('Count'); ?></span></span> 

</div>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
<div class="sch_last">
    <strong class="sch_tit"><?php e__('Search by Period'); ?></strong>
    <input type="text" name="fr_date" value="<?php echo $fr_date ?>" id="fr_date" class="frm_input date_input" size="11" maxlength="10" autocomplete="off">
    <label for="fr_date" class="sound_only"><?php e__('Start Date'); ?></label>
    ~
    <input type="text" name="to_date" value="<?php echo $to_date ?>" id="to_date" class="frm_input date_input" size="11" maxlength="10" autocomplete="off">
    <label for="to_date" class="sound_only"><?php e__('End Date'); ?></label>
    <input type="submit" class="btn_submit" value="<?php e__('Search'); ?>">

    <label for="sfl" class="sound_only"><?php e__('Search target'); ?></label>
    <select name="sfl" id="sfl">
        <option value="mb_id"<?php echo get_selected($_GET['sfl'], "mb_id"); ?>><?php e__('Member ID'); ?></option>
        <option value="ei_name_original"<?php echo get_selected($_GET['sfl'], "ei_name_original"); ?>><?php e__('Original name'); ?></option>
        <option value="ei_ip"<?php echo get_selected($_GET['sfl'], "ei_ip"); ?>><?php e__('IP'); ?></option>
    </select>
    <label for="stx" class="sound_only"><strong class="sound_only"> <?php e__('Search'); ?></strong></label>
    <input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
    <input type="submit" value="<?php e__('Search'); ?>" class="btn_submit">
</div>
</form>

<form name="feditorimglist" id="feditorimglist" method="post">
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
        <th scope="col" width="120"><?php e__('Preview'); ?></th>
        <th scope="col"><?php echo subject_sort_link('ei_size') ?><?php e__('File size');   // 파일 사이즈 ?></a></th>
        <th scope="col"><?php echo subject_sort_link('ei_name_original') ?><?php e__('Original name');  // 파일명 ?></a></th>
        <th scope="col"><?php e__('Upload info');   // 업로드 정보 ?></th>
        <th scope="col"><?php echo subject_sort_link('mb_id') ?><?php e__('Member ID');     // 회원 아이디 ?></a></th>
        <th scope="col"><?php e__('IP'); ?></th>
        <th scope="col"><?php e__('Date'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $bg = 'bg'.($i%2);
        
        $ei_tmp = $eImg->img_thumbnail(GML_DATA_PATH.preg_replace( '/(\.\.[\/\\\])+/', '', $row['ei_path']), 100, 80, 1);
        $ei_img = $ei_tmp['src'];
        
        $mb_id = get_sideview($row['mb_id'], $row['mb_id']);

        $upInfo = "";
        if( !empty($row['bo_table']) ) {
            $upInfo .= $arr_tbl[$row['bo_table']];

            if( $row['wr_id'] ) {
                if( $row['bo_table'] == "bbs/qa" ) {
                    $upInfo = "<a href=\"".GML_BBS_URL."/qaview.php?qa_id={$row['wr_id']}\" target=\"_blank\">{$upInfo}</a>";
                } else {
                    $upInfo = "<a href=\"".get_pretty_url($row['bo_table'], $row['wr_id'])."\" target=\"_blank\">{$upInfo}</a>";
                }
            }
        }
    ?>

    <tr class="<?php echo $bg; ?>">
        <td class="td_chk">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo $i; ?></label>
            <input type="checkbox" name="chk[]" value="<?php echo $row['ei_id'] ?>" id="chk_<?php echo $i ?>">
        </td>
        <td>
            <img src="<?php echo $ei_img; ?>" alt="<?php echo $row['ei_name']; ?>" />
        </td>
        <td><?php echo exchange_data($row['ei_size'], null, 2); ?></td>
        <td><?php echo $row['ei_name_original'] ?></td>
        <td><?php echo $upInfo; ?></td>
        <td><?php echo $mb_id; ?></td>
        <td><a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?sfl=ei_ip&stx=<?php echo $row['ei_ip']; ?>"><?php echo $row['ei_ip'] ?></a></td>
        <td><?php echo $row['ei_datetime'] ?></td>
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
get_localize_script('feditorimglist',
array(
'delete_msg'=>__('Are you sure you want to delete?'),  // 정말 삭제하시겠습니까?
'check_msg'=>__('Select at least one item to delete.'),    // 선택삭제 하실 항목을 하나 이상 선택하세요.
),
true);
?>
<script>
jQuery(function($) {
    $("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });

    $('#feditorimglist').submit(function() {
        if(confirm( feditorimglist.delete_msg )) {
            if (!is_checked("chk[]")) {
                alert( feditorimglist.check_msg );
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
