<?php
include_once('./_common.php');
include_once(GML_EDITOR_LIB);
include_once(GML_CAPTCHA_PATH.'/captcha.lib.php');

if (!$board['bo_table']) {
    alert(__('This bulletin board does not exist.'), GML_URL);
}

if (!$bo_table) {
    alert(sprintf(__('%s parameter is wrong'), 'bo_table'), GML_URL);
}

check_device($board['bo_device']);

$notice_array = explode(',', trim($board['bo_notice']));

if (!($w == '' || $w == 'u' || $w == 'r')) {
    alert(sprintf(__('%s parameter is wrong'), 'w'));
}

if ($w == 'u' || $w == 'r') {
    if ($write['wr_id']) {
        // 가변 변수로 $wr_1 .. $wr_10 까지 만든다.
        for ($i=1; $i<=10; $i++) {
            $vvar = "wr_".$i;
            $$vvar = $write['wr_'.$i];
        }
    } else {
        alert(__('The post does not exist.').'\\n\\n'.__('The post has been deleted or moved.'), GML_URL);
    }
}

start_event('bbs_write', $board, $wr_id, $w);

if ($w == '') {
    if ($wr_id) {
        alert(__('wr_id values are not used for writing.'), get_pretty_url($bo_table));
    }

    if ($member['mb_level'] < $board['bo_write_level']) {
        if ($member['mb_id']) {
            alert(__('You do not have permission to write.'));
        } else {
            alert(__('You do not have permission to write.')."\\n".__('If you are a member, log in and try it.'), './login.php?'.$qstr.'&amp;url='.urlencode($_SERVER['SCRIPT_NAME'].'?bo_table='.$bo_table));
        }
    }

    if ($is_member) {
        $tmp_point = ($member['mb_point'] > 0) ? $member['mb_point'] : 0;
        if ($tmp_point + $board['bo_write_point'] < 0 && !$is_admin) {
            alert(sprintf(__('Your point (%s) is missing or enough points, so you can not write (%s).'), number_format($member['mb_point']), number_format($board['bo_write_point'])).'\\n\\n'.__('Collect points and write again.'));
        }
    }

    $title_msg = __('Write');
} else if ($w == 'u') {
    //if ($member['mb_level'] < $board['bo_write_level']) {
    if($member['mb_id'] && $write['mb_id'] === $member['mb_id']) {
        ;
    } else if ($member['mb_level'] < $board['bo_write_level']) {
        if ($member['mb_id']) {
            alert(__('You do not have permission to modify the posts.'));
        } else {
            alert(__('You do not have permission to modify the posts.').'\\n\\n'.__('If you are a member, log in and try it.'), './login.php?'.$qstr.'&amp;url='.urlencode($_SERVER['SCRIPT_NAME'].'?bo_table='.$bo_table));
        }
    }

    $len = strlen($write['wr_reply']);
    if ($len < 0) $len = 0;
    $reply = substr($write['wr_reply'], 0, $len);

    // 원글만 구한다.
    $sql = " select count(*) as cnt from {$write_table}
                where wr_reply like '{$reply}%'
                and wr_id <> '{$write['wr_id']}'
                and wr_num = '{$write['wr_num']}'
                and wr_is_comment = 0 ";
    $row = sql_fetch($sql);
    if ($row['cnt'] && !$is_admin)
        alert(__('The Reply to this post exists and can not be modified.').'\\n\\n'.__('You can not modify a post with an Reply.'));

    // 코멘트 달린 원글의 수정 여부
    $sql = " select count(*) as cnt from {$write_table}
                where wr_parent = '{$wr_id}'
                and mb_id <> '{$member['mb_id']}'
                and wr_is_comment = 1 ";
    $row = sql_fetch($sql);
    if ($board['bo_count_modify'] && $row['cnt'] >= $board['bo_count_modify'] && !$is_admin)
        alert(__('The comment associated with this post exists and can not be modified.').'\\n\\n'.sprintf(__('The original post with %s or more comments can not be modified.'), $board['bo_count_modify']));

    $title_msg = __('Edit Post');
} else if ($w == 'r') {
    if ($member['mb_level'] < $board['bo_reply_level']) {
        if ($member['mb_id'])
            alert(__('You do not have permission to reply.'));
        else
            alert(__('You do not have permission to create an Reply.').'\\n\\n'.__('If you are a member, log in and try it.'), './login.php?'.$qstr.'&amp;url='.urlencode($_SERVER['SCRIPT_NAME'].'?bo_table='.$bo_table));
    }

    $tmp_point = isset($member['mb_point']) ? $member['mb_point'] : 0;
    if ($tmp_point + $board['bo_write_point'] < 0 && !$is_admin)
        alert(sprintf(__('Your point (%s) is missing or enough points, so you can not reply (%s).'), number_format($member['mb_point']), number_format($board['bo_comment_point'])).'\\n\\n'.__('Collect points and reply again.'));

    //if (preg_match("/[^0-9]{0,1}{$wr_id}[\r]{0,1}/",$board['bo_notice']))
    if (in_array((int)$wr_id, $notice_array))
        alert(__('Can not comment to the notice.'));

    //----------
    // 4.06.13 : 비밀글을 타인이 열람할 수 있는 오류 수정 (헐랭이, 플록님께서 알려주셨습니다.)
    // 코멘트에는 원글의 답변이 불가하므로
    if ($write['wr_is_comment'])
        alert(__('This is not a normal access.'));

    // 비밀글인지를 검사
    if (strstr($write['wr_option'], 'secret')) {
        if ($write['mb_id']) {
            // 회원의 경우는 해당 글쓴 회원 및 관리자
            if (!($write['mb_id'] === $member['mb_id'] || $is_admin))
                alert(__('The secret post can only be answered by own member or by an administrator.'));
        } else {
            // 비회원의 경우는 비밀글에 답변이 불가함
            if (!$is_admin)
                alert(__('Can not comment to secret writings of non-members.'));
        }
    }
    //----------

    // 게시글 배열 참조
    $reply_array = &$write;

    // 최대 답변은 테이블에 잡아놓은 wr_reply 사이즈만큼만 가능합니다.
    if (strlen($reply_array['wr_reply']) == 10)
        alert(__('You cant reply any more.').'\\n\\n'.__('You can reply only up to 10 steps.'));

    $reply_len = strlen($reply_array['wr_reply']) + 1;
    if ($board['bo_reply_order']) {
        $begin_reply_char = 'A';
        $end_reply_char = 'Z';
        $reply_number = +1;
        $sql = " select MAX(SUBSTRING(wr_reply, {$reply_len}, 1)) as reply from {$write_table} where wr_num = '{$reply_array['wr_num']}' and SUBSTRING(wr_reply, {$reply_len}, 1) <> '' ";
    } else {
        $begin_reply_char = 'Z';
        $end_reply_char = 'A';
        $reply_number = -1;
        $sql = " select MIN(SUBSTRING(wr_reply, {$reply_len}, 1)) as reply from {$write_table} where wr_num = '{$reply_array['wr_num']}' and SUBSTRING(wr_reply, {$reply_len}, 1) <> '' ";
    }
    if ($reply_array['wr_reply']) $sql .= " and wr_reply like '{$reply_array['wr_reply']}%' ";
    $row = sql_fetch($sql);

    if (!$row['reply'])
        $reply_char = $begin_reply_char;
    else if ($row['reply'] == $end_reply_char) // A~Z은 26 입니다.
        alert(__('Cant reply any more.').'\\n\\n'.__('You can reply only up to 26 writes.'));
    else
        $reply_char = chr(ord($row['reply']) + $reply_number);

    $reply = $reply_array['wr_reply'] . $reply_char;

    $title_msg = __('Reply Post');

    $write['wr_subject'] = 'Re: '.$write['wr_subject'];
}

