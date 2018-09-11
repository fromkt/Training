<?php
$sub_menu = '100310';
include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$gml['title'] = __('Manage pop-up layers');   //팝업레이어 관리
include_once (GML_ADMIN_PATH.'/admin.head.php');

$sql_common = " from {$gml['new_win_table']} ";

// 테이블의 전체 레코드수만 얻음
$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$sql = "select * $sql_common order by nw_id desc ";
$result = sql_query($sql);
?>

<div class="local_ov01 local_ov"><span class="btn_ov01"><span class="ov_txt"><?php e__('All'); ?> </span><span class="ov_num">  <?php echo $total_count; ?> <?php e__('Count'); ?></span></span></div>


<div class="btn_fixed_top ">
    <a href="./newwinform.php" class="btn btn_01"><?php e__('Add'); ?></a>
</div>

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $gml['title']; ?> <?php e__('List'); ?></caption>
    <thead>
    <tr>
        <th scope="col" rowspan="2"><?php e__('Number'); ?></th>
        <th scope="col" rowspan="2"><?php e__('Title'); ?></th>
        <th scope="col" class="m_no"><?php e__('Is_Mobile'); ?></th>
        <th scope="col"><?php e__('Start time'); ?></th>
        <th scope="col" class="m_no">Left</th>
        <th scope="col" class="m_no">Width</th>
        <th scope="col" rowspan="2"><?php e__('Manage'); ?></th>
    </tr>
    <tr>
        <th scope="col" class="m_no"><?php e__('Time'); ?></th>
        <th scope="col"><?php e__('End time'); ?></th>
        <th scope="col" class="m_no">Top</th>
        <th scope="col" class="m_no">Height</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $bg = 'bg'.($i%2);

        switch($row['nw_device']) {
            case 'pc':
                $nw_device = 'PC';
                break;
            case 'mobile':
                $nw_device = __('MOBILE');
                break;
            default:
                $nw_device = __('ALL');
                break;
        }
    ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_num_s" rowspan="2"><?php echo $row['nw_id']; ?></td>
        <td class="td_left" rowspan="2"><?php echo $row['nw_subject']; ?></td>
        <td class="td_device m_no"><?php echo $nw_device; ?></td>
        <td class="td_datetime"><?php echo substr($row['nw_begin_time'],2,14); ?></td>
        <td class="td_num m_no"><?php echo $row['nw_left']; ?>px</td>
        <td class="td_num m_no"><?php echo $row['nw_width']; ?>px</td>
        <td class="td_mng td_mng_s" rowspan="2">
            <a href="./newwinform.php?w=u&amp;nw_id=<?php echo $row['nw_id']; ?>" class="btn_03"><span class="sound_only"><?php echo $row['nw_subject']; ?> </span><?php e__('Edit'); ?></a><br>
            <a href="./newwinformupdate.php?w=d&amp;nw_id=<?php echo $row['nw_id']; ?>" onclick="return delete_confirm(this);" class="btn_02"><span class="sound_only"><?php echo $row['nw_subject']; ?> </span><?php e__('Delete'); ?></a>
         </td>
    </tr>
    <tr class="<?php echo $bg; ?>">
        <td class="td_num m_no"><?php echo $row['nw_disable_hours']; ?><?php e__('Hours'); ?></td>
        <td class="td_datetime"><?php echo substr($row['nw_end_time'],2,14); ?></td>
        <td class="td_num m_no"><?php echo $row['nw_top']; ?>px</td>
        <td class="td_num m_no"><?php echo $row['nw_height']; ?>px</td>
    </tr>
    <?php
    }

    if ($i == 0) {
        echo '<tr><td colspan="11" class="empty_table">'.__('No Data').'</td></tr>';
    }
    ?>
    </tbody>
    </table>
</div>


<?php
include_once (GML_ADMIN_PATH.'/admin.tail.php');
?>
