<?php
include_once('./_common.php');

/*==========================
$w == a : 답변
$w == r : 추가질문
$w == u : 수정
==========================*/

if($is_guest)
    alert(__('If you are a member, please Log in and use it.'), './login.php?url='.urlencode(GML_BBS_URL.'/qalist.php'));

$msg = array();

// 1:1문의 설정값
$qaconfig = get_qa_config();

if(trim($qaconfig['qa_category'])) {
    if($w != 'a') {
        $category = explode('|', $qaconfig['qa_category']);
        if(!in_array($qa_category, $category))
            alert(__('Please specify a valid Category.'));
    }
} else {
    alert(__('Please set the Category in 1:1 statement settings'));
}

// e-mail 체크
$qa_email = '';
if(isset($_POST['qa_email']) && $_POST['qa_email'])
    $qa_email = get_email_address(trim($_POST['qa_email']));

if($w != 'a' && $qaconfig['qa_req_email'] && !$qa_email)
    $msg[] = __('Please enter your email.');

$qa_subject = '';
if (isset($_POST['qa_subject'])) {
    $qa_subject = substr(trim($_POST['qa_subject']),0,255);
    $qa_subject = preg_replace("#[\\\]+$#", "", $qa_subject);
}
if ($qa_subject == '') {
    $msg[] = __('Please enter a <strong>Subject</strong>.');
}

$qa_content = '';
if (isset($_POST['qa_content'])) {
    $qa_content = substr(trim($_POST['qa_content']),0,65536);
    $qa_content = preg_replace("#[\\\]+$#", "", $qa_content);
}
if ($qa_content == '') {
    $msg[] = __('Please enter a <strong>Content</strong>.');
}

if (!empty($msg)) {
    $msg = implode('<br>', $msg);
    alert($msg);
}

if($qa_hp)
    $qa_hp = preg_replace('/[^0-9\-]/', '', strip_tags($qa_hp));

// 090710
if (substr_count($qa_content, '&#') > 50) {
    alert(__('Content contains many invalid codes.'));
    exit;
}

$upload_max_filesize = ini_get('upload_max_filesize');

if (empty($_POST)) {
    alert(__('An error occurred because the size of the file or post content exceeds the value set by the server.')."\\npost_max_size=".ini_get('post_max_size')." , upload_max_filesize=".$upload_max_filesize."\\n".__('Please contact your Board administrator or server administrator.'));
}

for ($i=1; $i<=5; $i++) {
    $var = "qa_$i";
    $$var = "";
    if (isset($_POST['qa_'.$i]) && $_POST['qa_'.$i]) {
        $$var = trim($_POST['qa_'.$i]);
    }
}

if($w == 'u' || $w == 'a' || $w == 'r') {
    if($w == 'a' && !$is_admin)
        alert(__('The answer can only be registered by an administrator.'));

    $sql = " select * from {$gml['qa_content_table']} where qa_id = '$qa_id' ";
    if(!$is_admin) {
        $sql .= " and mb_id = '{$member['mb_id']}' ";
    }

    $write = sql_fetch($sql);

    if($w == 'u') {
        if(!$write['qa_id'])
            alert(__('Post does not exist.').'\\n'.__('This is either deleted or not your own.'));

        if(!$is_admin) {
            if($write['qa_type'] == 0 && $write['qa_status'] == 1)
                alert(__('You can not modify an inquiry with a registered answer.'));

            if($write['mb_id'] != $member['mb_id'])
                alert(__('You do not have permission to modify the posts.').'\\n\\n'.__('Please use the correct method.'), GML_URL);
        }
    }

    if($w == 'a') {
        if(!$write['qa_id'])
            alert(__('The answer can not be registered because it does not exist.'));

        if($write['qa_type'] == 1)
            alert(__('You can not reregister an answer in an answer article.'));
    }
}

// 파일개수 체크
$file_count   = 0;
$upload_count = count($_FILES['bf_file']['name']);

