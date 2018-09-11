<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

if (GML_IS_MOBILE) {
    echo conv_content($qaconfig['qa_mobile_content_tail'], 1);
    // 모바일의 경우 설정을 따르지 않는다.
    include_once('./_tail.php');
} else {
    echo conv_content($qaconfig['qa_content_tail'], 1);
    if($qaconfig['qa_include_tail'] && is_include_path_check($qaconfig['qa_include_tail']))
        @include ($qaconfig['qa_include_tail']);
    else
        include ('./_tail.php');
}
?>