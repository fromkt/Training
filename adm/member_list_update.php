<?php
$sub_menu = "200100";
include_once('./_common.php');

check_demo();

if (!count($_POST['chk'])) {
    alert(sprintf(__('Please check at least one item to be done %s.'), $_POST['act_button']));
}

auth_check($auth[$sub_menu], 'w');

check_admin_token();

$mb_datas = array();

if ($_POST['act_button'] == "modify_selection") {

    for ($i=0; $i<count($_POST['chk']); $i++)
    {
        // 실제 번호를 넘김
        $k = $_POST['chk'][$i];

        $mb_datas[] = $mb = get_member($_POST['mb_id'][$k]);

        if (!$mb['mb_id']) {
            $msg .= $mb['mb_id'].' : '.__('Member data do not exist.').'\\n';
        } else if ($is_admin != 'super' && $mb['mb_level'] >= $member['mb_level']) {
            $msg .= $mb['mb_id'].' : '.__('Can not modify a member who has more authority than you or is equal to you.').'\\n';
        } else if ($member['mb_id'] == $mb['mb_id']) {
            $msg .= $mb['mb_id'].' : '.__('Can not modify an administrator who is logging in.').'\\n';
        } else {
            if($_POST['mb_certify'][$k])
                $mb_adult = $_POST['mb_adult'][$k];
            else
                $mb_adult = 0;

            $sql = " update {$gml['member_table']}
                        set mb_level = '{$_POST['mb_level'][$k]}',
                            mb_intercept_date = '{$_POST['mb_intercept_date'][$k]}',
                            mb_mailling = '{$_POST['mb_mailling'][$k]}',
                            mb_sms = '{$_POST['mb_sms'][$k]}',
                            mb_open = '{$_POST['mb_open'][$k]}',
                            mb_certify = '{$_POST['mb_certify'][$k]}',
                            mb_adult = '{$mb_adult}'
                        where mb_id = '{$_POST['mb_id'][$k]}' ";
            sql_query($sql);
        }
    }

} else if ($_POST['act_button'] == "delete_selection") {

    for ($i=0; $i<count($_POST['chk']); $i++)
    {
        // 실제 번호를 넘김
        $k = $_POST['chk'][$i];

        $mb_datas[] = $mb = get_member($_POST['mb_id'][$k]);

        if (!$mb['mb_id']) {
            $msg .= $mb['mb_id'].' : '.__('Member data do not exist.').'\\n';
        } else if ($member['mb_id'] == $mb['mb_id']) {
            $msg .= $mb['mb_id'].' : '.__('Can not modify an administrator who is logging in.').'\\n';
        } else if (is_admin($mb['mb_id']) == 'super') {
            $msg .= $mb['mb_id'].' : '.__('Can not delete a Super Admin.').'\\n';
        } else if ($is_admin != 'super' && $mb['mb_level'] >= $member['mb_level']) {
            $msg .= $mb['mb_id'].' : '.__('Can not delete a member who has more authority than you or is equal to you.').'\\n';
        } else {
            // 회원자료 삭제
            member_delete($mb['mb_id']);
        }
    }
}

if ($msg)
    //echo '<script> alert("'.$msg.'"); </script>';
    alert($msg);

start_event('admin_member_list_update', $_POST['act_button'], $mb_datas);

goto_url('./member_list.php?'.$qstr);
?>