for ($i=1; $i<=$upload_count; $i++) {
    if($_FILES['bf_file']['name'][$i] && is_uploaded_file($_FILES['bf_file']['tmp_name'][$i]))
        $file_count++;
}

if($file_count > 2)
    alert(__('Please upload no more than 2 attachments.'));

// 디렉토리가 없다면 생성합니다. (퍼미션도 변경하구요.)
@mkdir(GML_DATA_PATH.'/qa', GML_DIR_PERMISSION);
@chmod(GML_DATA_PATH.'/qa', GML_DIR_PERMISSION);

$chars_array = array_merge(range(0,9), range('a','z'), range('A','Z'));

// 가변 파일 업로드
$file_upload_msg = '';
$upload = array();
for ($i=1; $i<=count($_FILES['bf_file']['name']); $i++) {
    $upload[$i]['file']     = '';
    $upload[$i]['source']   = '';
    $upload[$i]['del_check'] = false;

    // 삭제에 체크가 되어있다면 파일을 삭제합니다.
    if (isset($_POST['bf_file_del'][$i]) && $_POST['bf_file_del'][$i]) {
        $upload[$i]['del_check'] = true;
        @unlink(GML_DATA_PATH.'/qa/'.$write['qa_file'.$i]);
        // 썸네일삭제
        if(preg_match("/\.({$config['cf_image_extension']})$/i", $write['qa_file'.$i])) {
            delete_qa_thumbnail($write['qa_file'.$i]);
        }
    }

    $tmp_file  = $_FILES['bf_file']['tmp_name'][$i];
    $filesize  = $_FILES['bf_file']['size'][$i];
    $filename  = $_FILES['bf_file']['name'][$i];
    $filename  = get_safe_filename($filename);

    // 서버에 설정된 값보다 큰파일을 업로드 한다면
    if ($filename) {
        if ($_FILES['bf_file']['error'][$i] == 1) {
            $file_upload_msg .= sprintf(__('The file " %s " can not be uploaded because its size is greater than the value set on the server (%s).'), $filename, $upload_max_filesize).'\\n';
            continue;
        }
        else if ($_FILES['bf_file']['error'][$i] != 0) {
            $file_upload_msg .= sprintf(__('The file " %s " has not been successfully uploaded.'), $filename).'\\n';
            continue;
        }
    }

    if (is_uploaded_file($tmp_file)) {
        // 관리자가 아니면서 설정한 업로드 사이즈보다 크다면 건너뜀
        if (!$is_admin && $filesize > $qaconfig['qa_upload_size']) {
            $file_upload_msg .= sprintf(__('The " %s " file will not be uploaded because its capacity ( %s bytes) is greater than the value set ( %s bytes) on the bulletin board.'), $filename, number_format($filesize), number_format($qaconfig['qa_upload_size'])).'\\n';
            continue;
        }

        //=================================================================\
        // 090714
        // 이미지나 플래시 파일에 악성코드를 심어 업로드 하는 경우를 방지
        // 에러메세지는 출력하지 않는다.
        //-----------------------------------------------------------------
        $timg = @getimagesize($tmp_file);
        // image type
        if ( preg_match("/\.({$config['cf_image_extension']})$/i", $filename) ||
             preg_match("/\.({$config['cf_flash_extension']})$/i", $filename) ) {
            if ($timg['2'] < 1 || $timg['2'] > 16)
                continue;
        }
        //=================================================================

        if ($w == 'u') {
            // 존재하는 파일이 있다면 삭제합니다.
            @unlink(GML_DATA_PATH.'/qa/'.$write['qa_file'.$i]);
            // 이미지파일이면 썸네일삭제
            if(preg_match("/\.({$config['cf_image_extension']})$/i", $write['qa_file'.$i])) {
                delete_qa_thumbnail($row['qa_file'.$i]);
            }
        }

        // 프로그램 원래 파일명
        $upload[$i]['source'] = $filename;
        $upload[$i]['filesize'] = $filesize;

        // 아래의 문자열이 들어간 파일은 -x 를 붙여서 웹경로를 알더라도 실행을 하지 못하도록 함
        $filename = preg_replace("/\.(php|pht|phtm|htm|cgi|pl|exe|jsp|asp|inc)/i", "$0-x", $filename);

        shuffle($chars_array);
        $shuffle = implode('', $chars_array);

        // 첨부파일 첨부시 첨부파일명에 공백이 포함되어 있으면 일부 PC에서 보이지 않거나 다운로드 되지 않는 현상이 있습니다. (길상여의 님 090925)
        $upload[$i]['file'] = abs(ip2long($_SERVER['REMOTE_ADDR'])).'_'.substr($shuffle,0,8).'_'.replace_filename($filename);

        $dest_file = GML_DATA_PATH.'/qa/'.$upload[$i]['file'];

        // 업로드가 안된다면 에러메세지 출력하고 죽어버립니다.
        $error_code = move_uploaded_file($tmp_file, $dest_file) or die($_FILES['bf_file']['error'][$i]);

        // 올라간 파일의 퍼미션을 변경합니다.
        chmod($dest_file, GML_FILE_PERMISSION);
    }
}

