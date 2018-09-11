<?php
if (!defined('_GNUBOARD_')) exit;

// 현재 접속자수 출력
function connect($skin_dir='basic')
{
    global $config, $gml;

    // 회원, 방문객 카운트
    $sql = " select sum(IF(mb_id<>'',1,0)) as mb_cnt, count(*) as total_cnt from {$gml['login_table']}  where mb_id <> '{$config['cf_admin']}' ";
    $row = sql_fetch($sql);

    if (GML_IS_MOBILE) {
        $connect_skin_path = GML_THEME_MOBILE_PATH.'/'.GML_SKIN_DIR.'/connect/'.$skin_dir;
        if(!is_dir($connect_skin_path))
            $connect_skin_path = GML_THEME_PATH.'/'.GML_SKIN_DIR.'/connect/'.$skin_dir;
        $connect_skin_url = str_replace(GML_PATH, GML_URL, $connect_skin_path);
    } else {
        $connect_skin_path = GML_THEME_PATH.'/'.GML_SKIN_DIR.'/connect/'.$skin_dir;
        $connect_skin_url = str_replace(GML_PATH, GML_URL, $connect_skin_path);
    }

    ob_start();
    include_once ($connect_skin_path.'/connect.skin.php');
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}
?>
