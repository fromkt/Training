<?php
// 이 파일은 새로운 파일 생성시 반드시 포함되어야 함
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(defined('GML_IS_ADMIN') && GML_IS_ADMIN === true) {
    include_once(GML_PATH. '/'. GML_THEME_DIR. '/basic/head.sub.php');
} else {
    if(GML_IS_MOBILE && is_file(GML_MOBILE_PATH.'/head.sub.php')) {
        include_once(GML_MOBILE_PATH.'/head.sub.php');
    } else {
        include_once(GML_THEME_PATH.'/head.sub.php');
    }
}
?>
