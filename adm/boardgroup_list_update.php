<?php
$sub_menu = "300200";
include_once('./_common.php');

//print_r2($_POST); exit;

check_demo();

auth_check($auth[$sub_menu], 'w');

check_admin_token();

$count = count($_POST['chk']);

if(!$count)
    alert(sprintf(__('Please select at least one bulletin board group to %s.'), $_POST['act_button']));

for ($i=0; $i<$count; $i++)
{
    $k     = $_POST['chk'][$i];
    $gr_id = $_POST['group_id'][$k];
    $gr_subject = strip_tags($_POST['gr_subject'][$k]);

    if($_POST['act_button'] == 'modify_selection') {
        $sql = " update {$gml['group_table']}
                    set gr_subject    = '{$gr_subject}',
                        gr_device     = '{$_POST['gr_device'][$k]}',
                        gr_admin      = '{$_POST['gr_admin'][$k]}',
                        gr_use_access = '{$_POST['gr_use_access'][$k]}',
                        gr_order      = '{$_POST['gr_order'][$k]}'
                  where gr_id         = '{$gr_id}' ";
        if ($is_admin != 'super')
            $sql .= " and gr_admin    = '{$_POST['gr_admin'][$k]}' ";
        sql_query($sql);
    } else if($_POST['act_button'] == 'delete_selection') {
        $row = sql_fetch(" select count(*) as cnt from {$gml['board_table']} where gr_id = '$gr_id' ");
        if ($row['cnt'])
            alert(__('A bulletin board belonging to this group exists and can not be deleted.')."\\n\\n".__('Please delete the bulletin boards that belong to this group first.'), './board_list.php?sfl=gr_id&amp;stx='.$gr_id);

        // Delete Group
        sql_query(" delete from {$gml['group_table']} where gr_id = '$gr_id' ");

        // Delete group access members, 그룹접근 회원 삭제
        sql_query(" delete from {$gml['group_member_table']} where gr_id = '$gr_id' ");
    }
}

goto_url('./boardgroup_list.php?'.$qstr);
?>
