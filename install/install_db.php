<?php
@set_time_limit(0);
$gmlnow = gmdate('D, d M Y H:i:s') . ' GMT';
header('Expires: 0'); // rfc2616 - Section 14.21
header('Last-Modified: ' . $gmlnow);
header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP/1.1
header('Pragma: no-cache'); // HTTP/1.0

include_once ('./install.header.php');

if( ! function_exists('safe_install_string_check') ){
    function safe_install_string_check( $str ) {
        $is_check = false;

        if(preg_match('#\);(passthru|eval|pcntl_exec|exec|system|popen|fopen|fsockopen|file|file_get_contents|readfile|unlink|include|include_once|require|require_once)\s?#i', $str)) {
            $is_check = true;
        }

        if(preg_match('#\$_(get|post|request)\s?\[.*?\]\s?\)#i', $str)){
            $is_check = true;
        }

        if($is_check){
            die(__('The value entered contains unsafe characters. Stop the installation.'));
        }

        return $str;
    }
}

$title = GML_VERSION." ".__('Installation complete')." 3/3";
include_once ('./install.inc.php');

$mysql_host  = safe_install_string_check($_POST['mysql_host']);
$mysql_user  = safe_install_string_check($_POST['mysql_user']);
$mysql_pass  = safe_install_string_check($_POST['mysql_pass']);
$mysql_db    = safe_install_string_check($_POST['mysql_db']);
$table_prefix= safe_install_string_check($_POST['table_prefix']);
$admin_id    = $_POST['admin_id'];
$admin_pass  = $_POST['admin_pass'];
$admin_name  = $_POST['admin_name'];
$admin_email = $_POST['admin_email'];

