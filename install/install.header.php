<?php
include_once ('../config.php');

$l10n = array();

@header('Content-Type: text/html; charset=utf-8');
@header('X-Robots-Tag: noindex');

include_once('../'.GML_LIB_DIR.'/hook.lib.php');    // hook 함수 파일
include_once('../'.GML_LIB_DIR.'/language.lib.php');    // 공통 언어함수 파일
include_once('../'.GML_LIB_DIR.'/common.lib.php');    // 공통 라이브러리
include_once('../'.GML_LIB_DIR.'/get_data.lib.php');

$lang = get_initialize_lang(true);
bind_lang_domain('default', '../'.GML_LANG_DIR."/$lang/install-{$lang}.mo");
?>