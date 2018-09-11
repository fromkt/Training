<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

// load language for l10n
bind_lang_domain( 'default', get_path_lang_dir('skin', $board_skin_path.'/'.GML_LANG_DIR) );

// Head of board manage 게시판 관리의 상단 내용
if (GML_IS_MOBILE) {
    // For mobile, do not follow the settings. 모바일의 경우 설정을 따르지 않는다.
    include_once(GML_BBS_PATH.'/_head.php');
    echo stripslashes($board['bo_mobile_content_head']);
} else {
    if(is_include_path_check($board['bo_include_head'])) {  // Check File Path 파일경로 체크
        if( strpos($board['bo_include_head'], DIRECTORY_SEPARATOR) === 0 || strpos($board['bo_include_head'], GML_PATH) === 0 ){
            @include ($board['bo_include_head']);
        } else {
            @include (GML_BBS_PATH.'/'.$board['bo_include_head']);
        }
    } else {    // Default file will be imported if file path is not correct
        include_once(GML_BBS_PATH.'/_head.php');
    }
    echo stripslashes($board['bo_content_head']);
}
?>
