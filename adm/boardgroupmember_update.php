<?php
$sub_menu = "300200";
include_once('./_common.php');

sql_query(" ALTER TABLE {$gml['group_member_table']} CHANGE `gml_id` `gml_id` INT( 11 ) DEFAULT '0' NOT NULL AUTO_INCREMENT ", false);

if ($w == '')
{
    auth_check($auth[$sub_menu], 'w');

    $mb = get_member($mb_id);
    if (!$mb['mb_id']) {
        alert(__('This member does not exist.'));
    }

    $gr = get_group($gr_id);
    if (!$gr['gr_id']) {
        alert(__('This group does not exist.'));
    }

    $sql = " select count(*) as cnt
                from {$gml['group_member_table']}
                where gr_id = '{$gr_id}'
                and mb_id = '{$mb_id}' ";
    $row = sql_fetch($sql);
    if ($row['cnt']) {
        alert(__('This data is already registered.'));
    }
    else
    {
        check_admin_token();

        $sql = " insert into {$gml['group_member_table']}
                    set gr_id = '{$_POST['gr_id']}',
                         mb_id = '{$_POST['mb_id']}',
                         gml_datetime = '".GML_TIME_YMDHIS."' ";
        sql_query($sql);
    }
}
else if ($w == 'd' || $w == 'ld')
{
    auth_check($auth[$sub_menu], 'd');

    $count = count($_POST['chk']);
    if(!$count)
        alert(__('Please select at least one list to delete.'));

    check_admin_token();

    for($i=0; $i<$count; $i++) {
        $gml_id = $_POST['chk'][$i];
        $sql = " select * from {$gml['group_member_table']} where gml_id = '$gml_id' ";
        $gml = sql_fetch($sql);
        if (!$gml['gml_id']) {
            if($count == 1)
                alert(__('This data does not exist.'));
            else
                continue;
        }

        $sql = " delete from {$gml['group_member_table']} where gml_id = '$gml_id' ";
        sql_query($sql);
    }
}

if ($w == 'ld')
    goto_url('./boardgroupmember_list.php?gr_id='.$gr_id);
else
    goto_url('./boardgroupmember_form.php?mb_id='.$mb_id);
?>