$dblink = sql_connect($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
if (!$dblink) {
?>

<div class="ins_inner">
    <p><?php echo sprintf(__('Please check %s.'), 'MySQL Host, User, Password'); ?></p>
    <div class="inner_btn"><a href="./install_config.php"><?php e__('Back'); ?></a></div>
</div>

<?php
    include_once ('./install.inc2.php');
    exit;
}

$select_db = sql_select_db($mysql_db, $dblink);
if (!$select_db) {
?>

<div class="ins_inner">
    <p><?php echo sprintf(__('Please check %s.'), 'MySQL DB'); ?></p>
    <div class="inner_btn"><a href="./install_config.php"><?php e__('Back'); ?></a></div>
</div>

<?php
    include_once ('./install.inc2.php');
    exit;
}

$mysql_set_mode = 'false';
sql_set_charset(GML_DB_CHARSET, $dblink);
$result = sql_query(" SELECT @@sql_mode as mode ", true, $dblink);
$row = sql_fetch_array($result);
if($row['mode']) {
    sql_query("SET SESSION sql_mode = ''", true, $dblink);
    $mysql_set_mode = 'true';
}
unset($result);
unset($row);
?>

<div class="ins_inner">
    <h2><?php echo sprintf(__('Installation of %s has started.'), GML_VERSION); ?></h2>

    <ol>
<?php
// Start Create DB TABLE ------------------------------------
$file = implode('', file('./gnuboardm.sql'));
eval("\$file = \"$file\";");

$file = preg_replace('/^--.*$/m', '', $file);
$file = preg_replace('/`gml_([^`]+`)/', '`'.$table_prefix.'$1', $file);

$f = explode(';', $file);
for ($i=0; $i<count($f); $i++) {
    if (trim($f[$i]) == '') continue;

    $sql = get_db_create_replace($f[$i]);

    sql_query($sql, true, $dblink);
}
// End Create DB TABLE ------------------------------------
?>

        <li><?php e__('Complete table creation'); ?></li>

<?php
$read_point = 0;
$write_point = 0;
$comment_point = 0;
$download_point = 0;

//-------------------------------------------------------------------------------------------------
// config 테이블 설정
$sql = " insert into `{$table_prefix}config`
            set cf_title = '".GML_VERSION."',
                cf_theme = 'basic',
                cf_admin = '$admin_id',
                cf_admin_email = '$admin_email',
                cf_admin_email_name = '".GML_VERSION."',
                cf_lang = '".$lang."',
                cf_bbs_rewrite = '".get_check_mod_rewrite()."',
                cf_use_point = '1',
                cf_use_copy_log = '1',
                cf_login_point = '100',
                cf_memo_send_point = '500',
                cf_cut_name = '15',
                cf_nick_modify = '60',
                cf_new_skin = 'basic',
                cf_new_rows = '15',
                cf_search_skin = 'basic',
                cf_connect_skin = 'basic',
                cf_read_point = '$read_point',
                cf_write_point = '$write_point',
                cf_comment_point = '$comment_point',
                cf_download_point = '$download_point',
                cf_write_pages = '10',
                cf_mobile_pages = '5',
                cf_link_target = '_blank',
                cf_delay_sec = '30',
                cf_filter = 'fuckyou, 凸, fucking, goddamn, scram, shutup',
                cf_possible_ip = '',
                cf_intercept_ip = '',
                cf_analytics = '',
                cf_member_skin = 'basic',
                cf_mobile_new_skin = 'basic',
                cf_mobile_search_skin = 'basic',
                cf_mobile_connect_skin = 'basic',
                cf_mobile_member_skin = 'basic',
                cf_faq_skin = 'basic',
                cf_mobile_faq_skin = 'basic',
                cf_editor = 'ckeditor4',
                cf_captcha_mp3 = 'basic',
                cf_register_level = '2',
                cf_register_point = '1000',
                cf_icon_level = '2',
                cf_leave_day = '30',
                cf_search_part = '10000',
                cf_email_use = '1',
                cf_prohibit_id = 'admin,administratorwebmaster,sysop,manager,root,su,guest',
                cf_prohibit_email = '',
                cf_new_del = '30',
                cf_memo_del = '180',
                cf_visit_del = '180',
                cf_popular_del = '180',
                cf_use_member_icon = '2',
                cf_member_icon_size = '5000',
                cf_member_icon_width = '22',
                cf_member_icon_height = '22',
                cf_member_img_size = '50000',
                cf_member_img_width = '60',
                cf_member_img_height = '60',
                cf_login_minutes = '10',
                cf_image_extension = 'gif|jpg|jpeg|png',
                cf_flash_extension = 'swf',
                cf_movie_extension = 'asx|asf|wmv|wma|mpg|mpeg|mov|avi|mp3',
                cf_formmail_is_member = '1',
                cf_page_rows = '15',
                cf_mobile_page_rows = '15',
                cf_cert_limit = '2',
                cf_stipulation = '".__('Enter the appropriate membership terms and conditions for the website.')."',
                cf_privacy = '".__('Enter the privacy policy that corresponds to the website.')."'
                ";
sql_query($sql, true, $dblink);

// 1:1문의 설정
$sql = " insert into `{$table_prefix}qa_config`
            ( qa_title, qa_category, qa_skin, qa_mobile_skin, qa_use_email, qa_req_email, qa_use_hp, qa_req_hp, qa_use_editor, qa_subject_len, qa_mobile_subject_len, qa_page_rows, qa_mobile_page_rows, qa_image_width, qa_upload_size, qa_insert_content )
          values
            ( '".__('1:1 inquiry')."', '".__('Member|Point')."', 'basic', 'basic', '1', '0', '1', '0', '1', '60', '30', '15', '15', '600', '1048576', '' ) ";
sql_query($sql, true, $dblink);

// 관리자 회원가입
$sql = " insert into `{$table_prefix}member`
            set mb_id = '$admin_id',
                 mb_password = PASSWORD('$admin_pass'),
                 mb_name = '$admin_name',
                 mb_nick = '$admin_name',
                 mb_email = '$admin_email',
                 mb_level = '10',
                 mb_mailling = '1',
                 mb_open = '1',
                 mb_email_certify = '".GML_TIME_YMDHIS."',
                 mb_datetime = '".GML_TIME_YMDHIS."',
                 mb_ip = '{$_SERVER['REMOTE_ADDR']}'
                 ";
sql_query($sql, true, $dblink);

// 내용관리 생성
sql_query(" insert into `{$table_prefix}content` set co_id = 'company', co_html = '1', co_subject = '".__('About Company')."', co_content= '<p align=center><b>".__('Please enter the information about your About Company.')."</b></p>' ", true, $dblink);
sql_query(" insert into `{$table_prefix}content` set co_id = 'privacy', co_html = '1', co_subject = '".__('Privacy Policy')."', co_content= '<p align=center><b>".__('Please enter your Privacy Policy.')."</b></p>' ", true, $dblink);
sql_query(" insert into `{$table_prefix}content` set co_id = 'provision', co_html = '1', co_subject = '".__('Terms of service')."', co_content= '<p align=center><b>".__('Please enter the service terms and conditions.')."</b></p>' ", true, $dblink);

// FAQ Master
sql_query(" insert into `{$table_prefix}faq_master` set fm_id = '1', fm_subject = '".__('Frequently Asked Questions')."' ", true, $dblink);

$tmp_gr_id = defined('GML_YOUNGCART_VER') ? 'shop' : 'community';
$tmp_gr_subject = defined('GML_YOUNGCART_VER') ? __('Shoppingmall') : __('Community');

// 게시판 그룹 생성
sql_query(" insert into `{$table_prefix}group` set gr_id = '$tmp_gr_id', gr_subject = '$tmp_gr_subject' ", true, $dblink);

// 게시판 생성
$tmp_bo_table   = array ("notice", "qa", "free", "gallery");
$tmp_bo_subject = array ('Notice', 'Questions Answer', 'Free Board', 'Gallery');
for ($i=0; $i<count($tmp_bo_table); $i++)
{

    $bo_skin = ($tmp_bo_table[$i] === 'gallery') ? 'gallery' : 'basic';

    $sql = " insert into `{$table_prefix}board`
                set bo_table = '$tmp_bo_table[$i]',
                    gr_id = '$tmp_gr_id',
                    bo_subject = '$tmp_bo_subject[$i]',
                    bo_device           = 'both',
                    bo_admin            = '',
                    bo_list_level       = '1',
                    bo_read_level       = '1',
                    bo_write_level      = '1',
                    bo_reply_level      = '1',
                    bo_comment_level    = '1',
                    bo_html_level       = '1',
                    bo_link_level       = '1',
                    bo_count_modify     = '1',
                    bo_count_delete     = '1',
                    bo_upload_level     = '1',
                    bo_download_level   = '1',
                    bo_read_point       = '0',
                    bo_write_point      = '0',
                    bo_comment_point    = '0',
                    bo_download_point   = '0',
                    bo_use_category     = '0',
                    bo_category_list    = '',
                    bo_use_sideview     = '0',
                    bo_use_file_content = '0',
                    bo_use_secret       = '0',
                    bo_use_dhtml_editor = '0',
                    bo_use_rss_view     = '0',
                    bo_use_good         = '0',
                    bo_use_nogood       = '0',
                    bo_use_name         = '0',
                    bo_use_signature    = '0',
                    bo_use_ip_view      = '0',
                    bo_use_list_view    = '0',
                    bo_use_list_content = '0',
                    bo_use_email        = '0',
                    bo_table_width      = '100',
                    bo_subject_len      = '60',
                    bo_mobile_subject_len      = '30',
                    bo_page_rows        = '15',
                    bo_mobile_page_rows = '15',
                    bo_new              = '24',
                    bo_hot              = '100',
                    bo_image_width      = '835',
                    bo_skin             = '$bo_skin',
                    bo_mobile_skin      = '$bo_skin',
                    bo_include_head     = '_head.php',
                    bo_include_tail     = '_tail.php',
                    bo_content_head     = '',
                    bo_content_tail     = '',
                    bo_mobile_content_head     = '',
                    bo_mobile_content_tail     = '',
                    bo_insert_content   = '',
                    bo_gallery_cols     = '4',
                    bo_gallery_width    = '202',
                    bo_gallery_height   = '150',
                    bo_mobile_gallery_width = '125',
                    bo_mobile_gallery_height= '100',
                    bo_upload_count     = '2',
                    bo_upload_size      = '1048576',
                    bo_reply_order      = '1',
                    bo_use_search       = '1',
                    bo_order            = '0'
                    ";
    sql_query($sql, true, $dblink);

    // 게시판 테이블 생성
    $file = file("../adm/sql_write.sql");
    $file = get_db_create_replace($file);

    $sql = implode($file, "\n");

    $create_table = $table_prefix.'write_' . $tmp_bo_table[$i];

    // sql_board.sql 파일의 테이블명을 변환
    $source = array("/__TABLE_NAME__/", "/;/");
    $target = array($create_table, "");
    $sql = preg_replace($source, $target, $sql);
    sql_query($sql, false, $dblink);
}
?>

        <li><?php e__('DB settings complete'); ?></li>

<?php
//-------------------------------------------------------------------------------------------------

// 디렉토리 생성
$dir_arr = array (
    $data_path.'/cache',
    $data_path.'/editor',
    $data_path.'/file',
    $data_path.'/log',
    $data_path.'/member',
    $data_path.'/member_image',
    $data_path.'/session',
    $data_path.'/content',
    $data_path.'/faq',
    $data_path.'/tmp'
);

for ($i=0; $i<count($dir_arr); $i++) {
    @mkdir($dir_arr[$i], GML_DIR_PERMISSION);
    @chmod($dir_arr[$i], GML_DIR_PERMISSION);
}
?>

        <li><?php e__('Data directory creation completed'); ?></li>

<?php
//-------------------------------------------------------------------------------------------------

// DB 설정 파일 생성
$file = '../'.GML_DATA_DIR.'/'.GML_DBCONFIG_FILE;
$f = @fopen($file, 'a');

fwrite($f, "<?php\n");
fwrite($f, "if (!defined('_GNUBOARD_')) exit;\n");
fwrite($f, "define('GML_MYSQL_HOST', '{$mysql_host}');\n");
fwrite($f, "define('GML_MYSQL_USER', '{$mysql_user}');\n");
fwrite($f, "define('GML_MYSQL_PASSWORD', '{$mysql_pass}');\n");
fwrite($f, "define('GML_MYSQL_DB', '{$mysql_db}');\n");
fwrite($f, "define('GML_MYSQL_SET_MODE', {$mysql_set_mode});\n\n");
fwrite($f, "define('GML_TABLE_PREFIX', '{$table_prefix}');\n\n");
fwrite($f, "\$gml['write_prefix'] = GML_TABLE_PREFIX.'write_'; // Board page name prefix\n\n");
fwrite($f, "\$gml['auth_table'] = GML_TABLE_PREFIX.'auth'; // Admin Permission Settings Table\n");
fwrite($f, "\$gml['config_table'] = GML_TABLE_PREFIX.'config'; // Config table\n");
fwrite($f, "\$gml['group_table'] = GML_TABLE_PREFIX.'group'; // Bulletin Group Table\n");
fwrite($f, "\$gml['group_member_table'] = GML_TABLE_PREFIX.'group_member'; // Board Group + Member Table\n");
fwrite($f, "\$gml['board_table'] = GML_TABLE_PREFIX.'board'; // Board Settings Table\n");
fwrite($f, "\$gml['board_file_table'] = GML_TABLE_PREFIX.'board_file'; // Board Attachment Table\n");
fwrite($f, "\$gml['board_good_table'] = GML_TABLE_PREFIX.'board_good'; // Good OR BAD table\n");
fwrite($f, "\$gml['board_new_table'] = GML_TABLE_PREFIX.'board_new'; // New Post Table\n");
fwrite($f, "\$gml['login_table'] = GML_TABLE_PREFIX.'login'; // Login table (number of users)\n");
fwrite($f, "\$gml['mail_table'] = GML_TABLE_PREFIX.'mail'; // Member mail Table\n");
fwrite($f, "\$gml['member_table'] = GML_TABLE_PREFIX.'member'; // Member Table\n");
fwrite($f, "\$gml['memo_table'] = GML_TABLE_PREFIX.'memo'; // Memo Table\n");
fwrite($f, "\$gml['poll_table'] = GML_TABLE_PREFIX.'poll'; // Poll Table\n");
fwrite($f, "\$gml['poll_etc_table'] = GML_TABLE_PREFIX.'poll_etc'; // Voting Other Comments Table\n");
fwrite($f, "\$gml['point_table'] = GML_TABLE_PREFIX.'point'; // point Table\n");
fwrite($f, "\$gml['popular_table'] = GML_TABLE_PREFIX.'popular'; // Popular search word table\n");
fwrite($f, "\$gml['scrap_table'] = GML_TABLE_PREFIX.'scrap'; // Post Scrap Table\n");
fwrite($f, "\$gml['visit_table'] = GML_TABLE_PREFIX.'visit'; // Visitors table\n");
fwrite($f, "\$gml['visit_sum_table'] = GML_TABLE_PREFIX.'visit_sum'; // Total Visitors Table\n");
fwrite($f, "\$gml['uniqid_table'] = GML_TABLE_PREFIX.'uniqid'; // Table for creating uniqid values\n");
fwrite($f, "\$gml['autosave_table'] = GML_TABLE_PREFIX.'autosave'; // Board temp post save Table\n");
fwrite($f, "\$gml['cert_history_table'] = GML_TABLE_PREFIX.'cert_history'; // Member Certification History Table\n");
fwrite($f, "\$gml['qa_config_table'] = GML_TABLE_PREFIX.'qa_config'; // 1:1 inquiry setup table\n");
fwrite($f, "\$gml['qa_content_table'] = GML_TABLE_PREFIX.'qa_content'; // 1:1 inquiry table\n");
fwrite($f, "\$gml['content_table'] = GML_TABLE_PREFIX.'content'; // Content Information Table\n");
fwrite($f, "\$gml['faq_table'] = GML_TABLE_PREFIX.'faq'; // Frequently Asked Questions Table\n");
fwrite($f, "\$gml['faq_master_table'] = GML_TABLE_PREFIX.'faq_master'; // Frequently asked questions master table\n");
fwrite($f, "\$gml['new_win_table'] = GML_TABLE_PREFIX.'new_win'; // Manage Popup layer Table\n");
fwrite($f, "\$gml['menu_table'] = GML_TABLE_PREFIX.'menu'; // Manage Menu table\n");
fwrite($f, "\$gml['social_profile_table'] = GML_TABLE_PREFIX.'member_social_profiles'; // Manage Social Login Table\n");
fwrite($f, "\$gml['notice_table'] = GML_TABLE_PREFIX.'notice'; // Notification Table\n");
fwrite($f, "\$gml['multi_lang_data_table'] = GML_TABLE_PREFIX.'multi_lang_data'; // Multi language data Table\n");
fwrite($f, "?>");

fclose($f);
@chmod($file, GML_FILE_PERMISSION);
?>

        <li><?php e__('DB settings file creation complete'); ?> (<?php echo $file ?>)</li>

<?php
// data 디렉토리 및 하위 디렉토리에서는 .htaccess .htpasswd .php .phtml .html .htm .inc .cgi .pl 파일을 실행할수 없게함.
$f = fopen($data_path.'/.htaccess', 'w');
$str = <<<EOD
<FilesMatch "\.(htaccess|htpasswd|[Pp][Hh][Pp]|[Pp][Hh][Tt]|[Pp]?[Hh][Tt][Mm][Ll]?|[Ii][Nn][Cc]|[Cc][Gg][Ii]|[Pp][Ll])">
Order allow,deny
Deny from all
</FilesMatch>
EOD;
fwrite($f, $str);
fclose($f);
//-------------------------------------------------------------------------------------------------
?>
    </ol>

    <p><?php sprintf(__('Congratulations!. Installation of %s is complete.'), GML_VERSION); ?></p>

</div>

<div class="ins_inner">

    <h2><?php e__('Follow these steps to change preferences.'); ?></h2>

    <ol>
        <li><?php e__('Go to Main Screen'); ?></li>
        <li><?php e__('Administrator login'); ?></li>
        <li><?php e__('Go to Administrator Page'); ?></li>
        <li><?php e__('Go to the Preferences page in the Preferences menu'); ?></li>
    </ol>

    <div class="inner_btn">
        <a href="../index.php"><?php e__('Go to the new GnuBoard M'); ?></a>
    </div>

</div>

<?php
include_once ('./install.inc2.php');
?>
