<?php
if (!defined('_GNUBOARD_')) exit;

function get_config($is_cache=false){
    global $gml;

    static $cache = array();

    $cache = apply_replace('get_config_cache', $cache, $is_cache);

    if( $is_cache && !empty($cache) ){
        return $cache;
    }

    $sql = " select * from {$gml['config_table']} ";
    $cache = apply_replace('get_config', sql_fetch($sql));

    return $cache;
}

// 1:1문의 설정로드
function get_qa_config($is_cache=false)
{
    global $gml;

    static $cache = array();

    if( $is_cache && !empty($cache) ){
        return $cache;
    }

    $sql = " select * from {$gml['qa_config_table']} ";
    $cache = apply_replace('get_qa_config', sql_fetch($sql));

    return $cache;
}

function get_content_db($co_id, $is_cache=false){
    global $gml;

    static $cache = array();

    $co_id = preg_replace('/[^a-z0-9_]/i', '', $co_id);
    $key = md5($co_id);

    if( $is_cache && isset($cache[$key]) ){
        return $cache[$key];
    }

    $sql = " select * from {$gml['content_table']} where co_id = '$co_id' ";

    $cache[$key] = sql_fetch($sql);

    return $cache[$key];
}

function get_content_by_lang($co, $is_cache=false) {
    global $lang, $gml;

    $co_id = preg_replace('/[^a-z0-9_]/i', '', $co['co_id']);

    $sql = " select * from {$gml['multi_lang_data_table']} where ml_case='content' and ml_lang='{$lang}' and ml_target_id='{$co_id}' ";
    $result = sql_query($sql);

    for($i=0; $row=sql_fetch_array($result); $i++) {
        $key = $row['ml_target_column'];
        $value = $row['ml_target_value'];

        $co[$key] = $value;
    }

    static $cache = array();

    $cache_key = md5($co_id);
    $cache[$cache_key] = $co;

    return $cache[$cache_key];
}

function get_faq_by_lang($data, $case) {
    global $lang, $gml;

    if($case == 'faqmaster') {
        $id_str = 'fm_id';
    } else {
        $id_str = 'fa_id';
    }

    $sql = " select * from {$gml['multi_lang_data_table']} where ml_case='{$case}' and ml_lang='{$lang}' and ml_target_id='{$data[$id_str]}' ";
    $result = sql_query($sql);

    for($i=0; $row=sql_fetch_array($result); $i++) {
        $key = $row['ml_target_column'];
        $value = $row['ml_target_value'];

        $data[$key] = $value;
    }

    return $data;
}

function get_board_db($bo_table, $is_cache=false){
    global $gml;

    static $cache = array();

    $cache = apply_replace('get_board_db_cache', $cache, $bo_table, $is_cache);

    $key = md5($bo_table);

    $bo_table = preg_replace('/[^a-z0-9_]/i', '', $bo_table);
    if( $is_cache && isset($cache[$key]) ){
        return $cache[$key];
    }

    if( !($cache[$key] = apply_replace('get_board_db', array(), $bo_table)) ){

        $sql = " select * from {$gml['board_table']} where bo_table = '$bo_table' ";

        $cache[$key] = sql_fetch($sql);

    }

    return $cache[$key];
}

// 게시판 테이블에서 하나의 행을 읽음
function get_write_db($write_table, $wr_id, $fields='*', $is_cache=false)
{
    static $cache = array();

    $wr_id = (int) $wr_id;
    $key = md5($write_table.'|'.$wr_id.$fields);

    if( $is_cache && isset($cache[$key]) ){
        return $cache[$key];
    }

    $sql = " select $fields from {$write_table} where wr_id = '{$wr_id}' ";

    $cache[$key] = sql_fetch($sql);

    return $cache[$key];
}

// 게시판 첨부파일 테이블에서 하나의 행을 읽음
function get_board_file_db($bo_table, $wr_id, $fields='*', $add_where='', $is_cache=false)
{
    global $gml;

    static $cache = array();

    $wr_id = (int) $wr_id;
    $key = md5($bo_table.'|'.$wr_id.$fields.$add_where);

    if( $is_cache && isset($cache[$key]) ){
        return $cache[$key];
    }

    $sql = " select $fields from {$gml['board_file_table']}
                where bo_table = '$bo_table' and wr_id = '$wr_id' $add_where order by bf_no limit 0, 1 ";

    $cache[$key] = sql_fetch($sql);

    return $cache[$key];
}

