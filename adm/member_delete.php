<?php
$sub_menu = "200100";
include_once("./_common.php");

check_demo();

auth_check($auth[$sub_menu], "d");

$mb = get_member($_POST['mb_id']);

if (!$mb['mb_id'])
    alert(__('Member data do not exist.'));
else if ($member['mb_id'] == $mb['mb_id'])
    alert(__('You can not delete an administrator who is logging in.'));
else if (is_admin($mb['mb_id']) == "super")
    alert(__('You can not delete a super administrator.'));
else if ($mb['mb_level'] >= $member['mb_level'])
    alert(__('You can not delete a member who has more authority than you or is equal to you.'));

check_admin_token();

// 회원자료 삭제
member_delete($mb['mb_id']);

if ($url)
    goto_url("{$url}?$qstr&amp;w=u&amp;mb_id=$mb_id");
else
    goto_url("./member_list.php?$qstr");
?>
