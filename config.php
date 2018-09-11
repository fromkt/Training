<?php

/********************
    상수 선언
********************/

define('GML_VERSION', 'GNUBOARD_M');
define('GML_GNUBOARD_VER', '0.1.6');

// 이 상수가 정의되지 않으면 각각의 개별 페이지는 별도로 실행될 수 없음
define('_GNUBOARD_', true);

if (PHP_VERSION >= '5.1.0') {
    //date_default_timezone_set('UTC');
    date_default_timezone_set("Asia/Seoul");
}

/********************
    경로 상수
********************/

/*
보안서버 도메인
회원가입, 글쓰기에 사용되는 https 로 시작되는 주소를 말합니다.
포트가 있다면 도메인 뒤에 :443 과 같이 입력하세요.
보안서버주소가 없다면 공란으로 두시면 되며 보안서버주소 뒤에 / 는 붙이지 않습니다.
입력예) https://www.domain.com:443/gnuboard5
*/
define('GML_DOMAIN', '');
define('GML_HTTPS_DOMAIN', '');

//디버깅 상수, 만약에 사이트를 실사용 중이면 반드시 false 로 설정해 놓고 사용해 주세요.
define('GML_DEBUG', false);

// Set Databse table default engine is Databse default_storage_engine, If you want to use MyISAM or InnoDB, change to MyISAM or InnoDB.
define('GML_DB_ENGINE', '');

// Set Databse table default Charset
define('GML_DB_CHARSET', 'utf8mb4');

// When to encrypt a member ID and member email, ADD to String. Enter Only 0-9A-Za-z any string.
// 회원 아이디와 이메일을 encrypt 할때 기존 함수 + GML_ENCRYPT_ADD_STRING 더하여 쓰입니다. 0-9A-Za-z 문자열 중 아무 문자를 입력해 주세요.
define('GML_ENCRYPT_ADD_STRING', '');

/*
www.sir.kr 과 sir.kr 도메인은 서로 다른 도메인으로 인식합니다. 쿠키를 공유하려면 .sir.kr 과 같이 입력하세요.
이곳에 입력이 없다면 www 붙은 도메인과 그렇지 않은 도메인은 쿠키를 공유하지 않으므로 로그인이 풀릴 수 있습니다.
*/
define('GML_COOKIE_DOMAIN', '');

define('GML_DBCONFIG_FILE',  'dbconfig.php');

define('GML_ADMIN_DIR',      'adm');
define('GML_BBS_DIR',        'bbs');
define('GML_CSS_DIR',        'css');
define('GML_DATA_DIR',       'data');
define('GML_EXTEND_DIR',     'extend');
define('GML_IMG_DIR',        'img');
define('GML_JS_DIR',         'js');
define('GML_LIB_DIR',        'lib');
define('GML_PLUGIN_DIR',     'plugin');
define('GML_SKIN_DIR',       'skin');
define('GML_EDITOR_DIR',     'editor');
define('GML_MOBILE_DIR',     'mobile');

define('GML_SNS_DIR',        'sns');
define('GML_SYNDI_DIR',      'syndi');
define('GML_PHPMAILER_DIR',  'PHPMailer');
define('GML_SESSION_DIR',    'session');
define('GML_THEME_DIR',      'theme');

define('GML_LANG_DIR',      'lang');

// URL 은 브라우저상에서의 경로 (도메인으로 부터의)
if (GML_DOMAIN) {
    define('GML_URL', GML_DOMAIN);
} else {
    if (isset($gml_path['url']))
        define('GML_URL', $gml_path['url']);
    else
        define('GML_URL', '');
}

if (isset($gml_path['path'])) {
    define('GML_PATH', $gml_path['path']);
} else {
    define('GML_PATH', '');
}

define('GML_ADMIN_URL',      GML_URL.'/'.GML_ADMIN_DIR);
define('GML_BBS_URL',        GML_URL.'/'.GML_BBS_DIR);
define('GML_DATA_URL',       GML_URL.'/'.GML_DATA_DIR);
define('GML_IMG_URL',        GML_URL.'/'.GML_IMG_DIR);
define('GML_JS_URL',         GML_URL.'/'.GML_JS_DIR);
define('GML_PLUGIN_URL',     GML_URL.'/'.GML_PLUGIN_DIR);
define('GML_EDITOR_URL',     GML_PLUGIN_URL.'/'.GML_EDITOR_DIR);
define('GML_SNS_URL',        GML_PLUGIN_URL.'/'.GML_SNS_DIR);
define('GML_SYNDI_URL',      GML_PLUGIN_URL.'/'.GML_SYNDI_DIR);

// PATH 는 서버상에서의 절대경로
define('GML_ADMIN_PATH',     GML_PATH.'/'.GML_ADMIN_DIR);
define('GML_BBS_PATH',       GML_PATH.'/'.GML_BBS_DIR);
define('GML_DATA_PATH',      GML_PATH.'/'.GML_DATA_DIR);
define('GML_EXTEND_PATH',    GML_PATH.'/'.GML_EXTEND_DIR);
define('GML_LIB_PATH',       GML_PATH.'/'.GML_LIB_DIR);
define('GML_PLUGIN_PATH',    GML_PATH.'/'.GML_PLUGIN_DIR);
define('GML_SESSION_PATH',   GML_DATA_PATH.'/'.GML_SESSION_DIR);
define('GML_EDITOR_PATH',    GML_PLUGIN_PATH.'/'.GML_EDITOR_DIR);

