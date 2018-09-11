<?php
function print_error($type, $msg) {
    if(strtolower($type) == "json") {
        $res = array();
        $res['uploaded'] = 0;
        $res['error']['message'] = $msg;
        echo json_encode($res);
    } else {
        echo "<script> alert('{$msg}'); </script>";
    }
    exit;
}

include_once("../../../common.php");
include_once(GML_EDITOR_LIB);

// 업로드 경로 세팅
$ym = date('ym', GML_SERVER_TIME);
$data_dir = GML_DATA_PATH.'/editor/'.$ym;
$data_url = GML_DATA_URL.'/editor/'.$ym;
@mkdir($data_dir, GML_DIR_PERMISSION);
@chmod($data_dir, GML_DIR_PERMISSION);
 
// 업로드 DIALOG 에서 전송된 값
$funcNum = $_GET['CKEditorFuncNum'] ;

// 업로드 대상 파일
$upFile = $_FILES['upload'];
if(empty($upFile['tmp_name'])) {
    $msg = e__("No files");
    print_error($responseType, $msg);
}

$fileInfo = pathinfo($upFile['name']);
$filename  = $fileInfo['filename'];
$extension = $fileInfo['extension'];
$extension = strtolower($extension);

if (!preg_match("/(jpe?g|gif|png)$/i", $extension)) {
    $msg = e__("Only 'jpg, gif, png' file");
    print_error($responseType, $msg);
}
// jpeg 확장자 jpg로 통일되도록
if($extension == 'jpeg') $extension = 'jpg';

// 윈도우에서 한글파일명으로 업로드 되지 않는 오류 해결
$file_name = sprintf('%u', ip2long($_SERVER['REMOTE_ADDR'])).'_'.get_microtime().".".$extension;
$save_dir = sprintf('%s/%s', $data_dir, $file_name);

if (move_uploaded_file($upFile["tmp_name"], $save_dir)) {
    $w_pc   = 835;
    $w_mo   = 320;
    $ei = new EditorImage();
    $ins = $ei->insert_data($upFile, $save_dir, $_GET['editor_form_name'], $_GET['editor_id'], $_GET['editor_uri'], $_GET['ei_token']);
    
    // 썸네일 생성
    $img_width = $is_mobile ? $w_mo : $w_pc;
    $tmp_thumb = $ei->img_thumbnail($save_dir, $img_width);
    $img_thumb = $tmp_thumb['src'];
    $save_url = sprintf('%s/%s', $data_url, $img_thumb);

    // 성공 결과 출력
    if(strtolower($responseType) == "json") {
        $res = array();
        $res['fileName'] = $file_name;
        $res['url'] = $save_url;
        $res['uploaded'] = 1;
        $res['inserted'] = $ins;

        if($file_name != $img_thumb) {  // 이름이 다르면 지정사이즈를 초과하여 썸네일화된것으로 간주, 출력 사이즈 지정
            $res['width'] = "100%";
            $res['height'] = "auto";
        }
        echo json_encode($res);
    } else {
        echo "<script>window.parent.CKEDITOR.tools.callFunction({$funcNum}, '{$save_url}', '');</script>";
    }
    exit;
}

$msg = e__("Upload failure.");
print_error($responseType, $msg);
?>