function get_poll_db($po_id, $is_cache=false){
    global $gml;

    static $cache = array();

    $po_id = (int) $po_id;
    $key = md5($po_id);

    if( $is_cache && isset($cache[$key]) ){
        return $cache[$key];
    }

    $sql = " select * from {$gml['poll_table']} where po_id = '{$po_id}' ";

    $cache[$key] = sql_fetch($sql);

    return $cache[$key];
}

function get_point_db($po_id, $is_cache=false){
    global $gml;

    static $cache = array();

    $po_id = (int) $po_id;
    $key = md5($po_id);

    if( $is_cache && isset($cache[$key]) ){
        return $cache[$key];
    }

    $sql = " select * from {$gml['point_table']} where po_id = '{$po_id}' ";

    $cache[$key] = sql_fetch($sql);

    return $cache[$key];
}

function get_mail_content_db($ma_id, $is_cache=false){
    global $gml;

    static $cache = array();

    $ma_id = (int) $ma_id;
    $key = md5($ma_id);

    if( $is_cache && isset($cache[$key]) ){
        return $cache[$key];
    }

    $sql = " select * from {$gml['mail_table']} where ma_id = '{$ma_id}' ";

    $cache[$key] = sql_fetch($sql);

    return $cache[$key];
}

function get_qacontent_db($qa_id, $is_cache=false){
    global $gml;

    static $cache = array();

    $qa_id = (int) $qa_id;
    $key = md5($qa_id);

    if( $is_cache && isset($cache[$key]) ){
        return $cache[$key];
    }

    $sql = " select * from {$gml['qa_content_table']} where qa_id = '{$qa_id}' ";

    $cache[$key] = sql_fetch($sql);

    return $cache[$key];
}

function get_thumbnail_find_cache($bo_table, $wr_id, $wr_key){
    global $gml;

    if( $cache_content = gml_latest_cache_data($bo_table, array(), $wr_id) ){
        if( $wr_key === 'content' ){
            return $cache_content;
        } else if ( $wr_key === 'file' && isset($cache_content['first_file_thumb']) ){
            return $cache_content['first_file_thumb'];
        }
    }

    if( $wr_key === 'content' ){
        $write_table = $gml['write_prefix'].$bo_table;
        return get_write_db($write_table, $wr_id, 'wr_content', true);
    }

    return get_board_file_db($bo_table, $wr_id, 'bf_file, bf_content', "and bf_type between '1' and '3'", true);
}

function get_db_charset($charset){

    $add_charset = $charset;

    if ( 'utf8mb4' === $charset ) {
        $add_charset .= ' COLLATE utf8mb4_unicode_ci';
    }

    return apply_replace('get_db_charset', $add_charset, $charset);
}

function get_db_create_replace($sql_str){

    if( in_array(strtolower(GML_DB_ENGINE), array('innodb', 'myisam')) ){
        $sql_str = preg_replace('/ENGINE=MyISAM/', 'ENGINE='.GML_DB_ENGINE, $sql_str);
    } else {
        $sql_str = preg_replace('/ENGINE=MyISAM/', '', $sql_str);
    }

    if( GML_DB_CHARSET !== 'utf8' ){
        $sql_str = preg_replace('/CHARSET=utf8/', 'CHARACTER SET '.get_db_charset(GML_DB_CHARSET), $sql_str);
    }

    return $sql_str;
}

function get_class_encrypt(){
    static $cache;

    if( $cache && is_object($obj) ){
        return $cache;
    }

    $cache = apply_replace('get_class_encrypt', new str_encrypt());

    return $cache;
}

function get_string_encrypt($str){

    $new = get_class_encrypt();

    $encrypt_str = $new->encrypt($str);

    return $encrypt_str;
}

function get_bool_encrypt_encoded($str){
    return apply_replace('get_bool_encrypt_encoded', is_url_base64_encoded($str), $str);
}

