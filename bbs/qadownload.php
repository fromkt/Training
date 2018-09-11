<?php
include_once('./_common.php');

// clean the output buffer
ob_end_clean();

$no = (int)$no;

// 쿠키에 저장된 ID값과 넘어온 ID값을 비교하여 같지 않을 경우 오류 발생
// 다른곳에서 링크 거는것을 방지하기 위한 코드
if (!get_session('ss_qa_view_'.$qa_id))
    alert(__('Its a wrong access.'));

$sql = " select qa_subject, qa_file{$no}, qa_source{$no} from {$gml['qa_content_table']} where qa_id = '$qa_id' ";
$file = sql_fetch($sql);
if (!$file['qa_file'.$no])
    alert_close(__('File information does not exist.'));

if($is_guest) {
    alert(__('You do not have download privileges.').'\\n'.__('If you are a member, log in and try it.'), GML_BBS_URL.'/login.php?url='.urlencode(GML_BBS_URL.'/qaview.php?qa_id='.$qa_id));
}

$filepath = GML_DATA_PATH.'/qa/'.$file['qa_file'.$no];
$filepath = addslashes($filepath);
if (!is_file($filepath) || !file_exists($filepath))
    alert(__('File does not exist.'));

$gml['title'] = __('Download').' &gt; '.conv_subject($file['qa_subject'], 255);

$original = urlencode($file['qa_source'.$no]);

if(preg_match("/msie/i", $_SERVER['HTTP_USER_AGENT']) && preg_match("/5\.5/", $_SERVER['HTTP_USER_AGENT'])) {
    header("content-type: doesn/matter");
    header("content-length: ".filesize("$filepath"));
    header("content-disposition: attachment; filename=\"$original\"");
    header("content-transfer-encoding: binary");
} else {
    header("content-type: file/unknown");
    header("content-length: ".filesize("$filepath"));
    header("content-disposition: attachment; filename=\"$original\"");
    header("content-description: php generated data");
}
header("pragma: no-cache");
header("expires: 0");
flush();

$fp = fopen($filepath, 'rb');

// 4.00 대체
// 서버부하를 줄이려면 print 나 echo 또는 while 문을 이용한 방법보다는 이방법이...
//if (!fpassthru($fp)) {
//    fclose($fp);
//}

$download_rate = 10;

while(!feof($fp)) {
    //echo fread($fp, 100*1024);
    /*
    echo fread($fp, 100*1024);
    flush();
    */

    print fread($fp, round($download_rate * 1024));
    flush();
    usleep(1000);
}
fclose ($fp);
flush();
?>
