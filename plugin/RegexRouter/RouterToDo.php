<?php

class RouterToDo {
    // 게시글 목록
    function board_list()
    {
		global $bo_table, $board, $member, $segments, $qstr, $qstr_except_amp, $is_admin, $is_guest, $write, $group, $sca, $sop, $stx, $sfl, $write_table, $spt, $page, $board_skin_url, $is_member, $sst, $sod, $config, $board_skin_path, $gml, $l10n, $lang, $mb_hash, $gml_debug;

        require GML_BBS_PATH. "/board.php";
        exit();
    }

    // 게시글 보기
    function board_view()
    {
		global $bo_table, $wr_id, $board, $member, $qstr, $qstr_except_amp, $is_admin, $is_guest, $write, $group, $sca, $sop, $stx, $sfl, $write_table, $spt, $page, $board_skin_url, $is_member, $sst, $sod, $config, $board_skin_path, $gml, $l10n, $lang, $mb_hash, $gml_debug;

        require GML_BBS_PATH. "/board.php";
        exit();
    }

    // 게시판 RSS 보기
    function board_rss()
    {
		global $bo_table, $gml, $config;

        require GML_BBS_PATH. "/rss.php";
        exit();
    }

    // 내용관리 보기
    function content_view()
    {
		global $segments, $gml, $is_admin, $lang, $config, $gml_debug;

        if (isset($segments[1]) && (string)$segments[1]) {
            $co_id = preg_replace('/[^a-z0-9_]/i', '', trim($segments[1]));
            $co_id = substr($co_id, 0, 20);
        } else {
            $co_id = '';
        }
        require GML_BBS_PATH. "/content.php";
        exit();
    }

    // 검색 결과
    function search_result()
    {
		global $segments, $gml, $member, $gr_id, $onetable, $is_admin, $config, $page, $search_skin_path, $search_skin_url, $is_member, $lang, $gml_debug;

        $sfl = $_GET['sfl'] ? : 'wr_subject||wr_content';
        $sop = $_GET['sop'] ? : 'or';
        $stx = $_GET['stx'] ? : '';

        require GML_BBS_PATH. "/search.php";
        exit();
    }

    // 검색 결과
    function group_index()
    {
		global $is_admin, $group, $gml, $gr_id, $member, $lang, $gml_debug, $is_member;

        $group_path = GML_IS_MOBILE ? GML_THEME_MOBILE_PATH : GML_THEME_PATH;

        require $group_path. "/group.php";
        exit();
    }

}
