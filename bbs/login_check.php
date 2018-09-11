<?php
include_once('./_common.php');

$gml['title'] = __('Check Login');

$mb_id       = trim($_POST['mb_id']);
$mb_password = trim($_POST['mb_password']);

if (!$mb_id || !$mb_password)
    alert(__('The member ID or password must not be blank.'));

$mb = get_member($mb_id);

// social_login variable

$is_social_login = false;
$is_social_password_check = false;

// IF is Social Login ...
if(function_exists('social_is_login_check')){
    $is_social_login = social_is_login_check();

    //패스워드를 체크할건지 결정합니다.
    //소셜로그인일때는 체크하지 않고, 계정을 연결할때는 체크합니다.
    $is_social_password_check = social_is_login_password_check($mb_id);
}

//소셜 로그인이 맞다면 패스워드를 체크하지 않습니다.
// 가입된 회원이 아니다. 비밀번호가 틀리다. 라는 메세지를 따로 보여주지 않는 이유는
// 회원아이디를 입력해 보고 맞으면 또 비밀번호를 입력해보는 경우를 방지하기 위해서입니다.
// 불법사용자의 경우 회원아이디가 틀린지, 비밀번호가 틀린지를 알기까지는 많은 시간이 소요되기 때문입니다.
if (!$is_social_password_check && (!$mb['mb_id'] || !check_password($mb_password, $mb['mb_password'])) ) {
    alert(__('The member ID is not registered or the password is incorrect.').'\\n'.__('Passwords are case-sensitive.'));
}

// Check this blocked ID?
if ($mb['mb_intercept_date'] && $mb['mb_intercept_date'] <= date("Ymd", GML_SERVER_TIME)) {
    $date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1 - \\2 - \\3", $mb['mb_intercept_date']);
    alert(__('Your ID is not allowed to access.').'\n'.__('Processing date :').' '.$date);
}

// Check this leaved ID?
if ($mb['mb_leave_date'] && $mb['mb_leave_date'] <= date("Ymd", GML_SERVER_TIME)) {
    $date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1 - \\2 - \\3", $mb['mb_leave_date']);
    alert(__('You can not access this ID because it has leave.').'\n'.__('Date of withdrawal :').' '.$date);
}

// If you have mail authentication settings
if ( is_use_email_certify() && !preg_match("/[1-9]/", $mb['mb_email_certify'])) {
    $ckey = md5($mb['mb_ip'].$mb['mb_datetime']);
    confirm(sprintf(__('You can log in only if you have received %s email verification.'), $mb['mb_email']).__('Please click Cancel to change to another e-mail address to authenticate.'), GML_URL, GML_BBS_URL.'/register_email.php?mb_id='.$mb_id.'&ckey='.$ckey);
}

// load language for l10n
bind_lang_domain( 'default', get_path_lang_dir('skin', $member_skin_path.'/'.GML_LANG_DIR) );

@include_once($member_skin_path.'/login_check.skin.php');

// Create a member ID session
set_session('ss_mb_id', $mb['mb_id']);
// Prevent XSS, XSS 공격에 대응하기 위하여 회원의 고유키를 생성해 놓는다. 관리자에서 검사함
set_session('ss_mb_key', md5($mb['mb_datetime'] . get_real_client_ip() . $_SERVER['HTTP_USER_AGENT']));

// Check Points
if($config['cf_use_point']) {
    $sum_point = get_point_sum($mb['mb_id']);

    $sql= " update {$gml['member_table']} set mb_point = '$sum_point' where mb_id = '{$mb['mb_id']}' ";
    sql_query($sql);
}

// 3.26
// Save in ID cookie for 1 month 아이디 쿠키에 한달간 저장
if ($auto_login) {
    // 3.27
    // 자동로그인 ---------------------------
    // 쿠키 한달간 저장
    $key = md5($_SERVER['SERVER_ADDR'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . $mb['mb_password']);
    set_cookie('ck_mb_id', $mb['mb_id'], 86400 * 31);
    set_cookie('ck_auto', $key, 86400 * 31);
    // 자동로그인 end ---------------------------
} else {
    set_cookie('ck_mb_id', '', 0);
    set_cookie('ck_auto', '', 0);
}

if ($url) {
    // url 체크
    check_url_host($url, '', GML_URL, true);

    $link = urldecode($url);
    // 2003-06-14 추가 (다른 변수들을 넘겨주기 위함)
    if (preg_match("/\?/", $link))
        $split= "&amp;";
    else
        $split= "?";

    // $_POST 배열변수에서 아래의 이름을 가지지 않은 것만 넘김
    $post_check_keys = array('mb_id', 'mb_password', 'x', 'y', 'url');
    
    //소셜 로그인 추가
    if($is_social_login){
        $post_check_keys[] = 'provider';
    }

    foreach($_POST as $key=>$value) {
        if ($key && !in_array($key, $post_check_keys)) {
            $link .= "$split$key=$value";
            $split = "&amp;";
        }
    }

} else  {
    $link = GML_URL;
}

// IF social login
if(function_exists('social_login_success_after')){
    // 로그인 성공시 소셜 데이터를 기존의 데이터와 비교하여 바뀐 부분이 있으면 업데이트 합니다.
    $link = social_login_success_after($mb, $link);
    social_login_session_clear(1);
}

start_event('member_login_check', $mb, $link);

goto_url($link);
?>
