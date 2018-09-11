<?php
if (!defined('_GNUBOARD_')) exit;

/*
// 081022 : CSRF 방지를 위해 코드를 작성했으나 효과가 없어 주석처리 함
if (!get_session('ss_admin')) {
    set_session('ss_admin', true);
    goto_url('.');
}
*/

// 스킨디렉토리를 SELECT 형식으로 얻음
function get_skin_select($skin_gubun, $id, $name, $selected='', $event='')
{
    global $config;

    $skins = get_skin_dir($skin_gubun);

    $str = "<select id=\"$id\" name=\"$name\" $event>\n";
    for ($i=0; $i<count($skins); $i++) {
        if ($i == 0) $str .= "<option value=\"\">".__('Select')."</option>";

        $str .= option_selected($skins[$i], $selected, $skins[$i]);
    }
    $str .= "</select>";
    return $str;
}

// 모바일 스킨디렉토리를 SELECT 형식으로 얻음
function get_mobile_skin_select($skin_gubun, $id, $name, $selected='', $event='')
{
    global $config;

    $skins = get_skin_dir($skin_gubun, GML_MOBILE_PATH.'/'.GML_SKIN_DIR);

    $str = "<select id=\"$id\" name=\"$name\" $event>\n";
    for ($i=0; $i<count($skins); $i++) {
        if ($i == 0) $str .= "<option value=\"\">".__('Select')."</option>";

        $str .= option_selected($skins[$i], $selected, $skins[$i]);
    }
    $str .= "</select>";
    return $str;
}


// 스킨경로를 얻는다
function get_skin_dir($skin, $skin_path=GML_SKIN_PATH)
{
    global $gml;

    $result_array = array();

    $dirname = $skin_path.'/'.$skin.'/';
    if(!is_dir($dirname))
        return;

    $handle = opendir($dirname);
    while ($file = readdir($handle)) {
        if($file == '.'||$file == '..') continue;

        if (is_dir($dirname.$file)) $result_array[] = $file;
    }
    closedir($handle);
    sort($result_array);

    return $result_array;
}


// 테마
function get_theme_dir()
{
    $result_array = array();

    $dirname = GML_PATH.'/'.GML_THEME_DIR.'/';
    $handle = opendir($dirname);
    while ($file = readdir($handle)) {
        if($file == '.'||$file == '..') continue;

        if (is_dir($dirname.$file)) {
            $theme_path = $dirname.$file;
            if( (is_file($theme_path.'/index.php') && is_file($theme_path.'/head.php') && is_file($theme_path.'/tail.php')) ||  (is_file($theme_path.'/mobile/index.php') && is_file($theme_path.'/mobile/head.php') && is_file($theme_path.'/mobile/tail.php')) ) {
                $result_array[] = $file;
            }
        }
    }
    closedir($handle);
    natsort($result_array);

    return $result_array;
}


// 테마정보
function get_theme_info($dir)
{
    $info = array();
    $path = GML_PATH.'/'.GML_THEME_DIR.'/'.$dir;

    if(is_dir($path)) {
        $screenshot = $path.'/screenshot.png';
        if(is_file($screenshot)) {
            $size = @getimagesize($screenshot);

            if($size[2] == 3)
                $screenshot_url = str_replace(GML_PATH, GML_URL, $screenshot);
        }

        $info['screenshot'] = $screenshot_url;

        $text = $path.'/readme.txt';
        if(is_file($text)) {
            $content = file($text, false);
            $content = array_map('trim', $content);

            preg_match('#^Theme Name:(.+)$#i', $content[0], $m0);
            preg_match('#^Theme URI:(.+)$#i', $content[1], $m1);
            preg_match('#^Maker:(.+)$#i', $content[2], $m2);
            preg_match('#^Maker URI:(.+)$#i', $content[3], $m3);
            preg_match('#^Version:(.+)$#i', $content[4], $m4);
            preg_match('#^Detail:(.+)$#i', $content[5], $m5);
            preg_match('#^License:(.+)$#i', $content[6], $m6);
            preg_match('#^License URI:(.+)$#i', $content[7], $m7);

            $info['theme_name'] = trim($m0[1]);
            $info['theme_uri'] = trim($m1[1]);
            $info['maker'] = trim($m2[1]);
            $info['maker_uri'] = trim($m3[1]);
            $info['version'] = trim($m4[1]);
            $info['detail'] = trim($m5[1]);
            $info['license'] = trim($m6[1]);
            $info['license_uri'] = trim($m7[1]);
        }

        if(!$info['theme_name'])
            $info['theme_name'] = $dir;
    }

    return $info;
}


