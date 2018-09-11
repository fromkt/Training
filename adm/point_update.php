<?php
$sub_menu = "200200";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

check_admin_token();

$mb_id = strip_tags($_POST['mb_id']);
$po_point = strip_tags($_POST['po_point']);
$po_content = strip_tags($_POST['po_content']);
$expire = preg_replace('/[^0-9]/', '', $_POST['po_expire_term']);

$mb = get_member($mb_id);

if (!$mb['mb_id'])
    alert(__('This member ID does not exist.'), './point_list.php?'.$qstr);

if (($po_point < 0) && ($po_point * (-1) > $mb['mb_point']))
    alert(__('If you are cutting points, they must not be less than the current point.'), './point_list.php?'.$qstr);

insert_point($mb_id, $po_point, $po_content, '@passive', $mb_id, $member['mb_id'].'-'.uniqid(''), $expire);

goto_url('./point_list.php?'.$qstr);
?>
