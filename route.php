<?php
include_once(dirname(__FILE__). "/common.php");

if($config['cf_bbs_rewrite']) {
require_once(dirname(__FILE__). "/plugin/RegexRouter/Router.php");
require_once(dirname(__FILE__). "/plugin/RegexRouter/RouterToDo.php");

    $router = new Router();
    $todo = new RouterToDo();
    $args = array();

    if($bo_table) {
        // 게시글 목록 등록
        $router->route('board_list', 'GET', "/{$bo_table}", array($todo, 'board_list'), $args);
        // 게시글 보기 등록
        $router->route('board_view', 'GET', "/{$bo_table}/(\d+)", array($todo, 'board_view'), $args);
        // 게시판 RSS 보기 등록
        $router->route('board_rss', 'GET', "/{$bo_table}/rss", array($todo, 'board_rss'), $args);
    }
    // 내용관리 보기 등록
    $router->route('content_view', 'GET', "/content/(\w+)", array($todo, 'content_view'), $args);
    // 전체 검색 결과 등록
    $router->route('search_result', 'GET', "/search", array($todo, 'search_result'), $args);
    // 전체 검색 결과 등록
    $router->route('group_index', 'GET', "/group/{$gr_id}", array($todo, 'group_index'), $args);

    $router->execute($baseuri);
}
?>
