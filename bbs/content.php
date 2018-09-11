<?php
include_once('./_common.php');

$co = get_content_db($co_id);
if($config['cf_use_multi_lang_data']) {
    $co = get_content_by_lang($co);
}
if (!$co['co_id'])
    alert(__('No content registered.'));

$gml['title'] = $co['co_subject'];

if (!GML_IS_MOBILE && $co['co_include_head'] && is_include_path_check($co['co_include_head'])){
    if( strpos($co['co_include_head'], DIRECTORY_SEPARATOR) === 0 || strpos($co['co_include_head'], GML_PATH) === 0 ){
        @include ($co['co_include_head']);
    } else {
        @include (GML_BBS_PATH.'/'.$co['co_include_head']);
    }
} else {
    include_once(GML_BBS_PATH. '/_head.php');
}

$co_content = (GML_IS_MOBILE && $co['co_mobile_content']) ? $co['co_mobile_content'] : $co['co_content'];

$str = conv_content($co_content, $co['co_html'], $co['co_tag_filter_use']);

// $src 를 $dst 로 변환
unset($src);
unset($dst);
$src[] = "/{{쇼핑몰명}}|{{홈페이지제목}}/";
$dst[] = $config['cf_title'];
$src[] = "/{{회사명}}|{{상호}}/";
$dst[] = $default['de_admin_company_name'];
$src[] = "/{{대표자명}}/";
$dst[] = $default['de_admin_company_owner'];
$src[] = "/{{사업자등록번호}}/";
$dst[] = $default['de_admin_company_saupja_no'];
$src[] = "/{{대표전화번호}}/";
$dst[] = $default['de_admin_company_tel'];
$src[] = "/{{팩스번호}}/";
$dst[] = $default['de_admin_company_fax'];
$src[] = "/{{통신판매업신고번호}}/";
$dst[] = $default['de_admin_company_tongsin_no'];
$src[] = "/{{사업장우편번호}}/";
$dst[] = $default['de_admin_company_zip'];
$src[] = "/{{사업장주소}}/";
$dst[] = $default['de_admin_company_addr'];
$src[] = "/{{운영자명}}|{{관리자명}}/";
$dst[] = $default['de_admin_name'];
$src[] = "/{{운영자e-mail}}|{{관리자e-mail}}/i";
$dst[] = $default['de_admin_email'];
$src[] = "/{{정보관리책임자명}}/";
$dst[] = $default['de_admin_info_name'];
$src[] = "/{{정보관리책임자e-mail}}|{{정보책임자e-mail}}/i";
$dst[] = $default['de_admin_info_email'];

$str = preg_replace($src, $dst, $str);

// Skin path
$skin = GML_IS_MOBILE ? $co['co_mobile_skin'] : $co['co_skin'];
if(trim($skin) == '') {
    $skin = 'basic';
}

$content_skin_path = get_skin_path('content', $skin);
$content_skin_url  = get_skin_url('content', $skin);
$skin_file = $content_skin_path.'/content.skin.php';

// load language for l10n
bind_lang_domain( 'default', get_path_lang_dir('skin', $content_skin_path.'/'.GML_LANG_DIR) );

if (!GML_IS_MOBILE && $is_admin)
    echo '<div class="ctt_admin"><a href="'.GML_ADMIN_URL.'/contentform.php?w=u&amp;co_id='.$co_id.'" class="btn_admin btn">'.__('Edit Content').'</a></div>';
?>

<?php
if(is_file($skin_file)) {
    if(!GML_IS_MOBILE) {
        $himg = GML_DATA_PATH.'/content/'.$co_id.'_h';
        if (file_exists($himg)) // Head image
            echo '<div id="ctt_himg" class="ctt_img"><img src="'.GML_DATA_URL.'/content/'.$co_id.'_h" alt=""></div>';
    }

    include($skin_file);

    if(!GML_IS_MOBILE) {
        $timg = GML_DATA_PATH.'/content/'.$co_id.'_t';
        if (file_exists($timg)) // Foot image
            echo '<div id="ctt_timg" class="ctt_img"><img src="'.GML_DATA_URL.'/content/'.$co_id.'_t" alt=""></div>';
    }
} else {
    echo '<p>'.str_replace(GML_PATH.'/', '', $skin_file).' '.__('File does not exist.').'</p>';
}

if (!GML_IS_MOBILE && $co['co_include_tail'] && is_include_path_check($co['co_include_tail'])){
    if( strpos($co['co_include_tail'], DIRECTORY_SEPARATOR) === 0 || strpos($co['co_include_tail'], GML_PATH) === 0 ){
        @include ($co['co_include_tail']);
    } else {
        @include (GML_BBS_PATH.'/'.$co['co_include_tail']);
    }
} else {
    include_once(GML_BBS_PATH. '/_tail.php');
}
?>
