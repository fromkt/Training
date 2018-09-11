<?php
if (!defined('_GNUBOARD_')) exit;

// 설문조사
function poll($skin_dir='basic', $po_id=false)
{
    global $config, $member, $gml, $is_admin;

    // 투표번호가 넘어오지 않았다면 가장 큰(최근에 등록한) 투표번호를 얻는다
    if (!$po_id) {
        $row = sql_fetch(" select MAX(po_id) as max_po_id from {$gml['poll_table']} ");
        $po_id = $row['max_po_id'];
    }

    if(!$po_id)
        return;

    if (GML_IS_MOBILE) {
        $poll_skin_path = GML_THEME_MOBILE_PATH.'/'.GML_SKIN_DIR.'/poll/'.$skin_dir;
        if(!is_dir($poll_skin_path))
            $poll_skin_path = GML_THEME_PATH.'/'.GML_SKIN_DIR.'/poll/'.$skin_dir;
        $poll_skin_url = str_replace(GML_PATH, GML_URL, $poll_skin_path);
    } else {
        $poll_skin_path = GML_THEME_PATH.'/'.GML_SKIN_DIR.'/poll/'.$skin_dir;
        $poll_skin_url = str_replace(GML_PATH, GML_URL, $poll_skin_path);
    }

    //설문조사 스킨 언어파일을 가져옵니다.
    bind_lang_domain( 'default', get_path_lang_dir('skin', $poll_skin_path.'/'.GML_LANG_DIR) );

    $po = get_poll_db($po_id);

    ob_start();
    include_once ($poll_skin_path.'/poll.skin.php');
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}
?>
