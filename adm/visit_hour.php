<?php
$sub_menu = "200800";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$gml['title'] = __('User count by hour');
include_once('./visit.sub.php');

$colspan = 4;

$max = 0;
$sum_count = 0;
$sql = " select SUBSTRING(vi_time,1,2) as vi_hour, count(vi_id) as cnt
            from {$gml['visit_table']}
            where vi_date between '{$fr_date}' and '{$to_date}'
            group by vi_hour
            order by vi_hour ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++) {
    $arr[$row['vi_hour']] = $row['cnt'];

    if ($row['cnt'] > $max) $max = $row['cnt'];

    $sum_count += $row['cnt'];
}
?>

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $gml['title']; ?> <?php e__('List'); ?></caption>
    <thead>
    <tr>
        <th scope="col"><?php e__('Time'); ?></th>
        <th scope="col"><?php e__('Graph'); ?></th>
        <th scope="col"><?php e__('Number of users'); ?></th>
        <th scope="col"><?php e__('Ratio(%)'); ?></th>
    </tr>
    </thead>
    <tfoot>
    <tr>
        <td colspan="2"><?php e__('Sum'); ?></td>
        <td><strong><?php echo number_format($sum_count) ?></strong></td>
        <td>100%</td>
    </tr>
    </tfoot>
    <tbody>
    <?php
    $k = 0;
    if ($i) {
        for ($i=0; $i<24; $i++) {
            $hour = sprintf("%02d", $i);
            $count = (int)$arr[$hour];

            $rate = ($count / $sum_count * 100);
            $s_rate = number_format($rate, 1);

            $bg = 'bg'.($i%2);
    ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_category"><?php echo $hour ?></td>
        <td>
            <div class="visit_bar">
                <span style="width:<?php echo $s_rate ?>%"></span>
            </div>
        </td>
        <td class="td_num_c3"><?php echo number_format($count) ?></td>
        <td class="td_num"><?php echo $s_rate ?></td>
    </tr>
    <?php
        }
    } else {
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">'.__('No Data').'</td></tr>';
    }
    ?>
    </tbody>
    </table>
</div>

<?php
include_once('./admin.tail.php');
?>
