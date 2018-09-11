<?php
$sub_menu = "300400";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

if (empty($fr_date) || ! preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date) ) $fr_date = GML_TIME_YMD;
if (empty($to_date) || ! preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date) ) $to_date = GML_TIME_YMD;

$qstr = "fr_date={$fr_date}&amp;to_date={$to_date}";

$sql_common = " from {$gml['popular_table']} a ";
$sql_search = " where trim(pp_word) <> '' and pp_date between '{$fr_date}' and '{$to_date}' ";
$sql_group = " group by pp_word ";
$sql_order = " order by cnt desc ";

$sql = " select pp_word {$sql_common} {$sql_search} {$sql_group} ";
$result = sql_query($sql);
$total_count = sql_num_rows($result);

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select pp_word, count(*) as cnt {$sql_common} {$sql_search} {$sql_group} {$sql_order} limit {$from_record}, {$rows} ";
$result = sql_query($sql);

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';

$gml['title'] = __('Top search ranking');
include_once('./admin.head.php');
include_once(GML_PLUGIN_PATH.'/jquery-ui/datepicker.php');

$colspan = 3;
?>

<script>
$(function(){
    $("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
});
</script>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt"><?php e__('Totals'); ?></span><span class="ov_num"> <?php echo number_format($total_count) ?> <?php e__('Count'); ?></span></span> 
</div>

<form name="fsearch" id="fsearch" class="local_sch03" method="get">
<div class="sch_last">
    <strong class="sch_tit"><?php e__('Search by Period'); ?></strong>
    <input type="text" name="fr_date" value="<?php echo $fr_date ?>" id="fr_date" class="frm_input date_input" size="11" maxlength="10">
    <label for="fr_date" class="sound_only"><?php e__('Start Date'); ?></label>
    ~
    <input type="text" name="to_date" value="<?php echo $to_date ?>" id="to_date" class="frm_input date_input" size="11" maxlength="10">
    <label for="to_date" class="sound_only"><?php e__('End Date'); ?></label>
    <input type="submit" class="btn_submit" value="<?php e__('Search'); ?>">
</div>
</form>

<form name="fpopularrank" id="fpopularrank" method="post">
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
        <th scope="col"><?php e__('Ranking'); ?></th>
        <th scope="col"><?php e__('Search term'); ?></th>
        <th scope="col"><?php e__('Search count'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $bg = 'bg'.($i%2);

        $word = get_text($row['pp_word']);
        $rank = ($i + 1 + ($rows * ($page - 1)));

    ?>

    <tr class="<?php echo $bg; ?>">
        <td class="td_num"><?php echo $rank ?></td>
        <td class="td_left"><?php echo $word ?></td>
        <td class="td_num"><?php echo $row['cnt'] ?></td>
    </tr>

    <?php
    }

    if ($i == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">'.__('No Data').'</td></tr>';
    ?>
    </tbody>
    </table>
</div>

</form>

<?php
echo get_paging(GML_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page=");
?>

<?php
include_once('./admin.tail.php');
?>
