<?php
if (!defined('_GNUBOARD_')) exit;

require_once(dirname(__FILE__) .'/Gettext/parser/mo.php');

//언어함수 모음

function allow_locale_langs(){

    return apply_replace('allow_locale_langs', array(
        'en_US' =>  __('English'),
        'ko_KR' =>  __('Korean'),
        'ja_JP' =>  __('Japanese'),
        'zh_CN' =>  __('Chinese'),
    ));

}

function display_locale_langs(){

    $display_locale_langs = array(
        'en_US' =>  'English',
        'ko_KR' =>  '한국어',
        'ja_JP' =>  '日本語',
        'zh_CN' =>  '简体中文',
    );

    $display_languages = array();

    foreach( (array) allow_locale_langs() as $key=>$value) {
        if (!empty($key) && array_key_exists($key, $display_locale_langs)) {
            $display_languages[$key] = $display_locale_langs[$key];
        }
    }

    return apply_replace('display_locale_langs', $display_languages, $display_locale_langs);
}

function __($original, $domain = 'default'){
    global $l10n;

    $translation = $original;

    if ( isset($l10n[$domain]) && $l10n[$domain]['messages'] && isset($l10n[$domain]['messages'][$original]) ) {
        $translation = $l10n[$domain]['messages'][$original][0];
    }

    return $translation;
}

function e__($original, $domain = 'default'){
    echo __($original, $domain);
}

function p__($original, $context, $domain = 'default'){
    global $l10n;

    $translation = $original;

    if ( isset($l10n[$domain]) && $l10n[$domain]['messages'] && isset($l10n[$domain]['messages'][$original]) ) {
        $translation = $l10n[$domain]['messages'][$original][0];
    }

    return $translation;
}

function ep__($original, $context, $domain = 'default'){
    echo p__($original, $domain);
}

function n__($original, $plural, $number, $domain = 'default'){
    global $l10n;

    $translation = (1 === (int) $number) ? $original : $plural;

    if ( isset($l10n[$domain]) && $l10n[$domain]['messages'] && isset($l10n[$domain]['messages'][$original]) ) {
        $index = (1 === (int) $number) ? 0 : 1;
        $translation = isset($l10n[$domain]['messages'][$original][$index]) ? $l10n[$domain]['messages'][$original][$index] : $l10n[$domain]['messages'][$original][0];
    }

    return $translation;
}

function get_localize_script($object_var, $outputs, $is_print=false) {

    foreach ((array) $outputs as $key => $value) {
        if(empty($key)) continue;

        $outputs[$key] = html_entity_decode((string) $value, ENT_QUOTES, 'UTF-8');
    }

    $script = 'var '.$object_var.' = '.json_encode($outputs).';';

    if($is_print){
        echo '<script type="text/javascript">'.PHP_EOL;
        echo '/* <![CDATA[ */'.PHP_EOL;
        echo $script.PHP_EOL;
        echo '/* ]]> */'.PHP_EOL;
        echo '</script>'.PHP_EOL;

        return true;
    }

    return $script;
}

function get_path_lang_dir( $domain, $path=GML_LANG_PATH ) {
    global $gml_debug, $lang;

	static $cached_mofiles = array();

    $md5_key = md5($path);

	if ( ! isset($cached_mofiles[$md5_key]) ) {
		$cached_mofiles[$md5_key] = array();

        $mofiles = glob( $path . "/$lang/*.mo" );
        if ( $mofiles ) {
            $cached_mofiles[$md5_key] = array_merge( $cached_mofiles, $mofiles );
        }

	}

	$mofile = "{$domain}-{$lang}.mo";

	$path = $path . '/'.$lang.'/' . $mofile;

	if ( in_array( $path, $cached_mofiles[$md5_key] ) ) {
		return $path;
	}

	return false;
}

function bind_lang_domain($domain, $mofile_path){
    global $l10n;

    $mofile_path = apply_replace( 'bind_lang_domain_mofile', $mofile_path, $domain );

    static $domain_files = array();

    $md5_name = $domain.$mofile_path;

    // 중복 파일 방지
    if ( in_array($md5_name, $domain_files) ){
        return false;
    }

    if ( !is_readable( $mofile_path ) ) return false;

    $gml_translations = new Gm_Translations();
    $gml_mo_parser = new Gm_Mo_Parser();

    $gml_translations->setDomain($domain);
    $gml_translations->set_filepath($mofile_path);

    $content = file_get_contents($mofile_path);

    $gml_mo_parser->fromString($content, $gml_translations);

    $content = $gml_mo_parser->return_array($gml_translations, true, true);

	if ( isset( $l10n[$domain] ) ) {
        $l10n[$domain] = array_merge_recursive($content, $l10n[$domain]);
    } else {
        $l10n[$domain] = $content;
    }

    $domain_files[] = $md5_name;

    return true;
}