if($w == '' || $w == 'a' || $w == 'r') {
    if($w == '' || $w == 'r') {
        $row = sql_fetch(" select MIN(qa_num) as min_qa_num from {$gml['qa_content_table']} ");
        $qa_num = $row['min_qa_num'] - 1;
    }

    if($w == 'a') {
        $qa_num = $write['qa_num'];
        $qa_parent = $write['qa_id'];
        $qa_related = $write['qa_related'];
        $qa_category = $write['qa_category'];
        $qa_type = 1;
        $qa_status = 1;
    }

    $sql = " insert into {$gml['qa_content_table']}
                set qa_num          = '$qa_num',
                    mb_id           = '{$member['mb_id']}',
                    qa_name         = '".addslashes($member['mb_nick'])."',
                    qa_email        = '$qa_email',
                    qa_hp           = '$qa_hp',
                    qa_type         = '$qa_type',
                    qa_parent       = '$qa_parent',
                    qa_related      = '$qa_related',
                    qa_category     = '$qa_category',
                    qa_email_recv   = '$qa_email_recv',
                    qa_sms_recv     = '$qa_sms_recv',
                    qa_html         = '$qa_html',
                    qa_subject      = '$qa_subject',
                    qa_content      = '$qa_content',
                    qa_status       = '$qa_status',
                    qa_file1        = '{$upload[1]['file']}',
                    qa_source1      = '{$upload[1]['source']}',
                    qa_file2        = '{$upload[2]['file']}',
                    qa_source2      = '{$upload[2]['source']}',
                    qa_ip           = '{$_SERVER['REMOTE_ADDR']}',
                    qa_datetime     = '".GML_TIME_YMDHIS."',
                    qa_1            = '$qa_1',
                    qa_2            = '$qa_2',
                    qa_3            = '$qa_3',
                    qa_4            = '$qa_4',
                    qa_5            = '$qa_5' ";
    sql_query($sql);

    if($w == '' || $w == 'r') {
        $qa_id = sql_insert_id();

        if($w == 'r' && $write['qa_related']) {
            $qa_related = $write['qa_related'];
        } else {
            $qa_related = $qa_id;
        }

        $sql = " update {$gml['qa_content_table']}
                    set qa_parent   = '$qa_id',
                        qa_related  = '$qa_related'
                    where qa_id = '$qa_id' ";
        sql_query($sql);
    }

    if($w == 'a') {
        $answer_id = sql_insert_id();

        $sql = " update {$gml['qa_content_table']}
                    set qa_status = '1'
                    where qa_id = '{$write['qa_parent']}' ";
        sql_query($sql);
    }
} else if($w == 'u') {
    if(!$upload[1]['file'] && !$upload[1]['del_check']) {
        $upload[1]['file'] = $write['qa_file1'];
        $upload[1]['source'] = $write['qa_source1'];
    }

    if(!$upload[2]['file'] && !$upload[2]['del_check']) {
        $upload[2]['file'] = $write['qa_file2'];
        $upload[2]['source'] = $write['qa_source2'];
    }

    $sql = " update {$gml['qa_content_table']}
                set qa_email    = '$qa_email',
                    qa_hp       = '$qa_hp',
                    qa_category = '$qa_category',
                    qa_html     = '$qa_html',
                    qa_subject  = '$qa_subject',
                    qa_content  = '$qa_content',
                    qa_file1    = '{$upload[1]['file']}',
                    qa_source1  = '{$upload[1]['source']}',
                    qa_file2    = '{$upload[2]['file']}',
                    qa_source2  = '{$upload[2]['source']}',
                    qa_1        = '$qa_1',
                    qa_2        = '$qa_2',
                    qa_3        = '$qa_3',
                    qa_4        = '$qa_4',
                    qa_5        = '$qa_5' ";
    if($qa_sms_recv)
        $sql .= ", qa_sms_recv = '$qa_sms_recv' ";
    $sql .= " where qa_id = '$qa_id' ";
    sql_query($sql);
}

