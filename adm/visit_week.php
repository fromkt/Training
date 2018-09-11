<?php
$sub_menu = "200800";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$gml['title'] = __('Daily user count');
include_once('./visit.sub.php');

$colspan = 4;
$weekday = array(p__('M', 'Monday'), p__('T', 'Tuesday'), p__('W', 'Wednesday'), p__('T', 'Thursday'), p__('F', 'Friday'), p__('S', 'Saturday'), p__('S', 'Sunday'));

$sum_count = 0;
$sql = " select WEEKDAY(vs_date) as weekday_date, SUM(vs_count) as cnt
            from {$gml['visit_sum_table']}
            where vs_date between '{$fr_date}' and '{$to_date}'
            group by weekday_date
            order by weekday_date ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++) {
    $arr[$row['weekday_date']] = $row['cnt'];

    $sum_count += $row['cnt'];
}
?>

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $gml['title']; ?> <?php e__('List'); ?></caption>
    <thead>
    <tr>
        <th scope="col"><?php ep__('Day', 'day of the week'); ?></th>
        <th scope="col"><?php e__('Graph'); ?></th>
        <th scope="col"><?php e__('Number of users'); ?></th>
        <th scope="col"><?php e__('Ratio(%)'); ?></th>
    </tr>
    </thead>
    <tfoot>
    <tr>
        <td colspan="2"><?php e__('Sum'); ?></td>
        <td><strong><?php echo $sum_count ?></strong></td>
        <td>100%</td>
    </tr>
    </tfoot>
    <tbody>
    <?php
    $k = 0;
    if ($i) {
        for ($i=0; $i<7; $i++) {
            $count = (int)$arr[$i];

            $rate = ($count / $sum_count * 100);
            $s_rate = number_format($rate, 1);

            $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>">
        <td class="td_category"><?php echo $weekday[$i] ?></td>
        <td>
            <div class="visit_bar">
                <span style="width:<?php echo $s_rate ?>%"></span>
            </div>
        </td>
        <td class="td_num_c3"><?php echo $count ?></td>
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
