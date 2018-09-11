<?php
include_once('./_common.php');

$row = get_member($mb_id, 'mb_id, mb_email, mb_datetime');

if (!$row['mb_id'])
    alert(__('This member does not exist.'), GML_URL);

if ($mb_md5) {
    $tmp_md5 = md5($row['mb_id'].$row['mb_email'].$row['mb_datetime']);
    if ($mb_md5 == $tmp_md5) {
        sql_query(" update {$gml['member_table']} set mb_mailling  = 0 where mb_id = '{$mb_id}' ");

        alert(__('You have declined to receive an information mail.'), GML_URL);
    }
}

alert(__('A valid value has not crossed.'), GML_URL);
?>