function get_string_check_decrypt($str, $field=''){

    if( $field === 'mb_id' ){
        $pattern = '/[^0-9a-z_]+/i';
    } else {
        $pattern = '/[^a-zA-Z0-9\/:@\.\+-s]/';
    }

    if( get_bool_encrypt_encoded($str) ){

        if( $field === 'mb_id' ){
            return preg_replace($pattern, '', get_string_decrypt($str));
        }

        return get_string_decrypt($str);
    }

    return preg_replace($pattern, '', strip_tags($str));
}

function get_string_decrypt($str){

    $new = get_class_encrypt();

    $decrypt_str = $new->decrypt($str);

    return $decrypt_str;
}

function get_mb_icon_name($mb_id){

    if( $icon_name = apply_replace('get_mb_icon_name', '', $mb_id) ){
        return $icon_name;
    }

    return md5($mb_id);
}

function get_member_by_hash($mb_id='', $mb_hash=''){

    global $is_admin;

    if( $is_admin && $mb_id ){
        return $mb_id;
    } else if ( $mb_hash ){
        return get_string_check_decrypt($mb_hash, 'mb_id');
    }

    return '';
}

function get_permission_debug_show(){
    global $member;

    $bool = false;
    if ( defined('GML_DEBUG') && GML_DEBUG ){
        $bool = true;
    }

    return apply_replace('get_permission_debug_show', $bool, $member);
}

function get_allow_head_filename(){
    return apply_replace('get_allow_head_filename', array('', '_head.php'));
}

function get_allow_tail_filename(){
    return apply_replace('get_allow_tail_filename', array('', '_tail.php'));
}

function get_check_mod_rewrite(){

    if( function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules()) )
        $mod_rewrite = 1;
    elseif( isset($_SERVER['IIS_UrlRewriteModule']) )
        $mod_rewrite = 1;
    else
        $mod_rewrite = 0;

    return $mod_rewrite;
}

// 생성되면 안되는 게시판명
function get_bo_table_banned_word(){

    $folders = array();

    foreach(glob(GML_PATH.'/*', GLOB_ONLYDIR) as $dir) {
        $folders[] = basename($dir);
    }

    return apply_replace('get_bo_table_banned_word', $folders);
}

// 읽지 않은 메모 갯수 반환
function get_memo_not_read($mb_id, $add_where='')
{
    global $gml;

    $sql = " SELECT count(*) as cnt FROM {$gml['memo_table']} WHERE me_recv_mb_id = '$mb_id' and me_type= 'recv' and me_read_datetime like '0%' $add_where ";
    $row = sql_fetch($sql, false);

    return $row['cnt'];
}

function get_scrap_totals($mb_id=''){
    global $gml;

    $add_where = $mb_id ? " and mb_id = '$mb_id' " : '';

    $sql = " select count(*) as cnt from {$gml['scrap_table']} where 1=1 $add_where";
    $row = sql_fetch($sql, false);

    return $row['cnt'];
}

function get_member_profile_img($mb_id='', $width='', $height='', $alt='profile_image', $title=''){
    global $member;

    static $no_profile_cache = '';
    static $member_cache = array();

    $src = '';

    if( $mb_id ){
        if( isset($member_cache[$mb_id]) ){
            $src = $member_cache[$mb_id];
        } else {
            $member_img = GML_DATA_PATH.'/member_image/'.substr($mb_id,0,2).'/'.get_mb_icon_name($mb_id).'.gif';
            if (is_file($member_img)) {
                $member_cache[$mb_id] = $src = str_replace(GML_DATA_PATH, GML_DATA_URL, $member_img);
            }
        }
    }

    if( !$src ){
        if( !empty($no_profile_cache) ){
            $src = $no_profile_cache;
        } else {
            // 프로필 이미지가 없을때 기본 이미지
            $no_profile_img = (defined('GML_THEME_NO_PROFILE_IMG') && GML_THEME_NO_PROFILE_IMG) ? GML_THEME_NO_PROFILE_IMG : GML_NO_PROFILE_IMG;
            $tmp = array();
            preg_match( '/src="([^"]*)"/i', $foo, $tmp );
            $no_profile_cache = $src = isset($tmp[1]) ? $tmp[1] : GML_IMG_URL.'/no_profile.gif';
        }
    }

    if( $src ){
        $attributes = array('src'=>$src, 'width'=>$width, 'height'=>$height, 'alt'=>$alt, 'title'=>$title);

        $output = '<img';
        foreach ($attributes as $name => $value) {
            if (!empty($value)) {
                $output .= sprintf(' %s="%s"', $name, $value);
            }
        }
        $output .= '>';

        return $output;
    }

    return '';
}