start_event('qawrite_update', $qa_id, $write, $w, $qaconfig);

// 답변 알림
if($w == 'a') {
    $sql = " select mb_id from {$gml['qa_content_table']} where qa_id = {$qa_parent} and qa_type = {$write['qa_type']} ";
    $row = sql_fetch($sql);
    $answer_mb_id = $row['mb_id'];
    add_notice("answer", $member['mb_id'], $answer_mb_id, "", $qa_id, $qa_parent);
}
// 문의 알림
if($w == '' || $w == 'r') {
    $case = ($w ? "$w-" : ""). "ask";
    add_notice($case, $member['mb_id'], strtolower($config['cf_admin']), "", $qa_id, $qa_related);
}

// 답변 이메일전송
if($w == 'a' && $write['qa_email_recv'] && trim($write['qa_email'])) {
    include_once(GML_LIB_PATH.'/mailer.lib.php');

    $subject = $config['cf_title'].' '.$qaconfig['qa_title'].' '.__('Reply notification mail');
    $content = nl2br(conv_unescape_nl(stripslashes($qa_content)));

    mailer($config['cf_admin_email_name'], $config['cf_admin_email'], $write['qa_email'], $subject, $content, 1);
}

// 문의글등록 이메일전송
if(($w == '' || $w == 'r') && trim($qaconfig['qa_admin_email'])) {
    include_once(GML_LIB_PATH.'/mailer.lib.php');

    $subject = $config['cf_title'].' '.$qaconfig['qa_title'].' '.__('Question notification mail');
    $content = nl2br(conv_unescape_nl(stripslashes($qa_content)));

    mailer($config['cf_admin_email_name'], $qa_email, $qaconfig['qa_admin_email'], $subject, $content, 1);
}

// CKEDITOR Upload images sources
if( $config['cf_editor'] == "ckeditor4" ) {
    include_once(GML_EDITOR_LIB);
    if( class_exists('EditorImage') ) {
        $_qa_id = empty( $answer_id ) ? $qa_id : $answer_id;
        // 게시글 등록/수정 시 이미지 실사용 체크
        $eImg = new EditorImage();
        $eImg->chk_update($qa_content, $ei_token, "bbs/qa", $_qa_id);
    }
}

if($w == 'a')
    $result_url = GML_BBS_URL.'/qaview.php?qa_id='.$qa_id.$qstr;
else if($w == 'u' && $write['qa_type'])
    $result_url = GML_BBS_URL.'/qaview.php?qa_id='.$write['qa_parent'].$qstr;
else
    $result_url = GML_BBS_URL.'/qalist.php'.preg_replace('/^&amp;/', '?', $qstr);

if ($file_upload_msg)
    alert($file_upload_msg, $result_url);
else
    goto_url($result_url);
?>
