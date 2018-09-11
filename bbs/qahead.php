<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

$qa_skin_path = get_skin_path('qa', (GML_IS_MOBILE ? $qaconfig['qa_mobile_skin'] : $qaconfig['qa_skin']));
$qa_skin_url  = get_skin_url('qa', (GML_IS_MOBILE ? $qaconfig['qa_mobile_skin'] : $qaconfig['qa_skin']));

// load language for l10n
bind_lang_domain( 'default', get_path_lang_dir('skin', $qa_skin_path.'/'.GML_LANG_DIR) );

if (GML_IS_MOBILE) {
    // 모바일의 경우 설정을 따르지 않는다.
    include_once('./_head.php');
    echo conv_content($qaconfig['qa_mobile_content_head'], 1);
} else {
    if($qaconfig['qa_include_head'] && is_include_path_check($qaconfig['qa_include_head']))
        @include ($qaconfig['qa_include_head']);
    else
        include ('./_head.php');
    echo conv_content($qaconfig['qa_content_head'], 1);
}
?>