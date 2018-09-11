<?php
if (!defined('_GNUBOARD_')) exit;
include_once(GML_LIB_PATH.'/thumbnail.lib.php');

// 최신글 추출
// $cache_time 캐시 갱신시간
function latest($skin_dir='', $bo_table, $rows=10, $subject_len=40, $cache_time=1, $options='')
{
    global $gml;

    if (!$skin_dir) $skin_dir = 'basic';

    if (GML_IS_MOBILE) {
        $latest_skin_path = GML_THEME_MOBILE_PATH.'/'.GML_SKIN_DIR.'/latest/'.$skin_dir;
        if(!is_dir($latest_skin_path))
            $latest_skin_path = GML_THEME_PATH.'/'.GML_SKIN_DIR.'/latest/'.$skin_dir;
        $latest_skin_url = str_replace(GML_PATH, GML_URL, $latest_skin_path);
    } else {
        $latest_skin_path = GML_THEME_PATH.'/'.GML_SKIN_DIR.'/latest/'.$skin_dir;
        $latest_skin_url = str_replace(GML_PATH, GML_URL, $latest_skin_path);
    }

    //최신글 스킨 언어파일을 가져옵니다.
    bind_lang_domain( 'default', get_path_lang_dir('skin', $latest_skin_path.'/'.GML_LANG_DIR) );

    $caches = null;

    if(GML_USE_CACHE) {
        $cache_file_name = "latest-{$bo_table}-{$skin_dir}-{$rows}-{$subject_len}-".gml_cache_secret_key();
        $caches = gml_get_cache($cache_file_name);
        $cache_list = isset($caches['list']) ? $caches['list'] : array();
        gml_latest_cache_data($bo_table, $cache_list);
    }

    if( $caches === null ){

        $list = array();

        $board = get_board_db($bo_table, true);
        $bo_subject = get_text($board['bo_subject']);
        $board['bo_use_sideview'] = 0;  // not use sideview

        $tmp_write_table = $gml['write_prefix'] . $bo_table; // 게시판 테이블 전체이름
        $sql = " select * from {$tmp_write_table} where wr_is_comment = 0 order by wr_num limit 0, {$rows} ";
        $result = sql_query($sql);
        for ($i=0; $row = sql_fetch_array($result); $i++) {
            try {
                unset($row['wr_password']);     //패스워드 저장 안함( 아예 삭제 )
            } catch (Exception $e) {
            }
            $row['wr_email'] = '';              //이메일 저장 안함
            if (strstr($row['wr_option'], 'secret')){           // 비밀글일 경우 내용, 링크, 파일 저장 안함
                $row['wr_content'] = $row['wr_link1'] = $row['wr_link2'] = '';
                $row['file'] = array('count'=>0);
            }
            $list[$i] = get_list($row, $board, $latest_skin_url, $subject_len);

            $list[$i]['first_file_thumb'] = (isset($row['wr_file']) && $row['wr_file']) ? get_board_file_db($bo_table, $row['wr_id'], 'bf_file, bf_content', "and bf_type between '1' and '3'", true) : array('bf_file'=>'', 'bf_content'=>'');
            $list[$i]['bo_table'] = $bo_table;
            // 썸네일 추가
            if($options) {
                $options_arr = explode(',', $options);
                $thumb_width = $options_arr[0];
                $thumb_height = $options_arr[1];
                $thumb = get_list_thumbnail($bo_table, $row['wr_id'], $thumb_width, $thumb_height, false, true);
                // 이미지 썸네일
                if($thumb['src']) {
                    $img_content = '<img src="'.$thumb['src'].'" alt="'.$thumb['alt'].'" width="'.$thumb_width.'" height="'.$thumb_height.'">';
                    $list[$i]['img_thumbnail'] = '<a href="'.$list[$i]['href'].'" class="lt_img">'.$img_content.'</a>';
                // } else {
                //     $img_content = '<img src="'. GML_IMG_URL.'/no_img.png'.'" alt="'.$thumb['alt'].'" width="'.$thumb_width.'" height="'.$thumb_height.'" class="no_img">';
                }
            }
        }
        gml_latest_cache_data($bo_table, $list);

        if(GML_USE_CACHE) {
            $caches = array(
                'list' => $list,
                'bo_subject' => sql_escape_string($bo_subject),
            );

            gml_set_cache($cache_file_name, $caches, 3600 * $cache_time);
        }
    } else {
        $list = $cache_list;
        $bo_subject = (is_array($caches) && isset($caches['bo_subject'])) ? $caches['bo_subject'] : '';
    }

    /*

    $cache_fwrite = false;

    if(GML_USE_CACHE) {

        $cache_file = GML_DATA_PATH."/cache/latest-{$bo_table}-{$skin_dir}-{$rows}-{$subject_len}-serial.php";

        if(!file_exists($cache_file)) {
            $cache_fwrite = true;
        } else {
            if($cache_time > 0) {
                $filetime = filemtime($cache_file);
                if($filetime && $filetime < (GML_SERVER_TIME - 3600 * $cache_time)) {
                    @unlink($cache_file);
                    $cache_fwrite = true;
                }
            }

            if(!$cache_fwrite) {
                try{
                    $file_contents = file_get_contents($cache_file);
                    $file_ex = explode("\n\n", $file_contents);
                    $caches = unserialize(base64_decode($file_ex[1]));

                    $list = (is_array($caches) && isset($caches['list'])) ? $caches['list'] : array();
                    $bo_subject = (is_array($caches) && isset($caches['bo_subject'])) ? $caches['bo_subject'] : '';
                } catch(Exception $e){
                    $cache_fwrite = true;
                    $list = array();
                }
            }
        }
    }

    if(!GML_USE_CACHE || $cache_fwrite) {
        $list = array();

        $board = get_board_db($bo_table, true);
        $bo_subject = get_text($board['bo_subject']);
        $board['bo_use_sideview'] = 0;  // not use sideview
        $tmp_write_table = $gml['write_prefix'] . $bo_table; // 게시판 테이블 전체이름
        $sql = " select * from {$tmp_write_table} where wr_is_comment = 0 order by wr_num limit 0, {$rows} ";
        $result = sql_query($sql);
        for ($i=0; $row = sql_fetch_array($result); $i++) {
            try {
                unset($row['wr_password']);     //패스워드 저장 안함( 아예 삭제 )
            } catch (Exception $e) {
            }
            $row['wr_email'] = '';              //이메일 저장 안함
            if (strstr($row['wr_option'], 'secret')){           // 비밀글일 경우 내용, 링크, 파일 저장 안함
                $row['wr_content'] = $row['wr_link1'] = $row['wr_link2'] = '';
                $row['file'] = array('count'=>0);
            }
            $list[$i] = get_list($row, $board, $latest_skin_url, $subject_len);
        }

        if($cache_fwrite) {
            $handle = fopen($cache_file, 'w');
            $caches = array(
                'list' => $list,
                'bo_subject' => sql_escape_string($bo_subject),
                );
            $cache_content = "<?php if (!defined('_GNUBOARD_')) exit; ?>\n\n";
            $cache_content .= base64_encode(serialize($caches));  //serialize

            fwrite($handle, $cache_content);
            fclose($handle);

            @chmod($cache_file, 0640);
        }
    }
    */

    $bo_subject = isset($bo_subject) ? get_board_gettext_titles($bo_subject) : '';

    ob_start();
    // 더 보기 링크 주소
    $see_more_href = get_pretty_url($bo_table);
    // 최신 글 게시물이 없을 때
    $show_no_list = (count($list) == 0) ? '<li class="empty_li">'.__('No post found.').'</li>' : '';

    include $latest_skin_path.'/latest.skin.php';
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}
?>