function get_countries(){

    static $countries = array();

    if( $countries ){
        return $countries;
    }

    include(dirname(__FILE__) .'/countries.lib.php');

    $countries = apply_replace('get_countries', $countries);

    return $countries;
}

function get_board_gettext_titles($key=''){

    $g_key = preg_replace('/[^0-9a-z_]/i', '', strtolower($key));

    $board_titles = apply_replace('get_board_gettext_titles', array(
        'notice' => __('Notice'),
        'questionsanswer' => __('Questions Answer'),
        'freeboard' => __('Free Board'),
        'gallery' => __('Gallery'),
        ), $key);
    
    return isset($board_titles[$g_key]) ? $board_titles[$g_key] : $key;
}

function get_menu_gettext_titles($key=''){

    $g_key = preg_replace('/[^0-9a-z_]/i', '', strtolower($key));

    $menu_titles = apply_replace('get_menu_gettext_titles', array(), $key);
    
    return isset($menu_titles[$g_key]) ? $menu_titles[$g_key] : $key;
}

function get_add_mbhash_params($qstr, $board, $wr_id){

    if( isset($_GET['mb_hash']) && is_url_base64_encoded($_GET['mb_hash']) ){
        $qstr .= '&amp;mb_hash=' . strip_tags($_GET['mb_hash']);
    }

    return $qstr;
}

function get_board_sfl_select_options($sfl){

    global $is_admin;

    $str = '';
    $str .= '<option value="wr_subject" '.get_selected($sfl, 'wr_subject', true).'>'.__('Subject').'</option>';
    $str .= '<option value="wr_content" '.get_selected($sfl, 'wr_content').'>'.__('Content').'</option>';
    $str .= '<option value="wr_subject||wr_content" '.get_selected($sfl, 'wr_subject||wr_content').'>'.__('Subject+Content').'</option>';
    if ( $is_admin ){
        $str .= '<option value="mb_id,1" '.get_selected($sfl, 'mb_id,1').'>'.__('Member ID').'</option>';
        $str .= '<option value="mb_id,0" '.get_selected($sfl, 'mb_id,0').'>'.p__('Member ID(C)', 'Search comments using member ID').'</option>';
    }
    $str .= '<option value="wr_name,1" '.get_selected($sfl, 'wr_name,1').'>'.__('Writer').'</option>';
    $str .= '<option value="wr_name,0" '.get_selected($sfl, 'wr_name,0').'>'.__('Commenter').'</option>';

    return apply_replace('get_board_sfl_select_options', $str, $sfl);
}

