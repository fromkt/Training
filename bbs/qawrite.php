<?php
include_once('./_common.php');
include_once(GML_EDITOR_LIB);

if($w != '' && $w != 'u' && $w != 'r') {
    alert(__('Please use the correct method.'));
}

if($is_guest)
    alert(__('If you are a member, log in and try it.'), './login.php?url='.urlencode(GML_BBS_URL.'/qalist.php'));

$qaconfig = get_qa_config();

$gml['title'] = $qaconfig['qa_title'];
include_once('./qahead.php');

$skin_file = $qa_skin_path.'/write.skin.php';

if(is_file($skin_file)) {
    /*==========================
    $w == a : Reply
    $w == r : Add QA
    $w == u : Edit
    ==========================*/

    if($w == 'u' || $w == 'r') {
        $sql = " select * from {$gml['qa_content_table']} where qa_id = '$qa_id' ";
        if(!$is_admin) {
            $sql .= " and mb_id = '{$member['mb_id']}' ";
        }

        $write = sql_fetch($sql);

        if($w == 'u') {
            if(!$write['qa_id'])
                alert(__('Post does not exist.').'\\n'.__('It has been deleted or is not your own'));

            if(!$is_admin) {
                if($write['qa_type'] == 0 && $write['qa_status'] == 1)
                    alert(__('You can not modify an inquiry with a registered answer.'));

                if($write['mb_id'] != $member['mb_id'])
                    alert(__('You do not have permission to modify the posts.').'\\n\\n'.__('Please use the correct method.'), GML_URL);
            }
        }
    }

    // Category
    $category_option = '';
    if(trim($qaconfig['qa_category'])) {
        $category = explode('|', $qaconfig['qa_category']);
        for($i=0; $i<count($category); $i++) {
            $category_option .= option_selected($category[$i], $write['qa_category']);
        }
    } else {
        alert(__('Please set the Category in 1:1 Inquiry settings'));
    }

    $is_dhtml_editor = false;
    if ($config['cf_editor'] && $qaconfig['qa_use_editor'] && (!is_mobile() || defined('GML_IS_MOBILE_DHTML_USE') && GML_IS_MOBILE_DHTML_USE)) {
        $is_dhtml_editor = true;
    }

    // CKEDITOR 에서 모바일 에디터 사용
    if($config['cf_editor'] == "ckeditor4" && $qaconfig['qa_use_editor']) {
        $is_dhtml_editor = true;
    }

    // 추가질문에서는 제목을 공백으로
    if($w == 'r')
        $write['qa_subject'] = '';

    $content = '';
    if ($w == '') {
        $content = $qaconfig['qa_insert_content'];
    } else if($w == 'r') {
        if($is_dhtml_editor)
            $content = '<div><br><br><br>====== '.__('Previous Answers').' =======<br></div>';
        else
            $content = "\n\n\n\n====== ".__('Previous Answers')." =======\n";

        $content .= get_text($write['qa_content'], 0);
    } else {
        //$content = get_text($write['qa_content'], 0);
        
        // KISA 취약점 권고사항 Stored XSS
        $content = get_text(html_purifier($write['qa_content']), 0);
    }

    $editor_html = editor_html('qa_content', $content, $is_dhtml_editor);
    $editor_js = '';
    $editor_js .= get_editor_js('qa_content', $is_dhtml_editor);
    $editor_js .= chk_editor_js('qa_content', $is_dhtml_editor);

    $upload_max_filesize = number_format($qaconfig['qa_upload_size']) . ' '.__('Bytes');

    $html_value = '';
    if ($write['qa_html']) {
        $html_checked = 'checked';
        $html_value = $write['qa_html'];

        if($w == 'r' && $write['qa_html'] == 1 && !$is_dhtml_editor)
            $html_value = 2;
    }

    $is_email = false;
    $req_email = '';
    if($qaconfig['qa_use_email']) {
        $is_email = true;

        if($qaconfig['qa_req_email'])
            $req_email = 'required';

        if($w == '' || $w == 'r')
            $write['qa_email'] = $member['mb_email'];

        if($w == 'u' && $is_admin && $write['qa_type'])
            $is_email = false;
    }

    $is_hp = false;
    $req_hp = '';
    if($qaconfig['qa_use_hp']) {
        $is_hp = true;

        if($qaconfig['qa_req_hp'])
            $req_hp = 'required';

        if($w == '' || $w == 'r')
            $write['qa_hp'] = $member['mb_hp'];

        if($w == 'u' && $is_admin && $write['qa_type'])
            $is_hp = false;
    }

    $list_href = GML_BBS_URL.'/qalist.php'.preg_replace('/^&amp;/', '?', $qstr);

    $action_url = https_url(GML_BBS_DIR).'/qawrite_update.php';

    include_once($skin_file);
} else {
    echo '<div>'.str_replace(GML_PATH.'/', '', $skin_file).__('File does not exist.').'</div>';
}

include_once('./qatail.php');
?>