// 테마설정 정보
function get_theme_config_value($dir, $key='*')
{
    $tconfig = array();

    $theme_config_file = GML_PATH.'/'.GML_THEME_DIR.'/'.$dir.'/theme.config.php';
    if(is_file($theme_config_file)) {
        include($theme_config_file);

        if($key == '*') {
            $tconfig = $theme_config;
        } else {
            $keys = array_map('trim', explode(',', $key));
            foreach($keys as $v) {
                $tconfig[$v] = trim($theme_config[$v]);
            }
        }
    }

    return $tconfig;
}


// 회원권한을 SELECT 형식으로 얻음
function get_member_level_select($name, $start_id=0, $end_id=10, $selected="", $event="")
{
    global $gml;

    $str = "\n<select id=\"{$name}\" name=\"{$name}\"";
    if ($event) $str .= " $event";
    $str .= ">\n";
    for ($i=$start_id; $i<=$end_id; $i++) {
        $str .= '<option value="'.$i.'"';
        if ($i == $selected)
            $str .= ' selected="selected"';
        $str .= ">{$i}</option>\n";
    }
    $str .= "</select>\n";
    return $str;
}


// 회원아이디를 SELECT 형식으로 얻음
function get_member_id_select($name, $level, $selected="", $event="")
{
    global $gml;

    $sql = " select mb_id from {$gml['member_table']} where mb_level >= '{$level}' ";
    $result = sql_query($sql);
    $str = '<select id="'.$name.'" name="'.$name.'" '.$event.'><option value="">'.__('Unselected').'</option>';
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
        $str .= '<option value="'.$row['mb_id'].'"';
        if ($row['mb_id'] == $selected) $str .= ' selected';
        $str .= '>'.$row['mb_id'].'</option>';
    }
    $str .= '</select>';
    return $str;
}

// include head 관련 값을 SELECT 형식으로 얻음
function get_include_head_select($name, $id='', $selected='', $event='')
{
    $str = '<select id="'.$id.'" name="'.$name.'" '.$event.'>';

    foreach( get_allow_head_filename() as $value ){

        $selected_option = ($value === '') ? __('Unselected') : $value;

        $str .= '<option value="'.$value.'"';
        if ($value === $selected) $str .= ' selected';
        $str .= '>'.$selected_option.'</option>';
    }

    $str .= '</select>';
    return $str;
}

// include tail 관련 값을 SELECT 형식으로 얻음
function get_include_tail_select($name, $id='', $selected='', $event='')
{
    $str = '<select id="'.$id.'" name="'.$name.'" '.$event.'>';

    foreach( get_allow_tail_filename() as $value ){

        $selected_option = ($value === '') ? __('Unselected') : $value;

        $str .= '<option value="'.$value.'"';
        if ($value === $selected) $str .= ' selected';
        $str .= '>'.$selected_option.'</option>';
    }

    $str .= '</select>';
    return $str;
}

// 권한 검사
function auth_check($auth, $attr, $return=false)
{
    global $is_admin;

    if ($is_admin == 'super') return;

    if (!trim($auth)) {
        $msg = __('You do not have access to this menu.').'\\n\\n'.__('Access can only be granted by the Super Admin.');
        if($return)
            return $msg;
        else
            alert($msg);
    }

    $attr = strtolower($attr);

    if (!strstr($auth, $attr)) {
        if ($attr == 'r') {
            $msg = __('You do not have permission to read.');
            if($return)
                return $msg;
            else
                alert($msg);
        } else if ($attr == 'w') {
            $msg = __('You do not have the permissions to enter, add, create, or modify.');
            if($return)
                return $msg;
            else
                alert($msg);
        } else if ($attr == 'd') {
            $msg = __('You do not have permission to delete.');
            if($return)
                return $msg;
            else
                alert($msg);
        } else {
            $msg = __('Invalid property.');
            if($return)
                return $msg;
            else
                alert($msg);
        }
    }
}


