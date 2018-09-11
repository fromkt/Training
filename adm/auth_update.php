<?php
$sub_menu = "100200";
include_once('./_common.php');

if ($is_admin != 'super')
    alert(__('Only the Super administrator can access it.'));

$mb = get_member($mb_id);
if (!$mb['mb_id'])
    alert(__('This member ID does not exist.'));

check_admin_token();

$sql = " insert into {$gml['auth_table']}
            set mb_id   = '{$_POST['mb_id']}',
                au_menu = '{$_POST['au_menu']}',
                au_auth = '{$_POST['r']},{$_POST['w']},{$_POST['d']}' ";
$result = sql_query($sql, FALSE);
if (!$result) {
    $sql = " update {$gml['auth_table']}
                set au_auth = '{$_POST['r']},{$_POST['w']},{$_POST['d']}'
              where mb_id   = '{$_POST['mb_id']}'
                and au_menu = '{$_POST['au_menu']}' ";
    sql_query($sql);
}

//sql_query(" OPTIMIZE TABLE `$gml['auth_table']` ");

goto_url('./auth_list.php?'.$qstr);
?>
