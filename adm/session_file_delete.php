<?php
$sub_menu = "100800";
include_once("./_common.php");

if ($is_admin != "super")
    alert(__('Only the Super administrator can access it.'), GML_URL);

$gml['title'] = __('Delete Session File Batch');  //세션파일 일괄삭제;
include_once("./admin.head.php");
?>

<div class="local_desc02 local_desc">
    <p>
        <?php e__('Do not stop the program from running until you receive a completion message.');   //완료 메세지가 나오기 전에 프로그램의 실행을 중지하지 마십시오. ?>
    </p>
</div>

    <?php
    flush();

    $list_tag_st = "";
    $list_tag_end = "";
    if (!$dir=@opendir(GML_DATA_PATH.'/session')) {
      echo '<p>'.__('Failed to open session directory.').'</p>';
    } else {
        $list_tag_st = "<ul class=\"del_ul\">\n<li>".__('Completed')."</li>\n";
        $list_tag_end = "</ul>\n";
    }

    $cnt=0;
    echo $list_tag_st;
    while($file=readdir($dir)) {

        if (!strstr($file,'sess_')) continue;
        if (strpos($file,'sess_')!=0) continue;

        $session_file = GML_DATA_PATH.'/session/'.$file;

        if (!$atime=@fileatime($session_file)) {
            continue;
        }
        if (time() > $atime + (3600 * 6)) {  // 지난시간을 초로 계산해서 적어주시면 됩니다. default : 6시간전
            $cnt++;
            $return = unlink($session_file);
            //echo "<script>document.getElementById('ct').innerHTML += '{$session_file}<br/>';</script>\n";
            echo "<li>{$session_file}</li>\n";

            flush();

            if ($cnt%10==0)
                //echo "<script>document.getElementById('ct').innerHTML = '';</script>\n";
                echo "\n";
        }
    }
    echo $list_tag_end;
    echo '<div class="local_desc01 local_desc"><p><strong>'.sprintf(__('%s session data has been deleted.'), $cnt).'</strong><br>'.__('You may complete the program.').'</p></div>'.PHP_EOL;
?>

<?php
include_once("./admin.tail.php");
?>
