<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

// 테마가 지원하는 장치 설정 pc, mobile
// 선언하지 않거나 값을 지정하지 않으면 그누보드 M 의 설정을 따른다.
// GML_SET_DEVICE 상수 설정 보다 우선 적용됨
define('GML_THEME_DEVICE', '');

$theme_config = array();

// 갤러리 이미지 수 등의 설정을 지정하시면 게시판관리에서 해당 값을
// 가져오기 기능을 통해 게시판 설정의 해당 필드에 바로 적용할 수 있습니다.
// 사용하지 않는 스킨 설정은 값을 비워두시면 됩니다.

$theme_config = array(
    'set_default_skin'          => false,   // 기본환경설정의 최근게시물 등의 기본스킨 변경여부 true, false
    'preview_board_skin'        => 'basic', // 테마 미리보기 때 적용될 기본 게시판 스킨
    'preview_mobile_board_skin' => 'basic', // 테마 미리보기 때 적용될 기본 모바일 게시판 스킨
    'cf_member_skin'            => 'basic', // 회원 스킨
    'cf_mobile_member_skin'     => 'basic', // 모바일 회원 스킨
    'cf_new_skin'               => 'basic', // 최근게시물 스킨
    'cf_mobile_new_skin'        => 'basic', // 모바일 최근게시물 스킨
    'cf_search_skin'            => 'basic', // 검색 스킨
    'cf_mobile_search_skin'     => 'basic', // 모바일 검색 스킨
    'cf_connect_skin'           => 'basic', // 접속자 스킨
    'cf_mobile_connect_skin'    => 'basic', // 모바일 접속자 스킨
    'cf_faq_skin'               => 'basic', // FAQ 스킨
    'cf_mobile_faq_skin'        => 'basic', // 모바일 FAQ 스킨
    'bo_gallery_cols'           => 4,       // 갤러리 이미지 수
    'bo_gallery_width'          => 210,     // 갤러리 이미지 폭
    'bo_gallery_height'         => 150,     // 갤러리 이미지 높이
    'bo_mobile_gallery_width'   => 125,     // 모바일 갤러리 이미지 폭
    'bo_mobile_gallery_height'  => 100,     // 모바일 갤러리 이미지 높이
    'bo_image_width'            => 600,     // 게시판 뷰 이미지 폭
    'qa_skin'                   => 'basic', // 1:1문의 스킨
    'qa_mobile_skin'            => 'basic',  // 1:1문의 모바일 스킨
    'theme_add_lang'            => array(
        'theme_name' => __('Gnuboard M Basic theme'),
        'theme_detail' => __('Basic theme is the Grunboard M theme provided by SIR. Basic themes comply with Web standards and accessibility.')
    ),
);

if(! function_exists('theme_lang_select_html')){
    put_replace('get_lang_select_html', 'theme_lang_select_html', 10, 5);

    function theme_lang_select_html($str, $name, $selected='', $add_html='', $is_option_url=false){

        if($is_option_url){
            $str .= '<script>'.PHP_EOL;
            $str .= 'jQuery("select.theme_select_lang").change(function(e) {'.PHP_EOL;
            $str .= 'location.replace($("option:selected", this).attr("data-url"));'.PHP_EOL;
            $str .= '});'.PHP_EOL;
            $str .= '</script>'.PHP_EOL;
        }
        return $str;
    }
}

if(! function_exists('theme_add_move_html_footer') ){
    put_event('move_html_footer', 'theme_add_move_html_footer');

    function theme_add_move_html_footer(){
        
        $str = '<script>'.PHP_EOL;
        $str .= 'jQuery(document).ready(function($){';
        $str .= '$("input:checkbox#chkall").change(function(){';
        $str .= 'if(this.checked){';
        $str .= '$(".td_chk input[type=checkbox]").prop("checked", true);';
        $str .= '$(".td_chk label").addClass("click_on");';
        $str .= '} else {';
        $str .= '$(".td_chk input[type=checkbox]").prop("checked", false);';
        $str .= '$(".td_chk label").removeClass("click_on");';
        $str .= '}';
        $str .= '});';
        $str .= '$(".td_chk label").click(function(){';
        $str .= '$(this).toggleClass("click_on");';
        $str .= '});';
        $str .= '});';
        $str .= '</script>'.PHP_EOL;

        echo $str;
    }
}
?>