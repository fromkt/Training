<?php
$sub_menu = "200820";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$gml['title'] = __('Delete visitor Log');
include_once('./admin.head.php');

// 최소년도 구함
$sql = " select min(vi_date) as min_date from {$gml['visit_table']} ";
$row = sql_fetch($sql);

$min_year = (int)substr($row['min_date'], 0, 4);
$now_year = (int)substr(GML_TIME_YMD, 0, 4);
?>

<div class="local_desc01 local_desc">
    <?php e__('Select the year and method for deleting the contact log.');   //접속자 로그를 삭제할 년도와 방법을 선택해주십시오. ?>
</div>

<form name="fvisitdelete" class="local_sch03" method="post" action="./visit_delete_update.php" onsubmit="return form_submit(this);">
    <div>
        <label for="year" class="sound_only"><?php e__('Select Year'); ?></label>
        <select name="year" id="year">
            <option value=""><?php e__('Select Year'); ?></option>
            <?php
            for($year=$min_year; $year<=$now_year; $year++) {
            ?>
            <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
            <?php
            }
            ?>
        </select> <?php e__('Year'); ?>
        <label for="month" class="sound_only"><?php e__('Select Month'); ?></label>
        <select name="month" id="month">
            <option value=""><?php e__('Select Month'); ?></option>
            <?php
            for($i=1; $i<=12; $i++) {
            ?>
            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
            <?php
            }
            ?>
        </select> <?php e__('Month'); ?>
        <label for="method" class="sound_only"><?php e__('Select Delete Method'); ?></label>
        <select name="method" id="method">
            <option value="before"><?php e__('Delete selected year / month prior data'); ?></option>
            <option value="specific"><?php e__('Delete data for selection year / month'); ?></option>
        </select>
    </div>
    <div class="visit_del_bt">
        <label for="pass"><?php e__('Administrator password'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label>
        <input type="password" name="pass" id="pass" class="frm_input required">
        <input type="submit" value="<?php e__('Apply'); ?>" class="btn_submit">
    </div>
</form>

<?php
get_localize_script('visit_delete',
array(
'select_year_msg'     =>  __('Please select a year.'),  // 년도를 선택해 주십시오.
'select_month_msg'    =>  __('Please select a month.'),    // 월을 선택해 주십시오.
'require_password_msg' => __('Enter admin password.'),  // 관리자 비밀번호를 입력해 주십시오.
'delete_confirm_msg'    => __('Are you sure you want to delete the data?'),  // 자료를 삭제하시겠습니까?
),
true);
?>
<script>
function form_submit(f)
{
    var year = $("#year").val();
    var month = $("#month").val();
    var method = $("#method").val();
    var pass = $("#pass").val();

    if(!year) {
        alert( visit_delete.select_year_msg );
        return false;
    }

    if(!month) {
        alert( visit_delete.select_month_msg );
        return false;
    }

    if(!pass) {
        alert( visit_delete.require_password_msg );
        return false;
    }

    return confirm( visit_delete.delete_confirm_msg );
}
</script>

<?php
include_once('./admin.tail.php');
?>