// 작업아이콘 출력
function icon($act, $link='', $target='_parent')
{
    global $gml;

    $img = array('enter'=>'insert', 'add'=>'insert', 'create'=>'insert', 'edit'=>'modify', 'delete'=>'delete', 'move'=>'move', 'group'=>'move', 'view'=>'view', 'preview'=>'view', 'copy'=>'copy');
    $icon = '<img src="'.GML_ADMIN_PATH.'/img/icon_'.$img[$act].'.gif" title="'.$act.'">';
    if ($link)
        $s = '<a href="'.$link.'">'.$icon.'</a>';
    else
        $s = $icon;
    return $s;
}


// rm -rf 옵션 : exec(), system() 함수를 사용할 수 없는 서버 또는 win32용 대체
// www.php.net 참고 : pal at degerstrom dot com
function rm_rf($file)
{
    if (file_exists($file)) {
        if (is_dir($file)) {
            $handle = opendir($file);
            while($filename = readdir($handle)) {
                if ($filename != '.' && $filename != '..')
                    rm_rf($file.'/'.$filename);
            }
            closedir($handle);

            @chmod($file, GML_DIR_PERMISSION);
            @rmdir($file);
        } else {
            @chmod($file, GML_FILE_PERMISSION);
            @unlink($file);
        }
    }
}

// 입력 폼 안내문
function help($help="")
{
    global $gml;

    $str  = '<button type="button" class="tooltip_btn">?</button><span class="tooltip">'.str_replace("\n", "<br>", $help).'</span>';

    return $str;
}

// 출력순서
function order_select($fld, $sel='')
{
    $s = '<select name="'.$fld.'" id="'.$fld.'">';
    for ($i=1; $i<=100; $i++) {
        $s .= '<option value="'.$i.'" ';
        if ($sel) {
            if ($i == $sel) {
                $s .= 'selected';
            }
        } else {
            if ($i == 50) {
                $s .= 'selected';
            }
        }
        $s .= '>'.$i.'</option>';
    }
    $s .= '</select>';

    return $s;
}

// 불법접근을 막도록 토큰을 생성하면서 토큰값을 리턴
function get_admin_token()
{
    $token = md5(uniqid(rand(), true));
    set_session('ss_admin_token', $token);

    return $token;
}

//input value 에서 xss 공격 filter 역할을 함 ( 반드시 input value='' 타입에만 사용할것 )
function get_sanitize_input($s, $is_html=false){

    if(!$is_html){
        $s = strip_tags($s);
    }

    $s = htmlspecialchars($s, ENT_QUOTES, 'utf-8');

    return $s;
}

function alert_json_print($msg='', $url='', $error=true, $post=false){
    $tmp = array(
            'msg' => $msg, 'url' => $url, 'error' => $error, 'post' => $post
        );
    die(json_encode( $tmp ));
}

// POST로 넘어온 토큰과 세션에 저장된 토큰 비교
function check_admin_token()
{
    $token = get_session('ss_admin_token');
    set_session('ss_admin_token', '');
    if(!$token || !$_REQUEST['token'] || $token != $_REQUEST['token'])
        alert(__('Please use the correct method.'), GML_URL);

    return true;
}

