<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// need thumbnail library.
include_once(GML_LIB_PATH."/thumbnail.lib.php");

function editor_html($id, $content, $is_dhtml_editor=true)
{
    global $g5, $config, $is_mobile, $w, $board, $write;
    static $js = true;
    if(
        $is_dhtml_editor && $content &&
        (
        (!$w && (isset($board['bo_insert_content']) && !empty($board['bo_insert_content'])))
        || ($w == 'u' && isset($write['wr_option']) && strpos($write['wr_option'], 'html') === false )
        )
    ){       //글쓰기 기본 내용 처리
        if( preg_match('/\r|\n/', $content) && $content === strip_tags($content, '<a><strong><b>') ) {  //textarea로 작성되고, html 내용이 없다면
            $content = nl2br($content);
        }
    }
    $editor_url = GML_EDITOR_URL.'/'.$config['cf_editor'];

    $html = "";
    $html .= "<span class=\"sound_only\">".__('Start Web editor')."</span>";
    if (!$is_mobile && $is_dhtml_editor) {
        $html .= '<script>document.write("<div class=\'cke_sc\'><!--<button type=\'button\' class=\'btn_cke_sc\'>'.__('View Shortcut').'</button>--></div>");</script>';
    }

    if ($is_dhtml_editor && $js) {
        $ei_token = uniqid(time());
        switch($id) {
            case "wr_content":  $editor_height = 350;   break;
            default :           $editor_height = 200;   break;
        }
        $html .= "\n".'<script src="'.$editor_url.'/ckeditor.js?v='.(defined('GML_JS_VER') ? GML_JS_VER : "").'"></script>';
        $html .= "\n".'<script>var gml_editor_url = "'.$editor_url.'";</script>';
        $html .= "\n<script>";
        $html .= '
        var editor_token = "'.$ei_token.'",
            editor_id = "'.$id.'",       // 에디터 구분
            editor_height = '.$editor_height.',     // 에디터 높이
            editor_chk_upload = true,           // 업로드 상태
            editor_uri = "'.urlencode($_SERVER['REQUEST_URI']).'";     // 업로드 경로
        $(function(){
            $(".btn_cke_sc").click(function(){
                if ($(this).next("div.cke_sc_def").length) {
                    $(this).next("div.cke_sc_def").remove();
                    $(this).text("'.__('View Shortcut').'");
                } else {
                    $(this).after("<div class=\'cke_sc_def\' />").next("div.cke_sc_def").load("'.$editor_url.'/shortcut.html");
                    $(this).text("'.__('Hide Shortcut').'");
                }
            });
            $(".btn_cke_sc_close").on("click",function(){
                $(this).parent("div.cke_sc_def").remove();
            });
        });';
        $html .= "\n</script>";
        $js = false;
    }

    // 로딩상태 띄우기 (에디터 사용상태일때만)
    if($is_dhtml_editor) {
        // 에디터 사용상태일때 textarea 숨기기
        $editor_taDisplay = "border:none;";
        $html .= "
            <style>
            .editor_loading { position: absolute; left: 50%; top: 50%; z-index: 1; margin: -25px 0 0 -25px; border: 5px solid #f3f3f3; border-radius: 50%; border-top: 5px solid #3498db; width: 30px; height: 30px; -webkit-animation: spin 2s linear infinite; animation: spin 2s linear infinite; }
            @-webkit-keyframes spin { 0% { -webkit-transform: rotate(0deg); } 100% { -webkit-transform: rotate(360deg); } }
            @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
            </style>
        ";
        $html .= "<div class=\"editor_loading\"></div>".PHP_EOL;    // 로딩이미지 출력 부
        $html .= "
            <script>
            try
            {
                CKEDITOR.on( 'instanceLoaded', function (e) {
                    var loader = $(\"div.editor_loading\").css(\"display\",\"none\");
                });
            }
            catch(e) {}
            </script>
        ";
    }

    $ckeditor_class = $is_dhtml_editor ? "ckeditor" : "";
    $html .= "\n<textarea id=\"$id\" name=\"$id\" class=\"$ckeditor_class\" maxlength=\"65536\" style=\"height:{$editor_height}px; {$editor_taDisplay}\">$content</textarea>";
    $html .= "\n<span class=\"sound_only\">".__('End Web editor')."</span>";
    // 현재 폼이름 GET
    $html .= "<script> var editor_form_name = document.getElementById('{$id}').form.name; </script>";
    $html .= "<input type=\"hidden\" name=\"ei_token\" value=\"".$ei_token."\" />";
    return $html;
}


// textarea 로 값을 넘긴다. javascript 반드시 필요
function get_editor_js($id, $is_dhtml_editor=true)
{
    $print_js = "";
    if ($is_dhtml_editor) {
        $print_js .= "var {$id}_editor_data = CKEDITOR.instances.{$id}.getData();\n";
    } else {
        $print_js .= "var {$id}_editor = document.getElementById('{$id}');\n";
    }

    return $print_js;
}

//  textarea 의 값이 비어 있는지 검사
function chk_editor_js($id, $is_dhtml_editor=true)
{
    $print_js = "";
    if ($is_dhtml_editor) {
        $print_js .= "if (!{$id}_editor_data) { alert(\"".__('Enter contents.')."\"); CKEDITOR.instances.{$id}.focus(); return false; }\n";
        $print_js .= "if (typeof(f.{$id})!=\"undefined\") f.{$id}.value = {$id}_editor_data;\n";
        // 썸네일 이미지경로 원본파일로 변경
        $print_js .= "
        var temp_data = {$id}_editor_data.replace(/thumb\-([_\d\.]+)_\d+x\d+/gim, function(res1, res2) { return res2; });
        CKEDITOR.instances.wr_content.setData(temp_data);".PHP_EOL;
    } else {
        $print_js .= "if (!{$id}_editor.value) { alert(\"".__('Enter contents.')."\"); {$id}_editor.focus(); return false; }\n";
    }
    $print_js .= "if(typeof(editor_chk_upload) != \"undefined\" && !editor_chk_upload) { alert(\"".__('Please wait, images uploading.')."\"); return false; }\n";

    return $print_js;
}

// 업로드 이미지 DB저장하여 관리하도록
Class EditorImage
{
    protected   $isUse      = true;    // 기능사용여부 설정 (true/false)
    protected   $tblName    = "editor_image";   // 테이블명
    protected   $delDay     = 1;    // 삭제 대기 일수

    function __construct() {
        // PREFIX 붙여주깅
        $this->tblName  = (defined('GML_TABLE_PREFIX') ? GML_TABLE_PREFIX : "").$this->tblName;

        $this->make_table();
    }

    function get_tblName() {
        return $this->tblName;
    }

    // 테이블 생성
    function make_table() {
        $sql = "
            CREATE TABLE IF NOT EXISTS `{$this->tblName}` (
            `ei_id` int(11) NOT NULL AUTO_INCREMENT,
            `ei_gubun` varchar(20) NOT NULL DEFAULT '',
            `ei_gubun_sub` varchar(20) NOT NULL DEFAULT '',
            `ei_gubun_path` varchar(255) NOT NULL DEFAULT '',
            `bo_table` varchar(20) NOT NULL DEFAULT '',
            `wr_id` INT(11) NOT NULL DEFAULT '0',
            `mb_id` varchar(20) NOT NULL DEFAULT '',
            `ei_name_original` varchar(255) NOT NULL DEFAULT '',
            `ei_name` varchar(255) NOT NULL DEFAULT '',
            `ei_path` varchar(255) NOT NULL DEFAULT '',
            `ei_ext` varchar(10) NOT NULL DEFAULT '',
            `ei_size` int(11) NOT NULL DEFAULT '0',
            `ei_width` int(11) NOT NULL DEFAULT '0',
            `ei_height` int(11) NOT NULL DEFAULT '0',
            `ei_ip` varchar(20) NOT NULL DEFAULT '',
            `ei_datetime` datetime NOT NULL,
            `ei_token` VARCHAR(100) NULL DEFAULT '',
            PRIMARY KEY (`ei_id`),
            KEY `mb_id` (`mb_id`),
            KEY `bo_table` (`bo_table`),
            KEY `ip` (`ei_ip`),
            KEY `select` (`ei_gubun`)
            ) DEFAULT CHARACTER SET = utf8;
        ";
        sql_query($sql);
    }

    // 이미지 경로 출력
    function img_url($path) {
        $_path  = preg_replace('/^\/.*\/'.GML_DATA_DIR.'/', '/'.GML_DATA_DIR, $path);
        $_url   = GML_URL.$_path;

        return $_url;
    }

    // 업로드 썸네일 생성
    function img_thumbnail($srcfile, $thumb_width=0, $thumb_height=0, $path=0) {
        $is_animated = false;   // animated GIF 체크
        if(is_file($srcfile)) {

            $filename = basename($srcfile);
            $filepath = dirname($srcfile);

            $size = @getimagesize($srcfile);
            $width = $size[0];  // 너비
            $height = $size[1]; // 높이
            $fType = $size[2];  // 파일 종류

            // 원본 크기가 지정한 크기보다 크면 썸네일 생성진행
            if($width > $thumb_width || ($thumb_height > 0 && $height > $thumb_height)) {
                #echo $width.PHP_EOL;
                #echo $thumb_width.PHP_EOL;
                #echo $height.PHP_EOL;
                #echo $thumb_height.PHP_EOL;

                // 원본비율에 맞게 너비/높이 계산
                $temp_width = $thumb_width;
                $temp_height = round(($temp_width * $height) / $width);
                // 계산된 높이가 지정된 높이보다 높을경우
                if($thumb_height > 0 && $thumb_height < $temp_height) {
                    $temp_height = $thumb_height;
                    $temp_width = round(($temp_height * $width) / $height);
                }

                // 썸네일 생성
                if($fType == 1 && is_animated_gif($srcfile)) {  // animated GIF 인 경우
                    $is_animated = true;

                    $thumb_filename = preg_replace("/\.[^\.]+$/i", "", $filename);
                    $thumb_filename = "thumb-{$thumb_filename}_{$temp_width}x{$temp_height}.gif";

                    // 썸네일이 없으면 생성시작
                    if(!is_file($filepath."/".$thumb_filename)) {
                        $ani_src = @ImageCreateFromGif($srcfile);
                        $ani_img = @imagecreatetruecolor($temp_width, $temp_height);
                        @ImageColorAllocate($ani_img, 255, 255, 255);
                        @ImageCopyResampled($ani_img, $ani_src, 0, 0, 0, 0, $temp_width, $temp_height, ImageSX($ani_src),ImageSY($ani_src));

                        @ImageInterlace($ani_img);
                        @ImageGif($ani_img, $filepath."/".$thumb_filename);
                    }
                } else {    // 일반 이미지
                    $thumb_filename = thumbnail($filename, $filepath, $filepath, $temp_width, $temp_height, false);
                }
            }
            // 처리된 내용이 없으면 기존 파일 사용
            if(empty($thumb_filename)) {
                $thumb_filename = $filename;
            }

            switch($path) {
                case 1 :
                    $thumb_file = $filepath."/".$thumb_filename;
                    $thumb_file = str_replace(GML_DATA_PATH, GML_DATA_URL, $thumb_file);
                break;
                default:
                    $thumb_file = $thumb_filename;
                break;
            }
        }

        $res = array();
        $res['src'] = $thumb_file;
        $res['animated'] = $is_animated;

        return $res;
    }

    // 이미지 삭제
    function img_del($arr) {
        if(count($arr) < 1) return;
        // 선택된 번호들 나열
        $del_ei_id = implode("', '", $arr);
        // 파일별 경로 조회
        $sql_chk = "select ei_id, ei_path ";
        $sql_chk .= " from {$this->tblName} ";
        $sql_chk .= " where ei_id in ('{$del_ei_id}')";
        $res_chk = sql_query($sql_chk);
        $del_arr = array();
        while($row = sql_fetch_array($res_chk)) {
            $path = GML_DATA_PATH.preg_replace( '/(\.\.[\/\\\])+/', '', $row['ei_path']);
            // 조회된 경로의 파일 삭제진행
            unlink($path);
            // 썸네일 삭제
            $filename = preg_replace("/\.[^\.]+$/i", "", basename($path));
            $filepath = dirname($path);
            $files = glob($filepath.'/thumb-'.$filename.'*');
            if (is_array($files)) {
                foreach($files as $filename) unlink($filename);
            }

            $del_arr[] = $row['ei_id'];
        }
        // 파일이 삭제가 되면 DB내용 삭제
        $sql_del = "delete ";
        $sql_del .= " from {$this->tblName} ";
        $sql_del .= " where ei_id in ('".implode("','", $del_arr)."')";
        sql_query($sql_del);
    }

    // 업로드 이미지 테이블 저장
    function insert_data($file, $upload, $gubun, $gubun_sub, $gubun_path, $ei_token) {
        if(!$this->isUse) return;

        global $member;
        $mb_id  = $member['mb_id'];

        $oname  = $file['name'];
        $fsize  = $file['size'];
        $ip     = $_SERVER['REMOTE_ADDR'];
        $ftmp   = explode(".", $oname);
        $fext   = $ftmp[count($ftmp)-1];
        $fpath  = str_replace(GML_DATA_PATH, '', $upload);
        $utmp   = getimagesize($upload);
        $width  = $utmp[0];
        $height = $utmp[1];
        $uftmp  = explode("/", $upload);
        $fname  = $uftmp[count($uftmp)-1];

        $sql = "
            insert into {$this->tblName} set
                ei_gubun    = '{$gubun}',
                ei_gubun_sub = '{$gubun_sub}',
                ei_gubun_path = '{$gubun_path}',
                bo_table    = '{$bo_table}',
                mb_id       = '{$mb_id}',
                ei_name_original = '{$oname}',
                ei_name     = '{$fname}',
                ei_path     = '{$fpath}',
                ei_ext      = '{$fext}',
                ei_size     = '{$fsize}',
                ei_width    = '{$width}',
                ei_height   = '{$height}',
                ei_ip       = '{$ip}',
                ei_token    = '{$ei_token}',
                ei_datetime = now()
        ";
        $res = sql_query($sql);

        return $res;
    }

    // 게시글 등록/수정시 실행내용
    function chk_update($cont, $token, $bo_table='', $wr_id=0) {
        // 수정 시 기존 이미지 삭제 여부체크
        $this->chk_modify($cont, $bo_table, $wr_id);

        // 업로드 이미지 관련 토큰 체크
        $this->chk_regist($cont, $token, $bo_table, $wr_id);
    }

    // 게시글 수정 시 기존이미지 제거확인
    function chk_modify($cont, $bo_table, $wr_id) {
        $del_arr = array();

        $sql = "select * from {$this->tblName} where bo_table = '{$bo_table}' and wr_id = '{$wr_id}'";
        $res = sql_query($sql);
        while($row = sql_fetch_array($res)) {
            $pattern = $row['ei_name'];
            $chk = preg_match("|".$pattern."|", $cont);
            if($chk == 0) {
                // 파일 삭제진행
                $del_file = GML_DATA_PATH.preg_replace('/(\.\.[\/\\\])+/', '', $row['ei_path']);

                if( file_exists($del_file) ){
                    @unlink($del_file);
                }

                $del_arr[] = $row['ei_id'];
            }
        }
        // 삭제된 기존이미지 DB에서도 삭제
        if(count($del_arr) > 0) {
            sql_query(" delete from {$this->tblName} where ei_id in ('".implode("','", $del_arr)."') ");
        }
    }

    // 게시글 등록 시 사용구분값 갱신
    function chk_regist($cont, $token, $bo_table, $wr_id) {
        if(empty($token)) return;
        $sql = "select * from {$this->tblName} where ei_token = '{$token}'";
        $res = sql_query($sql);
        while($row = sql_fetch_array($res)) {
            // 실제 사용된 이미지들 정보 갱신처리
            if( $this->chk_content($cont, $row['ei_name']) ) {
                sql_query("update {$this->tblName} set wr_id = {$wr_id}, bo_table = '{$bo_table}' where ei_id = '{$row['ei_id']}' and ei_token = '{$token}'");
            }
        }
    }

    // 게시글 삭제 시 해당 이미지 삭제
    function chk_delete($bo_table, $wr_id) {
        $sql_uf = "select * from {$this->tblName} where bo_table = '{$bo_table}' and wr_id = '{$wr_id}'";
        $res_uf = sql_query($sql_uf);
        if(sql_num_rows($res_uf) > 0) {
            $del_arr = array();
            while($row = sql_fetch_array($res_uf)) {
                // 파일 삭제진행
                $del_file = GML_DATA_PATH.preg_replace('/(\.\.[\/\\\])+/', '', $row['ei_path']);

                if( file_exists($del_file) ){
                    @unlink($del_file);
                }

                $del_arr[] = $row['ei_id'];
            }
            // 삭제 완료 시 리스트에서 제거
            if(count($del_arr) > 0) {
                sql_query(" delete from {$this->tblName} where ei_id in ('".implode("','", $del_arr)."') ");
            }
        }
    }

    // 글내용에 이미지 존재하는지 확인
    function chk_content($cont, $img) {
        // 파일명, 확장자 분리
        $ptn = "/(.+).(jpg|jpeg|gif|png)/";
        preg_match($ptn, $img, $res);
        // 썸네일 처리된 경우 존재하여
        $pattern = "/{$res[1]}(.*)?.{$res[2]}/";
        return preg_match($pattern, $cont, $tmp);
    }
}
?>
