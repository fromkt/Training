<?php
$sub_menu = "200900";
include_once('./_common.php');

check_demo();

auth_check($auth[$sub_menu], 'd');

check_admin_token();

$count = count($_POST['chk']);

if(!$count)
    alert(__('Please select at least one voting list to delete.'));

for($i=0; $i<$count; $i++) {
    $po_id = (int) $_POST['chk'][$i];

    $sql = " delete from {$gml['poll_table']} where po_id = '$po_id' ";
    sql_query($sql);

    $sql = " delete from {$gml['poll_etc_table']} where po_id = '$po_id' ";
    sql_query($sql);
}

goto_url('./poll_list.php?'.$qstr);
?>