<?php
// 이 파일은 새로운 파일 생성시 반드시 포함되어야 함
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$gml_debug['begin_time'] = $begin_time = get_microtime();

if (!isset($gml['title'])) {
    $gml['title'] = $config['cf_title'];
    $gml_head_title = $gml['title'];
}
else {
    $gml_head_title = $gml['title']; // 상태바에 표시될 제목
    $gml_head_title .= " | ".$config['cf_title'];
}

// 현재 접속자
// 게시판 제목에 ' 포함되면 오류 발생
$gml['lo_location'] = addslashes($gml['title']);
if (!$gml['lo_location'])
    $gml['lo_location'] = addslashes(clean_xss_tags($_SERVER['REQUEST_URI']));
$gml['lo_url'] = addslashes(clean_xss_tags($_SERVER['REQUEST_URI']));
if (strstr($gml['lo_url'], '/'.GML_ADMIN_DIR.'/') || $is_admin == 'super') $gml['lo_url'] = '';

/*
// 만료된 페이지로 사용하시는 경우
header("Cache-Control: no-cache"); // HTTP/1.1
header("Expires: 0"); // rfc2616 - Section 14.21
header("Pragma: no-cache"); // HTTP/1.0
*/
?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<?php
if (GML_IS_MOBILE) {
    echo '<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=10,user-scalable=yes">'.PHP_EOL;
    echo '<meta name="HandheldFriendly" content="true">'.PHP_EOL;
    echo '<meta name="format-detection" content="telephone=no">'.PHP_EOL;
} else {
    echo '<meta http-equiv="imagetoolbar" content="no">'.PHP_EOL;
    echo '<meta http-equiv="X-UA-Compatible" content="IE=edge">'.PHP_EOL;
}

start_event('head_print_meta');

if($config['cf_add_meta'])
    echo $config['cf_add_meta'].PHP_EOL;
?>
<title><?php echo $gml_head_title; ?></title>
<?php
    // if(admin mode || !theme preview)
    if(defined('GML_IS_ADMIN') && GML_IS_ADMIN === true && (!defined('_THEME_PREVIEW_') || _THEME_PREVIEW_ === false)) {
?>
<link rel="stylesheet" href="<?php echo apply_replace('theme_head_css_url', GML_ADMIN_URL.'/css/admin.css?ver='.GML_CSS_VER, GML_THEME_URL); ?>">
<?php } else { ?>
<link rel="stylesheet" href="<?php echo apply_replace('theme_head_css_url', GML_THEME_CSS_URL.'/'.(GML_IS_MOBILE ? 'mobile' : 'default').'.css?ver='.GML_CSS_VER, GML_THEME_URL); ?>">
<?php } ?>
<!--[if lte IE 8]>
<script src="<?php echo GML_JS_URL ?>/html5.js"></script>
<![endif]-->
<script>
// 자바스크립트에서 사용하는 전역변수 선언
var gml_url       = "<?php echo GML_URL ?>";
var gml_bbs_url   = "<?php echo GML_BBS_URL ?>";
var gml_is_member = "<?php echo isset($is_member)?$is_member:''; ?>";
var gml_is_admin  = "<?php echo isset($is_admin)?$is_admin:''; ?>";
var gml_is_mobile = "<?php echo GML_IS_MOBILE ?>";
var gml_bo_table  = "<?php echo isset($bo_table)?$bo_table:''; ?>";
var gml_sca       = "<?php echo isset($sca)?$sca:''; ?>";
var gml_editor    = "<?php echo ($config['cf_editor'] && $board['bo_use_dhtml_editor'])?$config['cf_editor']:''; ?>";
var gml_cookie_domain = "<?php echo GML_COOKIE_DOMAIN ?>";
var gml_lang = "<?php echo isset($lang)?$lang:''; ?>";
<?php if(defined('GML_IS_ADMIN')) { ?>
var gml_admin_url = "<?php echo GML_ADMIN_URL; ?>";
<?php } ?>
<?php start_event('head_print_javascript_variable'); ?>
</script>
<script src="<?php echo GML_JS_URL ?>/jquery-1.12.4.min.js"></script>
<script src="<?php echo GML_JS_URL ?>/jquery-migrate-1.4.1.min.js"></script>
<script src="<?php echo GML_JS_URL ?>/jquery.menu.js?ver=<?php echo GML_JS_VER; ?>"></script>
<?php print_l10n_js_text('common_js'); ?>
<script src="<?php echo GML_JS_URL ?>/common.js?ver=<?php echo GML_JS_VER; ?>"></script>
<?php print_l10n_js_text('wrest_js'); ?>
<script src="<?php echo GML_JS_URL ?>/wrest.js?ver=<?php echo GML_JS_VER; ?>"></script>
<script src="<?php echo GML_JS_URL ?>/placeholders.min.js"></script>
<script src="<?php echo GML_JS_URL ?>/swiper/swiper.jquery.min.js?ver=<?php echo GML_JS_VER; ?>"></script>
<script src="<?php echo GML_JS_URL ?>/swiper/swiper.extend.user.js?ver=<?php echo GML_JS_VER; ?>"></script>
<link rel="stylesheet" href="<?php echo GML_JS_URL ?>/swiper/swiper.min.css">
<link rel="stylesheet" href="<?php echo GML_JS_URL ?>/font-awesome/css/font-awesome.min.css">
<?php start_event('head_print_css_js'); ?>
<?php
if(GML_IS_MOBILE) {
    echo '<script src="'.GML_JS_URL.'/modernizr.custom.70111.js"></script>'.PHP_EOL; // overflow scroll 감지
}
if(!defined('GML_IS_ADMIN'))
    echo $config['cf_add_script'];
?>
</head>
<body<?php echo isset($gml['body_script']) ? $gml['body_script'] : ''; ?> class="<?php echo isset($lang)?'lang_'.$lang:''; ?>">
<?php
start_event('head_sub');
if ($is_member) { // 회원이라면 로그인 중이라는 메세지를 출력해준다.
    $sr_admin_msg = '';
    if ($is_admin == 'super') $sr_admin_msg = __('Super administrator').' ';    //최고관리자
    else if ($is_admin == 'group') $sr_admin_msg = __('Group administrator').' ';   //그룹관리자
    else if ($is_admin == 'board') $sr_admin_msg = __('Board administrator').' ';   //게시판관리자

    echo '<div id="hd_login_msg">'.sprintf(__('Logging in to %s'), $sr_admin_msg.get_text($member['mb_nick']));       //누구 님 로그인 중
    echo '<a href="'.GML_BBS_URL.'/logout.php">'.__('Logout').'</a></div>';    //로그아웃
}
?>
