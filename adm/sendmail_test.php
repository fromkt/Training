<?php
$sub_menu = '100300';
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

if (!$config['cf_email_use'])
    alert(__('You must check \'Enable mail sending\' in Default Preferences to send mail.'));    //환경설정에서 '메일발송 사용'에 체크하셔야 메일을 발송할 수 있습니다.

include_once(GML_LIB_PATH.'/mailer.lib.php');

$gml['title'] = __('Mail Test');   //메일 테스트
include_once('./admin.head.php');

if (isset($_POST['email'])) {
    $_POST['email'] = strip_tags($_POST['email']);
    $email = explode(',', $_POST['email']);

    $real_email = array();

    for ($i=0; $i<count($email); $i++){

        if (!preg_match("/([0-9a-zA-Z_-]+)@([0-9a-zA-Z_-]+)\.([0-9a-zA-Z_-]+)/", $email[$i])) continue;
        
        $real_email[] = $email[$i];
        mailer($config['cf_admin_email_name'], $config['cf_admin_email'], trim($email[$i]), __('[Check Mail] Title'), '<div style="font-size:9pt;">'.__('[Check Mail] Contents').'<p>'.__('If you see this right, there is nothing wrong with the outgoing mail server.').'</p><p>'.GML_TIME_YMDHIS.'</p><p>'.__('This email address will not be answered.').'</p></div>', 1);
    }

    if( $real_email ){
        echo '<section>';
        echo '<h2>'.__('Result Message').'</h2>';
        echo '<div class="local_desc01 local_desc"><p>';
        echo sprintf(__('Test mail has been sent to the following %s e-mail addresses.'), count($real_email));
        echo '</p></div>';
        echo '<ul>';
        for ($i=0;$i<count($real_email);$i++) {
            echo '<li>'.$real_email[$i].'</li>';
        }
        echo '</ul>';
        echo '<div class="local_desc02 local_desc"><p>';
        echo __('Please confirm that the test email has arrived at the address.').'<br>';
        echo __('If you do not receive a test email, try sending it to the email address of a wider range of accounts.').'<br>';
        echo __('If none of your mail arrives, you are likely to be out of the mail server(sendmail server), so please contact your web server administrator.').'<br>';
        echo '</p></div>';
        echo '</section>';
    }
}
?>

<section>
    <h2><?php e__('Send Test Mail'); //테스트 메일 발송 ?></h2>
    <div class="local_desc">
        <p>
            <?php e__('You can verify that the mail server is functioning normally.'); //메일서버가 정상적으로 동작 중인지 확인할 수 있습니다. ?><br>
            <?php e__('Enter the email address you would like to send a test email to at the address box below, and we will send a test email under the heading [Check Mail].'); //아래 입력칸에 테스트 메일을 발송하실 메일 주소를 입력하시면, [메일검사] 라는 제목으로 테스트 메일을 발송합니다. ?><br>
        </p>
    </div>
    <form name="fsendmailtest" method="post">
    <fieldset id="fsendmailtest">
        <legend><?php e__('Send Test Mail'); //테스트 메일 발송 ?></legend>
        <label for="email" class="lb_block"><?php e__('Incoming mail address');  //받는 메일주소 ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label>
        <input type="text" name="email" value="<?php echo $member['mb_email'] ?>" id="email" required class="required email frm_input frm_input1 m_full_input" size="50">
        <input type="submit" value="<?php e__('Send'); ?>" class=" btn_04 btn">
    </fieldset>
    </form>
    <div class="local_desc02 local_desc">
        <p>
            <?php e__('If the test mail does not arrive with the content of [Check Mail], there may be a problem with the outgoing mail server or the incoming mail server.'); ?><br>
            <?php e__('If you would like a more accurate test, please send a test email to several places.'); ?><br>
        </p>
    </div>
</section>

<?php
include_once('./admin.tail.php');
?>
