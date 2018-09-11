<?php
$sub_menu = '300700';
include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$gml['title'] = __('Manage FAQ details');
if ($fm_subject){
    $fm_subject = clean_xss_tags(strip_tags($fm_subject));
    $g5['title'] .= ' : '.$fm_subject;
}
 $fm_id = (int) $fm_id;
include_once (GML_ADMIN_PATH.'/admin.head.php');

$sql = " select * from {$gml['faq_master_table']} where fm_id = '$fm_id' ";
$fm = sql_fetch($sql);

$sql_common = " from {$gml['faq_table']} where fm_id = '$fm_id' ";

// 테이블의 전체 레코드수만 얻음
$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$sql = "select * $sql_common order by fa_order , fa_id ";
$result = sql_query($sql);
?>

<div class="local_ov01 local_ov">
    <span class="btn_ov01"><span class="ov_txt"><?php e__('Registered FAQ details'); ?> </span><span class="ov_num"> <?php echo sprintf(n__('%s total', '%s totals', $total_count), number_format($total_count)); ?> </span></span>

</div>

<div class="local_desc01 local_desc">
    <ol>
        <li><?php e__('You can register FAQs without restriction.'); ?></li>
        <li><?php e__('Click <strong>Add FAQ Details</strong> to enter frequently asked questions and answers.'); ?></li>
    </ol>
</div>

<div class="btn_fixed_top">
    <a href="./faqmasterlist.php" class="btn btn_02"><?php e__('Manage FAQ'); ?></a>
    <a href="./faqform.php?fm_id=<?php echo $fm['fm_id']; ?>" class="btn btn_01"><?php e__('Add FAQ details'); ?></a>
</div>

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $gml['title']; ?> <?php e__('List'); ?></caption>
    <thead>
    <tr>
        <th scope="col"><?php ep__('Num', 'Number'); ?></th>
        <th scope="col"><?php e__('Subject'); ?></th>
        <th scope="col"><?php e__('Order'); ?></th>
        <th scope="col"><?php e__('Edit'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
        $row1 = sql_fetch(" select COUNT(*) as cnt from {$gml['faq_table']} where fm_id = '{$row['fm_id']}' ");
        $cnt = $row1['cnt'];

        $s_mod = icon('edit', "");
        $s_del = icon('delete', "");

        $num = $i + 1;

        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>">
        <td class="td_num"><?php echo $num; ?></td>
        <td class="td_left"><?php echo stripslashes($row['fa_subject']); ?></td>
        <td class="td_num"><?php echo $row['fa_order']; ?></td>
        <td class="td_mng">
        <a href="./faqform.php?w=u&amp;fm_id=<?php echo $row['fm_id']; ?>&amp;fa_id=<?php echo $row['fa_id']; ?>" class="btn btn_03"><span class="sound_only"><?php echo stripslashes($row['fa_subject']); ?> </span><?php e__('Edit'); ?></a>
            <a href="./faqformupdate.php?w=d&amp;fm_id=<?php echo $row['fm_id']; ?>&amp;fa_id=<?php echo $row['fa_id']; ?>" onclick="return delete_confirm(this);" class="btn btn_02"><span class="sound_only"><?php echo stripslashes($row['fa_subject']); ?> </span><?php e__('Delete'); ?></a>
        </td>
    </tr>

    <?php
    }

    if ($i == 0) {
        echo '<tr><td colspan="4" class="empty_table">'.__('No Data').'</td></tr>';
    }
    ?>
    </tbody>
    </table>

</div>


<?php
include_once (GML_ADMIN_PATH.'/admin.tail.php');
?>
