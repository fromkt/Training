<?php
$sub_menu = "200100";
include_once("./_common.php");

check_demo();

auth_check($auth[$sub_menu], "d");

check_admin_token();

$msg = "";
for ($i=0; $i<count($chk); $i++)
{
    // 실제 번호를 넘김
    $k = $_POST['chk'][$i];

    $mb = get_member($_POST['mb_id'][$k]);

    if (!$mb['mb_id']) {
        $msg .= "{$mb['mb_id']} : ".__('Member data do not exist.')."\\n";
    } else if ($member['mb_id'] == $mb['mb_id']) {
        $msg .= "{$mb['mb_id']} : ".__('Can not delete an administrator who is logging in.')."\\n";
    } else if (is_admin($mb['mb_id']) == "super") {
        $msg .= "{$mb['mb_id']} : ".__('Can not delete a Super Admin.')."\\n";
    } else if ($is_admin != "super" && $mb['mb_level'] >= $member['mb_level']) {
        $msg .= "{$mb['mb_id']} : ".__('Can not delete a member who has more authority than you or is equal to you.')."\\n";
    } else {
        // Delete Member Data
        member_delete($mb['mb_id']);
    }
}

if ($msg)
    echo "<script type='text/javascript'> alert('$msg'); </script>";

goto_url("./member_list.php?$qstr");
?>
