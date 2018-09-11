<?php
include_once("_common.php");

function make_mp3()
{
    global $config, $gml;

    $number = get_session("ss_captcha_key");

    if ($number == "") return;
    if ($number == get_session("ss_captcha_save")) return;

    $mp3s = array();
    for($i=0;$i<strlen($number);$i++){

        $mp3_lang_file = GML_CAPTCHA_PATH.'/mp3/'.$config['cf_captcha_mp3'].'/'.$gml['lang'].'/'.$number[$i].'.mp3';

        if( file_exists( $mp3_lang_file ) ){
            $file = $mp3_lang_file;
        } else {
            $file = GML_CAPTCHA_PATH.'/mp3/'.$config['cf_captcha_mp3'].'/'.$number[$i].'.mp3';
        }
        $mp3s[] = $file;
    }

    $ip = sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
    $mp3_file = 'data/cache/kcaptcha-'.$ip.'_'.GML_SERVER_TIME.'.mp3';

    $contents = '';
    foreach ($mp3s as $mp3) {
        $contents .= file_get_contents($mp3);
    }

    file_put_contents(GML_PATH.'/'.$mp3_file, $contents);

    // 지난 캡챠 파일 삭제
    if (rand(0,99) == 0) {
        foreach (glob(GML_PATH.'/data/cache/kcaptcha-*.mp3') as $file) {
            if (filemtime($file) + 86400 < GML_SERVER_TIME) {
                @unlink($file);
            }
        }
    }

    set_session("ss_captcha_save", $number);

    return GML_URL.'/'.$mp3_file;
}

echo make_mp3();
?>