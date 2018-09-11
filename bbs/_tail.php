<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(GML_IS_MOBILE && is_file(GML_MOBILE_PATH.'/tail.php')) {
    include_once(GML_MOBILE_PATH.'/tail.php');
} else {
    include_once(GML_THEME_PATH.'/tail.php');
}
?>