function get_form_address($mbs, $html_attrs, $label_ids, $is_required='', $form_name=''){
    global $lang, $is_mobile;

    static $daum_juso_js = '';

    $sames = array('mb_zip'=>'', 'mb_country'=>'', 'mb_addr1'=>'', 'mb_addr2'=>'', 'mb_addr3'=>'');
    $mb = array_merge($sames, (array) $mbs);
    $attrs = array_merge($sames, (array) $html_attrs);
    $labels = array_merge($sames, (array) $label_ids);
    $str_required = $is_required ? 'required' : '';

    $countries = get_countries();

    $form_name = $form_name ? "'".$form_name."'" : 'this.form.name';
    $str = '';

    if( $lang === 'ko_KR' ){

        if( ! $daum_juso_js ){
            if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') {   //https 통신일때 daum 주소 js
                $daum_juso_js = '<script src="https://spi.maps.daum.net/imap/map_js_init/postcode.v2.js"></script>'.PHP_EOL;
            } else {  //http 통신일때 daum 주소 js
                $daum_juso_js = '<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>'.PHP_EOL;
            }

            $str .= $daum_juso_js;
        }

        $str .= '<label for="'.$labels['mb_zip'].'" class="sound_only">'.__('Postcode');
        if( $is_required ){
            $str .= '<strong class="sound_only"> '. __('Required').'</strong>';
        }
        $str .= '</label>';
        $str .= '<input type="text" name="mb_zip" value="'.$mb['mb_zip'].'" id="'.$labels['mb_zip'].'" '.$str_required.' '.$attrs['mb_zip'].' placeholder="'.__('Postcode').'">'.PHP_EOL;
        $str .= '<button type="button" class="btn_frmline postcode_btn" onclick="win_zip('.$form_name.', \'mb_zip\', \'mb_addr1\', \'mb_addr2\', \'mb_addr3\');">'.__('Search').'</button>';
        $str .= '<input type="hidden" name="mb_country" value="'.$mb['mb_country'].'" id="'.$labels['mb_country'].'" >';
        $str .= '<input type="text" name="mb_addr1" value="'.$mb['mb_addr1'].'" id="'.$labels['mb_addr1'].'" '.$str_required.' '.$attrs['mb_addr1'].' placeholder="'.__('Street address 1').'">';
        $str .= '<label for="'.$labels['mb_addr1'].'" class="sound_only">'.__('Street address 1').'</label><br>';
        $str .= '<input type="text" name="mb_addr2" value="'.$mb['mb_addr2'].'" id="'.$labels['mb_addr2'].'" '.$attrs['mb_addr2'].' placeholder="'.__('Street address 2').'">';
        $str .= '<label for="'.$labels['mb_addr2'].'" class="sound_only">'.__('Street address 2').'</label><br>';
        $str .= '<input type="hidden" name="mb_addr3" value="'.$mb['mb_addr3'].'" id="'.$labels['mb_addr3'].'" >';

    } else {

        $str .= '<label for="'.$labels['mb_zip'].'" class="sound_only">'.__('Postcode');
        if( $is_required ){
            $str .= '<strong class="sound_only"> '. __('Required').'</strong>';
        }
        $str .= '</label>';
        $str .= '<input type="text" name="mb_zip" value="'.$mb['mb_zip'].'" id="'.$labels['mb_zip'].'" '.$str_required.' '.$attrs['mb_zip'].' placeholder="'.__('Postcode').'"><br>';
        $str .= '<label for="'.$labels['mb_country'].'" class="sound_only">'.__('Country').'</label>';
        $str .= '<select name="mb_country" id="'.$labels['mb_country'].'" value="'.$mb['mb_country'].'" '.$attrs['mb_country'].'>';
        $str .= '<option>'.__('Select a country.').'</option>';
        foreach ( $countries as $key => $value ) {
            $selected = ($mb['mb_country'] === $key) ? 'selected="selected"' : '';
            $str .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
        }
        $str .= '</select>';
        $str .= '<input type="text" name="mb_addr1" value="'.$mb['mb_addr1'].'" id="'.$labels['mb_addr1'].'" '.$str_required.' '.$attrs['mb_addr1'].' placeholder="'.__('Street address 1').'">';
        $str .= '<label for="'.$labels['mb_addr1'].'" class="sound_only">'.__('Street address 1').'</label><br>';
        $str .= '<input type="text" name="mb_addr2" value="'.$mb['mb_addr2'].'" id="'.$labels['mb_addr2'].'" '.$attrs['mb_addr2'].' placeholder="'.__('Street address 2').'">';
        $str .= '<label for="'.$labels['mb_addr2'].'" class="sound_only">'.__('Street address 2').'</label><br>';
        $str .= '<input type="text" name="mb_addr3" value="'.$mb['mb_addr3'].'" id="'.$labels['mb_addr3'].'" '.$attrs['mb_addr3'].' placeholder="'.__('Town / City').'">';
        $str .= '<label for="'.$labels['mb_addr3'].'" class="sound_only">'.__('Town / City').'</label>'.PHP_EOL;
        $str .= '<link href="'.GML_JS_URL.'/select2/select2.min.css" rel="stylesheet" />'.PHP_EOL;
        $str .= '<script src="'.GML_JS_URL.'/select2/select2.min.js"></script>'.PHP_EOL;
        $str .= '<script>'.PHP_EOL;
        $str .= 'jQuery(document).ready(function($) {';
        $str .= '$("select#'.$labels['mb_country'].'").select2();';
        $str .= '});';
        $str .= '</script>'.PHP_EOL;
    }

    return apply_replace('get_form_address', $str, $mbs, $html_attrs, $label_ids, $is_required, $form_name);
}
?>
