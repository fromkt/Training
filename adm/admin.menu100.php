<?php
$menu['menu100'] = array (
    array('100000', __('Configuration'), GML_ADMIN_URL.'/config_form.php',   'config'),   // 환경설정
    array('100100', __('Default Preferences'), GML_ADMIN_URL.'/config_form.php',   'cf_basic'),     // 기본환경설정
    array('100200', p__('Administrative', 'Setting administrative privileges'), GML_ADMIN_URL.'/auth_list.php',     'cf_auth'),      // 관리권한설정
    array('100280', __('Theme Setup'), GML_ADMIN_URL.'/theme.php',     'cf_theme', 1),          // 테마설정
    array('100290', __('Menu settings'), GML_ADMIN_URL.'/menu_list.php',     'cf_menu', 1),       // 메뉴설정
    array('100300', __('Test Mail'), GML_ADMIN_URL.'/sendmail_test.php', 'cf_mailtest'),   // 메일 테스트
    array('100310', p__('Manage popup', 'Manage pop-up layers'), GML_ADMIN_URL.'/newwinlist.php', 'scf_poplayer'),  // 팝업관리
    array('100800', __('Delete Session Files'),GML_ADMIN_URL.'/session_file_delete.php', 'cf_session', 1),      // 세션파일 삭제
    array('100900', __('Delete Cache'),GML_ADMIN_URL.'/cache_file_delete.php',   'cf_cache', 1),        // 캐시 삭제
    array('100910', __('Delete Captcha Files'),GML_ADMIN_URL.'/captcha_file_delete.php',   'cf_captcha', 1),    // 캡챠파일 삭제
    array('100920', __('Delete Thumb Files'),GML_ADMIN_URL.'/thumbnail_file_delete.php',   'cf_thumbnail', 1),  // 썸네일파일 삭제
    array('100500', 'phpinfo()',        GML_ADMIN_URL.'/phpinfo.php',       'cf_phpinfo')
);

if(version_compare(phpversion(), '5.3.0', '>=') && defined('GML_BROWSCAP_USE') && GML_BROWSCAP_USE) {
    $menu['menu100'][] = array('100510', __('Browscap Update'), GML_ADMIN_URL.'/browscap.php', 'cf_browscap');     // Browscap 업데이트
    $menu['menu100'][] = array('100520', __('Convert Visit Log'), GML_ADMIN_URL.'/browscap_convert.php', 'cf_visit_cnvrt');  // 접속로그 변환
}
if(version_compare(phpversion(), '5.4.0', '>=') && defined('GML_BROWSCAP_USE') && GML_BROWSCAP_USE) {
    $menu['menu100'][] = array('100550', __('Theme Language File'), GML_ADMIN_URL.'/theme_lang.php', 'theme_lang');  // 테마 언어 파일
}
?>