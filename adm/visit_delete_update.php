<?php
$sub_menu = "200820";
include_once('./_common.php');

check_demo();

auth_check($auth[$sub_menu], 'd');

if ($is_admin != 'super')
    alert(__('Only the Super administrator can access it.'));

$year = preg_replace('/[^0-9]/', '', $_POST['year']);
$month = preg_replace('/[^0-9]/', '', $_POST['month']);
$method = $_POST['method'];
$pass = trim($_POST['pass']);

if(!$pass)
    alert(__('Enter admin password.'));

// 관리자 비밀번호 비교
$admin = get_admin('super');
if(!check_password($pass, $admin['mb_password']))
    alert(__('Administrator password mismatch.'));

if(!$year)
    alert(__('Please select a year.'));

if(!$month)
    alert(__('Please select month.'));

// 로그삭제 query
$del_date = $year.'-'.str_pad($month, 2, '0', STR_PAD_LEFT);
switch($method) {
    case 'before':
        $sql_common = " where substring(vi_date, 1, 7) < '$del_date' ";
        break;
    case 'specific':
        $sql_common = " where substring(vi_date, 1, 7) = '$del_date' ";
        break;
    default:
        alert(__('Please use the correct method.'));
        break;
}

// 총 로그수
$sql = " select count(*) as cnt from {$gml['visit_table']} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

// 로그삭제
$sql = " delete from {$gml['visit_table']} $sql_common ";
sql_query($sql);

// 삭제 후 총 로그수
$sql = " select count(*) as cnt from {$gml['visit_table']} ";
$row = sql_fetch($sql);
$total_count2 = $row['cnt'];

alert(sprintf(__('%s items have been deleted. ( %s totals )'), number_format($total_count - $total_count2), number_format($total_count)), './visit_delete.php');
?>