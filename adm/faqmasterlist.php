<?php
$sub_menu = '300700';
include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$gml['title'] = __('Manage FAQ');
include_once (GML_ADMIN_PATH.'/admin.head.php');

$sql_common = " from {$gml['faq_master_table']} ";

// 테이블의 전체 레코드수만 얻음
$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = "select * $sql_common order by fm_order, fm_id limit $from_record, {$config['cf_page_rows']} ";
$result = sql_query($sql);
?>

<div class="local_ov01 local_ov">
    <?php if ($page > 1) {?><a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>"><?php e__('All'); ?></a><?php } ?>
    <span class="btn_ov01"><span class="ov_txt"><?php e__('All FAQ'); ?> </span><span class="ov_num"> <?php echo number_format($total_count) ?> <?php e__('Count'); ?></span></span>

</div>

<div class="local_desc01 local_desc">
    <ol>
        <li><?php e__('You can register FAQs without restriction.'); ?></li>
        <li><?php e__('Click <strong>Add FAQ</strong> to create the FAQ Master. (Create one FAQ title : frequently asked questions, information on use, etc.)'); ?></li>
        <li><?php e__('You can manage the details by clicking the <strong>Title</strong> of the FAQ Master you created.'); ?></li>
    </ol>
</div>

<div class="btn_fixed_top">
    <a href="./faqmasterform.php" class="btn btn_01"><?php e__('Add FAQ'); ?></a>
</div>

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $gml['title']; ?> <?php e__('List'); ?></caption>
    <thead>
    <tr>
        <th scope="col">ID</th>
        <th scope="col"><?php e__('Title'); ?></th>
        <th scope="col"><?php e__('FAQs'); ?></th>
        <th scope="col"><?php e__('Sequence'); ?></th>
        <th scope="col"><?php e__('Edit'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php for ($i=0; $row=sql_fetch_array($result); $i++) {
        $sql1 = " select COUNT(*) as cnt from {$gml['faq_table']} where fm_id = '{$row['fm_id']}' ";
        $row1 = sql_fetch($sql1);
        $cnt = $row1['cnt'];
        $bg = 'bg'.($i%2);
        if($config['cf_use_multi_lang_data']) {
            $row = get_faq_by_lang($row, 'faqmaster');
        }
    ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_num"><?php echo $row['fm_id']; ?></td>
        <td class="td_left"><a href="./faqlist.php?fm_id=<?php echo $row['fm_id']; ?>&amp;fm_subject=<?php echo $row['fm_subject']; ?>"><?php echo stripslashes($row['fm_subject']); ?></a></td>
        <td class="td_num"><?php echo $cnt; ?></td>
        <td class="td_num"><?php echo $row['fm_order']?></td>
        <td class="td_mng">
            <a href="./faqmasterform.php?w=u&amp;fm_id=<?php echo $row['fm_id']; ?>" class="btn_03"><span class="sound_only"><?php echo stripslashes($row['fm_subject']); ?> </span><?php e__('Edit'); ?></a>
            <a href="<?php echo GML_BBS_URL; ?>/faq.php?fm_id=<?php echo $row['fm_id']; ?>" class="btn_02"><span class="sound_only"><?php echo stripslashes($row['fm_subject']); ?> </span><?php e__('View'); ?></a>
            <a href="./faqmasterformupdate.php?w=d&amp;fm_id=<?php echo $row['fm_id']; ?>" onclick="return delete_confirm(this);" class="btn_02"><span class="sound_only"><?php echo stripslashes($row['fm_subject']); ?> </span><?php e__('Delete'); ?></a>
         </td>
    </tr>
    <?php
    }

    if ($i == 0){
        echo '<tr><td colspan="5" class="empty_table"><span>'.__('No Data').'</span></td></tr>';
    }
    ?>
    </tbody>
    </table>
</div>

<?php echo get_paging(GML_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>

<?php
include_once (GML_ADMIN_PATH.'/admin.tail.php');
?>
