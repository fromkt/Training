<?php
$sub_menu = '200300';
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$sql_common = " from {$gml['mail_table']} ";

// 테이블의 전체 레코드수만 얻음
$sql = " select COUNT(*) as cnt {$sql_common} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$page = 1;

$sql = " select * {$sql_common} order by ma_id desc ";
$result = sql_query($sql);

$gml['title'] = __('Send Member Email');
include_once('./admin.head.php');

$colspan = 7;
?>

<div class="local_desc01 local_desc">
    <p>
        <?php e__('<b>Testing</b> sends a test email to the registered top administrator\'s email.'); ?><br>
        <?php echo sprintf(__('Currently, there are %s registered mails.'), $total_count); ?><br>
        <strong><?php e__('Caution) Not suitable for bulk mail shipments that recipients do not agree to. Please send it by dozens.'); ?></strong>
    </p>
</div>


<form name="fmaillist" id="fmaillist" action="./mail_delete.php" method="post">
<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $gml['title']; ?> <?php e__('List'); ?></caption>
    <thead>
    <tr>
        <th scope="col"><input type="checkbox" name="chkall" value="1" id="chkall" title="<?php e__('Select all of the current page list'); ?>" onclick="check_all(this.form)"></th>
        <th scope="col"><?php ep__('Num', 'Number'); ?></th>
        <th scope="col"><?php e__('Title'); ?></th>
        <th scope="col" class="m_no"><?php e__('Date'); ?></th>
        <th scope="col"><?php e__('Test'); ?></th>
        <th scope="col"><?php e__('Send'); ?></th>
        <th scope="col"><?php e__('Preview'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $s_vie = '<a href="./mail_preview.php?ma_id='.$row['ma_id'].'" target="_blank" class="btn_02">'.__('Preview').'</a>';

        $num = number_format($total_count - ($page - 1) * $config['cf_page_rows'] - $i);

        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>">
        <td class="td_chk">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo $row['ma_subject']; ?> <?php e__('Mail'); ?></label>
            <input type="checkbox" id="chk_<?php echo $i; ?>" name="chk[]" value="<?php echo $row['ma_id']; ?>">
        </td>
        <td class="td_num_s"><?php echo $num; ?></td>
        <td class="td_left"><a href="./mail_form.php?w=u&amp;ma_id=<?php echo $row['ma_id']; ?>"><?php echo $row['ma_subject']; ?></a></td>
        <td class="td_datetime m_no"><?php echo $row['ma_time']; ?></td>
        <td class="td_test td_mng"><a href="./mail_test.php?ma_id=<?php echo $row['ma_id']; ?>" class="btn_04"><?php e__('Test'); ?></a></td>
        <td class="td_send td_mng"><a href="./mail_select_form.php?ma_id=<?php echo $row['ma_id']; ?>" class="btn_03"><?php e__('Send'); ?></a></td>
        <td class="td_mngsmall td_mng"><?php echo $s_vie; ?></td>
    </tr>

    <?php
    }
    if (!$i)
        echo "<tr><td colspan=\"".$colspan."\" class=\"empty_table\">".__('No data')."</td></tr>";
    ?>
    </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <input type="submit" value="<?php e__('Delete selected'); ?>" class="btn_02 btn">
    <a href="./mail_form.php" id="mail_add" class="btn_01 btn"><?php e__('Add Mail Content'); ?></a>
</div>
</form>

<?php
get_localize_script('mail_list',
array(
'delete_msg'=>__('Are you sure you want to delete?'),  // 정말 삭제하시겠습니까?
'check_msg'=>__('Select at least one item to delete.'),    // 선택삭제 하실 항목을 하나 이상 선택하세요.
),
true);
?>
<script>
jQuery(function($) {
    $('#fmaillist').submit(function() {
        if(confirm( mail_list.delete_msg )) {
            if (!is_checked("chk[]")) {
                alert( mail_list.check_msg );
                return false;
            }

            return true;
        } else {
            return false;
        }
    });
});
</script>

<?php
include_once ('./admin.tail.php');
?>