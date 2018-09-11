<?php
// 흐름
// > 웹 주소 입력
// > index.php
// > _common.php
// > route.php
// > router->execute($baseuri)
// > plugin/RegexRouter/RouterToDo.php
//      > bbs/board.php (게시글 목록, 게시글 보기)
//      > bbs/content.php (내용 보기)
//      > bbs/search.php (검색 결과 보기)
//      > theme_path/index.php (각 테마 인덱스 페이지)
include_once('./_common.php');

require_once(GML_THEME_PATH.'/index.php');
return;
?>
