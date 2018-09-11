<?php
$sub_menu = "200300";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

$html_title = __('Send Member Email');

check_demo();

check_admin_token();

include_once('./admin.head.php');
include_once(GML_LIB_PATH.'/mailer.lib.php');

$countgap = 10; // 몇건씩 보낼지 설정
$maxscreen = 500; // 몇건씩 화면에 보여줄건지?
$sleepsec = 200;  // 천분의 몇초간 쉴지 설정

echo "<span style='font-size:9pt;'>";
echo "<p>".__('Sending mail')." ...<p> ".sprintf(__("Don't stop in the middle of the word %s"), "<font color=crimson><b>".__('[END]')."</b></font>" )."<p>";
echo "</span>";
?>

<span id="cont"></span>

<?php
include_once('./admin.tail.php');
?>

<?php
flush();
ob_flush();

$ma_id = trim($_POST['ma_id']);
$select_member_list = trim($_POST['ma_list']);

//print_r2($_POST); EXIT;
$member_list = explode("\n", conv_unescape_nl($select_member_list));

// 메일내용 가져오기
$sql = "select ma_subject, ma_content from {$gml['mail_table']} where ma_id = '$ma_id' ";
$ma = sql_fetch($sql);

$subject = $ma['ma_subject'];

$cnt = 0;
for ($i=0; $i<count($member_list); $i++)
{
    list($to_email, $mb_id, $name, $nick, $datetime) = explode("||", trim($member_list[$i]));

    $sw = preg_match("/[0-9a-zA-Z_]+(\.[0-9a-zA-Z_]+)*@[0-9a-zA-Z_]+(\.[0-9a-zA-Z_]+)*/", $to_email);
    // 올바른 메일 주소만
    if ($sw == true)
    {
        $cnt++;

        $mb_md5 = md5($mb_id.$to_email.$datetime);

        $content = $ma['ma_content'];
        $content = preg_replace("/{NAME}/", $name, $content);
        $content = preg_replace("/{NICKNAME}/", $nick, $content);
        $content = preg_replace("/{MEMBER_ID}/", $mb_id, $content);
        $content = preg_replace("/{EMAIL}/", $to_email, $content);

        $content = $content . "<hr size=0><p><span style='font-size:9pt;'>".sprintf(__('If you do not wish to receive further information, please %s.'), "[<a href='".GML_BBS_URL."/email_stop.php?mb_id={$mb_id}&amp;mb_md5={$mb_md5}' target='_blank'>".__('Unsubscribe')."</a>]")."</span></p>";

        mailer($config['cf_admin_email_name'], $config['cf_admin_email'], $to_email, $subject, $content, 1);

        echo "<script> document.all.cont.innerHTML += '$cnt. $to_email ($mb_id : $name)<br>'; </script>\n";
        //echo "+";
        flush();
        ob_flush();
        ob_end_flush();
        usleep($sleepsec);
        if ($cnt % $countgap == 0)
        {
            echo "<script> document.all.cont.innerHTML += '<br>'; document.body.scrollTop += 1000; </script>\n";
        }

        // 화면을 지운다... 부하를 줄임
        if ($cnt % $maxscreen == 0)
            echo "<script> document.all.cont.innerHTML = ''; document.body.scrollTop += 1000; </script>\n";
    }
}
?>
<?php
get_localize_script('mail_update',
array(
'send_msg'=>sprintf(__('Send %s totals'), number_format($cnt)),  // 총 %s 건 발송
'end_msg'=>__('[END]'),    // [끝]
),
true);
?>
<script> document.all.cont.innerHTML += "<br><br>"+mail_update.send_msg+"<br><br><font color=crimson><b>"+mail_update.end_msg+"</b></font>"; document.body.scrollTop += 1000; </script>
