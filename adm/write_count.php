<?php
$sub_menu = '300820';
include_once('./_common.php');

check_demo();

auth_check($auth[$sub_menu], 'r');

// http://www.jqplot.com/
add_stylesheet('<link rel="stylesheet" href="'.GML_PLUGIN_URL.'/jqplot/jquery.jqplot.css">', 0);
add_javascript('<script src="'.GML_PLUGIN_URL.'/jqplot/jquery.jqplot.js"></script>', 0);
add_javascript('<script src="'.GML_PLUGIN_URL.'/jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>', 0);
add_javascript('<script src="'.GML_PLUGIN_URL.'/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>', 0);
add_javascript('<script src="'.GML_PLUGIN_URL.'/jqplot/plugins/jqplot.pointLabels.min.js"></script>', 0);
add_javascript('<!--[if lt IE 9]><script src="'.GML_PLUGIN_URL.'/jqplot/excanvas.js"></script><![endif]-->', 0);

if (!($graph == 'line' || $graph == 'bar'))
    $graph = 'line';

if ($graph == 'bar') {
    // 바 타입으로 사용하는 코드입니다.
    add_javascript('<script src="'.GML_PLUGIN_URL.'/jqplot/jqplot.barRenderer.min.js"></script>', 0);
    add_javascript('<script src="'.GML_PLUGIN_URL.'/jqplot/jqplot.categoryAxisRenderer.min.js"></script>', 0);
    add_javascript('<script src="'.GML_PLUGIN_URL.'/jqplot/jqplot.pointLabels.min.js"></script>', 0);
}

$gml['title'] = __('Status of boards');
include_once ('./admin.head.php');

$period_array = array(
    __('Today')                     =>  array(__('Hours'), 0),
    __('Yesterday')                 =>  array(__('Hours'), 0),
    sprintf(n__('%s day ago', '%s days ago', 7), 7)    =>  array(__('Days'), 7),
    sprintf(n__('%s day ago', '%s days ago', 14), 14)   =>  array(__('Days'), 14),
    sprintf(n__('%s day ago', '%s days ago', 30), 30)   =>  array(__('Days'), 30),
    sprintf(n__('%s month ago', '%s months ago', 3), 3)  =>  array(__('Weeks'), 90),
    sprintf(n__('%s month ago', '%s months ago', 6), 6)  =>  array(__('Weeks'), 180),
    sprintf(n__('%s year ago', '%s years ago', 1), 1)    =>  array(__('months'), 365),
    sprintf(n__('%s year ago', '%s years ago', 2), 2)   =>  array(__('months'), 365*2),
    sprintf(n__('%s year ago', '%s years ago', 3), 3)   =>  array(__('months'), 365*3),
    sprintf(n__('%s year ago', '%s years ago', 5), 5)   =>  array(__('years'), 365*5),
    sprintf(n__('%s year ago', '%s years ago', 10), 10)   => array(__('years'), 365*10),
);

$is_period = false;
foreach($period_array as $key=>$value) {
    if ($key == $period) {
        $is_period = true;
        break;
    }
}
if (!$is_period)
    $period = __('Today');

$day = $period_array[$period][0];

$today = date('Y-m-d', GML_SERVER_TIME);
$yesterday = date('Y-m-d', GML_SERVER_TIME - 86400);

if ($period == __('Today')) {
    $from = $today;
    $to = $from;
} else if ($period == __('Yesterday')) {
    $from = $yesterday;
    $to = $from;
} else if ($period == __('Tomorrow')) {
    $from = date('Y-m-d', GML_SERVER_TIME + (86400 * 2));
    $to = $from;
} else {
    $from = date('Y-m-d', GML_SERVER_TIME - (86400 * $period_array[$period][1]));
    $to = $yesterday;
}

$sql_bo_table = '';
if ($bo_table)
    $sql_bo_table = "and bo_table = '$bo_table'";

