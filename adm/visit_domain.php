<?php
$sub_menu = "200800";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$gml['title'] = __('Total users by domain');
include_once('./visit.sub.php');

$colspan = 5;

$max = 0;
$sum_count = 0;
$sql = " select * from {$gml['visit_table']}
            where vi_date between '{$fr_date}' and '{$to_date}' ";
$result = sql_query($sql);
while ($row=sql_fetch_array($result)) {
    $str = $row['vi_referer'];
    preg_match("/^http[s]*:\/\/([\.\-\_0-9a-zA-Z]*)\//", $str, $match);
    $s = $match[1];
    $s = preg_replace("/^(www\.|search\.|dirsearch\.|dir\.search\.|dir\.|kr\.search\.|myhome\.)(.*)/", "\\2", $s);
    $arr[$s]++;

    if ($arr[$s] > $max) $max = $arr[$s];

    $sum_count++;
}
?>

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $gml['title']; ?> <?php e__('List'); ?></caption>
    <thead>
    <tr>
        <th scope="col"><?php e__('Ranking'); ?></th>
        <th scope="col"><?php e__('Connection domain'); ?></th>
        <th scope="col"><?php e__('Graph'); ?></th>
        <th scope="col"><?php e__('Number of users'); ?></th>
        <th scope="col"><?php e__('Ratio(%)'); ?></th>
    </tr>
    </thead>
    <tfoot>
    <tr>
        <td colspan="3"><?php e__('Sum'); ?></td>
        <td><strong><?php echo $sum_count ?></strong></td>
        <td>100%</td>
    </tr>
    </tfoot>
    <tbody>
    <?php
    $i = 0;
    $k = 0;
    $save_count = -1;
    $tot_count = 0;
    if (isset($arr) && count($arr)) {
        arsort($arr);
        foreach ($arr as $key=>$value) {
            $count = $arr[$key];
            if ($save_count != $count) {
                $i++;
                $no = $i;
                $save_count = $count;
            } else {
                $no = '';
            }

            if (!$key) {
                $link = '';
                $link2 = '';
                $key = __('Directly');
            } else {
                $link = '<a href="./visit_list.php?'.$qstr.'&amp;domain='.$key.'">';
                $link2 = '</a>';
            }

            $rate = ($count / $sum_count * 100);
            $s_rate = number_format($rate, 1);

            $bg = 'bg'.($i%2);
    ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_num"><?php echo $no ?></td>
        <td class="td_category td_left"><?php echo $link ?><?php echo $key ?><?php echo $link2 ?></td>
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