// 사이트 로드시 최초 언어설정
function get_initialize_lang($is_cookie=true){
    global $config, $gml;

    $lang = isset($config['cf_lang']) ? $config['cf_lang'] : 'en_US';

    if (isset($_GET["lang"]) && array_key_exists($_GET["lang"], allow_locale_langs())) {
        $lang = $_GET["lang"];
        set_session('lang', $lang);
        set_cookie('lang', $lang, 86400 * 365);
    } else if ($is_cookie && get_cookie('lang')){
        $lang = get_cookie("lang");
    } else if (!$is_cookie && get_session('lang')) {
        $lang = $_SESSION["lang"];
    }

    $gml['lang'] = apply_replace('get_initialize_lang', $lang, $is_cookie);

    return $gml['lang'];
}

// 언어설정을 SELECT 형식으로 얻음
function get_lang_select_html($name, $selected="", $add_html="", $is_option_url=false)
{
    global $gml, $lang;

    $languages = display_locale_langs();

    $str = "\n<select id=\"{$name}\" name=\"{$name}\"";
    if ($add_html) $str .= " $add_html";
    $str .= ">\n";

    foreach((array) $languages as $code=>$lang_text){
        $data_url = '';
        if($is_option_url && function_exists('get_params_merge_url')){
            $data_url = 'data-url="'.get_params_merge_url(array('lang'=>$code)).'"';
        }

        $str .= '<option '.$data_url.' value="'.$code.'"';
        if ($code == $selected)
            $str .= ' selected="selected"';
        $str .= ">{$lang_text}</option>\n";
    }
    $str .= "</select>\n";

    return apply_replace('get_lang_select_html', $str, $name, $selected, $add_html, $is_option_url);
}

function print_l10n_js_text($js_file){
    switch ($js_file) {

        case 'admin_js':
            get_localize_script('admin_js_l10n',
            array(
            'edit_text' => __('Edit'),  // 수정
            'delete_text' => __('Delete'),    // 삭제
            'select_s_text' => __('Please select at least one item to %s.'),  //%s 하실 항목을 하나 이상 선택하세요.
            'confirm_delete_text' => __('Are you sure you want to delete it?'),    // 정말 삭제하시겠습니까?
            'token_wrong_text'  =>  __('Token information is invalid.'),    //토큰정보가 올바르지 않습니다.
            ),
            true);

            break;
        case 'common_js':
            get_localize_script('common_js_l10n',
            array(
            'confirm_delete_text' => __('Are you sure you want to delete it?'),    // 정말 삭제하시겠습니까?
            'token_wrong_text'  =>  __('Token information is invalid.'),    //토큰정보가 올바르지 않습니다.
            ),
            true);

            break;
        case 'autosave_js':
            get_localize_script('autosave_js_l10n',
            array(
            'error_text' => __('Are you sure you want to delete it?'),    // 임시 저장된글을 삭제중에 오류가 발생하였습니다.
            ),
            true);

            break;
        case 'wrest_js':
            get_localize_script('wrest_js_l10n',
            array(
            'required_msg' => __('This is a required %s'),    // 필수 %s 입니다.
            'selection_msg' => __('Selection'),     //선택
            'enter_msg' => __('Enter'),     //입력
            'phone_number_msg'  =>  __('The phone number is not in the correct format.'),   // 전화번호 형식이 올바르지 않습니다.
            'hyphen_msg' => __('Enter including hyphen (-).'),    // 하이픈(-)을 포함하여 입력하세요.
            'email_msg' => __('Not in email address format.'),   // 이메일주소 형식이 아닙니다.
            'hangeul_check_msg' => __("(consonants and vowels are allowed only)"),      // 한글이 아닙니다. (자음, 모음 조합된 한글만 가능)
            'hangeul_not_msg'  =>  __("It's not Hangeul."), // 한글이 아닙니다.
            'char_check_msg'  => __('It is not Korean, English or number.'),  //한글, 영문, 숫자가 아닙니다.
            'str_check_msg' => __('It is not Korean or English.'),  //한글, 영문이 아닙니다.
            'number_check_msg' => __("It's not number."),  // 숫자가 아닙니다.
            'english_check_msg' => __("It's not English."),  //  영문이 아닙니다.
            'eon_check_msg' => __("It's not English or Number."),     //영문 또는 숫자가 아닙니다.
            'eonoh_check_msg' => __("It's not English, Number or hyphen (-)."),       //영문, 숫자, _ 가 아닙니다.
            'minlength_msg' => __('Please enter at least %s characters.'),     // 최소 %s 글자 이상 입력하세요.
            'image_check_msg' => __("It's not image file."),       //  이미지 파일이 아닙니다.
            'image_ex_check_msg' => __('Only .gif .jpg .png files are allowed.'),      //  .gif .jpg .png 파일만 가능합니다.
            'allow_file_msg' => __('Only files with extension .%s are allowed.'),        // 확장자가 .%s 파일만 가능합니다.
            'space_check_msg' => __('There should be no spaces.'),      //  공백이 없어야 합니다.
            ),
            true);

            break;
    }   //end switch
}   //end function print_l10n_js_text

function delete_multi_lang_data($id) {
    global $gml;

    $sql = " delete from {$gml['multi_lang_data_table']} where ml_target_id = '{$id}' ";
    sql_query($sql);
}

?>