function print_l10n_js_admin($js_file){
    switch ($js_file) {

        case 'theme_js':
        default :
            get_localize_script('theme_js_l10n',
            array(
            'theme_apply_text' => __('Do you want to apply the %s theme?'),  // // %s 테마를 적용하시겠습니까?
            'theme_setting_text' => __('Default preference, 1:1 contact skin change from theme to skin set?'),    // 기본환경설정, 1:1문의 스킨을 테마에서 설정된 스킨으로 변경하시겠습니까?
            'theme_skin_text' => __('Select Change to change member skin from theme to skin specified.'),  // 변경을 선택하시면 테마에서 지정된 스킨으로 회원스킨 등이 변경됩니다.
            'theme_disable_text' => __('Are you sure you want to disable the %s theme?'),    // %s 테마 사용설정을 해제하시겠습니까?
            'theme_disable_text2'  =>  __('Turning off a theme does not change the skin, such as a bulletin board, and requires individual changes.'),    // 테마 설정을 해제하셔도 게시판 등의 스킨은 변경되지 않으므로 개별 변경작업이 필요합니다.
            ),
            true);

            break;
    }
}
/**
 * @param string $directory    => DIRECTORY TO SCAN
 * @param string $regex        => REGULAR EXPRESSION TO BE USED IN MATCHING FILE-NAMES
 * @param string $get          => WHAT DO YOU WANT TO GET? 'dir'= DIRECTORIES, 'file'= FILES, 'both'=BOTH FILES+DIRECTORIES
 * @param bool   $useFullPath  => DO YOU WISH TO RETURN THE FULL PATH TO THE FOLDERS/FILES OR JUST THEIR BASE-NAMES?
 * @param array  $dirs         => LEAVE AS IS: USED DURING RECURSIVE TRIPS
 * @return array
 */
function scanDirRecursive($directory, $regex=null, $get="file", $useFullPath=false,  &$dirs=array(), &$files=array()) {

    if( !$directory || ! is_dir($directory) ){
        return array();
    }

    $iterator               = new DirectoryIterator ($directory);
    foreach($iterator as $info) {
        $fileDirName        = $info->getFilename();

        if ($info->isFile() && !preg_match("#^\..*?#", $fileDirName)) {
            if($get == 'file' || $get == 'both'){
                if($regex) {
                    if(preg_match($regex, $fileDirName)) {
                        if ($useFullPath) {
                            $files[] = $directory . DIRECTORY_SEPARATOR . $fileDirName;
                        }
                        else {
                            $files[] = $fileDirName;
                        }
                    }
                }else{
                    if($useFullPath){
                        $files[]   = $directory . DIRECTORY_SEPARATOR . $fileDirName;
                    }else{
                        $files[]   = $fileDirName;
                    }
                }
            }
        }else if ($info->isDir()  && !$info->isDot()) {
            $fullPathName   = $directory . DIRECTORY_SEPARATOR . $fileDirName;
            if($get == 'dir' || $get == 'both') {
                $dirs[]     = ($useFullPath) ? $fullPathName : $fileDirName;
            }
            scanDirRecursive($fullPathName, $regex, $get, $useFullPath, $dirs, $files);
        }
    }

    if($get == 'dir') {
        return $dirs;
    }else if($get == 'file'){
        return $files;
    }
    return array('dirs' => $dirs, 'files' => $files);
}

function get_theme_skin_dir_list($select_path, $is_skin_mobile=''){

    $skin_path = $is_skin_mobile ? $select_path.'/'.GML_MOBILE_DIR.'/'.GML_SKIN_DIR : $select_path.'/'.GML_SKIN_DIR;
    $skin_name_prefix = $is_skin_mobile ? GML_MOBILE_DIR.'/'.GML_SKIN_DIR.'/' : GML_SKIN_DIR.'/';

    $directorys = scandir($skin_path);
    $sub_dirs = array();
    $skin_sub_dirs = array();
    $social_folder = defined('GML_SOCIAL_LOGIN_DIR') ? GML_SOCIAL_LOGIN_DIR : '';

    foreach ($directorys as $item){

        if(empty($item)) continue;

        if ($item != '..' && $item != '.' && is_dir($skin_path . "/" . $item)){
            array_push($sub_dirs, $item);
        }
    }

    if( $sub_dirs ){
        foreach( $sub_dirs as $dir_name ){
            
            $skin_dir_path = $skin_path."/".$dir_name;
            
            if( $social_folder && $dir_name === $social_folder ){
                
                $skin_sub_dirs[] = $skin_name_prefix.$dir_name;

            } else {
                $skin_directorys = scandir($skin_dir_path);

                foreach ($skin_directorys as $skin_item){
                    if ($skin_item != '..' && $skin_item != '.' && is_dir($skin_dir_path . "/" . $skin_item)){
                        $skin_sub_dirs[] = $skin_name_prefix.$dir_name.'/'.$skin_item;
                    }
                }
            }
        }
    }

    return $skin_sub_dirs;
}

