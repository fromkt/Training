<?php
include_once('./_common.php');

$mb_id  = trim($_GET['mb_id']);
$mb_md5 = trim($_GET['mb_md5']);

$sql = " select mb_id, mb_email_certify2 from {$gml['member_table']} where mb_id = '{$mb_id}' ";
$row = sql_fetch($sql);
if (!$row['mb_id'])
    alert(__('This member does not exist.'), GML_URL);

// Certification links are only processed once. 인증 링크는 한번만 처리가 되게 한다.
sql_query(" update {$gml['member_table']} set mb_email_certify2 = '' where mb_id = '$mb_id' ");

if ($mb_md5)
{
    if ($mb_md5 == $row['mb_email_certify2'])
    {
        sql_query(" update {$gml['member_table']} set mb_email_certify = '".GML_TIME_YMDHIS."' where mb_id = '{$mb_id}' ");

        alert(__('Your mail authentication has been successfully processed.')."\\n\\n".sprintf(__('You can log in with an %s ID from now on.'), $mb_id), GML_URL);
    }
    else
    {
        alert(__('Invalid mail authentication request information.'), GML_URL);
    }
}

alert(__('A valid value has not crossed.'), GML_URL);
?>