switch ($day) {
    case __('Hours') :
        $sql = " select substr(bn_datetime,6,8) as hours, sum(if(wr_id=wr_parent,1,0)) as wcount, sum(if(wr_id=wr_parent,0,1)) as ccount from {$gml['board_new_table']} where substr(bn_datetime,1,10) between '$from' and '$to' {$sql_bo_table} group by hours order by bn_datetime ";
        $result = sql_query($sql);
        for ($i=0; $row=sql_fetch_array($result); $i++) {
            // 월-일 시간
            $line1[] = "['".substr($row['hours'],0,8)."',".$row['wcount'].']';
            $line2[] = "['".substr($row['hours'],0,8)."',".$row['ccount'].']';
        }
        break;
    case __('Days') :
        $sql  = " select substr(bn_datetime,1,10) as days, sum(if(wr_id=wr_parent,1,0)) as wcount, sum(if(wr_id=wr_parent,0,1)) as ccount from {$gml['board_new_table']} where substr(bn_datetime,1,10) between '$from' and '$to' {$sql_bo_table} group by days order by bn_datetime ";
        $result = sql_query($sql);
        for ($i=0; $row=sql_fetch_array($result); $i++) {
            // 월-일
            $line1[] = "['".substr($row['days'],5,5)."',".$row['wcount'].']';
            $line2[] = "['".substr($row['days'],5,5)."',".$row['ccount'].']';
        }
        break;
    case __('Weeks') :
        $sql  = " select concat(substr(bn_datetime,1,4), '-', weekofyear(bn_datetime)) as weeks, sum(if(wr_id=wr_parent,1,0)) as wcount, sum(if(wr_id=wr_parent,0,1)) as ccount from {$gml['board_new_table']} where substr(bn_datetime,1,10) between '$from' and '$to' {$sql_bo_table} group by weeks order by bn_datetime ";
        $result = sql_query($sql);
        for ($i=0; $row=sql_fetch_array($result); $i++) {
            // 올해의 몇주로 보여주면 바로 확인이 안되므로 주를 날짜로 바꾼다.
            // 년-월-일
            list($lyear, $lweek) = explode('-', $row['weeks']);
            $date = date('y-m-d', strtotime($lyear.'W'.str_pad($lweek, 2, '0', STR_PAD_LEFT)));
            $line1[] = "['".$date."',".$row['wcount'].']';
            $line2[] = "['".$date."',".$row['ccount'].']';
        }
        break;
    case __('Months') :
        $sql  = " select substr(bn_datetime,1,7) as months, sum(if(wr_id=wr_parent,1,0)) as wcount, sum(if(wr_id=wr_parent,0,1)) as ccount from {$gml['board_new_table']} where substr(bn_datetime,1,10) between '$from' and '$to' {$sql_bo_table} group by months order by bn_datetime ";
        $result = sql_query($sql);
        for ($i=0; $row=sql_fetch_array($result); $i++) {
            // 년-월
            $line1[] = "['".substr($row['months'],2,5)."',".$row['wcount'].']';
            $line2[] = "['".substr($row['months'],2,5)."',".$row['ccount'].']';
        }
        break;
    case __('Years') :
        $sql  = " select substr(bn_datetime,1,4) as years, sum(if(wr_id=wr_parent,1,0)) as wcount, sum(if(wr_id=wr_parent,0,1)) as ccount from {$gml['board_new_table']} where substr(bn_datetime,1,10) between '$from' and '$to' {$sql_bo_table} group by years order by bn_datetime ";
        $result = sql_query($sql);
        for ($i=0; $row=sql_fetch_array($result); $i++) {
            // 년(4자리)
            $line1[] = "['".substr($row['years'],0,4)."',".$row['wcount'].']';
            $line2[] = "['".substr($row['years'],0,4)."',".$row['ccount'].']';
        }
        break;
}
?>
<div id="wr_cont">
    <form class="local_sch03">
    <select name="bo_table">
    <option value=""><?php e__('All board'); ?></option>
    <?php
    $sql = " select bo_table, bo_subject from {$gml['board_table']} order by bo_count_write desc ";
    $result = sql_query($sql);
    for($i=0; $row=sql_fetch_array($result); $i++) {
        echo "<option value=\"{$row['bo_table']}\"";
        if ($bo_table == $row['bo_table'])
            echo ' selected="selected"';
        echo ">{$row['bo_subject']}</option>\n";
    }
    ?>
    </select>

    <select name="period">
    <?php
    foreach($period_array as $key=>$value) {
        echo "<option value=\"{$key}\"";
        if ($key == $period)
            echo " selected=\"selected\"";
        echo ">{$key}</option>\n";
    }
    ?>
    </select>

    <select name="graph">
    <option value="line" <?php echo ($graph == 'line' ? 'selected="selected"' : ''); ?>><?php e__('Line graph'); ?></option>
    <option value="bar" <?php echo ($graph == 'bar' ? 'selected="selected"' : ''); ?>><?php e__('Bar graph'); ?></option>
    </select>

    <input type="submit" class="btn_submit" value="<?php e__('Save'); ?>">
    </form>
    <ul id="grp_color">
        <li><span></span><?php e__('Number of Posts'); ?></li>
        <li class="color2"><span></span><?php e__('Number of Comments'); ?></li>
    </ul>
</div>
<div id="chart_wr">
    <?php
    if (empty($line1) || empty($line2)) {
        echo "<h5>".__('No Data')."</h5>\n";
    } else {
    ?>
    <div id="chart1" style="height:500px; width:100%;"></div>

    <script>
    $(document).ready(function(){
        var line1 = [<?php echo implode($line1, ','); ?>];
        var line2 = [<?php echo implode($line2, ','); ?>];
        var plot1 = $.jqplot ('chart1', [line1, line2], {
                seriesDefaults: {
                    <?php if ($graph == 'bar') { ?>
                    renderer:$.jqplot.BarRenderer,
                    <?php } ?>
                    pointLabels: { show: true }
                },
                axes:{
                    xaxis: {
                        renderer: $.jqplot.CategoryAxisRenderer,
                        label: '<?php echo $day; ?>',
                        pad:0,
                        max:23
                    },
                    yaxis: {
                        label: "<?php e__('Number of Posts'); ?>",
                        min: 0

                    }
                }
            });
    });
    </script>

    <?php } //end if?>
</div>

<?php
include_once ('./admin.tail.php');
?>