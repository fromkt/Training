<?php
$sub_menu = "200800";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$gml['title'] = __('Annual user count');
include_once('./visit.sub.php');

$colspan = 4;

$max = 0;
$sum_count = 0;
$sql = " select SUBSTRING(vs_date,1,4) as vs_year, SUM(vs_count) as cnt
            from {$gml['visit_sum_table']}
            where vs_date between '{$fr_date}' and '{$to_date}'
            group by vs_year
            order by vs_year desc ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++) {
    $arr[$row['vs_year']] = $row['cnt'];

    if ($row['cnt'] > $max) $max = $row['cnt'];

    $sum_count += $row['cnt'];
}
?>

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $gml['title']; ?> <?php e__('List'); ?></caption>
    <thead>
    <tr>
        <th scope="col"><?php e__('Year'); ?></th>
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
    $i = 0;
    $k = 0;
    $save_count = -1;
    $tot_count = 0;
    if (count($arr)) {
        foreach ($arr as $key=>$value) {
            $count = $value;

            $rate = ($count / $sum_count * 100);
            $s_rate = number_format($rate, 1);

            $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>">
        <td class="td_category"><a href="./visit_month.php?fr_date=<?php echo $key ?>-01-01&amp;to_date=<?php echo $key ?>-12-31"><?php echo $key ?></a></td>
        <td>
            <div class="visit_bar">
                <span style="width:<?php echo $s_rate ?>%"></span>
            </div>
        </td>
        <td class="td_num_c3"><?php echo number_format($value) ?></td>
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
