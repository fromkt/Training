<?php
$sub_menu = "200300";
include_once('./_common.php');

if (!$config['cf_email_use'])
    alert(__('You must check "Enable mail sending" in Preferences to send mail.'));

auth_check($auth[$sub_menu], 'r');

$sql = " select * from {$gml['mail_table']} where ma_id = '$ma_id' ";
$ma = sql_fetch($sql);
if (!$ma['ma_id'])
    alert(__('Please select what you want to send.'));

// 전체회원수
$sql = " select COUNT(*) as cnt from {$gml['member_table']} ";
$row = sql_fetch($sql);
$tot_cnt = $row['cnt'];

// 탈퇴대기회원수
$sql = " select COUNT(*) as cnt from {$gml['member_table']} where mb_leave_date <> '' ";
$row = sql_fetch($sql);
$finish_cnt = $row['cnt'];

$last_option = explode('||', $ma['ma_last_option']);
for ($i=0; $i<count($last_option); $i++) {
    $option = explode('=', $last_option[$i]);
    // 동적변수
    $var = $option[0];
    $$var = $option[1];
}

if (!isset($mb_id1)) $mb_id1 = 1;
if (!isset($mb_level_from)) $mb_level_from = 1;
if (!isset($mb_level_to)) $mb_level_to = 10;
if (!isset($mb_mailling)) $mb_mailling = 1;

$gml['title'] = __('Send Member Email');
include_once('./admin.head.php');
?>

<div class="local_ov01 local_ov">
    <?php e__('Select mail list'); ?>,
    <?php echo sprintf(__('All members %s totals, Member awaiting withdrawal %s totals, Normal members %s totals'), number_format($tot_cnt), number_format($finish_cnt), number_format($tot_cnt - $finish_cnt)); ?>
</div>

<form name="frmsendmailselectform" id="frmsendmailselectform" action="./mail_select_list.php" method="post" autocomplete="off">
<input type="hidden" name="ma_id" value="<?php echo $ma_id ?>">

<div class="tbl_frm01 tbl_wrap">
    <table>
    <caption><?php echo $gml['title']; ?> <?php e__('Select Target'); ?></caption>
    <tbody>
    <tr>
        <th scope="row"><?php e__('Member ID'); ?></th>
        <td>
            <input type="radio" name="mb_id1" value="1" id="mb_id1_all" <?php echo $mb_id1?"checked":""; ?>> <label for="mb_id1_all"><?php e__('All'); ?></label>
            <input type="radio" name="mb_id1" value="0" id="mb_id1_section" <?php echo !$mb_id1?"checked":""; ?>> <label for="mb_id1_section"><?php e__('Section'); ?></label>
            <input type="text" name="mb_id1_from" value="<?php echo $mb_id1_from ?>" id="mb_id1_from" title="<?php e('Start Section'); ?>" class="frm_input"> ~
            <input type="text" name="mb_id1_to" value="<?php echo $mb_id1_to ?>" id="mb_id1_to" title="<?php e('End Section'); ?>" class="frm_input">
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="mb_email"><?php e__('E-mail'); ?></label></th>
        <td>
            <?php echo help(__('Include words in mail address')." (".__('Example :')." @".preg_replace('#^(www[^\.]*\.){1}#', '', $_SERVER['HTTP_HOST']).")") ?>
            <input type="text" name="mb_email" value="<?php echo $mb_email ?>" id="mb_email" class="frm_input" size="50">
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="mb_mailling"><?php e__('Mailing'); ?></label></th>
        <td>
            <select name="mb_mailling" id="mb_mailling">
                <option value="1"><?php e__('Only members who have agreed to receive'); ?></option>
                <option value=""><?php e__('All'); ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row"><?php e__('Level'); ?></th>
        <td>
            <label for="mb_level_from" class="sound_only"><?php e__('Min Level'); ?></label>
            <select name="mb_level_from" id="mb_level_from">
            <?php for ($i=1; $i<=10; $i++) { ?>
                <option value="<?php echo $i ?>"><?php echo $i ?></option>
            <?php } ?>
            </select> ~
            <label for="mb_level_to" class="sound_only"><?php e__('Max Level'); ?></label>
            <select name="mb_level_to" id="mb_level_to">
            <?php for ($i=1; $i<=10; $i++) { ?>
                <option value="<?php echo $i ?>"<?php echo $i==10 ? " selected" : ""; ?>><?php echo $i ?></option>
            <?php } ?>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="gr_id"><?php e__('Bulletin Board Members'); ?></label></th>
        <td>
            <select name="gr_id" id="gr_id">
                <option value=''><?php e__('All'); ?></option>
                <?php
                $sql = " select gr_id, gr_subject from {$gml['group_table']} order by gr_subject ";
                $result = sql_query($sql);
                for ($i=0; $row=sql_fetch_array($result); $i++) {
                    echo '<option value="'.$row['gr_id'].'">'.$row['gr_subject'].'</option>';
                }
                ?>
            </select>
        </td>
    </tr>
    </tbody>
    </table>
</div>

<div class="btn_confirm01 btn_confirm">
    <input type="submit" value="<?php e__('Save'); ?>" class="btn_submit">
    <a href="./mail_list.php"><?php e__('List'); ?></a>
</div>
</form>

<?php
include_once('./admin.tail.php');
?>
