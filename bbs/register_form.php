<?php
include_once('./_common.php');
include_once(GML_CAPTCHA_PATH.'/captcha.lib.php');
include_once(GML_LIB_PATH.'/register.lib.php');

define("_DONT_WRAP_IN_CONTAINER_", true);

// 불법접근을 막도록 토큰생성
$token = md5(uniqid(rand(), true));
set_session("ss_token", $token);
set_session("ss_cert_no",   "");
set_session("ss_cert_hash", "");
set_session("ss_cert_type", "");

if( $provider && function_exists('social_nonce_is_valid') ){   //모바일로 소셜 연결을 했다면
    if( social_nonce_is_valid(get_session("social_link_token"), $provider) ){  //토큰값이 유효한지 체크
        $w = 'u';   //회원 수정으로 처리
        $_POST['mb_id'] = $member['mb_id'];
    }
}

if ($w == "") {

    // 회원 로그인을 한 경우 회원가입 할 수 없다
    // 경고창이 뜨는것을 막기위해 아래의 코드로 대체
    // alert("이미 로그인중이므로 회원 가입 하실 수 없습니다.", "./");
    if ($is_member) {
        goto_url(GML_URL);
    }

    // 리퍼러 체크
    referer_check();

    if (!isset($_POST['agree']) || !$_POST['agree']) {
        alert(__('You must agree to the terms and conditions of your membership before you can sign up.'), GML_BBS_URL.'/register.php');
    }

    if (!isset($_POST['agree2']) || !$_POST['agree2']) {
        alert(__('You must agree to the information handling policy guidelines before you can sign up.'), GML_BBS_URL.'/register.php');
    }

    $agree  = preg_replace('#[^0-9]#', '', $_POST['agree']);
    $agree2 = preg_replace('#[^0-9]#', '', $_POST['agree2']);

    $member['mb_birth'] = '';
    $member['mb_sex']   = '';
    $member['mb_name']  = '';
    if (isset($_POST['birth'])) {
        $member['mb_birth'] = $_POST['birth'];
    }
    if (isset($_POST['sex'])) {
        $member['mb_sex']   = $_POST['sex'];
    }
    if (isset($_POST['mb_name'])) {
        $member['mb_name']  = $_POST['mb_name'];
    }

    $gml['title'] = __('Register');

} else if ($w == 'u') {

    if ($is_admin)
        alert(__('Please modify admin profile on the admin screen.'), GML_URL);

    if (!$is_member)
        alert(__('Please log in and use.'), GML_URL);

    if ($member['mb_id'] != $_POST['mb_id'])
        alert(__('The logged in member and the passed in information are different.'));

    if ($_POST['mb_password']) {
        // 수정된 정보를 업데이트후 되돌아 온것이라면 비밀번호가 암호화 된채로 넘어온것임
        if ($_POST['is_update'])
            $tmp_password = $_POST['mb_password'];
        else
            $tmp_password = get_encrypt_string($_POST['mb_password']);

        if ($member['mb_password'] != $tmp_password)
            alert(__('Incorrect password.'));
    }

    $gml['title'] = __('Edit profile');

    set_session("ss_reg_mb_name", $member['mb_name']);
    set_session("ss_reg_mb_hp", $member['mb_hp']);

    $member['mb_email']       = get_text($member['mb_email']);
    $member['mb_homepage']    = get_text($member['mb_homepage']);
    $member['mb_birth']       = get_text($member['mb_birth']);
    $member['mb_tel']         = get_text($member['mb_tel']);
    $member['mb_hp']          = get_text($member['mb_hp']);
    $member['mb_addr1']       = get_text($member['mb_addr1']);
    $member['mb_addr2']       = get_text($member['mb_addr2']);
    $member['mb_signature']   = get_text($member['mb_signature']);
    $member['mb_recommend']   = get_text($member['mb_recommend']);
    $member['mb_profile']     = get_text($member['mb_profile']);
    $member['mb_1']           = get_text($member['mb_1']);
    $member['mb_2']           = get_text($member['mb_2']);
    $member['mb_3']           = get_text($member['mb_3']);
    $member['mb_4']           = get_text($member['mb_4']);
    $member['mb_5']           = get_text($member['mb_5']);
    $member['mb_6']           = get_text($member['mb_6']);
    $member['mb_7']           = get_text($member['mb_7']);
    $member['mb_8']           = get_text($member['mb_8']);
    $member['mb_9']           = get_text($member['mb_9']);
    $member['mb_10']          = get_text($member['mb_10']);
} else {
    alert(sprintf(__('%s parameter is wrong'), 'w'));
}

include_once('./_head.php');

// Member icon path 회원아이콘 경로
$mb_icon_path = GML_DATA_PATH.'/member/'.substr($member['mb_id'],0,2).'/'.get_mb_icon_name($member['mb_id']).'.gif';
$mb_icon_url  = GML_DATA_URL.'/member/'.substr($member['mb_id'],0,2).'/'.get_mb_icon_name($member['mb_id']).'.gif';

// Member image path 회원이미지 경로
$mb_img_path = GML_DATA_PATH.'/member_image/'.substr($member['mb_id'],0,2).'/'.get_mb_icon_name($member['mb_id']).'.gif';
$mb_img_url  = GML_DATA_URL.'/member_image/'.substr($member['mb_id'],0,2).'/'.get_mb_icon_name($member['mb_id']).'.gif';

$register_action_url = GML_HTTPS_BBS_URL.'/register_form_update.php';
$req_nick = !isset($member['mb_nick_date']) || (isset($member['mb_nick_date']) && $member['mb_nick_date'] <= date("Y-m-d", GML_SERVER_TIME - ($config['cf_nick_modify'] * 86400)));
$required = ($w=='') ? 'required' : '';
$readonly = ($w=='u') ? 'readonly' : '';

$agree  = preg_replace('#[^0-9]#', '', $agree);
$agree2 = preg_replace('#[^0-9]#', '', $agree2);

// load language for l10n
bind_lang_domain( 'default', get_path_lang_dir('skin', $member_skin_path.'/'.GML_LANG_DIR) );

include_once($member_skin_path.'/register_form.skin.php');
include_once('./_tail.php');
?>