// 관리자 페이지 referer 체크
function admin_referer_check($return=false)
{
    $referer = trim($_SERVER['HTTP_REFERER']);
    if(!$referer) {
        $msg = __('Invalid information.');

        if($return)
            return $msg;
        else
            alert($msg, GML_URL);
    }

    $p = @parse_url($referer);

    $host = preg_replace('/:[0-9]+$/', '', $_SERVER['HTTP_HOST']);
    $msg = '';

    if($host != $p['host']) {
        $msg = __('Please use the correct method.');
    }

    if( $p['path'] && ! preg_match( '/\/'.preg_quote(GML_ADMIN_DIR).'\//i', $p['path'] ) ){
        $msg = __('Please use the correct method.');
    }

    if( $msg ){
        if($return) {
            return $msg;
        } else {
            alert($msg, GML_URL);
        }
    }
}

// 접근 권한 검사
if (!$member['mb_id'])
{
    alert(__('Please login.'), GML_BBS_URL.'/login.php?url=' . urlencode(GML_ADMIN_URL));
}
else if ($is_admin != 'super')
{
    $auth = array();
    $sql = " select au_menu, au_auth from {$gml['auth_table']} where mb_id = '{$member['mb_id']}' ";
    $result = sql_query($sql);
    for($i=0; $row=sql_fetch_array($result); $i++)
    {
        $auth[$row['au_menu']] = $row['au_auth'];
    }

    if (!$i)
    {
        alert(__('Only super admin or members with administrative privileges are allowed to access.'), GML_URL);
    }
}

// 관리자의 아이피, 브라우저와 다르다면 세션을 끊고 관리자에게 메일을 보낸다.
$admin_key = md5($member['mb_datetime'] . get_real_client_ip() . $_SERVER['HTTP_USER_AGENT']);
if (get_session('ss_mb_key') !== $admin_key) {

    session_destroy();

    include_once(GML_LIB_PATH.'/mailer.lib.php');
    // 메일 알림
    mailer($member['mb_nick'], $member['mb_email'], $member['mb_email'], __('XSS Attack Alert'), sprintf(__('There was an XSS attack with IP %s.'), get_real_client_ip()).'\n\n'.__('This is an approach to take over administrator rights, so be careful.').'\n\n'.__('Please block this IP and check if there are any suspicious postings.').'\n\n'.GML_URL, 0);

    alert_close(__('Please login and access normally.'));
}

@ksort($auth);

// 가변 메뉴
unset($auth_menu);
unset($menu);
unset($amenu);
$tmp = dir(GML_ADMIN_PATH);
$menu_files = array();
while ($entry = $tmp->read()) {
    if (!preg_match('/^admin.menu([0-9]{3}).*\.php$/', $entry, $m))
        continue;  // 파일명이 menu 으로 시작하지 않으면 무시한다.

    $amenu[$m[1]] = $entry;
    $menu_files[] = GML_ADMIN_PATH.'/'.$entry;
}
@asort($menu_files);
foreach($menu_files as $file){
    include_once($file);
}
@ksort($amenu);

$arr_query = array();
if (isset($sst))  $arr_query[] = 'sst='.$sst;
if (isset($sod))  $arr_query[] = 'sod='.$sod;
if (isset($sfl))  $arr_query[] = 'sfl='.$sfl;
if (isset($stx))  $arr_query[] = 'stx='.$stx;
if (isset($page)) $arr_query[] = 'page='.$page;
$qstr = implode("&amp;", $arr_query);

// 관리자에서는 추가 스크립트는 사용하지 않는다.
//$config['cf_add_script'] = '';
?>