// 그룹접근 가능
if (!empty($group['gr_use_access'])) {
    if ($is_guest) {
        alert(__('You do not have access.')."\\n\\n".__('If you are a member, log in and try it.'), 'login.php?'.$qstr.'&amp;url='.urlencode($_SERVER['SCRIPT_NAME'].'?bo_table='.$bo_table));
    }

    if ($is_admin == 'super' || $group['gr_admin'] === $member['mb_id'] || $board['bo_admin'] === $member['mb_id']) {
        ; // 통과
    } else {
        // 그룹접근
        $sql = " select gr_id from {$gml['group_member_table']} where gr_id = '{$board['gr_id']}' and mb_id = '{$member['mb_id']}' ";
        $row = sql_fetch($sql);
        if (!$row['gr_id'])
            alert(__("You don't have access, so can't write.").'\\n\\n'.__('Please contact your administrator for questions.'));
    }
}

// 글자수 제한 설정값
if ($is_admin || $board['bo_use_dhtml_editor'])
{
    $write_min = $write_max = 0;
}
else
{
    $write_min = (int)$board['bo_write_min'];
    $write_max = (int)$board['bo_write_max'];
}

$gml['title'] = ((GML_IS_MOBILE && $board['bo_mobile_subject']) ? get_board_gettext_titles($board['bo_mobile_subject']) : get_board_gettext_titles($board['bo_subject'])).' '.$title_msg;

