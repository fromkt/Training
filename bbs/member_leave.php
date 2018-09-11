<?php
include_once('./_common.php');

if (!$member['mb_id'])
    alert(__('Only members can access it.'));

if ($is_admin == 'super')
    alert(__('The Super Admin can not leave'));

if (!($_POST['mb_password'] && check_password($_POST['mb_password'], $member['mb_password'])))
    alert(__('Incorrect password.'));

// Update member withdrawal date
$date = date("Ymd");
$sql = " update {$gml['member_table']} set mb_leave_date = '{$date}' where mb_id = '{$member['mb_id']}' ";
sql_query($sql);

// Logout , remove session
unset($_SESSION['ss_mb_id']);

if (!$url)
    $url = GML_URL;

// delete social_login data
if(function_exists('social_member_link_delete')){
    social_member_link_delete($member['mb_id']);
}

alert(sprintf(__('%s has leave membership at %s'), $member['mb_nick'], date("Y-m-d")), $url);
?>
