<?php
include_once('./_common.php');

if ($is_guest)
    alert(__('Once logged in, you can access it.'), GML_BBS_URL.'/login.php');

/*
if ($url)
    $urlencode = urlencode($url);
else
    $urlencode = urlencode($_SERVER[REQUEST_URI]);
*/

// IF social_login
if( function_exists('social_member_comfirm_redirect') ){    
    social_member_comfirm_redirect();
}

$gml['title'] = __('Confirm member password');
include_once('./_head.sub.php');

$url = clean_xss_tags($_GET['url']);

// Check url
check_url_host($url, '', GML_URL, true);

if( preg_match('#^/{3,}#', $url) ){
    $url = preg_replace('#^/{3,}#', '/', $url);
}

$url = get_text($url);

// load language for l10n
bind_lang_domain( 'default', get_path_lang_dir('skin', $member_skin_path.'/'.GML_LANG_DIR) );

include_once($member_skin_path.'/member_confirm.skin.php');

include_once('./_tail.sub.php');
?>
