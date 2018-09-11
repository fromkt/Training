<?php
include_once('./_common.php');

if($is_guest)
    alert(__('If you are a member, please Log in and use it.'), GML_URL);

$delete_token = get_session('ss_qa_delete_token');
set_session('ss_qa_delete_token', '');

// If not Admin, use captcha 관리자가 아닌경우에는 토큰을 검사합니다.
if (!$is_admin && !($token && $delete_token == $token))
    alert(__('Unable to delete due to token error.'));

$tmp_array = array();
if ($qa_id) // 건별삭제
    $tmp_array[0] = $qa_id;
else // 일괄삭제
    $tmp_array = $_POST['chk_qa_id'];

$count = count($tmp_array);
if(!$count)
    alert(__('Please select at least one post to delete.'));

for($i=0; $i<$count; $i++) {
    $qa_id = (int) $tmp_array[$i];

    $sql = " select qa_id, mb_id, qa_type, qa_status, qa_parent, qa_content, qa_file1, qa_file2
                from {$gml['qa_content_table']}
                where qa_id = '$qa_id' ";
    $row = sql_fetch($sql);

    if(!$row['qa_id'])
        continue;

    // 자신의 글이 아니면 건너뜀
    if($is_admin != 'super' && $row['mb_id'] !== $member['mb_id'])
        continue;

    // 답변이 달린 글은 삭제못함
    if($is_admin != 'super' && !$row['qa_type'] && $row['qa_status'])
        continue;

    // 첨부파일 삭제
    for($k=1; $k<=2; $k++) {
        @unlink(GML_DATA_PATH.'/qa/'.$row['qa_file'.$k]);
        // 썸네일삭제
        if(preg_match("/\.({$config['cf_image_extension']})$/i", $row['qa_file'.$k])) {
            delete_qa_thumbnail($row['qa_file'.$k]);
        }
    }

    // 에디터 썸네일 삭제
    delete_editor_thumbnail($row['qa_content']);

    // 답변이 있는 질문글이라면 답변글 삭제
    if(!$row['qa_type'] && $row['qa_status']) {
        $row2 = sql_fetch(" select qa_content, qa_file1, qa_file2 from {$gml['qa_content_table']} where qa_parent = '$qa_id' ");
        // 첨부파일 삭제
        for($k=1; $k<=2; $k++) {
            @unlink(GML_DATA_PATH.'/qa/'.$row2['qa_file'.$k]);
            // 썸네일삭제
            if(preg_match("/\.({$config['cf_image_extension']})$/i", $row2['qa_file'.$k])) {
                delete_qa_thumbnail($row2['qa_file'.$k]);
            }
        }

        // 에디터 썸네일 삭제
        delete_editor_thumbnail($row2['qa_content']);

        sql_query(" delete from {$gml['qa_content_table']} where qa_type = '1' and qa_parent = '$qa_id' ");
    }

    // 답변글 삭제시 질문글의 상태변경
    if($row['qa_type']) {
        sql_query(" update {$gml['qa_content_table']} set qa_status = '0' where qa_id = '{$row['qa_parent']}' ");
    }

    // 글삭제
    sql_query(" delete from {$gml['qa_content_table']} where qa_id = '$qa_id' ");
}

// CKEDITOR Upload images sources
if( $config['cf_editor'] == "ckeditor4" ) {
    include_once(GML_EDITOR_LIB);
    if( class_exists('EditorImage') ) {
        // 게시글 삭제 시 업로드 이미지 삭제
        $eImg = new EditorImage();
        $eImg->chk_delete("bbs/qa", $qa_id);
    }
}

goto_url(GML_BBS_URL.'/qalist.php'.preg_replace('/^&amp;/', '?', $qstr));
?>