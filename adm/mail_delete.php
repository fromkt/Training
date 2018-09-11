<?php
$sub_menu = '200300';
include_once('./_common.php');

check_demo();

auth_check($auth[$sub_menu], 'd');

check_admin_token();

$count = count($_POST['chk']);

if(!$count)
    alert(__('Please select at least one mail list to delete.'));

for($i=0; $i<$count; $i++) {
    $ma_id = $_POST['chk'][$i];

    $sql = " delete from {$gml['mail_table']} where ma_id = '$ma_id' ";
    sql_query($sql);
}

goto_url('./mail_list.php');
?>