$is_notice = false;
$notice_checked = '';
if ($is_admin && $w != 'r') {
    $is_notice = true;

    if ($w == 'u') {
        // 답변 수정시 공지 체크 없음
        if ($write['wr_reply']) {
            $is_notice = false;
        } else {
            if (in_array((int)$wr_id, $notice_array)) {
                $notice_checked = 'checked';
            }
        }
    }
}

$is_html = false;
if ($member['mb_level'] >= $board['bo_html_level'])
    $is_html = true;

$is_secret = $board['bo_use_secret'];

$is_mail = false;
if ($config['cf_email_use'] && $board['bo_use_email'])
    $is_mail = true;

$recv_email_checked = '';
if ($w == '' || strstr($write['wr_option'], 'mail'))
    $recv_email_checked = 'checked';

$is_name     = false;
$is_password = false;
$is_email    = false;
$is_homepage = false;
if ($is_guest || ($is_admin && $w == 'u' && $member['mb_id'] !== $write['mb_id'])) {
    $is_name = true;
    $is_password = true;
    $is_email = true;
    $is_homepage = true;
}

$is_category = false;
$category_option = '';
if ($board['bo_use_category']) {
    $ca_name = "";
    if (isset($write['ca_name']))
        $ca_name = $write['ca_name'];
    $category_option = get_category_option($bo_table, $ca_name);
    $is_category = true;
}

$is_link = false;
if ($member['mb_level'] >= $board['bo_link_level']) {
    $is_link = true;
}

$is_file = false;
if ($member['mb_level'] >= $board['bo_upload_level']) {
    $is_file = true;
}

$is_file_content = false;
if ($board['bo_use_file_content']) {
    $is_file_content = true;
}

$file_count = (int)$board['bo_upload_count'];

$name     = "";
$email    = "";
$homepage = "";
if ($w == "" || $w == "r") {
    if ($is_member) {
        if (isset($write['wr_name'])) {
            $name = get_text(cut_str(stripslashes($write['wr_name']),20));
        }
        $email = get_email_address($member['mb_email']);
        $homepage = get_text(stripslashes($member['mb_homepage']));
    }
}

$html_checked   = "";
$html_value     = "";
$secret_checked = "";

if ($w == '') {
    $password_required = 'required';
} else if ($w == 'u') {
    $password_required = '';

    if (!$is_admin) {
        if (!($is_member && $member['mb_id'] === $write['mb_id'])) {
            if (!check_password($wr_password, $write['wr_password'])) {
                alert(__('Incorrect password.'));
            }
        }
    }

    $name = get_text(cut_str(stripslashes($write['wr_name']),20));
    $email = get_email_address($write['wr_email']);
    $homepage = get_text(stripslashes($write['wr_homepage']));

    for ($i=1; $i<=GML_LINK_COUNT; $i++) {
        $write['wr_link'.$i] = get_text($write['wr_link'.$i]);
        $link[$i] = $write['wr_link'.$i];
    }

    if (strstr($write['wr_option'], 'html1')) {
        $html_checked = 'checked';
        $html_value = 'html1';
    } else if (strstr($write['wr_option'], 'html2')) {
        $html_checked = 'checked';
        $html_value = 'html2';
    }

    if (strstr($write['wr_option'], 'secret')) {
        $secret_checked = 'checked';
    }

    $file = get_file($bo_table, $wr_id);
    if($file_count < $file['count'])
        $file_count = $file['count'];
} else if ($w == 'r') {
    if (strstr($write['wr_option'], 'secret')) {
        $is_secret = true;
        $secret_checked = 'checked';
    }

    $password_required = "required";

    for ($i=1; $i<=GML_LINK_COUNT; $i++) {
        $write['wr_link'.$i] = get_text($write['wr_link'.$i]);
    }
}

set_session('ss_bo_table', $_REQUEST['bo_table']);
set_session('ss_wr_id', $_REQUEST['wr_id']);

$subject = "";
if (isset($write['wr_subject'])) {
    $subject = str_replace("\"", "&#034;", get_text(cut_str($write['wr_subject'], 255), 0));
}

$content = '';
if ($w == '') {
    $content = $board['bo_insert_content'];
} else if ($w == 'r') {
    if (!strstr($write['wr_option'], 'html')) {
        $content = "\n\n\n &gt; "
                 ."\n &gt; "
                 ."\n &gt; ".str_replace("\n", "\n> ", get_text($write['wr_content'], 0))
                 ."\n &gt; "
                 ."\n &gt; ";

    }
} else {
    $content = get_text($write['wr_content'], 0);
}

$upload_max_filesize = number_format($board['bo_upload_size']) . ' '.__('Bytes');

