<?php
if (!defined('_GNUBOARD_')) exit;

// 인기검색어 출력
// $skin_dir : 스킨 디렉토리
// $pop_cnt : 검색어 몇개
// $date_cnt : 몇일 동안
function popular($skin_dir='basic', $pop_cnt=7, $date_cnt=3)
{
    global $config, $gml;

    if (!$skin_dir) $skin_dir = 'basic';

    $date_gap = date("Y-m-d", GML_SERVER_TIME - ($date_cnt * 86400));
    $sql = " select pp_word, count(*) as cnt from {$gml['popular_table']} where pp_date between '$date_gap' and '".GML_TIME_YMD."' group by pp_word order by cnt desc, pp_word limit 0, $pop_cnt ";
    $result = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $list[$i] = $row;
        // 스크립트등의 실행금지
        //$list[$i]['pp_word'] = get_text($list[$i]['pp_word']);
    }

    if (GML_IS_MOBILE) {
        $popular_skin_path = GML_THEME_MOBILE_PATH.'/'.GML_SKIN_DIR.'/popular/'.$skin_dir;
        if(!is_dir($popular_skin_path))
            $popular_skin_path = GML_THEME_PATH.'/'.GML_SKIN_DIR.'/popular/'.$skin_dir;
        $popular_skin_url = str_replace(GML_PATH, GML_URL, $popular_skin_path);
    } else {
        $popular_skin_path = GML_THEME_PATH.'/'.GML_SKIN_DIR.'/popular/'.$skin_dir;
        $popular_skin_url = str_replace(GML_PATH, GML_URL,$popular_skin_path);
    }

    // load language for l10n
    bind_lang_domain( 'default', get_path_lang_dir('skin', $popular_skin_path.'/'.GML_LANG_DIR) );

    ob_start();
    include_once ($popular_skin_path.'/popular.skin.php');
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}
?>
