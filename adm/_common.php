<?php
define('GML_IS_ADMIN', true);
include_once ('../common.php');

// 환경설정 또는 게시판 수정에서 처음에 열기 상태를 적용한 코드입니다.
define('GML_ADMIN_HTML_TAB_CLASS', 'tab_tit close');
define('GML_ADMIN_HTML_CON_CLASS', 'tab_con');

// 환경설정 또는 게시판 수정에서 처음에 닫기 상태를 적용한 코드입니다.
//define('GML_ADMIN_HTML_TAB_CLASS', 'tab_tit');
//define('GML_ADMIN_HTML_CON_CLASS', 'tab_con hide');

if( isset($token) ){
    $token = @htmlspecialchars(strip_tags($token), ENT_QUOTES);
}

// 관리자 언어 파일을 로드 합니다.
bind_lang_domain('default', get_path_lang_dir('admin') );

include_once(GML_ADMIN_PATH.'/admin.lib.php');

start_event('admin_common');
?>