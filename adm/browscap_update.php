<?php
ini_set('memory_limit', '-1');

$sub_menu = "100510";
include_once('./_common.php');

// clean the output buffer
ob_end_clean();

if(!(version_compare(phpversion(), '5.3.0', '>=') && defined('GML_BROWSCAP_USE') && GML_BROWSCAP_USE))
    die(__('Function unavailable.'));

if ($is_admin != 'super')
    die(__('Only the Super Admin can access it.'));

include_once(GML_PLUGIN_PATH.'/browscap/Browscap.php');

$browscap = new phpbrowscap\Browscap(GML_DATA_PATH.'/cache');
$browscap->updateMethod = 'cURL';
$browscap->cacheFilename = 'browscap_cache.php';
$browscap->updateCache();

die('');
?>