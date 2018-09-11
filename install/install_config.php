<?php
$gmlnow = gmdate('D, d M Y H:i:s').' GMT';
header('Expires: 0'); // rfc2616 - Section 14.21
header('Last-Modified: ' . $gmlnow);
header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP/1.1
header('Pragma: no-cache'); // HTTP/1.0

include_once ('./install.header.php');

$title = GML_VERSION." ".__('Initial Preferences')." 2/3";
include_once ('./install.inc.php');

$install_mininum_php_version = '5.3';
$errors = array();

if( version_compare( PHP_VERSION, $install_mininum_php_version, '<' ) ){
    $errors[] = sprintf(__('Requires php ( %s ) version or higher to install. Can not install the Current php ( %s ) version because it is low.'), $install_mininum_php_version, PHP_VERSION);
}

if (!isset($_POST['agree']) || $_POST['agree'] != 'agree') {
    $errors[] = __('You must accept the license to continue with the installation.');
}

if($errors){
    
    foreach($errors as $error){
        echo "<div class=\"ins_inner\"><p>".$error."</p>".PHP_EOL;
    }

    echo "<div class=\"inner_btn\"><a href=\"./\">".__('Back')."</a></div></div>".PHP_EOL;
    exit;
}
?>


<form id="frm_install" method="post" action="./install_db.php" autocomplete="off" onsubmit="return frm_install_submit(this)">

<div class="ins_inner ins_inner2">
    <div class="ins_left">
        <div class="ins_frm">
            <h2><?php echo sprintf(__('Enter %s information'), 'MYSQL'); ?></h2>
            <li>
                <label for="mysql_host">Host</label>
                <input name="mysql_host" type="text" value="localhost" id="mysql_host" class="ipt_text">
            </li>
            <li>
                <label for="mysql_user">User</label>
                <input name="mysql_user" type="text" id="mysql_user" class="ipt_text">
            </li>
            <li>
                <label for="mysql_pass">Password</label>
                <input name="mysql_pass" type="text" id="mysql_pass" class="ipt_text">
            </li>
            <li>
                <label for="mysql_db">DB</label>
                <input name="mysql_db" type="text" id="mysql_db" class="ipt_text">
            </li>
            <li>
                <label for="table_prefix"><?php e__('Table name prefix'); ?></label>
                <input name="table_prefix" type="text" value="gml_" id="table_prefix" class="ipt_text">
                <span><?php e__('Do not make any possible changes.'); ?></span>
            </li>
        </div>
        
        <div class="ins_frm">
            <h2><?php e__('Enter Super administrator information'); ?></h2>
            <li>
                <label for="admin_id"><?php e__('Member ID'); ?></label></th>
                <input name="admin_id" type="text" value="admin" id="admin_id" class="ipt_text">
            </li>
            <li>
                <label for="admin_pass"><?php e__('Password'); ?></label></th>
                <input name="admin_pass" type="text" id="admin_pass" class="ipt_text">
            </li>
            <li>
                <label for="admin_name"><?php e__('Name'); ?></label></th>
                <input name="admin_name" type="text" value="<?php e__('Super_Admin'); ?>" id="admin_name" class="ipt_text">
            </li>
            <li>
                <label for="admin_email"><?php e__('E-mail'); ?></label></th>
                <input name="admin_email" type="text" value="admin@domain.com" id="admin_email" class="ipt_text">
            </li>
        </div>

        <div class="inner_btn">
            <input type="submit" value="<?php e__('Next'); ?>">
        </div>
    </div>
    <div class="ins_right">
        <i class="fa fa-pencil"></i>
        <h2><?php e__('Entering Information'); ?></h2>
        <p>
            <strong class="st_strong"><?php echo sprintf(__('WARNING! If %s already exists, DB data will be lost, so be careful.'), GML_VERSION); ?></strong><br>
            <?php e__('You understand the precautions, and click Next to proceed with the installation of the GNUBOARD.'); ?>
        </p>
        <div class="ins_progress">
            <h3><?php e__('Installation order'); ?></h3>
            <ol>
                <li><span class="ins_num">1</span><span class="ins_text"><?php e__('License'); ?></span></li>
                <li class="plogress_sl"><span class="ins_num">2</span><span class="ins_text"><?php e__('Entering Information'); ?></span></li>
                <li><span class="ins_num">3</span><span class="ins_text"><?php e__('Install'); ?></span></li>
            </ol>
        </div>

    </div>