$width = $board['bo_table_width'];
if ($width <= 100)
    $width .= '%';
else
    $width .= 'px';

$captcha_html = '';
$captcha_js   = '';
$is_use_captcha = ((($board['bo_use_captcha'] && $w !== 'u') || $is_guest) && !$is_admin) ? 1 : 0;

if ($is_use_captcha) {
    $captcha_html = captcha_html();
    $captcha_js   = chk_captcha_js();
}

$is_dhtml_editor = false;
$is_dhtml_editor_use = false;
$editor_content_js = '';
if(!is_mobile() || defined('GML_IS_MOBILE_DHTML_USE') && GML_IS_MOBILE_DHTML_USE)
    $is_dhtml_editor_use = true;

// CKEDITOR 에서 모바일 에디터 사용
if($config['cf_editor'] == "ckeditor4" && $board['bo_use_dhtml_editor']) {
    $is_dhtml_editor_use = true;
}

// 모바일에서는 GML_IS_MOBILE_DHTML_USE 설정에 따라 DHTML 에디터 적용
if ($config['cf_editor'] && $is_dhtml_editor_use && $board['bo_use_dhtml_editor'] && $member['mb_level'] >= $board['bo_html_level']) {
    $is_dhtml_editor = true;

    if ( $w == 'u' && (! $is_member || ! $is_admin || $write['mb_id'] !== $member['mb_id']) ){
        // kisa 취약점 제보 xss 필터 적용
        $content = get_text(html_purifier($write['wr_content']), 0);
    }

    if(is_file(GML_EDITOR_PATH.'/'.$config['cf_editor'].'/autosave.editor.js'))
        $editor_content_js = '<script src="'.GML_EDITOR_URL.'/'.$config['cf_editor'].'/autosave.editor.js"></script>'.PHP_EOL;
}
$editor_html = editor_html('wr_content', $content, $is_dhtml_editor);
$editor_js = '';
$editor_js .= get_editor_js('wr_content', $is_dhtml_editor);
$editor_js .= chk_editor_js('wr_content', $is_dhtml_editor);

// 임시 저장된 글 수
$autosave_count = autosave_count($member['mb_id']);

include_once(GML_PATH.'/head.sub.php');
@include_once ($board_skin_path.'/write.head.skin.php');
include_once('./board_head.php');

$action_url = https_url(GML_BBS_DIR)."/write_update.php";

echo '<!-- skin : '.(GML_IS_MOBILE ? $board['bo_mobile_skin'] : $board['bo_skin']).' -->';

$option = '';
$option_hidden = '';
if ($is_notice || $is_html || $is_secret || $is_mail) {
    $option = '';
    if ($is_notice) {
        $option .= "\n".'<input type="checkbox" id="notice" name="notice" value="1" '.$notice_checked.'>'."\n".'<label for="notice">'.__('NOTICE').'</label>';
    }

    if ($is_html) {
        if ($is_dhtml_editor) {
            $option_hidden .= '<input type="hidden" value="html1" name="html">';
        } else {
            $option .= "\n".'<input type="checkbox" id="html" name="html" onclick="html_auto_br(this);" value="'.$html_value.'" '.$html_checked.'>'."\n".'<label for="html">HTML</label>';
        }
    }

    if ($is_secret) {
        if ($is_admin || $is_secret==1) {
            $option .= "\n".'<input type="checkbox" id="secret" name="secret" value="secret" '.$secret_checked.'>'."\n".'<label for="secret">'.__('Secret').'</label>';
        } else {
            $option_hidden .= '<input type="hidden" name="secret" value="secret">';
        }
    }

    if ($is_mail) {
        $option .= "\n".'<input type="checkbox" id="mail" name="mail" value="mail" '.$recv_email_checked.'>'."\n".'<label for="mail">'.__('Receive response mail').'</label>';
    }
}

$dhtml_editor_class = $is_dhtml_editor ? $config['cf_editor'] : '';
$use_character_number = $write_min || $write_max;

for ($i=0; $is_file && $i<$file_count; $i++) {
	$file_bf_content[$i] = ($w == 'u') ? $file[$i]['bf_content'] : '';
	$file_deletable[$i] = ($w == 'u' && $file[$i]['file']);
	$file_delete_lable[$i] = $file_deletable[$i] ? $file[$i]['source'].'('.$file[$i]['size'].')' : '';
}

include_once ($board_skin_path.'/write.skin.php');

include_once (GML_THEME_JS_PATH.'/write_js.php');

include_once('./board_tail.php');
@include_once ($board_skin_path.'/write.tail.skin.php');
include_once(GML_PATH.'/tail.sub.php');
?>
