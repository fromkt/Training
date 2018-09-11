<?php
$sub_menu = "200300";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$ma_last_option = "";

$sql_common = " from {$gml['member_table']} ";
$sql_where = " where (1) ";

// 회원ID ..에서 ..까지
if ($mb_id1 != 1)
    $sql_where .= " and mb_id between '{$mb_id1_from}' and '{$mb_id1_to}' ";

// E-mail에 특정 단어 포함
if ($mb_email != "")
    $sql_where .= " and mb_email like '%{$mb_email}%' ";

// 메일링
if ($mb_mailling != "")
    $sql_where .= " and mb_mailling = '{$mb_mailling}' ";

// 권한
$sql_where .= " and mb_level between '{$mb_level_from}' and '{$mb_level_to}' ";

// 게시판그룹회원
if ($gr_id) {
    $group_member = "";
    $comma = "";
    $sql2 = " select mb_id from {$gml['group_member_table']} where gr_id = '{$gr_id}' order by mb_id ";
    $result2 = sql_query($sql2);
    for ($k=0; $row2=sql_fetch_array($result2); $k++) {
        $group_member .= "{$comma}'{$row2['mb_id']}'";
        $comma = ",";
    }

    if (!$group_member)
        alert(__('There are no bulletin board group members selected.'));

    $sql_where .= " and mb_id in ($group_member) ";
}

// 탈퇴, 차단된 회원은 제외
$sql_where .= " and mb_leave_date = '' and mb_intercept_date = '' ";

$sql = " select COUNT(*) as cnt {$sql_common} {$sql_where} ";
$row = sql_fetch($sql);
$cnt = $row['cnt'];
if ($cnt == 0)
    alert(__('No membership data is applicable for the selected content.'));

// 마지막 옵션을 저장합니다.
$ma_last_option .= "mb_id1={$mb_id1}";
$ma_last_option .= "||mb_id1_from={$mb_id1_from}";
$ma_last_option .= "||mb_id1_to={$mb_id1_to}";
$ma_last_option .= "||mb_email={$mb_email}";
$ma_last_option .= "||mb_mailling={$mb_mailling}";
$ma_last_option .= "||mb_level_from={$mb_level_from}";
$ma_last_option .= "||mb_level_to={$mb_level_to}";
$ma_last_option .= "||gr_id={$gr_id}";

sql_query(" update {$gml['mail_table']} set ma_last_option = '{$ma_last_option}' where ma_id = '{$ma_id}' ");

$gml['title'] = __('Mail destination member');
include_once('./admin.head.php');
?>

<form name="fmailselectlist" id="fmailselectlist" method="post" action="./mail_select_update.php">
<input type="hidden" name="token" value="">
<input type="hidden" name="ma_id" value="<?php echo $ma_id ?>">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $gml['title']; ?> <?php e__('List'); ?></caption>
    <thead>
    <tr>
        <th scope="col"><?php ep__('Num', 'Number'); ?></th>
        <th scope="col"><?php e__('Member ID'); ?></th>
        <th scope="col"><?php e__('Name'); ?></th>
        <th scope="col"><?php e__('Nickname'); ?></th>
        <th scope="col"><?php e__('E-mail'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $sql = " select mb_id, mb_name, mb_nick, mb_email, mb_datetime $sql_common $sql_where order by mb_id ";
    $result = sql_query($sql);
    $i=0;
    $ma_list = "";
    $cr = "";
    while ($row=sql_fetch_array($result)) {
        $i++;
        $ma_list .= $cr . $row['mb_email'] . "||" . $row['mb_id'] . "||" . get_text($row['mb_name']) . "||" . $row['mb_nick'] . "||" . $row['mb_datetime'];
        $cr = "\n";

        $bg = 'bg'.($i%2);
    ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_num"><?php echo $i ?></td>
        <td class="td_mbid"><?php echo $row['mb_id'] ?></td>
        <td class="td_mbname"><?php echo get_text($row['mb_name']); ?></td>
        <td class="td_mbname"><?php echo $row['mb_nick'] ?></td>
        <td><?php echo $row['mb_email'] ?></td>
    </tr>
    <?php } ?>
    </tbody>
    </table>
    <textarea name="ma_list" style="display:none"><?php echo $ma_list?></textarea>
</div>

<div class="btn_confirm01 btn_confirm">
    <input type="submit" value="<?php e__('Send Mail'); ?>" class="btn_submit">
    <a href="./mail_select_form.php?ma_id=<?php echo $ma_id ?>"><?php e__('Back'); ?></a>
</div>

</form>

<?php
include_once('./admin.tail.php');
?>
