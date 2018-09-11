<?php
ini_set('memory_limit', '-1');
include_once('./_common.php');

// clean the output buffer
ob_end_clean();

if(!(version_compare(phpversion(), '5.3.0', '>=') && defined('GML_BROWSCAP_USE') && GML_BROWSCAP_USE))
    die(__('Function unavailable.'));

if($is_admin != 'super')
    die(__('Log in as a Super admin and run.'));

// browscap cache 파일 체크
if(!is_file(GML_DATA_PATH.'/cache/browscap_cache.php')) {
    echo '<p>'.__('Browscap information is missing. Please update your information and browscap to the link below.').'</p>'.PHP_EOL;
    echo '<p><a href="'.GML_ADMIN_URL.'/browscap.php">Browscap Update</a></p>'.PHP_EOL;
    exit;
}

include_once(GML_PLUGIN_PATH.'/browscap/Browscap.php');
$browscap = new phpbrowscap\Browscap(GML_DATA_PATH.'/cache');
$browscap->doAutoUpdate = false;
$browscap->cacheFilename = 'browscap_cache.php';

// 데이터 변환
$rows = preg_replace('#[^0-9]#', '', $_GET['rows']);
if(!$rows)
    $rows = 100;

$sql_common = " from {$gml['visit_table']} where vi_agent <> '' and ( vi_browser = '' or vi_os = '' or vi_device = '' ) ";
$sql_order  = " order by vi_id desc ";
$sql_limit  = " limit 0, $rows ";

$sql = " select count(vi_id) as cnt $sql_common ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$sql = " select vi_id, vi_agent, vi_browser, vi_os, vi_device
            $sql_common
            $sql_order
            $sql_limit ";
$result = sql_query($sql);

$cnt = 0;
for($i=0; $row=sql_fetch_array($result); $i++) {
    $info = $browscap->getBrowser($row['vi_agent']);

    $brow = $row['vi_browser'];
    if(!$brow)
        $brow = $info->Comment;

    $os = $row['vi_os'];
    if(!$os)
        $os = $info->Platform;

    $device = $row['vi_device'];
    if(!$device)
        $device = $info->Device_Type;

    $sql2 = " update {$gml['visit_table']}
                set vi_browser  = '$brow',
                    vi_os       = '$os',
                    vi_device   = '$device'
                where vi_id = '{$row['vi_id']}' ";
    sql_query($sql2);

    $cnt++;
}

if(($total_count - $cnt) == 0 || $total_count == 0)
    echo '<div class="check_processing"></div><p>'.__('Conversion complete').'</p>';
else
    echo '<p>'.sprintf(__('%s conversion complete ( %s totals )'), number_format($cnt), number_format($total_count)).'<br><br>'.__('Click the update button below to convert more connection logs.').'</p><button type="button" id="run_update">'.__('Update').'</button>';
?>