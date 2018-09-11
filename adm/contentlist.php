<?php
$sub_menu = '300600';
include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$gml['title'] = __('Manage Content');
include_once (GML_ADMIN_PATH.'/admin.head.php');

$sql_common = " from {$gml['content_table']} ";

// 테이블의 전체 레코드수만 얻음
$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = "select * $sql_common order by co_id limit $from_record, {$config['cf_page_rows']} ";
$result = sql_query($sql);
?>

<div class="local_ov01 local_ov">
    <?php if ($page > 1) {?><a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>"><?php e__('All'); ?></a><?php } ?>
    <span class="btn_ov01"><span class="ov_txt"><?php e__('All contents'); ?> </span><span class="ov_num"> <?php echo $total_count; ?> <?php e__('Count'); ?></span></span>
</div>

<div class="btn_fixed_top">
    <a href="./contentform.php" class="btn btn_01"><?php e__('Add Content'); ?></a>
</div>

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $gml['title']; ?> <?php e__('List'); ?></caption>
    <thead>
    <tr>
        <th scope="col">ID</th>
        <th scope="col"><?php e__('Subject'); ?></th>
        <th scope="col"><?php e__('Edit'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php for ($i=0; $row=sql_fetch_array($result); $i++) {
        $bg = 'bg'.($i%2);

        if($config['cf_use_multi_lang_data']) {
            $row = get_content_by_lang($row);
        }
    ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_id"><?php echo $row['co_id']; ?></td>
        <td class="td_left"><?php echo htmlspecialchars2($row['co_subject']); ?></td>
        <td class="td_mng">
            <a href="./contentform.php?w=u&amp;co_id=<?php echo $row['co_id']; ?>" class="btn_03"><span class="sound_only"><?php echo htmlspecialchars2($row['co_subject']); ?> </span><?php e__('Edit'); ?></a>
            <a href="<?php echo get_pretty_url('content', $row['co_id']) ?>" class="btn_02"><span class="sound_only"><?php echo htmlspecialchars2($row['co_subject']); ?> </span> <?php e__('View'); ?></a>
            <a href="./contentformupdate.php?w=d&amp;co_id=<?php echo $row['co_id']; ?>" onclick="return delete_confirm(this);" class="btn_02"><span class="sound_only"><?php echo htmlspecialchars2($row['co_subject']); ?> </span><?php e__('Delete'); ?></a>
        </td>
    </tr>
    <?php
    }
    if ($i == 0) {
        echo '<tr><td colspan="3" class="empty_table">'.__('No Data').'</td></tr>';
    }
    ?>
    </tbody>
    </table>
</div>

<?php echo get_paging(GML_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>

<?php
include_once (GML_ADMIN_PATH.'/admin.tail.php');
?>
