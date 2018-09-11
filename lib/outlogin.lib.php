<?php
if (!defined('_GNUBOARD_')) exit;

// 외부로그인
function outlogin($skin_dir='basic')
{
    global $config, $member, $gml, $urlencode, $is_admin, $is_member;

    if (array_key_exists('mb_nick', $member)) {
        $nick  = get_text(cut_str($member['mb_nick'], $config['cf_cut_name']));
    }
    if (array_key_exists('mb_point', $member)) {
        $point = number_format($member['mb_point']);
    }

    if (GML_IS_MOBILE) {
        $outlogin_skin_path = GML_THEME_MOBILE_PATH.'/'.GML_SKIN_DIR.'/outlogin/'.$skin_dir;
        if(!is_dir($outlogin_skin_path))
            $outlogin_skin_path = GML_THEME_PATH.'/'.GML_SKIN_DIR.'/outlogin/'.$skin_dir;
        $outlogin_skin_url = str_replace(GML_PATH, GML_URL, $outlogin_skin_path);
    } else {
        $outlogin_skin_path = GML_THEME_PATH.'/'.GML_SKIN_DIR.'/outlogin/'.$skin_dir;
        $outlogin_skin_url = str_replace(GML_PATH, GML_URL, $outlogin_skin_path);
    }

    // 외부로그인 스킨 언어파일을 가져옵니다.
    bind_lang_domain( 'default', get_path_lang_dir('skin', $outlogin_skin_path.'/'.GML_LANG_DIR) );

    if ($is_member) {
        if( isset($member['mb_memo_cnt']) ){
            $memo_not_read = $member['mb_memo_cnt'];
        } else {
            $memo_not_read = get_memo_not_read($member['mb_id']);
        }

        if( isset($member['mb_scrap_cnt']) ){
            $scrap_cnt = $member['mb_scrap_cnt'];
        } else {
            $scrap_cnt = get_scrap_totals($member['mb_id']);
        }

        $is_auth = false;
        $sql = " select count(*) as cnt from {$gml['auth_table']} where mb_id = '{$member['mb_id']}' ";
        $row = sql_fetch($sql);
        if ($row['cnt'])
            $is_auth = true;

        $notice_cnt = $member['mb_notice_cnt'] ? : 0;
    }

    $outlogin_url        = login_url($urlencode);
    $outlogin_action_url = GML_HTTPS_BBS_URL.'/login_check.php';

    ob_start();
    if ($is_member) {
        include_once ($outlogin_skin_path.'/outlogin.skin.2.php');
    } else { // 로그인 전이라면
        // import the 'social login' language file.
        bind_lang_domain( 'default', get_path_lang_dir('skin', get_social_skin_path().'/'.GML_LANG_DIR) );
        include_once ($outlogin_skin_path.'/outlogin.skin.1.php');
    }
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}
?>
