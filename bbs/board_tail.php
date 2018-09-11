<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// Foot of board manage 게시판 관리의 하단 파일 경로
if (GML_IS_MOBILE) {
    echo stripslashes($board['bo_mobile_content_tail']);
    // For mobile, do not follow the settings. 모바일의 경우 설정을 따르지 않는다.
    include_once(GML_BBS_PATH.'/_tail.php');
} else {
    echo stripslashes($board['bo_content_tail']);
    if(is_include_path_check($board['bo_include_tail'])) {  // Check File Path 파일경로 체크
        if( strpos($board['bo_include_tail'], DIRECTORY_SEPARATOR) === 0 || strpos($board['bo_include_tail'], GML_PATH) === 0 ){
            @include ($board['bo_include_tail']);
        } else {
            @include (GML_BBS_PATH.'/'.$board['bo_include_tail']);
        }
    } else {    // Default file will be imported if file path is not correct
        include_once(GML_BBS_PATH.'/_tail.php');
    }
}
?>