</div>
</form>
<?php
get_localize_script('install_config',
array(
'enter_check_msg'=>__('Enter %s.'),  // %s 를 입력하십시오.
'admin_id_msg'=>__('Admin ID'),    // 관리자 아이디
'admin_password_msg' => __('Admin Password'),  //관리자 비밀번호
'admin_name_msg' => __('Admin Name'),  //관리자 이름
'admin_email_msg' => __('Admin E-mail'),  //관리자 E-mail
'validate_msg' => __('The %s contains invalid characters. Please replace it with another letter.'),   //%s 에 유효하지 않는 문자가 있습니다. 다른 문자로 대체해 주세요.
'table_prefix' => __('Table name prefix'),
'id_check_msg' => __('You must create a super administrator member ID with the first character only in alphabetic and alphabetic and numeric characters.'),
),
true);
?>
<script>
function frm_install_submit(f)
{
    if (f.mysql_host.value == '')
    {
        alert(js_sprintf(install_config.enter_check_msg, 'MySQL Host')); f.mysql_host.focus(); return false;
    }
    else if (f.mysql_user.value == '')
    {
        alert(js_sprintf(install_config.enter_check_msg, 'MySQL User')); f.mysql_user.focus(); return false;
    }
    else if (f.mysql_db.value == '')
    {
        alert(js_sprintf(install_config.enter_check_msg, 'MySQL DB')); f.mysql_db.focus(); return false;
    }
    else if (f.admin_id.value == '')
    {
        alert(js_sprintf(install_config.enter_check_msg, install_config.admin_id_msg)); f.admin_id.focus(); return false;
    }
    else if (f.admin_pass.value == '')
    {
        alert(js_sprintf(install_config.enter_check_msg, install_config.admin_password_msg)); f.admin_pass.focus(); return false;
    }
    else if (f.admin_name.value == '')
    {
        alert(js_sprintf(install_config.enter_check_msg, install_config.admin_name_msg)); f.admin_name.focus(); return false;
    }
    else if (f.admin_email.value == '')
    {
        alert(js_sprintf(install_config.enter_check_msg, install_config.admin_email_msg)); f.admin_email.focus(); return false;
    }

    var reg = /\);(passthru|eval|pcntl_exec|exec|system|popen|fopen|fsockopen|file|file_get_contents|readfile|unlink|include|include_once|require|require_once)\s?\(\$_(get|post|request)\s?\[.*?\]\s?\)/gi;

    if( reg.test(f.mysql_host.value) ){
        alert(js_sprintf(install_config.validate_msg, "MySQL Host")); f.mysql_host.focus(); return false;
    }

    if( reg.test(f.mysql_user.value) ){
        alert(js_sprintf(install_config.validate_msg, "MySQL User")); f.mysql_user.focus(); return false;
    }

    if( f.mysql_pass.value && reg.test(f.mysql_pass.value) ){
        alert(js_sprintf(install_config.validate_msg, "MySQL PASSWORD")); f.mysql_pass.focus(); return false;
    }

    if( reg.test(f.mysql_db.value) ){
        alert(js_sprintf(install_config.validate_msg, "MySQL DB")); f.mysql_db.focus(); return false;
    }

    if( f.table_prefix.value && reg.test(f.table_prefix.value) ){
        alert(js_sprintf(install_config.validate_msg, install_config.table_prefix)); f.table_prefix.focus(); return false;
    }

    if(/^[a-z][a-z0-9]/i.test(f.admin_id.value) == false) {
        alert(install_config.id_check_msg);
        f.admin_id.focus();
        return false;
    }

    return true;
}
</script>

<?php
include_once ('./install.inc2.php');
?>