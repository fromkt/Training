<?php
$sub_menu = "100100";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

if (apply_replace('check_admin_permission', $is_admin != 'super', $is_admin)){
    alert(__('Only the Super administrator can access it.'));
}

$config = apply_replace('load_admin_config', $config, $is_admin);

//다국어 관련 필드
if( ! isset($config['cf_lang']) ) {
    sql_query("ALTER TABLE `{$gml['config_table']}`
                ADD `cf_lang` char(10) NOT NULL DEFAULT '' AFTER `cf_admin_email_name`
    ", true);
}

if( ! isset($config['cf_use_multi_lang_data']) ) {
    sql_query("ALTER TABLE `{$gml['config_table']}`
                ADD `cf_use_multi_lang_data` tinyint(4) NOT NULL DEFAULT '0' AFTER `cf_lang`
    ", true);
}

if (!isset($config['cf_syndi_token'])) {
    sql_query(" ALTER TABLE `{$gml['config_table']}`
                    ADD `cf_syndi_token` VARCHAR(255) NOT NULL AFTER `cf_add_meta` ", true);
}

if (!isset($config['cf_syndi_except'])) {
    sql_query(" ALTER TABLE `{$gml['config_table']}`
                    ADD `cf_syndi_except` TEXT NOT NULL AFTER `cf_syndi_token` ", true);
}

if (!isset($config['cf_bbs_rewrite'])) {
    sql_query(" ALTER TABLE `{$gml['config_table']}`
                    ADD `cf_bbs_rewrite` tinyint(4) NOT NULL DEFAULT '0' AFTER `cf_link_target` ", true);
}

if(!$config['cf_faq_skin']) $config['cf_faq_skin'] = "basic";
if(!$config['cf_mobile_faq_skin']) $config['cf_mobile_faq_skin'] = "basic";

$gml['title'] = __('Basic settings');   //기본설정
include_once ('./admin.head.php');

if (!$config['cf_icode_server_ip'])   $config['cf_icode_server_ip'] = '211.172.232.124';
if (!$config['cf_icode_server_port']) $config['cf_icode_server_port'] = '7295';

if ($config['cf_sms_use'] && $config['cf_icode_id'] && $config['cf_icode_pw']) {
    $userinfo = get_icode_userinfo($config['cf_icode_id'], $config['cf_icode_pw']);
}

// 알림 테이블이 없을 경우 생성
if(!sql_query(" DESC {$gml['notice_table']} ", false)) {
    sql_query(" CREATE TABLE IF NOT EXISTS `{$gml['notice_table']}` (
        `no_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `no_case` varchar(50) NOT NULL DEFAULT '',
        `mb_id` varchar(20) NOT NULL DEFAULT '0',
        `rel_mb_id` varchar(20) NOT NULL DEFAULT '0',
        `bo_table` varchar(20) NOT NULL DEFAULT '',
        `wr_id` int(11) NOT NULL DEFAULT '0',
        `rel_wr_id` int(11) NOT NULL DEFAULT '0',
        `no_notice_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
        `no_read_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY (`no_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8; ", false);
}

// 알림 삭제 지정일 설정값 추가
if(!isset($config['cf_notice_del'])) {
    sql_query(" ALTER TABLE `{$gml['config_table']}`
                ADD `cf_notice_del` int(11) NOT NULL DEFAULT '0' AFTER `cf_popular_del`"
    , true);

    $sql = " update {$gml['config_table']} set cf_notice_del = 60 ";
    sql_query($sql, false);

    $config['cf_notice_del'] = 60;
}

// 알림 수 컬럼
if(!isset($member['mb_notice_cnt'])) {
    sql_query(" ALTER TABLE `{$gml['member_table']}`
                ADD `mb_notice_cnt` int(11) NOT NULL DEFAULT '0' AFTER `mb_memo_call`
              ", true);
}

// 읽지 않은 메모 수, 스크랩 수 칼럼
if(!isset($member['mb_memo_cnt'])) {
    sql_query(" ALTER TABLE `{$gml['member_table']}`
                ADD `mb_memo_cnt` int(11) NOT NULL DEFAULT '0' AFTER `mb_notice_cnt`,
                ADD `mb_scrap_cnt` int(11) NOT NULL DEFAULT '0' AFTER `mb_memo_cnt`
              ", true);
}

if(!isset($config['cf_github_clientid'])) {
    sql_query(" ALTER TABLE `{$gml['config_table']}`
                    ADD `cf_github_clientid` VARCHAR(255) NOT NULL AFTER `cf_google_secret`,
                    ADD `cf_github_secret` VARCHAR(255) NOT NULL AFTER `cf_github_clientid` ", true);
}

// 접속자 정보 필드 추가
if(!sql_query(" select vi_browser from {$gml['visit_table']} limit 1 ")) {
    sql_query(" ALTER TABLE `{$gml['visit_table']}`
                    ADD `vi_browser` varchar(255) NOT NULL DEFAULT '' AFTER `vi_agent`,
                    ADD `vi_os` varchar(255) NOT NULL DEFAULT '' AFTER `vi_browser`,
                    ADD `vi_device` varchar(255) NOT NULL DEFAULT '' AFTER `vi_os` ", true);
}

// 다국어 데이터 테이블이 없을 경우 생성
if(!sql_query(" DESC {$gml['multi_lang_data_table']} ", false)) {
    sql_query(" CREATE TABLE IF NOT EXISTS `{$gml['multi_lang_data_table']}` (
        `ml_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `ml_case` varchar(50) NOT NULL DEFAULT '',
        `ml_lang` varchar(20) NOT NULL DEFAULT '0',
        `ml_target_id` varchar(20) NOT NULL DEFAULT '0',
        `ml_target_column` varchar(50) NOT NULL DEFAULT '0',
        `ml_target_value` text NOT NULL,
        PRIMARY KEY (`ml_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8; ", false);
}

// if(!isset($config['cf_instagram_clientid'])) {
//     sql_query(" ALTER TABLE `{$gml['config_table']}`
//                     ADD `cf_instagram_clientid` VARCHAR(255) NOT NULL AFTER `cf_google_secret`,
//                     ADD `cf_instagram_secret` VARCHAR(255) NOT NULL AFTER `cf_instagram_clientid` ", true);
// }

add_javascript('<script src="'.GML_ADMIN_URL.'/js/horizon-swiper.min.js"></script>', 10);
add_stylesheet('<link rel="stylesheet" href="'.GML_ADMIN_URL.'/js/horizon-swiper.min.css">', 1);

?>

<div class="anchor horizon-swiper">
    <div class="horizon-item"><a href="#anc_cf_basic"><?php e__('Basic environment');    //기본환경 ?></a></div><div class="horizon-item"><a href="#anc_cf_board"><?php e__('Board');     //게시판 ?></a></div><div class="horizon-item"><a href="#anc_cf_join"><?php e__('Member');   //회원 ?></a></div><div class="horizon-item"><a href="#anc_cf_mail"><?php e__('Mail');      //메일 ?></a></div><div class="horizon-item"><a href="#anc_cf_sns"><?php e__('SNS');        //SNS ?></a></div><div class="horizon-item"><a href="#anc_cf_lay"><?php echo p__('Add Code', 'Add html head css, script');  //코드 추가 ?></a></div><div class="horizon-item"><a href="#anc_cf_extra"><?php e__('DB EXTRA FIELD');  //DB EXTRA FIELD ?></a></div>
    <?php start_event('admin_config_form_anchor'); ?>
</div>

<form name="fconfigform" id="fconfigform" method="post" onsubmit="return fconfigform_submit(this);">
<input type="hidden" name="token" value="" id="token">

<button id="anc_cf_basic" type="button" class="<?php echo GML_ADMIN_HTML_TAB_CLASS; ?>"><?php e__('Basic environment');    //기본환경 ?></button>
<section class="<?php echo GML_ADMIN_HTML_CON_CLASS; ?>">
    <h2 class="h2_frm"><?php e__('Basic environment');    //기본환경 ?></h2>
    <ul class="frm_ul">
        <li class="li_clear">
            <span class="lb_block">
                <label for="cf_lang"><?php e__('Site Language'); ?><strong class="sound_only"><?php e__('required'); ?></strong></label>
            </span>
            <?php echo get_lang_select_html('cf_lang', $config['cf_lang'], ''); ?>
        </li>
        <li class="li_50">
            <span class="lb_block"><label for="cf_bbs_rewrite"><?php ep__('Board Short URL', 'Board use rewrite');  //게시판 짧은주소 ?><strong class="sound_only"><?php e__('required'); ?></strong></label> <?php echo help(__('Use short URLs for Boards, content pages, and FAQ pages.')); ?></span>
            <input type="checkbox" name="cf_bbs_rewrite" value="1" id="cf_bbs_rewrite" <?php echo (!empty($config['cf_bbs_rewrite']))?'checked':''; ?>> <label for="cf_bbs_rewrite"><?php e__('Enable on check'); ?></label>
        </li>
        <li class="li_50">
            <span class="lb_block"><label for="cf_use_multi_lang_data"><?php e__('Use Multi Language Data');   //다국어 데이터 사용 ?></label> <?php echo help(__('When enabled, content management and FAQ management pages can be entered and printed by language. When disabled, it integrates as one and inputs and outputs.')); ?></span>
            <input type="checkbox" name="cf_use_multi_lang_data" value="1" id="cf_use_multi_lang_data" <?php echo $config['cf_use_multi_lang_data']?'checked':''; ?>> <label for="cf_use_multi_lang_data"><?php e__('Enable on check'); ?></label>
        </li>
        <li class="li_clear">
            <span class="lb_block">
                <label for="cf_title"><?php e__('Site Title'); ?><strong class="sound_only"><?php e__('required'); ?></strong></label>
            </span>
            <input type="text" name="cf_title" value="<?php echo $config['cf_title'] ?>" id="cf_title" required class="required frm_input m_full_input" size="40">
        </li>
        <li>
            <span class="lb_block"><label for="cf_admin"><?php e__('Super administrator');   //최고관리자 ?><strong class="sound_only"><?php e__('required'); ?></strong></label></span>
            <?php echo get_member_id_select('cf_admin', 10, $config['cf_admin'], 'required') ?>
        </li>
        <li class="li_50">
            <span class="lb_block"><label for="cf_admin_email"><?php e__('Admin email address'); //관리자 메일 주소 ?><strong class="sound_only"><?php e__('required'); ?></strong></label>
            <?php echo help(__('Enter the email address used by the administrator for sending and receiving. (Used in sign-up, certified mail, testing, sending member mails, etc.')); ?></span>
            <input type="text" name="cf_admin_email" value="<?php echo $config['cf_admin_email'] ?>" id="cf_admin_email" required class="required email frm_input frm_input_full" size="40">

        </li>
        <li class="li_50">
            <span class="lb_block"><label for="cf_admin_email_name"><?php e__('Admin mailing name');   //관리자 메일 발송이름 ?><strong class="sound_only"><?php e__('required'); ?></strong></label>
            <?php echo help(__('Type the shipping name of the mail that the administrator uses for sending and receiving purposes. (Used in sign-up, certified mail, testing, sending member mails, etc.)')); ?></span>
            <input type="text" name="cf_admin_email_name" value="<?php echo $config['cf_admin_email_name'] ?>" id="cf_admin_email_name" required class="required frm_input frm_input_full" size="40">

        </li>
        <li class="li_clear">
            <span class="lb_block"><label for="cf_use_point"><?php e__('Using Points');   //포인트 사용 ?></label></span>
            <input type="checkbox" name="cf_use_point" value="1" id="cf_use_point" <?php echo $config['cf_use_point']?'checked':''; ?>> <?php e__('used');   //사용 ?>
        </li>
        <li class="li_50">
            <span class="lb_block"><label for="cf_login_point"><?php e__('Login point');     //로그인 포인트 ?><strong class="sound_only"><?php e__('required'); ?></strong></label>
            <?php echo help(__('Set up only once a day when a member logs in')); ?></span>
            <input type="text" name="cf_login_point" value="<?php echo $config['cf_login_point'] ?>" id="cf_login_point" required class="required frm_input" size="5"> <?php e__('point'); ?>
        </li>
        <li class="li_50">
            <span class="lb_block"><label for="cf_memo_send_point"><?php e__('Deduction point when sending memo');      //쪽지보낼시 차감 포인트 ?><strong class="sound_only"><?php e__('required'); ?></strong></label>
             <?php echo help(__('Please enter a positive number. 0 points do not deduct points when sending a note.')); ?></span>
            <input type="text" name="cf_memo_send_point" value="<?php echo $config['cf_memo_send_point'] ?>" id="cf_memo_send_point" required class="required frm_input" size="5"> <?php e__('point'); ?>

        </li>
        <li class="li_clear">
           <span class="lb_block"> <label for="cf_cut_name"><?php e__('Display Name');   //이름(닉네임) 표시 ?></label></span>
            <input type="text" name="cf_cut_name" value="<?php echo $config['cf_cut_name'] ?>" id="cf_cut_name" class="frm_input" size="5"> <?php e__('Display Cut string');   //자리만 표시 ?>
        </li>
        <li class="li_50">
            <span class="lb_block"><label for="cf_nick_modify"><?php e__('Edit nickname');   //닉네임 수정 ?></label></span>
            <input type="text" name="cf_nick_modify" value="<?php echo $config['cf_nick_modify'] ?>" id="cf_nick_modify" class="frm_input" size="3"> <?php e__('Could not edit for input days');     // 입력한 일 동안 수정할수 없습니다 ?>
        </li>
        <li class="li_50">
            <span class="lb_block"><label for="cf_open_modify"><?php e__('Edit information disclosure');    //정보공개 수정 ?></label></span>
            <input type="text" name="cf_open_modify" value="<?php echo $config['cf_open_modify'] ?>" id="cf_open_modify" class="frm_input" size="3"> <?php e__('Could not edit for input days');     // 입력한 일 동안 수정할수 없습니다 ?>
        </li>
        <li class="li_clear  li_50">
            <span class="lb_block"><label for="cf_new_del"><?php e__('Remove latest posts');    //최근게시물 삭제 ?></label>
            <?php echo help(__('Automatically delete new posts past the set date')); ?></span>
            <input type="text" name="cf_new_del" value="<?php echo $config['cf_new_del'] ?>" id="cf_new_del" class="frm_input" size="5"> <?php e__('days'); ?>

        </li>
        <li class="li_50">
            <span class="lb_block"><label for="cf_memo_del"><?php e__('Remove memo');    //쪽지 삭제 ?></label>
            <?php echo help(__('Automatically delete memos past the set date')) ?></span>
            <input type="text" name="cf_memo_del" value="<?php echo $config['cf_memo_del'] ?>" id="cf_memo_del" class="frm_input" size="5"> <?php e__('days'); ?>

        </li>
        <li class="li_50 li_clear">
            <span class="lb_block"><label for="cf_visit_del"><?php e__('Remove visitor log');    //접속자 로그 삭제 ?></label>
            <?php echo help(__('Automatically delete visiter logs older than the set date')); ?></span>
            <input type="text" name="cf_visit_del" value="<?php echo $config['cf_visit_del'] ?>" id="cf_visit_del" class="frm_input" size="5"> <?php e__('days'); ?>

        </li>
        <li class="li_50 li_clear">
            <span class="lb_block"><label for="cf_notice_del"><?php e__('Remove notifications');    //알림 삭제 ?></label>
            <?php echo help(__('Automatically delete notification past the set date')); ?></span>
            <input type="text" name="cf_notice_del" value="<?php echo $config['cf_notice_del'] ?>" id="cf_notice_del" class="frm_input" size="5"> <?php e__('days'); ?>

        </li>
        <li class="li_50">
            <span class="lb_block"><label for="cf_popular_del"><?php e__('Remove Top Searches');    //인기검색어 삭제 ?></label>
            <?php echo help(__('Automatically delete popular search terms that are older than the set date')); ?></span>
            <input type="text" name="cf_popular_del" value="<?php echo $config['cf_popular_del'] ?>" id="cf_popular_del" class="frm_input" size="5"> <?php e__('days'); ?>

        </li>
        <li  class="li_clear li_50">
            <span class="lb_block"><label for="cf_login_minutes"><?php e__('Connected visitor');    //접속된 방문자 ?></label>
            <?php echo help(__('User within set value is recognized as current user')); ?></span>
            <input type="text" name="cf_login_minutes" value="<?php echo $config['cf_login_minutes'] ?>" id="cf_login_minutes" class="frm_input" size="3"> <?php e__('headcount');   //인원수 ?>

        </li>
        <li class="li_50">
            <span class="lb_block"><label for="cf_new_rows"><?php e__('latest Post lines');    //최근게시물 라인수 ?></label>
            <?php echo help(__('Lines per page in the list')); ?></span>
            <input type="text" name="cf_new_rows" value="<?php echo $config['cf_new_rows'] ?>" id="cf_new_rows" class="frm_input" size="3"> <?php e__('line');   //라인 ?>

        </li>
        <li class="li_clear li_50">
            <span class="lb_block"><label for="cf_page_rows"><?php e__('Lines per page');    //한페이지당 라인수 ?></label>
            <?php echo help(__('List (list) lines per page')); ?></span>
            <input type="text" name="cf_page_rows" value="<?php echo $config['cf_page_rows'] ?>" id="cf_page_rows" class="frm_input" size="3"> <?php e__('line');   //라인 ?>

        </li>
        <li class="li_50">
            <span class="lb_block"><label for="cf_mobile_page_rows"><?php e__('Lines per page for mobile');    //모바일 한페이지당 라인수 ?></label>
            <?php echo help(__('Line Count per Mobile List Page')) ?></span>
            <input type="text" name="cf_mobile_page_rows" value="<?php echo $config['cf_mobile_page_rows'] ?>" id="cf_mobile_page_rows" class="frm_input" size="3"> <?php e__('line');   //라인 ?>

        </li>
        <li class="li_clear li_50">
            <span class="lb_block"><label for="cf_write_pages"><?php e__('Display setting for page number');     //페이지 표시 수 ?><strong class="sound_only"><?php e__('required'); ?></strong></label></span>
            <input type="text" name="cf_write_pages" value="<?php echo $config['cf_write_pages'] ?>" id="cf_write_pages" required class="required numeric frm_input" size="3"> <?php e__('Show per page');   //페이지씩 표시 ?>

        </li>
        <li class="li_50">
            <span class="lb_block"><label for="cf_mobile_pages"><?php e__('Display setting for page number ( MOBILE )');    //모바일 페이지 표시 수 ?><strong class="sound_only"><?php e__('required'); ?></strong></label></span>
            <input type="text" name="cf_mobile_pages" value="<?php echo $config['cf_mobile_pages'] ?>" id="cf_mobile_pages" required class="required numeric frm_input" size="3"> <?php e__('Show per page');   //페이지씩 표시 ?>

        </li>
        <li class="li_clear li_50">
            <span class="lb_block"><label for="cf_new_skin"><?php e__('Latest Posts Skin');    //최근게시물 스킨 ?><strong class="sound_only"><?php e__('required'); ?></strong></label></span>
            <?php echo get_skin_select('new', 'cf_new_skin', 'cf_new_skin', $config['cf_new_skin'], 'required'); ?>

        </li>
        <li class="li_50">
            <span class="lb_block"><label for="cf_mobile_new_skin"><?php e__('Latest Posts skin ( MOBILE )');    //모바일 최근게시물 스킨 ?><strong class="sound_only"><?php e__('required'); ?></strong></label></span>
            <?php echo get_mobile_skin_select('new', 'cf_mobile_new_skin', 'cf_mobile_new_skin', $config['cf_mobile_new_skin'], 'required'); ?>

        </li>
        <li class="li_50">
            <span class="lb_block"><label for="cf_search_skin"><?php e__('Search skin');     //검색 스킨 ?><strong class="sound_only"><?php e__('required'); ?></strong></label></span>
            <?php echo get_skin_select('search', 'cf_search_skin', 'cf_search_skin', $config['cf_search_skin'], 'required'); ?>


        </li>
        <li class="li_50">
            <span class="lb_block"><label for="cf_mobile_search_skin"><?php e__('Search skin ( MOBILE )');     //모바일 검색 스킨 ?><strong class="sound_only"><?php e__('required'); ?></strong></label></span>
            <?php echo get_mobile_skin_select('search', 'cf_mobile_search_skin', 'cf_mobile_search_skin', $config['cf_mobile_search_skin'], 'required'); ?>

        </li>
        <li class="li_50">
            <span class="lb_block"><label for="cf_connect_skin"><?php e__('Connector skin');     //접속자 스킨 ?><strong class="sound_only"><?php e__('required'); ?></strong></label></span>
            <?php echo get_skin_select('connect', 'cf_connect_skin', 'cf_connect_skin', $config['cf_connect_skin'], 'required'); ?>

        </li>
        <li class="li_50">
            <span class="lb_block"><label for="cf_mobile_connect_skin"><?php e__('Connector skin ( MOBILE )');     //모바일 접속자 스킨 ?><strong class="sound_only"><?php e__('required'); ?></strong></label></span>
            <?php echo get_mobile_skin_select('connect', 'cf_mobile_connect_skin', 'cf_mobile_connect_skin', $config['cf_mobile_connect_skin'], 'required'); ?>

        </li>
        <li class="li_50">
            <span class="lb_block"><label for="cf_faq_skin"><?php e__('FAQ skin');     //FAQ 스킨 ?><strong class="sound_only"><?php e__('required'); ?></strong></label></span>
            <?php echo get_skin_select('faq', 'cf_faq_skin', 'cf_faq_skin', $config['cf_faq_skin'], 'required'); ?>

        </li>
        <li class="li_50">
            <span class="lb_block"><label for="cf_mobile_faq_skin"><?php e__('FAQ skin ( MOBILE )');     //모바일 FAQ 스킨 ?><strong class="sound_only"><?php e__('required'); ?></strong></label></span>
                <?php echo get_mobile_skin_select('faq', 'cf_mobile_faq_skin', 'cf_mobile_faq_skin', $config['cf_mobile_faq_skin'], 'required'); ?>
        </li>
        <li class="li_clear">
            <span class="lb_block"><label for="cf_editor"><?php e__('Select editor');    //에디터 선택 ?></label>
            <?php echo help(sprintf(__('Select the DHTML Editor folder under %s.'), GML_EDITOR_URL)); ?></span>
            <select name="cf_editor" id="cf_editor">
            <?php
            $arr = get_skin_dir('', GML_EDITOR_PATH);
            for ($i=0; $i<count($arr); $i++) {
                if ($i == 0) echo "<option value=\"\">".__('not use')."</option>";
                echo "<option value=\"".$arr[$i]."\"".get_selected($config['cf_editor'], $arr[$i]).">".$arr[$i]."</option>\n";
            }
            ?>
            </select>

        </li>
        <li class="li_clear">
            <span class="lb_block"><label for="cf_captcha"><?php e__('Select captcha');    //캡챠 선택 ?><strong class="sound_only"><?php e__('required'); ?></strong></label><br>
            <select name="cf_captcha" id="cf_captcha" required class="required">
            <option value="kcaptcha" <?php echo get_selected($config['cf_captcha'], 'kcaptcha') ; ?>>Kcaptcha</option>
            <option value="recaptcha" <?php echo get_selected($config['cf_captcha'], 'recaptcha') ; ?>>reCAPTCHA V2</option>
            <option value="recaptcha_inv" <?php echo get_selected($config['cf_captcha'], 'recaptcha_inv') ; ?>>Invisible reCAPTCHA</option>
            </select>

        </li>
        <li class="li_clear kcaptcha_mp3">
            <span class="lb_block"><label for="cf_captcha_mp3"><?php e__('Select captcha sound');    //음성캡챠 선택 ?><strong class="sound_only"><?php e__('required'); ?></strong></label>
            <?php echo help(sprintf(__('When using kcaptcha, select the voice folder under %s.'), str_replace(array('recaptcha_inv', 'recaptcha'), 'kcaptcha', GML_CAPTCHA_URL).'/mp3')); ?></span>
            <select name="cf_captcha_mp3" id="cf_captcha_mp3" required class="required">
            <?php
            $arr = get_skin_dir('mp3', str_replace(array('recaptcha_inv', 'recaptcha'), 'kcaptcha', GML_CAPTCHA_PATH));
            for ($i=0; $i<count($arr); $i++) {
                if ($i == 0) echo "<option value=\"\">".__('select')."</option>";
                echo "<option value=\"".$arr[$i]."\"".get_selected($config['cf_captcha_mp3'], $arr[$i]).">".$arr[$i]."</option>\n";
            }
            ?>
            </select>

        </li>
        <li class="li_50">
            <span class="lb_block"><label for="cf_recaptcha_site_key"><?php e__('Google reCAPTCHA Site key'); ?></label>
            <?php echo help(__('The sitekey and secret keys of the re-caPTCHA V2 and the Invisible reCAPTCHA CAPTCHA CAPTCHA are not the same; they have different keys issued.')); ?>
            </span>
            <input type="text" name="cf_recaptcha_site_key" value="<?php echo $config['cf_recaptcha_site_key']; ?>" id="cf_recaptcha_site_key" class="frm_input" size="52"> <a href="https://www.google.com/recaptcha/admin" target="_blank" class="btn_frmline"><?php e__('Register reCAPTCHA'); ?></a>
        </li>
        <li class="li_50">
            <span class="lb_block"><label for="cf_recaptcha_secret_key"><?php e__('Google reCAPTCHA Secret key'); ?></label>
            </span>
            <input type="text" name="cf_recaptcha_secret_key" value="<?php echo $config['cf_recaptcha_secret_key']; ?>" id="cf_recaptcha_secret_key" class="frm_input" size="52">
        </li>
        <li class="li_50 li_left_clear">
            <span class="lb_block"><label for="cf_use_copy_log"><?php e__('Copy, move log');     //복사, 이동시 로그 ?></label>

            <?php echo help(__('Copy from whom, marked moved under post')); ?></span>
            <input type="checkbox" name="cf_use_copy_log" value="1" id="cf_use_copy_log" <?php echo $config['cf_use_copy_log']?'checked':''; ?>> <?php e__('record'); ?>

        </li>
        <li class="li_50">
            <span class="lb_block"><label for="cf_point_term"><?php e__('Point expiry date');    //포인트 유효기간 ?></label>
            <?php echo help(__('The point validity period does not take effect when the period is set to 0.')); ?></span>

            <input type="text" name="cf_point_term" value="<?php echo $config['cf_point_term']; ?>" id="cf_point_term" required class="required frm_input" size="5"> <?php e__('day'); ?>

        </li>
        <li class="li_clear">
            <span class="lb_block"><label for="cf_possible_ip"><?php e__('Access IP');    //접근가능 IP ?></label>
            <?php echo help(__('Only computers with the entered IP can be accessed.').'<br>'.__('123.123. + can be entered. (Separate by enter)')); ?></span>
            <textarea name="cf_possible_ip" id="cf_possible_ip"><?php echo $config['cf_possible_ip'] ?></textarea>

        </li>
        <li>
            <span class="lb_block"><label for="cf_intercept_ip"><?php e__('BLOCK IP');    //접근차단 IP ?></label>
            <?php echo help(__('Computers with the IP entered are inaccessible.').'<br>'.__('123.123. + can be entered. (Separate by enter)')); ?></span>
            <textarea name="cf_intercept_ip" id="cf_intercept_ip"><?php echo $config['cf_intercept_ip'] ?></textarea>

        </li>
        <li>
            <span class="lb_block"><label for="cf_analytics"><?php e__('Visitor Analysis Script');   //방문자분석 스크립트 ?></label>
            <?php echo help(__('Enter the visitor analysis script code. E.g.) Google Analytics')); ?></span>
            <textarea name="cf_analytics" id="cf_analytics"><?php echo $config['cf_analytics']; ?></textarea>

        </li>
        <li>
            <span class="lb_block"><label for="cf_add_meta"><?php e__('Add Meta tag');    //추가 메타태그 ?></label>
            <?php echo help(__('Enter additional meta tag to use.')); ?></span>
            <textarea name="cf_add_meta" id="cf_add_meta"><?php echo $config['cf_add_meta']; ?></textarea>

        </li>
    </ul>
    <div class="btn_confirm01 btn_confirm"><button type="button" class="get_theme_confc" data-type="conf_skin"><?php e__('Import theme skin settings');    //테마 스킨설정 가져오기 ?></button></div>
</section>

<button id="anc_cf_board" type="button" class="<?php echo GML_ADMIN_HTML_TAB_CLASS; ?>"><?php e__('Board'); ?></button>
<section class="<?php echo GML_ADMIN_HTML_CON_CLASS; ?>">
    <h2 class="h2_frm"><?php e__('Board'); ?></h2>
    <div class="local_desc">
        <p><?php e__('Can be set individually in each bulletin board management');   //각 게시판 관리에서 개별적으로 설정할 수 있습니다. ?></p>
    </div>

    <div class="frm_ul">
        <ul>
            <li>
                <span class="lb_block"><label for="cf_delay_sec"><?php e__('Intervals during for writing');  //글쓰기 간격 ?><strong class="sound_only"><?php e__('required'); ?></strong></label></span>
                <input type="text" name="cf_delay_sec" value="<?php echo $config['cf_delay_sec'] ?>" id="cf_delay_sec" required class="required numeric frm_input" size="3"> <?php e__('Intervals after');   //초 지난후 가능 ?>
            </li>
            <li class="li_50">
                <span class="lb_block"><label for="cf_link_target"><?php e__('Link for target');     //링크 타겟 ?></label>
                <?php echo help(__('Specifies the target of the link is automatically posts content.'));    //글내용중 자동 링크되는 타켓을 지정합니다. ?></span>
                <select name="cf_link_target" id="cf_link_target">
                    <option value="_blank"<?php echo get_selected($config['cf_link_target'], '_blank') ?>>_blank</option>
                    <option value="_self"<?php echo get_selected($config['cf_link_target'], '_self') ?>>_self</option>
                    <option value="_top"<?php echo get_selected($config['cf_link_target'], '_top') ?>>_top</option>
                    <option value="_new"<?php echo get_selected($config['cf_link_target'], '_new') ?>>_new</option>
                </select>

            </li>
            <li class="li_50">
                <span class="lb_block"><label for="cf_read_point"><?php e__('Read points');  //글읽기 포인트 ?><strong class="sound_only"><?php e__('required'); ?></strong></label></span>
                <input type="text" name="cf_read_point" value="<?php echo $config['cf_read_point'] ?>" id="cf_read_point" required class="required frm_input" size="3"> <?php e__('point'); ?>
            </li>
            <li class="li_50 li_clear">
                <span class="lb_block"><label for="cf_write_point"><?php e__('Write points');  //글쓰기 포인트 ?></label></span>
                <input type="text" name="cf_write_point" value="<?php echo $config['cf_write_point'] ?>" id="cf_write_point" required class="required frm_input" size="3"> <?php e__('point'); ?>
            </li>
            <li class="li_50">
                <span class="lb_block"><label for="cf_comment_point"><?php e__('Comment points');    //댓글쓰기 포인트 ?></label></span>
                <input type="text" name="cf_comment_point" value="<?php echo $config['cf_comment_point'] ?>" id="cf_comment_point" required class="required frm_input" size="3"> <?php e__('point'); ?>
            </li>
            <li class="li_50 li_clear">
                <span class="lb_block"><label for="cf_download_point"><?php e__('Download points');    //다운로드 포인트 ?></label></span>
                <input type="text" name="cf_download_point" value="<?php echo $config['cf_download_point'] ?>" id="cf_download_point" required class="required frm_input" size="3"> <?php e__('point'); ?>
            </li>
            <li class="li_50">
                <span class="lb_block"><label for="cf_search_part"><?php e__('Search Units');    //검색단위 ?></label></span>
                <input type="text" name="cf_search_part" value="<?php echo $config['cf_search_part'] ?>" id="cf_search_part" class="frm_input" size="4"> <?php e__('Units use Search');  //건 단위로 검색 ?>
            </li>
            <li class="li_clear">
                <span class="lb_block"><label for="cf_image_extension"><?php e__('Allowed image extensions');    //이미지 업로드 확장자 ?></label>
                <?php echo help(__('Allowed image extensions separated by( period or vertical bar )'));     //게시판 글작성시 이미지 파일 업로드 가능 확장자. | 로 구분 ?></span>

                <input type="text" name="cf_image_extension" value="<?php echo $config['cf_image_extension'] ?>" id="cf_image_extension" class="frm_input m_full_input" size="70">

            </li>
            <li>
                <span class="lb_block"><label for="cf_movie_extension"><?php e__('Allowed movie extensions');    //동영상 업로드 확장자 ?></label>
                <?php echo help(__('Allowed movie extensions separated by( period or vertical bar )'));     //게시판 글작성시 동영상 파일 업로드 가능 확장자. | 로 구분 ?></span>
                <input type="text" name="cf_movie_extension" value="<?php echo $config['cf_movie_extension'] ?>" id="cf_movie_extension" class="frm_input m_full_input" size="70">

            </li>
            <li>
                <span class="lb_block"><label for="cf_filter"><?php e__('Filter words');     //단어 필터링 ?></label>
                <?php echo help(__('The Contents contained is entered word can not published. The words between words, separated by commas.'));     //입력된 단어가 포함된 내용은 게시할 수 없습니다. 단어와 단어 사이는 ,로 구분합니다. ?></span>
                <textarea name="cf_filter" id="cf_filter" rows="7"><?php echo $config['cf_filter'] ?></textarea>

            </li>
        </ul>
    </div>
</section>

<button id="anc_cf_join" type="button" class="<?php echo GML_ADMIN_HTML_TAB_CLASS; ?>"><?php e__('Member Page');  //회원 페이지 ?></button>
<section class="<?php echo GML_ADMIN_HTML_CON_CLASS; ?>">
    <h2 class="h2_frm"><?php e__('Membership Settings');     //회원가입 설정 ?></h2>
    <div class="local_desc">
        <p><?php e__('You can set the skin to use and the information to receive when signing up.'); //회원가입 시 사용할 스킨과 입력 받을 정보 등을 설정할 수 있습니다. ?></p>
    </div>

    <div  class="frm_ul">
        <ul>
            <li class="li_50">
                <span class="lb_block"><label for="cf_member_skin"><?php e__('Member Skin');  //회원 스킨 ?><strong class="sound_only"><?php e__('required');   //필수 ?></strong></label></span>
                <?php echo get_skin_select('member', 'cf_member_skin', 'cf_member_skin', $config['cf_member_skin'], 'required'); ?>
            </li>
            <li class="li_50 bd_0">
                <span class="lb_block"><label for="cf_mobile_member_skin"><?php e__('Member Skin ( MOBILE )');  //모바일 회원 스킨 ?><strong class="sound_only"><?php e__('required');   //필수 ?></strong></label></span>
                <?php echo get_mobile_skin_select('member', 'cf_mobile_member_skin', 'cf_mobile_member_skin', $config['cf_mobile_member_skin'], 'required'); ?>

            </li>
            <li class="li_50">
                <span class="lb_block"><?php e__('Enter homepage');  //홈페이지 입력 ?></span>
                <input type="checkbox" name="cf_use_homepage" value="1" id="cf_use_homepage" <?php echo $config['cf_use_homepage']?'checked':''; ?>> <label for="cf_use_homepage"><?php e__('Show'); //보이기 ?></label>
                <input type="checkbox" name="cf_req_homepage" value="1" id="cf_req_homepage" <?php echo $config['cf_req_homepage']?'checked':''; ?>> <label for="cf_req_homepage"><?php e__('Required');   //필수 ?></label>
            </li>
            <li class="li_50">
                <span class="lb_block"><?php e__('Enter address');  //주소 입력 ?></span>
                <input type="checkbox" name="cf_use_addr" value="1" id="cf_use_addr" <?php echo $config['cf_use_addr']?'checked':''; ?>> <label for="cf_use_addr"><?php e__('Show'); //보이기 ?></label>
                <input type="checkbox" name="cf_req_addr" value="1" id="cf_req_addr" <?php echo $config['cf_req_addr']?'checked':''; ?>> <label for="cf_req_addr"><?php e__('Required');   //필수 ?></label>

            </li>
            <li class="li_50">
                <span class="lb_block"><?php e__('Enter Phone Number');  //전화번호 입력 ?></span>
                <input type="checkbox" name="cf_use_tel" value="1" id="cf_use_tel" <?php echo $config['cf_use_tel']?'checked':''; ?>> <label for="cf_use_tel"><?php e__('Show'); //보이기 ?></label>
                <input type="checkbox" name="cf_req_tel" value="1" id="cf_req_tel" <?php echo $config['cf_req_tel']?'checked':''; ?>> <label for="cf_req_tel"><?php e__('Required');   //필수 ?></label>

            </li>
            <li class="li_50">
                <span class="lb_block"><?php e__('Enter Mobile Number');  //휴대폰번호 입력 ?></span>
                <input type="checkbox" name="cf_use_hp" value="1" id="cf_use_hp" <?php echo $config['cf_use_hp']?'checked':''; ?>> <label for="cf_use_hp"><?php e__('Show'); //보이기 ?></label>
                <input type="checkbox" name="cf_req_hp" value="1" id="cf_req_hp" <?php echo $config['cf_req_hp']?'checked':''; ?>> <label for="cf_req_hp"><?php e__('Required');   //필수 ?></label>

            </li>
            <li class="li_50">
                <span class="lb_block"><?php e__('Enter signature');  //서명 입력 ?></span>
                <input type="checkbox" name="cf_use_signature" value="1" id="cf_use_signature" <?php echo $config['cf_use_signature']?'checked':''; ?>> <label for="cf_use_signature"><?php e__('Show'); //보이기 ?></label>
                <input type="checkbox" name="cf_req_signature" value="1" id="cf_req_signature" <?php echo $config['cf_req_signature']?'checked':''; ?>> <label for="cf_req_signature"><?php e__('Required');   //필수 ?></label>
            </li>
            <li class="li_50">
                <span class="lb_block"><?php e__('Enter Self Introduction');  //자기소개 입력 ?></span>
                <input type="checkbox" name="cf_use_profile" value="1" id="cf_use_profile" <?php echo $config['cf_use_profile']?'checked':''; ?>> <label for="cf_use_profile"><?php e__('Show'); //보이기 ?></label>
                <input type="checkbox" name="cf_req_profile" value="1" id="cf_req_profile" <?php echo $config['cf_req_profile']?'checked':''; ?>> <label for="cf_req_profile"><?php e__('Required');   //필수 ?></label>

            </li>
            <li class="li_50">
                <span class="lb_block"><label for="cf_register_level"><?php e__('Permission at Register');  //회원가입시 권한 ?></label></span>
                <?php echo get_member_level_select('cf_register_level', 1, 9, $config['cf_register_level']) ?>
            </li>
            <li class="li_50">
                <span class="lb_block"><label for="cf_register_point"><?php e__('Points in Register');  //회원가입시 포인트 ?></label></span>
                <input type="text" name="cf_register_point" value="<?php echo $config['cf_register_point'] ?>" id="cf_register_point" class="frm_input" size="5"> <?php e__('point');   //점 ?>
            </li>
            <li class="li_clear">
                <span class="lb_block"><label for="cf_leave_day"><?php e__('Deletion date after member withdrawal');  //회원탈퇴후 삭제일 ?></label></span>
                <input type="text" name="cf_leave_day" value="<?php echo $config['cf_leave_day'] ?>" id="cf_leave_day" class="frm_input" size="2"> <?php e__('Automatically delete after days');     //일 후 자동 삭제 ?>
            </li>
            <li class="li_50">
                <span class="lb_block"><label for="cf_use_member_icon"><?php e__('Enable Member Icon');     //회원아이콘 사용 ?></label>
                <?php echo help(__("Use icon instead of publisher's nickname for posts")); ?></span>
                <select name="cf_use_member_icon" id="cf_use_member_icon">
                    <option value="0"<?php echo get_selected($config['cf_use_member_icon'], '0') ?>><?php e__('Unused'); //미사용 ?>
                    <option value="1"<?php echo get_selected($config['cf_use_member_icon'], '1') ?>><?php e__('Show Icons Only'); //아이콘만 표시 ?>
                    <option value="2"<?php echo get_selected($config['cf_use_member_icon'], '2') ?>><?php e__('Show Icon + Display Name'); //아이콘+이름 표시 ?>
                </select>
            </li>
            <li class="li_50">
                <span class="lb_block"><label for="cf_icon_level"><?php e__('Member icon, image upload Level');  //회원 아이콘, 이미지 업로드 권한 ?></label></span>
                <?php echo get_member_level_select('cf_icon_level', 1, 9, $config['cf_icon_level']) ?> <?php e__('More than');    //이상 ?>
            </li>
            <li class="li_50">
                <span class="lb_block"><label for="cf_member_icon_size"><?php e__('Member icon capacity');   //회원아이콘 용량   ?></label></span>
                <input type="text" name="cf_member_icon_size" value="<?php echo $config['cf_member_icon_size'] ?>" id="cf_member_icon_size" class="frm_input" size="10"> <?php e__('Bytes or less');  //바이트 이하 ?>
            </li>
            <li class="li_50">
                <span class="lb_block"><?php e__('Member icon size');    //회원아이콘 사이즈 ?></span>
                <label for="cf_member_icon_width"><?php e__('width'); ?></label>
                <input type="text" name="cf_member_icon_width" value="<?php echo $config['cf_member_icon_width'] ?>" id="cf_member_icon_width" class="frm_input" size="2">
                <label for="cf_member_icon_height"><?php e__('height'); ?></label>
                <input type="text" name="cf_member_icon_height" value="<?php echo $config['cf_member_icon_height'] ?>" id="cf_member_icon_height" class="frm_input" size="2">
                <?php e__('Pixel or less');  //픽셀 이하 ?>
            </li>
            <li class="li_50 li_left_clear">
                <span class="lb_block"><label for="cf_member_img_size"><?php e__('Member image capacity');   //회원이미지 용량   ?></label></span>
                <input type="text" name="cf_member_img_size" value="<?php echo $config['cf_member_img_size'] ?>" id="cf_member_img_size" class="frm_input" size="10"> <?php e__('Bytes or less');  //바이트 이하 ?>
            </li>
            <li class="li_50">
                <span class="lb_block"><?php e__('Member image size');    //회원이미지 사이즈 ?></span>
                <label for="cf_member_img_width"><?php e__('width'); ?></label>
                <input type="text" name="cf_member_img_width" value="<?php echo $config['cf_member_img_width'] ?>" id="cf_member_img_width" class="frm_input" size="2">
                <label for="cf_member_img_height"><?php e__('height'); ?></label>
                <input type="text" name="cf_member_img_height" value="<?php echo $config['cf_member_img_height'] ?>" id="cf_member_img_height" class="frm_input" size="2">
                <?php e__('Pixel or less');  //픽셀 이하 ?>
            </li>

            <li class="li_left_clear li_50">
                <span class="lb_block"><label for="cf_use_recommend"><?php e__('Use of recommender system');     //추천인제도 사용 ?></label></span>
                <input type="checkbox" name="cf_use_recommend" value="1" id="cf_use_recommend" <?php echo $config['cf_use_recommend']?'checked':''; ?>> <?php e__('Used');   //사용 ?>
            </li>
            <li class="li_50">
                <span class="lb_block"><label for="cf_recommend_point"><?php e__('Recommendation point');    //추천인 포인트 ?></label></span>
                <input type="text" name="cf_recommend_point" value="<?php echo $config['cf_recommend_point'] ?>" id="cf_recommend_point" class="frm_input"> <?php e__('Point'); ?>
            </li>
            <li class="li_clear">
                <span class="lb_block"><label for="cf_prohibit_id"><?php e__('Id, nickname forbidden word'); //아이디,닉네임 금지단어?></label>
                <?php echo help(__('Select a member ID and a word that can not be used as a nickname. Comma (,) separated')); ?></span>
                <textarea name="cf_prohibit_id" id="cf_prohibit_id" rows="5"><?php echo $config['cf_prohibit_id'] ?></textarea>
            </li>
            <li>
                <span class="lb_block"><label for="cf_prohibit_email"><?php e__('Input prohibited mail');   //입력 금지 메일 ?></label>
                <?php echo help('입력 받지 않을 도메인을 지정합니다. 엔터로 구분 ex) hotmail.com') ?></span>
                <textarea name="cf_prohibit_email" id="cf_prohibit_email" rows="5"><?php echo $config['cf_prohibit_email'] ?></textarea>
            </li>
            <li>
                <span class="lb_block"><label for="cf_stipulation"><?php e__('Terms and conditions of Register');  //회원가입약관 ?></label></span>
                <textarea name="cf_stipulation" id="cf_stipulation" rows="10"><?php echo $config['cf_stipulation'] ?></textarea>
            </li>
            <li>
                <span class="lb_block"><label for="cf_privacy"><?php e__('Privacy Policy');  //개인정보처리방침 ?></label></span>
                <textarea id="cf_privacy" name="cf_privacy" rows="10"><?php echo $config['cf_privacy'] ?></textarea>
            </li>
        </ul>
    </div>
    <div class="btn_confirm01 btn_confirm"><button type="button" class="get_theme_confc" data-type="conf_member"><?php e__('Get theme member skin settings');    //테마 회원스킨설정 가져오기 ?></button></div>
</section>

<button id="anc_cf_mail" type="button" class="<?php echo GML_ADMIN_HTML_TAB_CLASS; ?>"><?php e__('Mail'); ?></button>

<section class="<?php echo GML_ADMIN_HTML_CON_CLASS; ?>">
    <h2 class="h2_frm_show index0"><?php e__('Default Mail Environment');    //기본 메일 환경 ?></h2>

    <div class="frm_ul">
        <ul>

            <li class="li_50">
                <span class="lb_block"><label for="cf_email_use"><?php e__('Enable mail sending');   //메일발송 사용 ?></label>

                <?php echo help(__("If you don't check, don't use Mail Sending at all. You can not test your mail.")); ?></span>
                <input type="checkbox" name="cf_email_use" value="1" id="cf_email_use" <?php echo $config['cf_email_use']?'checked':''; ?>> <?php e__('Used'); ?>

            </li>
            <li class="li_50 bd_0">
                <span class="lb_block"><label for="cf_use_email_certify"><?php e__('Enable Mail Authentication');   //메일인증 사용 ?></label>
                <?php $tmp = !(defined('GML_SOCIAL_CERTIFY_MAIL') && GML_SOCIAL_CERTIFY_MAIL) ? '<br>('.__('Social login members using SNS do not authenticate member mail. For general members only.').')' : ''; ?>
                <?php echo help(__("Do must click the authentication address delivered in member's mail to accept it as a member.").$tmp); ?></span>
                <input type="checkbox" name="cf_use_email_certify" value="1" id="cf_use_email_certify" <?php echo $config['cf_use_email_certify']?'checked':''; ?>> <?php e__('Used'); ?>

            </li>
            <li class="li_clear">
                <span class="lb_block"><label for="cf_formmail_is_member"><?php e__('Formmail use status');   //폼메일 사용 여부 ?></label>

                <?php echo help(__('If not checked, non-members can also be used.')); ?></span>
                <input type="checkbox" name="cf_formmail_is_member" value="1" id="cf_formmail_is_member" <?php echo $config['cf_formmail_is_member']?'checked':''; ?>> <?php e__('Use for members only');    //회원만 사용 ?>

            </li>
        </ul>
    </div>

    <h2 class="h2_frm_show"><?php e__('Mail settings when writing bulletin boards');  //게시판 글 작성 시 메일 설정 ?></h2>

    <div class="frm_ul">
        <ul>

            <li>
                <span class="lb_block"><label for="cf_email_wr_super_admin"><?php e__('Super administrator');   //최고관리자 ?></label>
                <?php echo help(__('Send mail to Super Admin.')); ?></span>
                <input type="checkbox" name="cf_email_wr_super_admin" value="1" id="cf_email_wr_super_admin" <?php echo $config['cf_email_wr_super_admin']?'checked':''; ?>> <?php e__('Used'); ?>

            </li>
            <li class="li_50">
                <span class="lb_block"><label for="cf_email_wr_group_admin"><?php e__('Group administrator');    //그룹관리자 ?></label>
                <?php echo help(__('Send mail to Group Admin.')); ?></span>
                <input type="checkbox" name="cf_email_wr_group_admin" value="1" id="cf_email_wr_group_admin" <?php echo $config['cf_email_wr_group_admin']?'checked':''; ?>> <?php e__('Used'); ?>

            </li>
            <li class="li_50">
               <span class="lb_block"> <label for="cf_email_wr_board_admin"><?php e__('Board administrator');    //게시판관리자 ?></label>
                <?php echo help(__('Sends a mail to the board admin.')); ?></span>
                <input type="checkbox" name="cf_email_wr_board_admin" value="1" id="cf_email_wr_board_admin" <?php echo $config['cf_email_wr_board_admin']?'checked':''; ?>> <?php e__('Used'); ?>

            </li>
            <li class="li_50">
                <span class="lb_block"><label for="cf_email_wr_write"><?php e__('A writer');  //원글작성자 ?></label>
                <?php echo help(__('Send a mail to the original post writer.')); ?></span>
                <input type="checkbox" name="cf_email_wr_write" value="1" id="cf_email_wr_write" <?php echo $config['cf_email_wr_write']?'checked':''; ?>> <?php e__('Used'); ?>

            </li>
            <li class="li_50">
                <span class="lb_block"><label for="cf_email_wr_comment_all"><?php e__('Comment writer');  //댓글작성자 ?></label>
                <?php echo help(__('If a comment is posted on the original post, it will be sent to everyone who wrote the comment.')) ?></span>
                <input type="checkbox" name="cf_email_wr_comment_all" value="1" id="cf_email_wr_comment_all" <?php echo $config['cf_email_wr_comment_all']?'checked':''; ?>> <?php e__('Used'); ?>

            </li>
        </ul>
    </div>

    <h2 class="h2_frm_show"><?php e__('Mail setting when joining a membership');  //회원가입 시 메일 설정 ?></h2>

    <div class="frm_ul">
        <ul>
            <li class="li_50">
                <span class="lb_block"><label for="cf_email_mb_super_admin"><?php e__('Send Super Administrator Mail');   //최고관리자 메일발송 ?></label>

                <?php echo help(__('Send mail to Super Admin.')) ?></span>
                <input type="checkbox" name="cf_email_mb_super_admin" value="1" id="cf_email_mb_super_admin" <?php echo $config['cf_email_mb_super_admin']?'checked':''; ?>> <?php e__('Used'); ?>

            </li>
            <li class="li_50 bd_0">
                <span class="lb_block"><label for="cf_email_mb_member"><?php e__('Send mail to members');   //회원님께 메일발송 ?></label>

                <?php echo help(__('Send an email to a member who has joined.')); ?></span>
                <input type="checkbox" name="cf_email_mb_member" value="1" id="cf_email_mb_member" <?php echo $config['cf_email_mb_member']?'checked':''; ?>> <?php e__('Used'); ?>

            </li>
        </ul>
    </div>

    <h2 class="h2_frm_show"><?php e__('Set up mail when writing a poll or comments');     //투표 기타의견 작성 시 메일 설정 ?></h2>

    <div class="frm_ul">
        <ul>
            <li>
                <span class="lb_block"><label for="cf_email_po_super_admin"><?php e__('Send Super Administrator Mail');   //최고관리자 메일발송 ?></label>

                <?php echo help(__('Send mail to Super Admin.')); ?></span>
                <input type="checkbox" name="cf_email_po_super_admin" value="1" id="cf_email_po_super_admin" <?php echo $config['cf_email_po_super_admin']?'checked':''; ?>> <?php e__('Used'); ?>

            </li>
        </ul>
    </div>
</section>


<button id="anc_cf_sns" type="button" class="<?php echo GML_ADMIN_HTML_TAB_CLASS; ?>">SNS</button>
<section class="<?php echo GML_ADMIN_HTML_CON_CLASS; ?>">
    <h2 class="h2_frm"><?php e__('Social Network Service');  //소셜네트워크서비스 ?></h2>
    <div class="frm_ul">
        <ul>
            <li class="li_clear bd_0">
                <span class="lb_block"><label for="cf_social_login_use"><?php e__('Social Login Settings');  //소셜로그인설정 ?></label>
                <?php echo help(__('Use social login.')); ?>
                <a href="https://sir.kr/manual/g5/276" class="btn btn_03 social_menual_btn" target="_blank" ><?php e__('View manual related to settings');   //설정 관련 메뉴얼 보기 ?></a>
                </span>
                <input type="checkbox" name="cf_social_login_use" value="1" id="cf_social_login_use" <?php echo (!empty($config['cf_social_login_use']))?'checked':''; ?>> <?php e__('Enable on check'); ?>
            </li>
            <li class="social_config_explain">
                <span class="lb_block"><label for="cf_social_servicelist"><?php e__('Social Login Settings');  //소셜로그인설정 ?></label></span>
                <div class="explain_box">
                    <input type="checkbox" name="cf_social_servicelist[]" id="check_social_facebook" value="facebook" <?php echo option_array_checked('facebook', $config['cf_social_servicelist']); ?> >
                    <label for="check_social_facebook"><?php e__('Use Facebook login'); //페이스북 로그인을 사용 ?></label>
                    <h3><?php e__('Facebook Valid OAuth Redirect URI'); ?></h3>
                    <p><?php echo get_social_callbackurl('facebook'); ?></p>
                </div>
                <div class="explain_box">
                    <input type="checkbox" name="cf_social_servicelist[]" id="check_social_google" value="google" <?php echo option_array_checked('google', $config['cf_social_servicelist']); ?> >
                    <label for="check_social_google"><?php e__('Use Google login'); //구글 로그인을 사용합니다 ?></label>
                    <h3><?php e__('Google Authorized Redirect URI'); ?></h3>
                    <p><?php echo get_social_callbackurl('google'); ?></p>
                </div>
                <div class="explain_box">
                    <input type="checkbox" name="cf_social_servicelist[]" id="check_social_twitter" value="twitter" <?php echo option_array_checked('twitter', $config['cf_social_servicelist']); ?> >
                    <label for="check_social_twitter"><?php e__('Use Twitter login'); //트위터 로그인을 사용합니다 ?></label>
                    <h3><?php e__('Twitter CallbackURL'); ?></h3>
                    <p><?php echo get_social_callbackurl('twitter'); ?></p>
                </div>
                <div class="explain_box">
                    <input type="checkbox" name="cf_social_servicelist[]" id="check_social_github" value="github" <?php echo option_array_checked('github', $config['cf_social_servicelist']); ?> >
                    <label for="check_social_github"><?php e__('Use Github login'); //깃 허브 로그인을 사용합니다 ?></label>
                    <h3><?php e__('Github Authorization callback URL'); ?></h3>
                    <p><?php echo get_social_callbackurl('github'); ?></p>
                </div>
                <div class="explain_box">
                    <input type="checkbox" name="cf_social_servicelist[]" id="check_social_naver" value="naver" <?php echo option_array_checked('naver', $config['cf_social_servicelist']); ?> >
                    <label for="check_social_naver"><?php e__('Use Naver login'); //네이버 로그인을 사용 ?></label>
                    <h3><?php e__('Naver CallbackURL'); ?></h3>
                    <p><?php echo get_social_callbackurl('naver'); ?></p>
                </div>
                <div class="explain_box">
                    <input type="checkbox" name="cf_social_servicelist[]" id="check_social_kakao" value="kakao" <?php echo option_array_checked('kakao', $config['cf_social_servicelist']); ?> >
                    <label for="check_social_kakao"><?php e__('Use Kakao login'); //카카오 로그인을 사용 ?></label>
                    <h3><?php e__('Kakao Web Redirect Path'); ?></h3>
                    <p><?php echo get_social_callbackurl('kakao'); ?></p>
                </div>
            </li>
            <li class="li_50">
                <span class="lb_block"><label for="cf_facebook_appid"><?php e__('Facebook APP ID'); //페이스북 앱 ID ?></label></span>
                <input type="text" name="cf_facebook_appid" value="<?php echo $config['cf_facebook_appid'] ?>" id="cf_facebook_appid" class="frm_input" size="40"> <a href="https://developers.facebook.com/apps" target="_blank" class="btn_frmline"><?php e__('Register app');    //앱 등록하기 ?></a>
            </li>
            <li class="li_50">
                <span class="lb_block"><label for="cf_facebook_secret"><?php e__('Facebook APP Secret'); //페이스북 앱 Secret ?></label></span>
                <input type="text" name="cf_facebook_secret" value="<?php echo $config['cf_facebook_secret'] ?>" id="cf_facebook_secret" class="frm_input" size="45">
            </li>
            <li class="li_50 li_left_clear">
                <span class="lb_block"><label for="cf_google_clientid"><?php e__('Google Client ID');    //구글 Client ID ?></label></span>
                <input type="text" name="cf_google_clientid" value="<?php echo $config['cf_google_clientid'] ?>" id="cf_google_clientid" class="frm_input" size="40"> <a href="https://console.developers.google.com" target="_blank" class="btn_frmline"><?php e__('Register app');    //앱 등록하기 ?></a>
            </li>
            <li class="li_50">
                <span class="lb_block"><label for="cf_google_secret"><?php e__('Google Client Secret');  //구글 Client Secret ?></label></span>
                <input type="text" name="cf_google_secret" value="<?php echo $config['cf_google_secret'] ?>" id="cf_google_secret" class="frm_input" size="45">
            </li>
            <li class="li_clear">
                <span class="lb_block"><label for="cf_googl_shorturl_apikey"><?php e__('Google Short URL API Key');  //구글 짧은주소 API Key ?></label></span>
                <input type="text" name="cf_googl_shorturl_apikey" value="<?php echo $config['cf_googl_shorturl_apikey'] ?>" id="cf_googl_shorturl_apikey" class="frm_input" size="40"> <a href="http://code.google.com/apis/console/" target="_blank" class="btn_frmline"><?php e__('Register API KEY');    //API Key 등록하기 ?></a>
            </li>
            <li class="li_50 li_left_clear">
                <span class="lb_block"><label for="cf_twitter_key"><?php e__('Twitter Consumer Key');    //트위터 컨슈머 Key ?></label></span>
                <input type="text" name="cf_twitter_key" value="<?php echo $config['cf_twitter_key'] ?>" id="cf_twitter_key" class="frm_input" size="40"> <a href="https://dev.twitter.com/apps" target="_blank" class="btn_frmline"><?php e__('Register app');    //앱 등록하기 ?></a>
            </li>
            <li class="li_50">
                <span class="lb_block"><label for="cf_twitter_secret"><?php e__('Twitter Consumer Secret'); //트위터 컨슈머 Secret ?></label></span>
                <input type="text" name="cf_twitter_secret" value="<?php echo $config['cf_twitter_secret'] ?>" id="cf_twitter_secret" class="frm_input" size="45">
            </li>
            <li class="li_50 li_left_clear">
                <span class="lb_block"><label for="cf_github_clientid"><?php e__('Github Client ID');    //깃 허브 Client ID ?></label></span>
                <input type="text" name="cf_github_clientid" value="<?php echo $config['cf_github_clientid'] ?>" id="cf_github_clientid" class="frm_input" size="40"> <a href="https://github.com/settings/developers" target="_blank" class="btn_frmline"><?php e__('Register app');    //앱 등록하기 ?></a>
            </li>
            <li class="li_50">
                <span class="lb_block"><label for="cf_github_secret"><?php e__('Github Client Secret');  //깃 허브 Client Secret ?></label></span>
                <input type="text" name="cf_github_secret" value="<?php echo $config['cf_github_secret'] ?>" id="cf_github_secret" class="frm_input" size="45">
            </li>
            <li class="li_50 li_left_clear">
                <span class="lb_block"><label for="cf_naver_clientid"><?php e__('Naver Client ID'); //네이버 Client ID ?></label></span>
                <input type="text" name="cf_naver_clientid" value="<?php echo $config['cf_naver_clientid'] ?>" id="cf_naver_clientid" class="frm_input" size="40"> <a href="https://developers.naver.com/apps/#/register" target="_blank" class="btn_frmline"><?php e__('Register app');    //앱 등록하기 ?></a>
            </li>
            <li class="li_50">
                <span class="lb_block"><label for="cf_naver_secret"><?php e__('Naver Client Secret'); //네이버 Client Secret ?></label></span>
                <input type="text" name="cf_naver_secret" value="<?php echo $config['cf_naver_secret'] ?>" id="cf_naver_secret" class="frm_input" size="45">
            </li>
            <li class="li_50 li_left_clear">
                <span class="lb_block"><label for="cf_kakao_rest_key"><?php e__('Kakao Rest Api Key');    //카카오 REST API 키 ?></label></span>
                <input type="text" name="cf_kakao_rest_key" value="<?php echo $config['cf_kakao_rest_key'] ?>" id="cf_kakao_rest_key" class="frm_input" size="40"> <a href="https://developers.kakao.com/apps/new" target="_blank" class="btn_frmline"><?php e__('Register app');    //앱 등록하기 ?></a>
            </li>
            <li class="li_50">
                <span class="lb_block"><label for="cf_kakao_client_secret"><?php e__('Kakao Client Secret');  //카카오 Client Secret ?></label></span>
                <input type="text" name="cf_kakao_client_secret" value="<?php echo $config['cf_kakao_client_secret'] ?>" id="cf_kakao_client_secret" class="frm_input" size="45">
            </li>
            <li class="li_clear">
                <span class="lb_block"><label for="cf_kakao_js_apikey"><?php e__('Kakao Javascript Key');  //카카오 JavaScript 키 ?></label></span>
                <input type="text" name="cf_kakao_js_apikey" value="<?php echo $config['cf_kakao_js_apikey'] ?>" id="cf_kakao_js_apikey" class="frm_input" size="45">
            </li>
        </ul>
    </div>
</section>


<button id="anc_cf_lay" type="button" class="<?php echo GML_ADMIN_HTML_TAB_CLASS; ?>"><?php e__('Add Layout Code');    //레이아웃 코드 추가 ?></button>
<section class="<?php echo GML_ADMIN_HTML_CON_CLASS; ?>">
    <h2 class="h2_frm"><?php e__('Add Layout Settings');  //레이아웃 추가설정  ?></h2>
    <div class="local_desc">
        <p><?php e__('Add or change the default file paths and scripts, css');   //기본 설정된 파일 경로 및 script, css 를 추가하거나 변경할 수 있습니다. ?></p>
    </div>

    <div class="frm_ul">
        <ul>

            <li>
                <span class="lb_block"><label for="cf_add_script"><?php e__('Add script, css'); ?></label>
                <?php echo help(__('Set the JavaScript and the css code to be added above the &lt;/HEAD&gt; tag of HTML.').'<br>'.__('This code is not used on the Administrators page.')); ?></span>
                <textarea name="cf_add_script" id="cf_add_script"><?php echo get_text($config['cf_add_script']); ?></textarea>

            </li>
        </ul>
    </div>
</section>


<button id="anc_cf_extra" type="button" class="<?php echo GML_ADMIN_HTML_TAB_CLASS; ?>"><?php e__('Extra field');   //여분필드 ?></button>
<section class="<?php echo GML_ADMIN_HTML_CON_CLASS; ?>">
    <h2 class="h2_frm"><?php e__('Extra field Settings'); //여분필드 기본 설정 ?></h2>
    <div class="local_desc">
        <p><?php e__('Set it individually in Manage Each Bulletin Board');   //각 게시판 관리에서 개별적으로 설정 가능합니다. ?></p>
    </div>

    <div class="frm_ul extra_ul">

        <ul>
            <?php for ($i=1; $i<=10; $i++) { ?>
            <li>
                <span class="lb_block"><?php echo sprintf(__('Extra field %d'), $i); ?></span>
                <label for="cf_<?php echo $i ?>_subj" class="extra_lb"><?php echo sprintf(__('Extra field %d Title'), $i); ?></label>
                <input type="text" name="cf_<?php echo $i ?>_subj" value="<?php echo get_text($config['cf_'.$i.'_subj']) ?>" id="cf_<?php echo $i ?>_subj" class="frm_input m_full_input" size="30">
                <label for="cf_<?php echo $i ?>" class="extra_lb"><?php echo sprintf(__('Extra field %d Value'), $i); ?></label>
                <input type="text" name="cf_<?php echo $i ?>" value="<?php echo $config['cf_'.$i] ?>" id="cf_<?php echo $i ?>" class="frm_input m_full_input" size="30">

            </li>
            <?php } ?>
        </ul>
    </div>
</section>

<?php start_event('admin_config_form_tag'); ?>

<div class="btn_fixed_top btn_confirm">
    <input type="submit" value="<?php e__('Save'); ?>" class="btn_submit btn" accesskey="s">
</div>

</form>

<?php
get_localize_script('config_form',
array(
'default_set_msg'=>__('Default Environment Skin Settings'),  // 기본환경 스킨 설정
'memeber_skin_msg'=>__('Basic environment member skin setting'),    // 기본환경 회원스킨 설정
'theme_msg' => __('Do you want to apply %s of the current theme?'),  // 현재 테마의 %s 를 적용하시겠습니까?
),
true);
?>
<script>
jQuery(function($){

    $(".horizon-swiper").horizonSwiper();
    $(".horizon-swiper").display = "";
    $(".horizon-swiper").fadeIn('slow');


    $("#fconfigform").on("click", ".tab_tit", function(e){
        $(this).next().slideToggle("slow", function() {
            $(this).toggleClass("hide");

            if( $(this).is(":visible") ){
            // IF USE smarteditor2 스마트에디터를 사용한다면
            <?php if( 'smarteditor2' === $config['cf_editor'] ){ ?>
            var othis = $(this);
            if( othis.find("textarea.smarteditor2").length ){
                othis.find("textarea.smarteditor2").each( function(index){
                    var name_attr = $(this).attr("name");

                    // IE 에서는 height 가 0인 버그가 있음
                    if( ! $(this).next("iframe").height() ){
                        oEditors.getById[name_attr].exec("SE_FIT_IFRAME", []);
                        oEditors.getById[name_attr].exec("CHANGE_EDITING_MODE", ["WYSIWYG", true]);
                    }
                });
            }
            <?php } ?>
            }
        });

        $(this).toggleClass('close');
    });

    $("#cf_captcha").on("change", function(){
        if ($(this).val() == 'recaptcha' || $(this).val() == 'recaptcha_inv') {
            $("[class^='kcaptcha_']").hide();
        } else {
            $("[class^='kcaptcha_']").show();
        }
    }).trigger("change");

    $(".get_theme_confc").on("click", function() {
        var type = $(this).data("type");
        var msg = config_form.default_set_msg;
        if(type == "conf_member")
            msg = config_form.memeber_skin_msg;

        if(!confirm( js_sprintf(config_form.theme_msg, msg) ))
            return false;

        $.ajax({
            type: "POST",
            url: "./theme_config_load.php",
            cache: false,
            async: false,
            data: { type: type },
            dataType: "json",
            success: function(data) {
                if(data.error) {
                    alert(data.error);
                    return false;
                }

                var field = Array('cf_member_skin', 'cf_mobile_member_skin', 'cf_new_skin', 'cf_mobile_new_skin', 'cf_search_skin', 'cf_mobile_search_skin', 'cf_connect_skin', 'cf_mobile_connect_skin', 'cf_faq_skin', 'cf_mobile_faq_skin');
                var count = field.length;
                var key;

                for(i=0; i<count; i++) {
                    key = field[i];

                    if(data[key] != undefined && data[key] != "")
                        $("select[name="+key+"]").val(data[key]);
                }
            }
        });
    });
});

function fconfigform_submit(f)
{

    <?php start_event('admin_config_form_sumit', $w); ?>

    f.action = "./config_form_update.php";
    return true;
}

<?php start_event('admin_config_form_script'); ?>

</script>

<?php
include_once ('./admin.tail.php');
?>