define('GML_SNS_PATH',       GML_PLUGIN_PATH.'/'.GML_SNS_DIR);
define('GML_SYNDI_PATH',     GML_PLUGIN_PATH.'/'.GML_SYNDI_DIR);
define('GML_PHPMAILER_PATH', GML_PLUGIN_PATH.'/'.GML_PHPMAILER_DIR);

define('GML_LANG_PATH', GML_PATH.'/'.GML_LANG_DIR);
define('GML_DATA_CACHE_PATH',      GML_DATA_PATH.'/cache');

//==============================================================================


//==============================================================================
// 사용기기 설정
// pc 설정 시 모바일 기기에서도 PC화면 보여짐
// mobile 설정 시 PC에서도 모바일화면 보여짐
// both 설정 시 접속 기기에 따른 화면 보여짐
//------------------------------------------------------------------------------
define('GML_SET_DEVICE', 'both');

define('GML_USE_MOBILE', true); // 모바일 홈페이지를 사용하지 않을 경우 false 로 설정
define('GML_USE_CACHE',  true); // 최신글등에 cache 기능 사용 여부

// IF TRUE GML_USE_CACHE, Cache save type ( file, apcu, apc, memcache, memcached, redis, memory, session )
define('GML_CACHE_TYPE', 'file');

/********************
    시간 상수
********************/
// 서버의 시간과 실제 사용하는 시간이 틀린 경우 수정하세요.
// 하루는 86400 초입니다. 1시간은 3600초
// 6시간이 빠른 경우 time() + (3600 * 6);
// 6시간이 느린 경우 time() - (3600 * 6);
define('GML_SERVER_TIME',    time());
define('GML_TIME_YMDHIS',    date('Y-m-d H:i:s', GML_SERVER_TIME));
define('GML_TIME_YMD',       substr(GML_TIME_YMDHIS, 0, 10));
define('GML_TIME_HIS',       substr(GML_TIME_YMDHIS, 11, 8));

// 입력값 검사 상수 (숫자를 변경하시면 안됩니다.)
define('GML_ALPHAUPPER',      1); // 영대문자
define('GML_ALPHALOWER',      2); // 영소문자
define('GML_ALPHABETIC',      4); // 영대,소문자
define('GML_NUMERIC',         8); // 숫자
define('GML_HANGUL',         16); // 한글
define('GML_SPACE',          32); // 공백
define('GML_SPECIAL',        64); // 특수문자

// 퍼미션
define('GML_DIR_PERMISSION',  0755); // 디렉토리 생성시 퍼미션
define('GML_FILE_PERMISSION', 0644); // 파일 생성시 퍼미션

// 모바일 인지 결정 $_SERVER['HTTP_USER_AGENT']
define('GML_MOBILE_AGENT',   'phone|samsung|lgtel|mobile|[^A]skt|nokia|blackberry|BB10|android|sony');

// SMTP
// lib/mailer.lib.php 에서 사용
define('GML_SMTP',      '127.0.0.1');
define('GML_SMTP_PORT', '25');


/********************
    기타 상수
********************/

// 암호화 함수 지정
// 사이트 운영 중 설정을 변경하면 로그인이 안되는 등의 문제가 발생합니다.
define('GML_STRING_ENCRYPT_FUNCTION', 'sql_password');

// SQL 에러를 표시할 것인지 지정
// 에러를 표시하려면 TRUE 로 변경
define('GML_DISPLAY_SQL_ERROR', FALSE);

// escape string 처리 함수 지정
// addslashes 로 변경 가능
define('GML_ESCAPE_FUNCTION', 'sql_escape_string');

// sql_escape_string 함수에서 사용될 패턴
//define('GML_ESCAPE_PATTERN',  '/(and|or).*(union|select|insert|update|delete|from|where|limit|create|drop).*/i');
//define('GML_ESCAPE_REPLACE',  '');

// 게시판에서 링크의 기본개수를 말합니다.
// 필드를 추가하면 이 숫자를 필드수에 맞게 늘려주십시오.
define('GML_LINK_COUNT', 2);

// 썸네일 jpg Quality 설정
define('GML_THUMB_JPG_QUALITY', 90);

// 썸네일 png Compress 설정
define('GML_THUMB_PNG_COMPRESS', 5);

// 모바일 기기에서 DHTML 에디터 사용여부를 설정합니다.
define('GML_IS_MOBILE_DHTML_USE', false);

// MySQLi 사용여부를 설정합니다.
define('GML_MYSQLI_USE', true);

// Browscap 사용여부를 설정합니다.
define('GML_BROWSCAP_USE', true);

// 접속자 기록 때 Browscap 사용여부를 설정합니다.
define('GML_VISIT_BROWSCAP_USE', false);

// ip 숨김방법 설정
/* 123.456.789.012 ip의 숨김 방법을 변경하는 방법은
\\1 은 123, \\2는 456, \\3은 789, \\4는 012에 각각 대응되므로
표시되는 부분은 \\1 과 같이 사용하시면 되고 숨길 부분은 ♡등의
다른 문자를 적어주시면 됩니다.
*/
define('GML_IP_DISPLAY', '\\1.♡.\\3.\\4');
?>
