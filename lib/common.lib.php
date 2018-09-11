<?php
if (!defined('_GNUBOARD_')) exit;

/*************************************************************************
**
**  일반 함수 모음
**
*************************************************************************/

// required 혹은 공백 리턴
function element_required($el)
{
    return $el ? "required" : "";
}

// 변수의 값이 존재하는 지 확인하고 없으면 공백 반환
function isset_variable($var)
{
    return isset($var) ? $var : '';
}

// 마이크로 타임을 얻어 계산 형식으로 만듦
function get_microtime()
{
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}

/*
 *
 * 알림 발생
 * $mb_id : 알림을 발생시킨 사용자 id
 * $rel_mb_id : 알림을 받는 사용자 id
 * $bo_table : 게시판일 경우 게시판 테이블
 * $no_case : 알림 종류
 * $no_wr_id : 알림을 발생시킨 대상의 id(wr_id, qa_id, me_id, ...)
 * $no_rel_id : 알림을 받는 대상의 id(wr_id, qa_id, me_id, ...)
 *
 */
function add_notice($no_case, $mb_id, $rel_mb_id, $bo_table, $wr_id, $rel_wr_id=0)
{
    global $gml;

    if(!$rel_wr_id)
        $rel_wr_id = $wr_id;

    if($mb_id != $rel_mb_id) {
        $sql = " insert into {$gml['notice_table']}
                    set no_case = '{$no_case}',
                        mb_id = '{$mb_id}',
                        rel_mb_id = '{$rel_mb_id}',
                        bo_table = '{$bo_table}',
                        wr_id = {$wr_id},
                        rel_wr_id = {$rel_wr_id},
                        no_notice_datetime = '".GML_TIME_YMDHIS."'" ;
        sql_query($sql);
        // 알림 대상 사용자의 알림 개수를 업데이트
        $sql = " update {$gml['member_table']}
                    set mb_notice_cnt = mb_notice_cnt + 1
                    where mb_id = '{$rel_mb_id}' ";
        sql_query($sql);
    }
}

// create notice message
function get_notice_subject($row) {
    global $gml;

    $msg = '';
    // notifications related to bulletin boards
    if($row['bo_table']) {
        $write_table = "{$gml['write_prefix']}{$row['bo_table']}";
        switch ($row['no_case']) {
            case 'reply':
                $write = get_write($write_table, $row['rel_wr_id']);
                $member = get_member($row['mb_id']);
                $subject = cut_str($write['wr_subject'], 70);
                $name = $member['mb_nick'] ? : $write['wr_name'];
                $marking_name = '<b class="arm_nick">'.$name.'</b>';
                $msg = sprintf(__('%s replied to [%s].'), $marking_name, $subject);
                $msg = '<a href="'.GML_URL.'/'.$row['bo_table'].'/'.$row['wr_id'].'">'.$msg.'</a>';
                break;
            case 'comment':
                $write = get_write($write_table, $row['rel_wr_id']);
                if($write['wr_is_comment']) {
                    $member = get_member($row['mb_id']);
                    $name = $member['mb_nick'] ? : $write['wr_name'];
                    $marking_name = '<b class="arm_nick">'.$name.'</b>';
                    $msg = sprintf(__('%s commented on my comment.'), $marking_name);
                } else {
                    $sql = " select a.wr_id as wr_id,
                                    a.wr_parent as wr_parent,
                                    b.wr_subject as wr_subject,
                                    b.wr_name as wr_name,
                                    c.mb_nick as mb_nick
                                from {$write_table} as a
                                inner join {$write_table} as b
                                on a.wr_parent = b.wr_id
                                inner join {$gml['member_table']} as c
                                on a.mb_id = c.mb_id
                                where a.wr_id = {$row['wr_id']} ";
                    $write2 = sql_fetch($sql);
                    $subject = cut_str($write2['wr_subject'], 70) ? : $write['wr_subject'];
                    $write3 = get_write($write_table, $row['wr_id']);
                    $name = $write2['mb_nick'] ? : $write3['wr_name'];
                    $marking_name = '<b class="arm_nick">'.$name.'</b>';
                    $msg = sprintf(__('%s commented on [%s].'), $marking_name, $subject);
                }
                $msg = '<a href="'.GML_URL.'/'.$row['bo_table'].'/'.$write['wr_parent'].'#c_'.$row['wr_id'].'">'.$msg.'</a>';
                break;
            case 'good':
            case 'nogood':
                $write = get_write($write_table, $row['rel_wr_id']);
                $member = get_member($row['mb_id']);
                $subject = cut_str($write['wr_subject'], 70);
                $good = ($row['no_case']) == 'good' ? __('Good') : __('Bad');
                $marking_name = '<b class="arm_nick">'.$member['mb_nick'].'</b>';
                $msg = sprintf(__('%s pressed [%s] in [%s].'), $marking_name, $subject, $good);
                $msg = '<a href="'.GML_URL.'/'.$row['bo_table'].'/'.$write['wr_id'].'">'.$msg.'</a>';
                break;
            default:
                break;
        }
    } else {
        switch ($row['no_case']) {
            case 'ask':
            case 'r-ask':
                $sql = " select * from {$gml['qa_content_table']} where qa_id = {$row['wr_id']} ";
                $qa = sql_fetch($sql);
                $subject = cut_str($qa['qa_subject'], 70);
                $marking_name = '<b class="arm_nick">'.$qa['qa_name'].'</b>';
                $msg = sprintf(__('%s asked [%s] question.'), $marking_name, $subject);
                $msg = "<a href=\"". GML_BBS_URL. "/qaview.php?qa_id={$qa['qa_id']}"."\">$msg</a>";
                break;
            case 'answer':
                $sql = " select a.qa_id as qa_id, a.qa_parent as qa_parent, b.qa_subject as qa_subject, a.qa_name as qa_name
                            from {$gml['qa_content_table']} as a
                            inner join {$gml['qa_content_table']} as b
                            on a.qa_parent = b.qa_id
                            where a.qa_id = {$row['wr_id']} ";
                $qa = sql_fetch($sql);
                $subject = cut_str($qa['qa_subject'], 70);
                $marking_name = '<b class="arm_nick">'.$qa['qa_name'].'</b>';
                $msg = sprintf(__('%s has answered [%s] question.'), $marking_name, $subject);
                $msg = "<a href=\"". GML_BBS_URL. "/qaview.php?qa_id={$qa['qa_parent']}"."\">$msg</a>";
                break;
            case 'memo':
                $member = get_member($row['mb_id']);
                $marking_name = '<b class="arm_nick">'.$member['mb_nick'].'</b>';
                $msg = sprintf(__('%s has sent you a message.'), $marking_name);
                $msg = "<a href=\"". GML_BBS_URL. "/memo_view.php?me_id={$row['wr_id']}&amp;kind=recv"."\" onclick=\"win_memo(this.href); return false;\">$msg</a>";
                break;
            default:
                break;
        }
    }

    return $msg;
}

// display relative time
// source : mattytemple/relative-time.php
// https://gist.github.com/mattytemple/3804571
function time2str($ts) {
    if(!ctype_digit($ts)) {
        $ts = strtotime($ts);
    }
    $diff = time() - $ts;
    if($diff == 0) {
        return __('now');
    } elseif($diff > 0) {
        $day_diff = floor($diff / 86400);
        if($day_diff == 0) {
            if($diff < 60) return __('just now');
            if($diff < 120) return __('1 minute ago');
            if($diff < 3600) return floor($diff / 60) . __(' minutes ago');
            if($diff < 7200) return __('1 hour ago');
            if($diff < 86400) return floor($diff / 3600) . __(' hours ago');
        }
        if($day_diff == 1) { return __('yesterday'); }
        if($day_diff < 7) { return $day_diff . __(' days ago'); }
        if($day_diff < 31) { return ceil($day_diff / 7) . __(' weeks ago'); }
        if($day_diff < 60) { return __('last month'); }
        if($day_diff < 365) { return ceil($day_diff / 30). __(' months ago'); }
        return date('F Y', $ts);
    }
}

// create pagination
function get_paging($write_pages, $cur_page, $total_page, $url, $add="")
{
    //$url = preg_replace('#&amp;page=[0-9]*(&amp;page=)$#', '$1', $url);
    // $url = preg_replace('#&amp;page=[0-9]*#', '', $url) . '&amp;page=';

    $url = url_reasembly($url);

    $str = '';
    if ($cur_page > 1) {
        $str .= '<a href="'.$url.'1'.$add.'" class="pg_page pg_start"><i class="fa fa-angle-double-left" aria-hidden="true"></i><span class="sound_only">'.__('First').'</span></a>'.PHP_EOL;
    }

    $start_page = ( ( (int)( ($cur_page - 1 ) / $write_pages ) ) * $write_pages ) + 1;
    $end_page = $start_page + $write_pages - 1;

    if ($end_page >= $total_page) $end_page = $total_page;

    if ($start_page > 1) $str .= '<a href="'.$url.($start_page-1).$add.'" class="pg_page pg_prev"><i class="fa fa-angle-left" aria-hidden="true"></i><span class="sound_only">'.__('Prev').'</span></a>'.PHP_EOL;

    if ($total_page > 1) {
        for ($k=$start_page;$k<=$end_page;$k++) {
            if ($cur_page != $k)
                $str .= '<a href="'.$url.$k.$add.'" class="pg_page">'.$k.'<span class="sound_only">'.__('Page').'</span></a>'.PHP_EOL;
            else
                $str .= '<span class="sound_only">'.__('Current').'</span><strong class="pg_current">'.$k.'</strong><span class="sound_only">'.__('Page').'</span>'.PHP_EOL;
        }
    }

    if ($total_page > $end_page) $str .= '<a href="'.$url.($end_page+1).$add.'" class="pg_page pg_next"><i class="fa fa-angle-right" aria-hidden="true"></i><span class="sound_only">'.__('Next').'</span></a>'.PHP_EOL;

    if ($cur_page < $total_page) {
        $str .= '<a href="'.$url.$total_page.$add.'" class="pg_page pg_end"><i class="fa fa-angle-double-right" aria-hidden="true"></i><span class="sound_only">'.__('Last').'</span></a>'.PHP_EOL;
    }

    if ($str)
        return "<nav class=\"pg_wrap\"><span class=\"pg\">{$str}</span></nav>";
    else
        return "";
}

// URL reassembly(pretty url)
function url_reasembly($url)
{
	$url = preg_replace('#(\&){0,1}page=[0-9]*#', '', $url);
    //$url = explode('?', $url)[1];   // left query string
    if( $tmp = explode('?', $url) ){
        if ( isset($tmp[1]) )
            $url = $tmp[1];
    }
    $url_arr = explode('&amp;', $url);
    $page_str = 'page=';
    $qstr = '';
    foreach($url_arr as $param) {
        $param_arr = explode('=', $param);
        if(isset($param_arr[1]) && $param_arr[1]) {
            if(!$qstr)
                $qstr .= $param;
            else
                $qstr .= '&amp;'. $param;
        }
    }
    if($qstr) {
        $page_str = '&amp;'. $page_str;
    }

	return $_SERVER['SCRIPT_URI']. '?'. $qstr. $page_str;
}

// 페이징 코드의 <nav><span> 태그 다음에 코드를 삽입
function page_insertbefore($paging_html, $insert_html)
{
    if(!$paging_html)
        $paging_html = '<nav class="pg_wrap"><span class="pg"></span></nav>';

    return preg_replace("/^(<nav[^>]+><span[^>]+>)/", '$1'.$insert_html.PHP_EOL, $paging_html);
}

// 페이징 코드의 </span></nav> 태그 이전에 코드를 삽입
function page_insertafter($paging_html, $insert_html)
{
    if(!$paging_html)
        $paging_html = '<nav class="pg_wrap"><span class="pg"></span></nav>';

    if(preg_match("#".PHP_EOL."</span></nav>#", $paging_html))
        $php_eol = '';
    else
        $php_eol = PHP_EOL;

    return preg_replace("#(</span></nav>)$#", $php_eol.$insert_html.'$1', $paging_html);
}

// 변수 또는 배열의 이름과 값을 얻어냄. print_r() 함수의 변형
function print_r2($var)
{
    ob_start();
    print_r($var);
    $str = ob_get_contents();
    ob_end_clean();
    $str = str_replace(" ", "&nbsp;", $str);
    echo nl2br("<span style='font-family:Tahoma, Gulim; font-size:9pt;'>$str</span>");
}

// 디버깅용 함수
function dd()
{
    var_dump(func_get_args());
    die();
}

// 메타태그를 이용한 URL 이동
// header("location:URL") 을 대체
function goto_url($url)
{
    $url = str_replace("&amp;", "&", $url);
    //echo "<script> location.replace('$url'); </script>";

    if (!headers_sent())
        header('Location: '.$url);
    else {
        echo '<script>';
        echo 'location.replace("'.$url.'");';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
        echo '</noscript>';
    }
    exit;
}


// 세션변수 생성
function set_session($session_name, $value)
{
    if (PHP_VERSION < '5.3.0')
        session_register($session_name);
    // PHP 버전별 차이를 없애기 위한 방법
    $$session_name = $_SESSION[$session_name] = $value;
}


// 세션변수값 얻음
function get_session($session_name)
{
    return isset($_SESSION[$session_name]) ? $_SESSION[$session_name] : '';
}


// 쿠키변수 생성
function set_cookie($cookie_name, $value, $expire)
{
    global $gml;

    $cookie_name = apply_replace('gml_cookie_name', $cookie_name);

    setcookie(md5($cookie_name), base64_encode($value), GML_SERVER_TIME + $expire, '/', GML_COOKIE_DOMAIN);
}


// 쿠키변수값 얻음
function get_cookie($cookie_name)
{
    $cookie = md5(apply_replace('gml_cookie_name', $cookie_name));
    if (array_key_exists($cookie, $_COOKIE))
        return base64_decode($_COOKIE[$cookie]);
    else
        return "";
}


// 경고메세지를 경고창으로
function alert($msg='', $url='', $error=true, $post=false)
{
    global $gml, $config, $member;
    global $is_admin;

    start_event('alert', $msg, $url, $error, $post);

    $msg = $msg ? strip_tags($msg, '<br>') : __('Please use the correct method.');  //올바른 방법으로 이용해 주십시오.

    $header = '';
    if (isset($gml['title'])) {
        $header = $gml['title'];
    }
    include_once(GML_BBS_PATH.'/alert.php');
    exit;
}


// 경고메세지 출력후 창을 닫음
function alert_close($msg, $error=true)
{
    global $gml;

    start_event('alert_close', $msg, $error);

    $msg = strip_tags($msg, '<br>');

    $header = '';
    if (isset($gml['title'])) {
        $header = $gml['title'];
    }
    include_once(GML_BBS_PATH.'/alert_close.php');
    exit;
}

// confirm 창
function confirm($msg, $url1='', $url2='', $url3='')
{
    global $gml;

    if (!$msg) {
        $msg = __('Please use the correct method.');  //올바른 방법으로 이용해 주십시오.
        alert($msg);
    }

    if(!trim($url1) || !trim($url2)) {
        $msg = sprintf(__('Please specify %s and %s'), $url1, $url2);   // %s 과 %s 를 지정해 주세요.
        alert($msg);
    }

    if (!$url3) $url3 = clean_xss_tags($_SERVER['HTTP_REFERER']);

    $msg = str_replace("\\n", "<br>", $msg);

    $header = '';
    if (isset($gml['title'])) {
        $header = $gml['title'];
    }
    include_once(GML_BBS_PATH.'/confirm.php');
    exit;
}


// way.co.kr 의 wayboard 참고
function url_auto_link($str)
{
    global $gml;
    global $config;

    // 140326 유창화님 제안코드로 수정
    // http://sir.kr/pg_lecture/461
    // http://sir.kr/pg_lecture/463
    $attr_nofollow = (function_exists('check_html_link_nofollow') && check_html_link_nofollow('url_auto_link')) ? ' rel="nofollow"' : '';
    $str = str_replace(array("&lt;", "&gt;", "&amp;", "&quot;", "&nbsp;", "&#039;"), array("\t_lt_\t", "\t_gt_\t", "&", "\"", "\t_nbsp_\t", "'"), $str);
    //$str = preg_replace("`(?:(?:(?:href|src)\s*=\s*(?:\"|'|)){0})((http|https|ftp|telnet|news|mms)://[^\"'\s()]+)`", "<A HREF=\"\\1\" TARGET='{$config['cf_link_target']}'>\\1</A>", $str);
    $str = preg_replace("/([^(href=\"?'?)|(src=\"?'?)]|\(|^)((http|https|ftp|telnet|news|mms):\/\/[a-zA-Z0-9\.-]+\.[가-힣\xA1-\xFEa-zA-Z0-9\.:&#!=_\?\/~\+%@;\-\|\,\(\)]+)/i", "\\1<A HREF=\"\\2\" TARGET=\"{$config['cf_link_target']}\" $attr_nofollow>\\2</A>", $str);
    $str = preg_replace("/(^|[\"'\s(])(www\.[^\"'\s()]+)/i", "\\1<A HREF=\"http://\\2\" TARGET=\"{$config['cf_link_target']}\" $attr_nofollow>\\2</A>", $str);
    $str = preg_replace("/[0-9a-z_-]+@[a-z0-9._-]{4,}/i", "<a href=\"mailto:\\0\" $attr_nofollow>\\0</a>", $str);
    $str = str_replace(array("\t_nbsp_\t", "\t_lt_\t", "\t_gt_\t", "'"), array("&nbsp;", "&lt;", "&gt;", "&#039;"), $str);

    /*
    // 속도 향상 031011
    $str = preg_replace("/&lt;/", "\t_lt_\t", $str);
    $str = preg_replace("/&gt;/", "\t_gt_\t", $str);
    $str = preg_replace("/&amp;/", "&", $str);
    $str = preg_replace("/&quot;/", "\"", $str);
    $str = preg_replace("/&nbsp;/", "\t_nbsp_\t", $str);
    $str = preg_replace("/([^(http:\/\/)]|\(|^)(www\.[^[:space:]]+)/i", "\\1<A HREF=\"http://\\2\" TARGET='{$config['cf_link_target']}'>\\2</A>", $str);
    //$str = preg_replace("/([^(HREF=\"?'?)|(SRC=\"?'?)]|\(|^)((http|https|ftp|telnet|news|mms):\/\/[a-zA-Z0-9\.-]+\.[\xA1-\xFEa-zA-Z0-9\.:&#=_\?\/~\+%@;\-\|\,]+)/i", "\\1<A HREF=\"\\2\" TARGET='$config['cf_link_target']'>\\2</A>", $str);
    // 100825 : () 추가
    // 120315 : CHARSET 에 따라 링크시 글자 잘림 현상이 있어 수정
    $str = preg_replace("/([^(HREF=\"?'?)|(SRC=\"?'?)]|\(|^)((http|https|ftp|telnet|news|mms):\/\/[a-zA-Z0-9\.-]+\.[가-힣\xA1-\xFEa-zA-Z0-9\.:&#=_\?\/~\+%@;\-\|\,\(\)]+)/i", "\\1<A HREF=\"\\2\" TARGET='{$config['cf_link_target']}'>\\2</A>", $str);

    // 이메일 정규표현식 수정 061004
    //$str = preg_replace("/(([a-z0-9_]|\-|\.)+@([^[:space:]]*)([[:alnum:]-]))/i", "<a href='mailto:\\1'>\\1</a>", $str);
    $str = preg_replace("/([0-9a-z]([-_\.]?[0-9a-z])*@[0-9a-z]([-_\.]?[0-9a-z])*\.[a-z]{2,4})/i", "<a href='mailto:\\1'>\\1</a>", $str);
    $str = preg_replace("/\t_nbsp_\t/", "&nbsp;" , $str);
    $str = preg_replace("/\t_lt_\t/", "&lt;", $str);
    $str = preg_replace("/\t_gt_\t/", "&gt;", $str);
    */

    return $str;
}


// url에 http:// 를 붙인다
function set_http($url)
{
    if (!trim($url)) return;

    if (!preg_match("/^(http|https|ftp|telnet|news|mms)\:\/\//i", $url))
        $url = "http://" . $url;

    return $url;
}


// 파일의 용량을 구한다.
//function get_filesize($file)
function get_filesize($size)
{
    //$size = @filesize(addslashes($file));
    if ($size >= 1048576) {
        $size = number_format($size/1048576, 1) . "M";
    } else if ($size >= 1024) {
        $size = number_format($size/1024, 1) . "K";
    } else {
        $size = number_format($size, 0) . "byte";
    }
    return $size;
}


// 게시글에 첨부된 파일을 얻는다. (배열로 반환)
function get_file($bo_table, $wr_id)
{
    global $gml, $qstr;

    $file['count'] = 0;
    $sql = " select * from {$gml['board_file_table']} where bo_table = '$bo_table' and wr_id = '$wr_id' order by bf_no ";
    $result = sql_query($sql);
    while ($row = sql_fetch_array($result))
    {
        $no = $row['bf_no'];
        $file[$no]['href'] = GML_BBS_URL."/download.php?bo_table=$bo_table&amp;wr_id=$wr_id&amp;no=$no" . $qstr;
        $file[$no]['download'] = $row['bf_download'];
        // 4.00.11 - 파일 path 추가
        $file[$no]['path'] = GML_DATA_URL.'/file/'.$bo_table;
        $file[$no]['size'] = get_filesize($row['bf_filesize']);
        $file[$no]['datetime'] = $row['bf_datetime'];
        $file[$no]['source'] = addslashes($row['bf_source']);
        $file[$no]['bf_content'] = $row['bf_content'];
        $file[$no]['content'] = get_text($row['bf_content']);
        //$file[$no]['view'] = view_file_link($row['bf_file'], $file[$no]['content']);
        $file[$no]['view'] = view_file_link($row['bf_file'], $row['bf_width'], $row['bf_height'], $file[$no]['content']);
        $file[$no]['file'] = $row['bf_file'];
        $file[$no]['image_width'] = $row['bf_width'] ? $row['bf_width'] : 640;
        $file[$no]['image_height'] = $row['bf_height'] ? $row['bf_height'] : 480;
        $file[$no]['image_type'] = $row['bf_type'];
        $file['count']++;
    }

    return $file;
}


// 폴더의 용량 ($dir는 / 없이 넘기세요)
function get_dirsize($dir)
{
    $size = 0;
    $d = dir($dir);
    while ($entry = $d->read()) {
        if ($entry != '.' && $entry != '..') {
            $size += filesize($dir.'/'.$entry);
        }
    }
    $d->close();
    return $size;
}


/*************************************************************************
**
**  그누보드 관련 함수 모음
**
*************************************************************************/

// 게시물 정보($write_row)를 출력하기 위하여 $list로 가공된 정보를 복사 및 가공
function get_list($write_row, $board, $skin_url, $subject_len=40)
{
    global $gml, $config;
    global $qstr, $page;

    //$t = get_microtime();

    // 배열전체를 복사
    $list = $write_row;
    unset($write_row);

    $board_notice = array_map('trim', explode(',', $board['bo_notice']));
    $list['is_notice'] = in_array($list['wr_id'], $board_notice);

    if ($subject_len)
        $list['subject'] = conv_subject($list['wr_subject'], $subject_len, '…');
    else
        $list['subject'] = conv_subject($list['wr_subject'], get_board_gettext_titles($board['bo_subject_len']), '…');

    // 목록에서 내용 미리보기 사용한 게시판만 내용을 변환함 (속도 향상) : kkal3(커피)님께서 알려주셨습니다.
    if ($board['bo_use_list_content'])
	{
		$html = 0;
		if (strstr($list['wr_option'], 'html1'))
			$html = 1;
		else if (strstr($list['wr_option'], 'html2'))
			$html = 2;

        $list['content'] = conv_content($list['wr_content'], $html);
	}

    $list['comment_cnt'] = '';
    if ($list['wr_comment'])
        $list['comment_cnt'] = "<span class=\"cnt_cmt\">".$list['wr_comment']."</span>";

    // 당일인 경우 시간으로 표시함
    $list['datetime'] = substr($list['wr_datetime'],0,10);
    $list['datetime2'] = $list['wr_datetime'];
    if ($list['datetime'] == GML_TIME_YMD)
        $list['datetime2'] = substr($list['datetime2'],11,5);
    else
        $list['datetime2'] = substr($list['datetime2'],5,5);
    // 4.1
    $list['last'] = substr($list['wr_last'],0,10);
    $list['last2'] = $list['wr_last'];
    if ($list['last'] == GML_TIME_YMD)
        $list['last2'] = substr($list['last2'],11,5);
    else
        $list['last2'] = substr($list['last2'],5,5);

    $list['wr_homepage'] = get_text($list['wr_homepage']);

    $tmp_name = get_text(cut_str($list['wr_name'], $config['cf_cut_name'])); // 설정된 자리수 만큼만 이름 출력
    $tmp_name2 = cut_str($list['wr_name'], $config['cf_cut_name']); // 설정된 자리수 만큼만 이름 출력
    if ($board['bo_use_sideview'])
        $list['name'] = get_sideview($list['mb_id'], $tmp_name2, $list['wr_email'], $list['wr_homepage']);
    else
        $list['name'] = '<span class="'.($list['mb_id']?'sv_member':'sv_guest').'">'.$tmp_name.'</span>';

    $reply = $list['wr_reply'];

    $list['reply'] = strlen($reply)*20;

    $list['icon_reply'] = '';
    if ($list['reply'])
        $list['icon_reply'] = '<img src="'.$skin_url.'/img/icon_reply.gif" class="icon_reply" alt="Answer">';

    $list['icon_link'] = '';
    if ($list['wr_link1'] || $list['wr_link2'])
        $list['icon_link'] = '<i class="fa fa-link" aria-hidden="true"></i> ';

    // 분류명 링크
    $list['ca_name_href'] = get_pretty_url($board['bo_table'], '', 'sca='.urlencode($list['ca_name']));
    $list['href'] = get_pretty_url($board['bo_table'], $list['wr_id'], $qstr);

    $list['comment_href'] = $list['href'];

    $list['icon_new'] = '';
    if ($board['bo_new'] && $list['wr_datetime'] >= date("Y-m-d H:i:s", GML_SERVER_TIME - ($board['bo_new'] * 3600)))
        $list['icon_new'] = '<img src="'.$skin_url.'/img/icon_new.gif" class="title_icon" alt="New"> ';

    $list['icon_hot'] = '';
    if ($board['bo_hot'] && $list['wr_hit'] >= $board['bo_hot'])
        $list['icon_hot'] = '<i class="fa fa-heart" aria-hidden="true"></i> ';

    $list['icon_secret'] = '';
    if (strstr($list['wr_option'], 'secret'))
        $list['icon_secret'] = '<i class="fa fa-lock" aria-hidden="true"></i> ';

    // 링크
    for ($i=1; $i<=GML_LINK_COUNT; $i++) {
        if($list['wr_link'.$i]) {
            $list['link'][$i] = set_http(get_text($list["wr_link{$i}"]));
            $list['link_href'][$i] = GML_BBS_URL.'/link.php?bo_table='.$board['bo_table'].'&amp;wr_id='.$list['wr_id'].'&amp;no='.$i.$qstr;
            $list['link_hit'][$i] = (int)$list["wr_link{$i}_hit"];
        }
    }

    // 가변 파일
    if ($board['bo_use_list_file'] || ($list['wr_file'] && $subject_len == 255) /* view 인 경우 */) {
        $list['file'] = get_file($board['bo_table'], $list['wr_id']);
    } else {
        $list['file']['count'] = $list['wr_file'];
    }

    if ($list['file']['count'])
        $list['icon_file'] = '<i class="fa fa-download" aria-hidden="true"></i> ';

    // 최신글 제목
    if ($list['is_notice']) // 공지
        $list['show_subject'] = "<strong>".$list['subject']."</strong>";
    else
        $list['show_subject'] = $list['subject'];

    return $list;
}

// get_list 의 alias
function get_view($write_row, $board, $skin_url)
{
    return get_list($write_row, $board, $skin_url, 255);
}


// set_search_font(), get_search_font() 함수를 search_font() 함수로 대체
function search_font($stx, $str)
{
    global $config;

    // 문자앞에 \ 를 붙입니다.
    $src = array('/', '|');
    $dst = array('\/', '\|');

    if (!trim($stx) && $stx !== '0') return $str;

    // 검색어 전체를 공란으로 나눈다
    $s = explode(' ', $stx);

    // "/(검색1|검색2)/i" 와 같은 패턴을 만듬
    $pattern = '';
    $bar = '';
    for ($m=0; $m<count($s); $m++) {
        if (trim($s[$m]) == '') continue;
        // 태그는 포함하지 않아야 하는데 잘 안되는군. ㅡㅡa
        //$pattern .= $bar . '([^<])(' . quotemeta($s[$m]) . ')';
        //$pattern .= $bar . quotemeta($s[$m]);
        //$pattern .= $bar . str_replace("/", "\/", quotemeta($s[$m]));
        $tmp_str = quotemeta($s[$m]);
        $tmp_str = str_replace($src, $dst, $tmp_str);
        $pattern .= $bar . $tmp_str . "(?![^<]*>)";
        $bar = "|";
    }

    // 지정된 검색 폰트의 색상, 배경색상으로 대체
    $replace = "<b class=\"sch_word\">\\1</b>";

    return preg_replace("/($pattern)/i", $replace, $str);
}


// 제목을 변환
function conv_subject($subject, $len, $suffix='')
{
    return get_text(cut_str($subject, $len, $suffix));
}

// 내용을 변환
function conv_content($content, $html, $filter=true)
{
    global $config, $board;

    if ($html)
    {
        $source = array();
        $target = array();

        $source[] = "//";
        $target[] = "";

        if ($html == 2) { // 자동 줄바꿈
            $source[] = "/\n/";
            $target[] = "<br/>";
        }

        // 테이블 태그의 개수를 세어 테이블이 깨지지 않도록 한다.
        $table_begin_count = substr_count(strtolower($content), "<table");
        $table_end_count = substr_count(strtolower($content), "</table");
        for ($i=$table_end_count; $i<$table_begin_count; $i++)
        {
            $content .= "</table>";
        }

        $content = preg_replace($source, $target, $content);
        $editor_auto_url = ($config['cf_editor'] === 'ckeditor4') ? true : false;

        if( apply_replace('editor_conv_content', $editor_auto_url, $filter, $config['cf_editor']) ){
            $content = url_auto_link($content);
        }

        if($filter)
            $content = html_purifier($content);
    }
    else // text 이면
    {
        // & 처리 : &amp; &nbsp; 등의 코드를 정상 출력함
        $content = html_symbol($content);

        // 공백 처리
		//$content = preg_replace("/  /", "&nbsp; ", $content);
		$content = str_replace("  ", "&nbsp; ", $content);
		$content = str_replace("\n ", "\n&nbsp;", $content);

        $content = get_text($content, 1);
        $content = url_auto_link($content);
    }

    return $content;
}

function check_html_link_nofollow($type=''){
    return true;
}

// http://htmlpurifier.org/
// Standards-Compliant HTML Filtering
// Safe  : HTML Purifier defeats XSS with an audited whitelist
// Clean : HTML Purifier ensures standards-compliant output
// Open  : HTML Purifier is open-source and highly customizable
function html_purifier($html)
{
    $f = file(GML_PLUGIN_PATH.'/htmlpurifier/safeiframe.txt');
    $domains = array();
    foreach($f as $domain){
        // 첫행이 # 이면 주석 처리
        if (!preg_match("/^#/", $domain)) {
            $domain = trim($domain);
            if ($domain)
                array_push($domains, $domain);
        }
    }
    // 내 도메인도 추가
    array_push($domains, $_SERVER['HTTP_HOST'].'/');
    $safeiframe = implode('|', $domains);

    include_once(GML_PLUGIN_PATH.'/htmlpurifier/HTMLPurifier.standalone.php');
    include_once(GML_PLUGIN_PATH.'/htmlpurifier/extend.video.php');
    $config = HTMLPurifier_Config::createDefault();
    // data/cache 디렉토리에 CSS, HTML, URI 디렉토리 등을 만든다.
    $config->set('Cache.SerializerPath', GML_DATA_PATH.'/cache');
    $config->set('HTML.SafeEmbed', false);
    $config->set('HTML.SafeObject', false);
    $config->set('Output.FlashCompat', false);
    $config->set('HTML.SafeIframe', true);
    if( (function_exists('check_html_link_nofollow') && check_html_link_nofollow('html_purifier')) ){
        $config->set('HTML.Nofollow', true);    // rel=nofollow 으로 스팸유입을 줄임
    }
    $config->set('URI.SafeIframeRegexp','%^(https?:)?//('.$safeiframe.')%');
    $config->set('Attr.AllowedFrameTargets', array('_blank'));
    //유튜브, 비메오 전체화면 가능하게 하기
    $config->set('Filter.Custom', array(new HTMLPurifier_Filter_Iframevideo()));
    $purifier = new HTMLPurifier($config);
    return $purifier->purify($html);
}


// 검색 구문을 얻는다.
function get_sql_search($search_ca_name, $search_field, $search_text, $search_operator='and', $mb_hashkey='')
{
    global $gml, $is_admin;

    $str = "";
    if ($search_ca_name)
        $str = " ca_name = '$search_ca_name' ";

    $search_text = strip_tags(($search_text));
    $search_text = trim(stripslashes($search_text));

    if (!$search_text && $search_text !== '0' && !$mb_hashkey) {
        if ($search_ca_name) {
            return $str;
        } else {
            return '0';
        }
    }

    if ($str)
        $str .= " and ";

    // 쿼리의 속도를 높이기 위하여 ( ) 는 최소화 한다.
    $op1 = "";

    // 검색어를 구분자로 나눈다. 여기서는 공백
    $s = array();
    $s = explode(" ", $search_text);

    // 검색필드를 구분자로 나눈다. 여기서는 +
    $tmp = array();
    $tmp = explode(",", trim($search_field));
    $field = explode("||", $tmp[0]);
    $not_comment = "";
    if (!empty($tmp[1]))
        $not_comment = $tmp[1];

    $str .= "(";
    for ($i=0; $i<count($s); $i++) {
        // 검색어
        $search_str = trim($s[$i]);
        if ($search_str == "") continue;

        // 인기검색어
        insert_popular($field, $search_str);

        $str .= $op1;
        $str .= "(";

        $op2 = "";
        for ($k=0; $k<count($field); $k++) { // 필드의 수만큼 다중 필드 검색 가능 (필드1+필드2...)

            // SQL Injection 방지
            // 필드값에 a-z A-Z 0-9 _ , | 이외의 값이 있다면 검색필드를 wr_subject 로 설정한다.
            $field[$k] = preg_match("/^[\w\,\|]+$/", $field[$k]) ? strtolower($field[$k]) : "wr_subject";

            $str .= $op2;
            switch ($field[$k]) {
                case "mb_id" :
                    if( $is_admin ){
                        $str .= " $field[$k] = '$s[$i]' ";
                        $mb_hashkey = '';
                    }
                    break;
                case "wr_name" :
                    $str .= " $field[$k] = '$s[$i]' ";
                    break;
                case "wr_hit" :
                case "wr_good" :
                case "wr_nogood" :
                    $str .= " $field[$k] >= '$s[$i]' ";
                    break;
                // 번호는 해당 검색어에 -1 을 곱함
                case "wr_num" :
                    $str .= "$field[$k] = ".((-1)*$s[$i]);
                    break;
                case "wr_ip" :
                case "wr_password" :
                    $str .= "1=0"; // 항상 거짓
                    break;
                // LIKE 보다 INSTR 속도가 빠름
                default :
                    if (preg_match("/[a-zA-Z]/", $search_str))
                        $str .= "INSTR(LOWER($field[$k]), LOWER('$search_str'))";
                    else
                        $str .= "INSTR($field[$k], '$search_str')";
                    break;
            }
            $op2 = " or ";
        }
        $str .= ")";

        $op1 = " $search_operator ";
    }

    if ($mb_hashkey){
        $str .= (preg_match('/[a-z]/i', $str) ? " $search_operator" : '' )." mb_id = '".get_string_check_decrypt($mb_hashkey, 'mb_id')."' ";
        $not_comment = 1;
    }

    $str .= " ) ";

    if ($not_comment)
        $str .= " and wr_is_comment = '0' ";

    return $str;
}


// 게시판 테이블에서 하나의 행을 읽음
function get_write($write_table, $wr_id, $is_cache=false)
{
    return get_write_db($write_table, $wr_id, '*', $is_cache);
}


// 게시판의 다음글 번호를 얻는다.
function get_next_num($table)
{
    // 가장 작은 번호를 얻어
    $sql = " select min(wr_num) as min_wr_num from $table ";
    $row = sql_fetch($sql);
    // 가장 작은 번호에 1을 빼서 넘겨줌
    return (int)($row['min_wr_num'] - 1);
}


// 그룹 설정 테이블에서 하나의 행을 읽음
function get_group($gr_id, $is_cache=false)
{
    global $gml;

    static $cache = array();

    $gr_id = preg_replace('/[^a-z0-9_]/i', '', $gr_id);
    $key = md5($gr_id);

    if( $is_cache && isset($cache[$key]) ){
        return $cache[$key];
    }

    $sql = " select * from {$gml['group_table']} where gr_id = '$gr_id' ";

    $cache[$key] = apply_replace('get_group', sql_fetch($sql), $gr_id, $is_cache);

    return $cache[$key];
}


// 회원 정보를 얻는다.
function get_member($mb_id, $fields='*', $is_cache=false)
{
    global $gml;

    static $cache = array();

    $key = md5($fields);

    if( $is_cache && isset($cache[$mb_id]) && isset($cache[$mb_id][$key]) ){
        return $cache[$mb_id][$key];
    }

    $sql = " select $fields from {$gml['member_table']} where mb_id = TRIM('$mb_id') ";

    $cache[$mb_id][$key] = apply_replace('get_member', sql_fetch($sql), $mb_id, $fields, $is_cache);

    return $cache[$mb_id][$key];
}


// 날짜, 조회수의 경우 높은 순서대로 보여져야 하므로 $flag 를 추가
// $flag : asc 낮은 순서 , desc 높은 순서
// 제목별로 컬럼 정렬하는 QUERY STRING
function subject_sort_link($col, $query_string='', $flag='asc')
{
    global $sst, $sod, $sfl, $stx, $page, $sca, $config;

    $q1 = "sst=$col";
    if ($flag == 'asc')
    {
        $q2 = 'sod=asc';
        if ($sst == $col)
        {
            if ($sod == 'asc')
            {
                $q2 = 'sod=desc';
            }
        }
    }
    else
    {
        $q2 = 'sod=desc';
        if ($sst == $col)
        {
            if ($sod == 'desc')
            {
                $q2 = 'sod=asc';
            }
        }
    }

    $arr_query = array();
    $arr_query[] = $query_string;
    $arr_query[] = $q1;
    $arr_query[] = $q2;
    $arr_query[] = 'sfl='.$sfl;
    $arr_query[] = 'stx='.$stx;
    $arr_query[] = 'sca='.$sca;
    $arr_query[] = 'page='.$page;
    $qstr = implode("&amp;", $arr_query);

    if($config['cf_bbs_rewrite']) {
        $scripturi = str_replace("?". $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);
    } else {
        $scripturi = $_SERVER['SCRIPT_NAME'];
    }

    return "<a href=\"{$scripturi}?{$qstr}\">";
}


// 관리자 정보를 얻음
function get_admin($admin='super', $fields='*')
{
    global $config, $group, $board;
    global $gml;

    $is = false;
    if ($admin == 'board') {
        $mb = sql_fetch("select {$fields} from {$gml['member_table']} where mb_id in ('{$board['bo_admin']}') limit 1 ");
        $is = true;
    }

    if (($is && !$mb['mb_id']) || $admin == 'group') {
        $mb = sql_fetch("select {$fields} from {$gml['member_table']} where mb_id in ('{$group['gr_admin']}') limit 1 ");
        $is = true;
    }

    if (($is && !$mb['mb_id']) || $admin == 'super') {
        $mb = sql_fetch("select {$fields} from {$gml['member_table']} where mb_id in ('{$config['cf_admin']}') limit 1 ");
    }

    return $mb;
}


// 관리자인가?
function is_admin($mb_id)
{
    global $config, $group, $board;

    if (!$mb_id) return;

    if ($config['cf_admin'] == $mb_id) return 'super';
    if (isset($group['gr_admin']) && ($group['gr_admin'] == $mb_id)) return 'group';
    if (isset($board['bo_admin']) && ($board['bo_admin'] == $mb_id)) return 'board';
    return '';
}


// 분류 옵션을 얻음
// 4.00 에서는 카테고리 테이블을 없애고 보드테이블에 있는 내용으로 대체
function get_category_option($bo_table='', $ca_name='')
{
    global $gml, $board, $is_admin;

    $categories = explode('|', $board['bo_category_list'].($is_admin?'|'.__('Notice'):'')); // 구분자가 | 로 되어 있음
    $str = '';
    for ($i=0; $i<count($categories); $i++) {
        $category = trim($categories[$i]);
        if (!$category) continue;

        $str .= "<option value=\"$categories[$i]\"";
        if ($category == $ca_name) {
            $str .= ' selected="selected"';
        }
        $str .= ">$categories[$i]</option>\n";
    }

    return $str;
}


// 게시판 그룹을 SELECT 형식으로 얻음
function get_group_select($name, $selected='', $event='')
{
    global $gml, $is_admin, $member;

    $sql = " select gr_id, gr_subject from {$gml['group_table']} a ";
    if ($is_admin == "group") {
        $sql .= " left join {$gml['member_table']} b on (b.mb_id = a.gr_admin)
                  where b.mb_id = '{$member['mb_id']}' ";
    }
    $sql .= " order by a.gr_id ";

    $result = sql_query($sql);
    $str = "<select id=\"$name\" name=\"$name\" $event>\n";
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        if ($i == 0) $str .= "<option value=\"\">".__('Select')."</option>";
        $str .= option_selected($row['gr_id'], $selected, $row['gr_subject']);
    }
    $str .= "</select>";
    return $str;
}


function option_selected($value, $selected, $text='')
{
    if (!$text) $text = $value;
    if ($value == $selected)
        return "<option value=\"$value\" selected=\"selected\">$text</option>\n";
    else
        return "<option value=\"$value\">$text</option>\n";
}


// '예', '아니오'를 SELECT 형식으로 얻음
function get_yn_select($name, $selected='1', $event='')
{
    $str = "<select name=\"$name\" $event>\n";
    if ($selected) {
        $str .= "<option value=\"1\" selected>".__('Yes')."</option>\n";
        $str .= "<option value=\"0\">".__('No')."</option>\n";
    } else {
        $str .= "<option value=\"1\">".__('Yes')."</option>\n";
        $str .= "<option value=\"0\" selected>".__('No')."</option>\n";
    }
    $str .= "</select>";
    return $str;
}


// 포인트 부여
function insert_point($mb_id, $point, $content='', $rel_table='', $rel_id='', $rel_action='', $expire=0)
{
    global $config;
    global $gml;
    global $is_admin;

    // 포인트 사용을 하지 않는다면 return
    if (!$config['cf_use_point']) { return 0; }

    // 포인트가 없다면 업데이트 할 필요 없음
    if ($point == 0) { return 0; }

    // 회원아이디가 없다면 업데이트 할 필요 없음
    if ($mb_id == '') { return 0; }
    $mb = get_member($mb_id);
    if (!$mb['mb_id']) { return 0; }

    // 회원포인트
    $mb_point = get_point_sum($mb_id);

    // 이미 등록된 내역이라면 건너뜀
    if ($rel_table || $rel_id || $rel_action)
    {
        $sql = " select count(*) as cnt from {$gml['point_table']}
                  where mb_id = '$mb_id'
                    and po_rel_table = '$rel_table'
                    and po_rel_id = '$rel_id'
                    and po_rel_action = '$rel_action' ";
        $row = sql_fetch($sql);
        if ($row['cnt'])
            return -1;
    }

    // 포인트 건별 생성
    $po_expire_date = '9999-12-31';
    if($config['cf_point_term'] > 0) {
        if($expire > 0)
            $po_expire_date = date('Y-m-d', strtotime('+'.($expire - 1).' days', GML_SERVER_TIME));
        else
            $po_expire_date = date('Y-m-d', strtotime('+'.($config['cf_point_term'] - 1).' days', GML_SERVER_TIME));
    }

    $po_expired = 0;
    if($point < 0) {
        $po_expired = 1;
        $po_expire_date = GML_TIME_YMD;
    }
    $po_mb_point = $mb_point + $point;

    $sql = " insert into {$gml['point_table']}
                set mb_id = '$mb_id',
                    po_datetime = '".GML_TIME_YMDHIS."',
                    po_content = '".addslashes($content)."',
                    po_point = '$point',
                    po_use_point = '0',
                    po_mb_point = '$po_mb_point',
                    po_expired = '$po_expired',
                    po_expire_date = '$po_expire_date',
                    po_rel_table = '$rel_table',
                    po_rel_id = '$rel_id',
                    po_rel_action = '$rel_action' ";
    sql_query($sql);

    // 포인트를 사용한 경우 포인트 내역에 사용금액 기록
    if($point < 0) {
        insert_use_point($mb_id, $point);
    }

    // 포인트 UPDATE
    $sql = " update {$gml['member_table']} set mb_point = '$po_mb_point' where mb_id = '$mb_id' ";
    sql_query($sql);

    return 1;
}

// 사용포인트 입력
function insert_use_point($mb_id, $point, $po_id='')
{
    global $gml, $config;

    if($config['cf_point_term'])
        $sql_order = " order by po_expire_date asc, po_id asc ";
    else
        $sql_order = " order by po_id asc ";

    $point1 = abs($point);
    $sql = " select po_id, po_point, po_use_point
                from {$gml['point_table']}
                where mb_id = '$mb_id'
                  and po_id <> '$po_id'
                  and po_expired = '0'
                  and po_point > po_use_point
                $sql_order ";
    $result = sql_query($sql);
    for($i=0; $row=sql_fetch_array($result); $i++) {
        $point2 = $row['po_point'];
        $point3 = $row['po_use_point'];

        if(($point2 - $point3) > $point1) {
            $sql = " update {$gml['point_table']}
                        set po_use_point = po_use_point + '$point1'
                        where po_id = '{$row['po_id']}' ";
            sql_query($sql);
            break;
        } else {
            $point4 = $point2 - $point3;
            $sql = " update {$gml['point_table']}
                        set po_use_point = po_use_point + '$point4',
                            po_expired = '100'
                        where po_id = '{$row['po_id']}' ";
            sql_query($sql);
            $point1 -= $point4;
        }
    }
}

// 사용포인트 삭제
function delete_use_point($mb_id, $point)
{
    global $gml, $config;

    if($config['cf_point_term'])
        $sql_order = " order by po_expire_date desc, po_id desc ";
    else
        $sql_order = " order by po_id desc ";

    $point1 = abs($point);
    $sql = " select po_id, po_use_point, po_expired, po_expire_date
                from {$gml['point_table']}
                where mb_id = '$mb_id'
                  and po_expired <> '1'
                  and po_use_point > 0
                $sql_order ";
    $result = sql_query($sql);
    for($i=0; $row=sql_fetch_array($result); $i++) {
        $point2 = $row['po_use_point'];

        $po_expired = $row['po_expired'];
        if($row['po_expired'] == 100 && ($row['po_expire_date'] == '9999-12-31' || $row['po_expire_date'] >= GML_TIME_YMD))
            $po_expired = 0;

        if($point2 > $point1) {
            $sql = " update {$gml['point_table']}
                        set po_use_point = po_use_point - '$point1',
                            po_expired = '$po_expired'
                        where po_id = '{$row['po_id']}' ";
            sql_query($sql);
            break;
        } else {
            $sql = " update {$gml['point_table']}
                        set po_use_point = '0',
                            po_expired = '$po_expired'
                        where po_id = '{$row['po_id']}' ";
            sql_query($sql);

            $point1 -= $point2;
        }
    }
}

// 소멸포인트 삭제
function delete_expire_point($mb_id, $point)
{
    global $gml, $config;

    $point1 = abs($point);
    $sql = " select po_id, po_use_point, po_expired, po_expire_date
                from {$gml['point_table']}
                where mb_id = '$mb_id'
                  and po_expired = '1'
                  and po_point >= 0
                  and po_use_point > 0
                order by po_expire_date desc, po_id desc ";
    $result = sql_query($sql);
    for($i=0; $row=sql_fetch_array($result); $i++) {
        $point2 = $row['po_use_point'];
        $po_expired = '0';
        $po_expire_date = '9999-12-31';
        if($config['cf_point_term'] > 0)
            $po_expire_date = date('Y-m-d', strtotime('+'.($config['cf_point_term'] - 1).' days', GML_SERVER_TIME));

        if($point2 > $point1) {
            $sql = " update {$gml['point_table']}
                        set po_use_point = po_use_point - '$point1',
                            po_expired = '$po_expired',
                            po_expire_date = '$po_expire_date'
                        where po_id = '{$row['po_id']}' ";
            sql_query($sql);
            break;
        } else {
            $sql = " update {$gml['point_table']}
                        set po_use_point = '0',
                            po_expired = '$po_expired',
                            po_expire_date = '$po_expire_date'
                        where po_id = '{$row['po_id']}' ";
            sql_query($sql);

            $point1 -= $point2;
        }
    }
}

// 포인트 내역 합계
function get_point_sum($mb_id)
{
    global $gml, $config;

    if($config['cf_point_term'] > 0) {
        // 소멸포인트가 있으면 내역 추가
        $expire_point = get_expire_point($mb_id);
        if($expire_point > 0) {
            $mb = get_member($mb_id, 'mb_point');
            $content = '포인트 소멸';
            $rel_table = '@expire';
            $rel_id = $mb_id;
            $rel_action = 'expire'.'-'.uniqid('');
            $point = $expire_point * (-1);
            $po_mb_point = $mb['mb_point'] + $point;
            $po_expire_date = GML_TIME_YMD;
            $po_expired = 1;

            $sql = " insert into {$gml['point_table']}
                        set mb_id = '$mb_id',
                            po_datetime = '".GML_TIME_YMDHIS."',
                            po_content = '".addslashes($content)."',
                            po_point = '$point',
                            po_use_point = '0',
                            po_mb_point = '$po_mb_point',
                            po_expired = '$po_expired',
                            po_expire_date = '$po_expire_date',
                            po_rel_table = '$rel_table',
                            po_rel_id = '$rel_id',
                            po_rel_action = '$rel_action' ";
            sql_query($sql);

            // 포인트를 사용한 경우 포인트 내역에 사용금액 기록
            if($point < 0) {
                insert_use_point($mb_id, $point);
            }
        }

        // 유효기간이 있을 때 기간이 지난 포인트 expired 체크
        $sql = " update {$gml['point_table']}
                    set po_expired = '1'
                    where mb_id = '$mb_id'
                      and po_expired <> '1'
                      and po_expire_date <> '9999-12-31'
                      and po_expire_date < '".GML_TIME_YMD."' ";
        sql_query($sql);
    }

    // 포인트합
    $sql = " select sum(po_point) as sum_po_point
                from {$gml['point_table']}
                where mb_id = '$mb_id' ";
    $row = sql_fetch($sql);

    return $row['sum_po_point'];
}

// 소멸 포인트
function get_expire_point($mb_id)
{
    global $gml, $config;

    if($config['cf_point_term'] == 0)
        return 0;

    $sql = " select sum(po_point - po_use_point) as sum_point
                from {$gml['point_table']}
                where mb_id = '$mb_id'
                  and po_expired = '0'
                  and po_expire_date <> '9999-12-31'
                  and po_expire_date < '".GML_TIME_YMD."' ";
    $row = sql_fetch($sql);

    return $row['sum_point'];
}

// 포인트 삭제
function delete_point($mb_id, $rel_table, $rel_id, $rel_action)
{
    global $gml;

    $result = false;
    if ($rel_table || $rel_id || $rel_action)
    {
        // 포인트 내역정보
        $sql = " select * from {$gml['point_table']}
                    where mb_id = '$mb_id'
                      and po_rel_table = '$rel_table'
                      and po_rel_id = '$rel_id'
                      and po_rel_action = '$rel_action' ";
        $row = sql_fetch($sql);

        if($row['po_point'] < 0) {
            $mb_id = $row['mb_id'];
            $po_point = abs($row['po_point']);

            delete_use_point($mb_id, $po_point);
        } else {
            if($row['po_use_point'] > 0) {
                insert_use_point($row['mb_id'], $row['po_use_point'], $row['po_id']);
            }
        }

        $result = sql_query(" delete from {$gml['point_table']}
                     where mb_id = '$mb_id'
                       and po_rel_table = '$rel_table'
                       and po_rel_id = '$rel_id'
                       and po_rel_action = '$rel_action' ", false);

        // po_mb_point에 반영
        $sql = " update {$gml['point_table']}
                    set po_mb_point = po_mb_point - '{$row['po_point']}'
                    where mb_id = '$mb_id'
                      and po_id > '{$row['po_id']}' ";
        sql_query($sql);

        // 포인트 내역의 합을 구하고
        $sum_point = get_point_sum($mb_id);

        // 포인트 UPDATE
        $sql = " update {$gml['member_table']} set mb_point = '$sum_point' where mb_id = '$mb_id' ";
        $result = sql_query($sql);
    }

    return $result;
}

// 회원 레이어
function get_sideview($mb_id, $name='', $email='', $homepage='')
{
    global $config;
    global $gml;
    global $bo_table, $sca, $is_admin, $member;

    $email = get_string_encrypt($email);
    $homepage = set_http(clean_xss_tags($homepage));

    $name     = get_text($name, 0, true);
    $email    = get_text($email);
    $homepage = get_text($homepage);

    $tmp_name = "";
    $en_mb_id = $mb_id;

    if( $is_admin ){
        $mb_params = 'mb_id='.$mb_id;
    } else {
        $en_mb_id = get_string_encrypt($mb_id);
        $mb_params = 'mb_hash='.$en_mb_id;
    }

    if ($mb_id) {
        $tmp_name = '<a href="'.GML_BBS_URL.'/profile.php?'.$mb_params.'" class="sv_member" title="'.$name.' '.__('Profile').'" target="_blank" rel="nofollow" onclick="return false;">';

        if ($config['cf_use_member_icon']) {
            $mb_dir = substr($mb_id,0,2);
            $icon_file = GML_DATA_PATH.'/member/'.$mb_dir.'/'.get_mb_icon_name($mb_id).'.gif';

            if (file_exists($icon_file)) {
                $width = $config['cf_member_icon_width'];
                $height = $config['cf_member_icon_height'];
                $icon_file_url = GML_DATA_URL.'/member/'.$mb_dir.'/'.get_mb_icon_name($mb_id).'.gif';
                $tmp_name .= '<span class="profile_img"><img src="'.$icon_file_url.'" width="'.$width.'" height="'.$height.'" alt=""></span>';

                if ($config['cf_use_member_icon'] == 2) // Member Icon + Name, 회원아이콘+이름
                    $tmp_name = $tmp_name.' '.$name;
            } else {
                if( defined('GML_THEME_NO_PROFILE_IMG') ){
                    $tmp_name .= GML_THEME_NO_PROFILE_IMG;
                } else if( defined('GML_NO_PROFILE_IMG') ){
                    $tmp_name .= GML_NO_PROFILE_IMG;
                }
                if ($config['cf_use_member_icon'] == 2) // Member Icon + Name, 회원아이콘+이름
                    $tmp_name = $tmp_name.' '.$name;
            }
        } else {
            $tmp_name = $tmp_name.' '.$name;
        }
        $tmp_name .= '</a>';
    } else {
        if(!$bo_table)
            return $name;

        $tmp_url = get_pretty_url($bo_table, '', 'sca='.$sca.'&amp;sfl=wr_name,1&amp;stx='.$name);
        $tmp_name = '<a href="'.$tmp_url.'" title="'.$name.' '.__('Search by name').'" class="sv_guest" rel="nofollow" onclick="return false;">'.$name.'</a>';
    }

    $str = "<span class=\"sv_wrap\">\n";
    $str .= $tmp_name."\n";

    $str2 = "<span class=\"sv\">\n";
    if($mb_id)
        $str2 .= "<a href=\"".GML_BBS_URL."/memo_form.php?".$mb_params."\" onclick=\"win_memo(this.href); return false;\">".__('Send Memo')."</a>\n";
    if($email)
        $str2 .= "<a href=\"".GML_BBS_URL."/formmail.php?".$mb_params."&amp;name=".urlencode($name)."&amp;email=".$email."\" onclick=\"win_email(this.href); return false;\">".__('Send Mail')."</a>\n";
    if($homepage)
        $str2 .= "<a href=\"".$homepage."\" target=\"_blank\">".__('Homepage')."</a>\n";
    if($mb_id)
        $str2 .= "<a href=\"".GML_BBS_URL."/profile.php?".$mb_params."\" onclick=\"win_profile(this.href); return false;\">".__('profile')."</a>\n";
    if($bo_table) {
        if($mb_id) {
            if( $is_admin ){
                $str2 .= "<a href=\"".get_pretty_url($bo_table, '', "sca=".$sca."&amp;sfl=mb_id,1&amp;stx=".$en_mb_id)."\">".__('Search by ID')."</a>\n";
            } else {
                $str2 .= "<a href=\"".get_pretty_url($bo_table, '', "sca=".$sca."&amp;".$mb_params)."\">".__('Search by ID')."</a>\n";
            }
        } else {
            $str2 .= "<a href=\"".get_pretty_url($bo_table, '', "sca=".$sca."&amp;sfl=wr_name,1&amp;stx=".$name)."\">".__('Search by name')."</a>\n";
        }
    }
    if($mb_id)
        $str2 .= "<a href=\"".GML_BBS_URL."/new.php?".$mb_params."\">".__('Full post')."</a>\n";
    if($is_admin == "super" && $mb_id) {
        $str2 .= "<a href=\"".GML_ADMIN_URL."/member_form.php?w=u&amp;mb_id=".$en_mb_id."\" target=\"_blank\">".__('Edit Profile')."</a>\n";
        $str2 .= "<a href=\"".GML_ADMIN_URL."/point_list.php?sfl=mb_id&amp;stx=".$en_mb_id."\" target=\"_blank\">".__('Point History')."</a>\n";
    }
    $str2 .= "</span>\n";
    $str .= $str2;
    $str .= "\n<noscript class=\"sv_nojs\">".$str2."</noscript>";

    $str .= "</span>";

    return $str;
}

// 파일을 보이게 하는 링크 (이미지, 플래쉬, 동영상)
function view_file_link($file, $width, $height, $content='')
{
    global $config, $board;
    global $gml;
    static $ids;

    if (!$file) return;

    $ids++;

    // 파일의 폭이 게시판설정의 이미지폭 보다 크다면 게시판설정 폭으로 맞추고 비율에 따라 높이를 계산
    if ($width > $board['bo_image_width'] && $board['bo_image_width'])
    {
        $rate = $board['bo_image_width'] / $width;
        $width = $board['bo_image_width'];
        $height = (int)($height * $rate);
    }

    // 폭이 있는 경우 폭과 높이의 속성을 주고, 없으면 자동 계산되도록 코드를 만들지 않는다.
    if ($width)
        $attr = ' width="'.$width.'" height="'.$height.'" ';
    else
        $attr = '';

    if (preg_match("/\.({$config['cf_image_extension']})$/i", $file)) {
        $img = '<a href="'.GML_BBS_URL.'/view_image.php?bo_table='.$board['bo_table'].'&amp;fn='.urlencode($file).'" target="_blank" class="view_image">';
        $img .= '<img src="'.GML_DATA_URL.'/file/'.$board['bo_table'].'/'.urlencode($file).'" alt="'.$content.'" '.$attr.'>';
        $img .= '</a>';

        return $img;
    }
}


// view_file_link() 함수에서 넘겨진 이미지를 보이게 합니다.
// {img:0} ... {img:n} 과 같은 형식
function view_image($view, $number, $attribute)
{
    if ($view['file'][$number]['view'])
        return preg_replace("/>$/", " $attribute>", $view['file'][$number]['view']);
    else
        return "";
}

function cut_str($str, $len, $suffix="…")
{
    $arr_str = preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
    $str_len = count($arr_str);

    if ($str_len >= $len) {
        $slice_str = array_slice($arr_str, 0, $len);
        $str = join("", $slice_str);

        return $str . ($str_len > $len ? $suffix : '');
    } else {
        $str = join("", $arr_str);
        return $str;
    }
}


// TEXT 형식으로 변환
function get_text($str, $html=0, $restore=false)
{
    $source[] = "<";
    $target[] = "&lt;";
    $source[] = ">";
    $target[] = "&gt;";
    $source[] = "\"";
    $target[] = "&#034;";
    $source[] = "\'";
    $target[] = "&#039;";

    if($restore)
        $str = str_replace($target, $source, $str);

    // 3.31
    // TEXT 출력일 경우 &amp; &nbsp; 등의 코드를 정상으로 출력해 주기 위함
    if ($html == 0) {
        $str = html_symbol($str);
    }

    if ($html) {
        $source[] = "\n";
        $target[] = "<br/>";
    }

    return str_replace($source, $target, $str);
}

// 3.31
// HTML SYMBOL 변환
// &nbsp; &amp; &middot; 등을 정상으로 출력
function html_symbol($str)
{
    return preg_replace("/\&([a-z0-9]{1,20}|\#[0-9]{0,3});/i", "&#038;\\1;", $str);
}


/*************************************************************************
**
**  SQL 관련 함수 모음
**
*************************************************************************/

// DB 연결
function sql_connect($host, $user, $pass, $db=GML_MYSQL_DB)
{
    global $gml;

    if(function_exists('mysqli_connect') && GML_MYSQLI_USE) {
        $link = mysqli_connect($host, $user, $pass, $db);

        // 연결 오류 발생 시 스크립트 종료
        if (mysqli_connect_errno()) {
            die('Connect Error: '.mysqli_connect_error());
        }
    } else {
        $link = mysql_connect($host, $user, $pass);
    }

    return $link;
}


// DB 선택
function sql_select_db($db, $connect)
{
    global $gml;

    if(function_exists('mysqli_select_db') && GML_MYSQLI_USE)
        return @mysqli_select_db($connect, $db);
    else
        return @mysql_select_db($db, $connect);
}


function sql_set_charset($charset, $link=null)
{
    global $gml;

    if(!$link)
        $link = $gml['connect_db'];

    if(function_exists('mysqli_set_charset') && GML_MYSQLI_USE)
        mysqli_set_charset($link, $charset);
    else
        mysql_query(" set names {$charset} ", $link);
}


// mysqli_query 와 mysqli_error 를 한꺼번에 처리
// mysql connect resource 지정 - 명랑폐인님 제안
function sql_query($sql, $error=GML_DISPLAY_SQL_ERROR, $link=null)
{
    global $gml, $gml_debug, $is_admin;

    if(!$link)
        $link = $gml['connect_db'];

    // Blind SQL Injection 취약점 해결
    $sql = trim($sql);
    // union의 사용을 허락하지 않습니다.
    //$sql = preg_replace("#^select.*from.*union.*#i", "select 1", $sql);
    $sql = preg_replace("#^select.*from.*[\s\(]+union[\s\)]+.*#i ", "select 1", $sql);
    // `information_schema` DB로의 접근을 허락하지 않습니다.
    $sql = preg_replace("#^select.*from.*where.*`?information_schema`?.*#i", "select 1", $sql);

    $is_debug = get_permission_debug_show();

    $start_time = $is_debug ? get_microtime() : 0;

    if(function_exists('mysqli_query') && GML_MYSQLI_USE) {
        if ($error) {
            $result = @mysqli_query($link, $sql) or die("<p>$sql<p>" . mysqli_errno($link) . " : " .  mysqli_error($link) . "<p>error file : {$_SERVER['SCRIPT_NAME']}");
        } else {
            $result = @mysqli_query($link, $sql);
        }
    } else {
        if ($error) {
            $result = @mysql_query($sql, $link) or die("<p>$sql<p>" . mysql_errno() . " : " .  mysql_error() . "<p>error file : {$_SERVER['SCRIPT_NAME']}");
        } else {
            $result = @mysql_query($sql, $link);
        }
    }

    if($result && $is_debug) {
        // 여기에 실행한 sql문을 화면에 표시하는 로직 넣기
        $gml_debug['sql'][] = $sql;
		$gml_debug['start_time'][] = $start_time;
		$gml_debug['end_time'][] = get_microtime();
    }

    return $result;
}


// 쿼리를 실행한 후 결과값에서 한행을 얻는다.
function sql_fetch($sql, $error=GML_DISPLAY_SQL_ERROR, $link=null)
{
    global $gml;

    if(!$link)
        $link = $gml['connect_db'];

    $result = sql_query($sql, $error, $link);
    //$row = @sql_fetch_array($result) or die("<p>$sql<p>" . mysqli_errno() . " : " .  mysqli_error() . "<p>error file : $_SERVER['SCRIPT_NAME']");
    $row = sql_fetch_array($result);
    return $row;
}


// 결과값에서 한행 연관배열(이름으로)로 얻는다.
function sql_fetch_array($result)
{
    if(function_exists('mysqli_fetch_assoc') && GML_MYSQLI_USE)
        $row = @mysqli_fetch_assoc($result);
    else
        $row = @mysql_fetch_assoc($result);

    return $row;
}


// $result에 대한 메모리(memory)에 있는 내용을 모두 제거한다.
// sql_free_result()는 결과로부터 얻은 질의 값이 커서 많은 메모리를 사용할 염려가 있을 때 사용된다.
// 단, 결과 값은 스크립트(script) 실행부가 종료되면서 메모리에서 자동적으로 지워진다.
function sql_free_result($result)
{
    if(function_exists('mysqli_free_result') && GML_MYSQLI_USE)
        return mysqli_free_result($result);
    else
        return mysql_free_result($result);
}


function sql_password($value)
{
    // mysql 4.0x 이하 버전에서는 password() 함수의 결과가 16bytes
    // mysql 4.1x 이상 버전에서는 password() 함수의 결과가 41bytes
    $row = sql_fetch(" select password('$value') as pass ");

    return $row['pass'];
}


function sql_insert_id($link=null)
{
    global $gml;

    if(!$link)
        $link = $gml['connect_db'];

    if(function_exists('mysqli_insert_id') && GML_MYSQLI_USE)
        return mysqli_insert_id($link);
    else
        return mysql_insert_id($link);
}


function sql_num_rows($result)
{
    if(function_exists('mysqli_num_rows') && GML_MYSQLI_USE)
        return mysqli_num_rows($result);
    else
        return mysql_num_rows($result);
}


function sql_field_names($table, $link=null)
{
    global $gml;

    if(!$link)
        $link = $gml['connect_db'];

    $columns = array();

    $sql = " select * from `$table` limit 1 ";
    $result = sql_query($sql, $link);

    if(function_exists('mysqli_fetch_field') && GML_MYSQLI_USE) {
        while($field = mysqli_fetch_field($result)) {
            $columns[] = $field->name;
        }
    } else {
        $i = 0;
        $cnt = mysql_num_fields($result);
        while($i < $cnt) {
            $field = mysql_fetch_field($result, $i);
            $columns[] = $field->name;
            $i++;
        }
    }

    return $columns;
}


function sql_error_info($link=null)
{
    global $gml;

    if(!$link)
        $link = $gml['connect_db'];

    if(function_exists('mysqli_error') && GML_MYSQLI_USE) {
        return mysqli_errno($link) . ' : ' . mysqli_error($link);
    } else {
        return mysql_errno($link) . ' : ' . mysql_error($link);
    }
}


// PHPMyAdmin 참고
function get_table_define($table, $crlf="\n")
{
    global $gml;

    // For MySQL < 3.23.20
    $schema_create .= 'CREATE TABLE ' . $table . ' (' . $crlf;

    $sql = 'SHOW FIELDS FROM ' . $table;
    $result = sql_query($sql);
    while ($row = sql_fetch_array($result))
    {
        $schema_create .= '    ' . $row['Field'] . ' ' . $row['Type'];
        if (isset($row['Default']) && $row['Default'] != '')
        {
            $schema_create .= ' DEFAULT \'' . $row['Default'] . '\'';
        }
        if ($row['Null'] != 'YES')
        {
            $schema_create .= ' NOT NULL';
        }
        if ($row['Extra'] != '')
        {
            $schema_create .= ' ' . $row['Extra'];
        }
        $schema_create     .= ',' . $crlf;
    } // end while
    sql_free_result($result);

    $schema_create = preg_replace('/,' . $crlf . '$/', '', $schema_create);

    $sql = 'SHOW KEYS FROM ' . $table;
    $result = sql_query($sql);
    while ($row = sql_fetch_array($result))
    {
        $kname    = $row['Key_name'];
        $comment  = (isset($row['Comment'])) ? $row['Comment'] : '';
        $sub_part = (isset($row['Sub_part'])) ? $row['Sub_part'] : '';

        if ($kname != 'PRIMARY' && $row['Non_unique'] == 0) {
            $kname = "UNIQUE|$kname";
        }
        if ($comment == 'FULLTEXT') {
            $kname = 'FULLTEXT|$kname';
        }
        if (!isset($index[$kname])) {
            $index[$kname] = array();
        }
        if ($sub_part > 1) {
            $index[$kname][] = $row['Column_name'] . '(' . $sub_part . ')';
        } else {
            $index[$kname][] = $row['Column_name'];
        }
    } // end while
    sql_free_result($result);

    while (list($x, $columns) = @each($index)) {
        $schema_create     .= ',' . $crlf;
        if ($x == 'PRIMARY') {
            $schema_create .= '    PRIMARY KEY (';
        } else if (substr($x, 0, 6) == 'UNIQUE') {
            $schema_create .= '    UNIQUE ' . substr($x, 7) . ' (';
        } else if (substr($x, 0, 8) == 'FULLTEXT') {
            $schema_create .= '    FULLTEXT ' . substr($x, 9) . ' (';
        } else {
            $schema_create .= '    KEY ' . $x . ' (';
        }
        $schema_create     .= implode($columns, ', ') . ')';
    } // end while

    $schema_create .= $crlf . ') ENGINE=MyISAM DEFAULT CHARSET=utf8';

    return $schema_create;
} // end of the 'PMA_getTableDef()' function


// 리퍼러 체크
function referer_check($url='')
{
    /*
    // 제대로 체크를 하지 못하여 주석 처리함
    global $gml;

    if (!$url)
        $url = GML_URL;

    if (!preg_match("/^http['s']?:\/\/".$_SERVER['HTTP_HOST']."/", $_SERVER['HTTP_REFERER']))
        alert("제대로 된 접근이 아닌것 같습니다.", $url);
    */
}


// 한글 요일
function get_yoil($date, $full=0)
{
    $arr_yoil = array ('일', '월', '화', '수', '목', '금', '토');

    $yoil = date("w", strtotime($date));
    $str = $arr_yoil[$yoil];
    if ($full) {
        $str .= '요일';
    }
    return $str;
}


// 날짜를 select 박스 형식으로 얻는다
function date_select($date, $name='')
{
    global $gml;

    $s = '';
    if (substr($date, 0, 4) == "0000") {
        $date = GML_TIME_YMDHIS;
    }
    preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $date, $m);

    // 년
    $s .= "<select name='{$name}_y'>";
    for ($i=$m['0']-3; $i<=$m['0']+3; $i++) {
        $s .= "<option value='$i'";
        if ($i == $m['0']) {
            $s .= " selected";
        }
        $s .= ">$i";
    }
    $s .= "</select>년 \n";

    // 월
    $s .= "<select name='{$name}_m'>";
    for ($i=1; $i<=12; $i++) {
        $s .= "<option value='$i'";
        if ($i == $m['2']) {
            $s .= " selected";
        }
        $s .= ">$i";
    }
    $s .= "</select>월 \n";

    // 일
    $s .= "<select name='{$name}_d'>";
    for ($i=1; $i<=31; $i++) {
        $s .= "<option value='$i'";
        if ($i == $m['3']) {
            $s .= " selected";
        }
        $s .= ">$i";
    }
    $s .= "</select>일 \n";

    return $s;
}


// 시간을 select 박스 형식으로 얻는다
// 1.04.00
// 경매에 시간 설정이 가능하게 되면서 추가함
function time_select($time, $name="")
{
    preg_match("/([0-9]{2}):([0-9]{2}):([0-9]{2})/", $time, $m);

    // 시
    $s .= "<select name='{$name}_h'>";
    for ($i=0; $i<=23; $i++) {
        $s .= "<option value='$i'";
        if ($i == $m['0']) {
            $s .= " selected";
        }
        $s .= ">$i";
    }
    $s .= "</select>시 \n";

    // 분
    $s .= "<select name='{$name}_i'>";
    for ($i=0; $i<=59; $i++) {
        $s .= "<option value='$i'";
        if ($i == $m['2']) {
            $s .= " selected";
        }
        $s .= ">$i";
    }
    $s .= "</select>분 \n";

    // 초
    $s .= "<select name='{$name}_s'>";
    for ($i=0; $i<=59; $i++) {
        $s .= "<option value='$i'";
        if ($i == $m['3']) {
            $s .= " selected";
        }
        $s .= ">$i";
    }
    $s .= "</select>초 \n";

    return $s;
}


// DEMO 라는 파일이 있으면 데모 화면으로 인식함
function check_demo()
{
    global $is_admin;
    if ($is_admin != 'super' && file_exists(GML_PATH.'/DEMO'))
        alert(__("You can't do this on the demo screen."));
}


// 문자열이 한글, 영문, 숫자, 특수문자로 구성되어 있는지 검사
function check_string($str, $options)
{
    global $gml;

    $s = '';
    for($i=0;$i<strlen($str);$i++) {
        $c = $str[$i];
        $oc = ord($c);

        // 한글
        if ($oc >= 0xA0 && $oc <= 0xFF) {
            if ($options & GML_HANGUL) {
                $s .= $c . $str[$i+1] . $str[$i+2];
            }
            $i+=2;
        }
        // 숫자
        else if ($oc >= 0x30 && $oc <= 0x39) {
            if ($options & GML_NUMERIC) {
                $s .= $c;
            }
        }
        // 영대문자
        else if ($oc >= 0x41 && $oc <= 0x5A) {
            if (($options & GML_ALPHABETIC) || ($options & GML_ALPHAUPPER)) {
                $s .= $c;
            }
        }
        // 영소문자
        else if ($oc >= 0x61 && $oc <= 0x7A) {
            if (($options & GML_ALPHABETIC) || ($options & GML_ALPHALOWER)) {
                $s .= $c;
            }
        }
        // 공백
        else if ($oc == 0x20) {
            if ($options & GML_SPACE) {
                $s .= $c;
            }
        }
        else {
            if ($options & GML_SPECIAL) {
                $s .= $c;
            }
        }
    }

    // 넘어온 값과 비교하여 같으면 참, 틀리면 거짓
    return ($str == $s);
}


// 한글(2bytes)에서 마지막 글자가 1byte로 끝나는 경우
// 출력시 깨지는 현상이 발생하므로 마지막 완전하지 않은 글자(1byte)를 하나 없앰
function cut_hangul_last($hangul)
{
    global $gml;

    // 한글이 반쪽나면 ?로 표시되는 현상을 막음
    $cnt = 0;
    for($i=0;$i<strlen($hangul);$i++) {
        // 한글만 센다
        if (ord($hangul[$i]) >= 0xA0) {
            $cnt++;
        }
    }

    return $hangul;
}


// 테이블에서 INDEX(키) 사용여부 검사
function explain($sql)
{
    if (preg_match("/^(select)/i", trim($sql))) {
        $q = "explain $sql";
        echo $q;
        $row = sql_fetch($q);
        if (!$row['key']) $row['key'] = "NULL";
        echo " <font color=blue>(type={$row['type']} , key={$row['key']})</font>";
    }
}

// 악성태그 변환
function bad_tag_convert($code)
{
    global $view;
    global $member, $is_admin;

    if ($is_admin && $member['mb_id'] != $view['mb_id']) {
        //$code = preg_replace_callback("#(\<(embed|object)[^\>]*)\>(\<\/(embed|object)\>)?#i",
        // embed 또는 object 태그를 막지 않는 경우 필터링이 되도록 수정
        $code = preg_replace_callback("#(\<(embed|object)[^\>]*)\>?(\<\/(embed|object)\>)?#i",
                    create_function('$matches', 'return "<div class=\"embedx\">'.__('Can not view embedded or object tags with administrator ID due to security issues. To check, log on with another ID that does not have administrative rights.').'</div>";'),
                    $code);
    }

    return preg_replace("/\<([\/]?)(script|iframe|form)([^\>]*)\>?/i", "&lt;$1$2$3&gt;", $code);
}


// 토큰 생성
function _token()
{
    return md5(uniqid(rand(), true));
}


// 불법접근을 막도록 토큰을 생성하면서 토큰값을 리턴
function get_token()
{
    $token = md5(uniqid(rand(), true));
    set_session('ss_token', $token);

    return $token;
}


// POST로 넘어온 토큰과 세션에 저장된 토큰 비교
function check_token()
{
    set_session('ss_token', '');
    return true;
}


// 문자열에 utf8 문자가 들어 있는지 검사하는 함수
// 코드 : http://in2.php.net/manual/en/function.mb-check-encoding.php#95289
function is_utf8($str)
{
    $len = strlen($str);
    for($i = 0; $i < $len; $i++) {
        $c = ord($str[$i]);
        if ($c > 128) {
            if (($c > 247)) return false;
            elseif ($c > 239) $bytes = 4;
            elseif ($c > 223) $bytes = 3;
            elseif ($c > 191) $bytes = 2;
            else return false;
            if (($i + $bytes) > $len) return false;
            while ($bytes > 1) {
                $i++;
                $b = ord($str[$i]);
                if ($b < 128 || $b > 191) return false;
                $bytes--;
            }
        }
    }
    return true;
}


// UTF-8 문자열 자르기
// 출처 : https://www.google.co.kr/search?q=utf8_strcut&aq=f&oq=utf8_strcut&aqs=chrome.0.57j0l3.826j0&sourceid=chrome&ie=UTF-8
function utf8_strcut( $str, $size, $suffix='...' )
{
        $substr = substr( $str, 0, $size * 2 );
        $multi_size = preg_match_all( '/[\x80-\xff]/', $substr, $multi_chars );

        if ( $multi_size > 0 )
            $size = $size + intval( $multi_size / 3 ) - 1;

        if ( strlen( $str ) > $size ) {
            $str = substr( $str, 0, $size );
            $str = preg_replace( '/(([\x80-\xff]{3})*?)([\x80-\xff]{0,2})$/', '$1', $str );
            $str .= $suffix;
        }

        return $str;
}


/*
-----------------------------------------------------------
    Charset 을 변환하는 함수
-----------------------------------------------------------
iconv 함수가 있으면 iconv 로 변환하고
없으면 mb_convert_encoding 함수를 사용한다.
둘다 없으면 사용할 수 없다.
*/
function convert_charset($from_charset, $to_charset, $str)
{

    if( function_exists('iconv') )
        return iconv($from_charset, $to_charset, $str);
    elseif( function_exists('mb_convert_encoding') )
        return mb_convert_encoding($str, $to_charset, $from_charset);
    else
        die("Not found 'iconv' or 'mbstring' library in server.");
}


// mysqli_real_escape_string 의 alias 기능을 한다.
function sql_real_escape_string($str, $link=null)
{
    global $gml;

    if(!$link)
        $link = $gml['connect_db'];

    if(function_exists('mysqli_connect') && GML_MYSQLI_USE) {
        return mysqli_real_escape_string($link, $str);
    }

    return mysql_real_escape_string($str, $link);
}

function escape_trim($field)
{
    $str = call_user_func(GML_ESCAPE_FUNCTION, $field);
    return $str;
}


// $_POST 형식에서 checkbox 엘리먼트의 checked 속성에서 checked 가 되어 넘어 왔는지를 검사
function is_checked($field)
{
    return !empty($_POST[$field]);
}


function abs_ip2long($ip='')
{
    $ip = $ip ? $ip : $_SERVER['REMOTE_ADDR'];
    return abs(ip2long($ip));
}


function get_selected($field, $value)
{
    return ($field==$value) ? ' selected="selected"' : '';
}


function get_checked($field, $value)
{
    return ($field==$value) ? ' checked="checked"' : '';
}


function is_mobile()
{
    return apply_replace('is_mobile', preg_match('/'.GML_MOBILE_AGENT.'/i', $_SERVER['HTTP_USER_AGENT']));
}


/*******************************************************************************
    유일한 키를 얻는다.

    결과 :

        년월일시분초00 ~ 년월일시분초99
        년(4) 월(2) 일(2) 시(2) 분(2) 초(2) 100분의1초(2)
        총 16자리이며 년도는 2자리로 끊어서 사용해도 됩니다.
        예) 2008062611570199 또는 08062611570199 (2100년까지만 유일키)

    사용하는 곳 :
    1. 게시판 글쓰기시 미리 유일키를 얻어 파일 업로드 필드에 넣는다.
    2. 주문번호 생성시에 사용한다.
    3. 기타 유일키가 필요한 곳에서 사용한다.
*******************************************************************************/
// 기존의 get_unique_id() 함수를 사용하지 않고 get_uniqid() 를 사용한다.
function get_uniqid()
{
    global $gml;

    sql_query(" LOCK TABLE {$gml['uniqid_table']} WRITE ");
    while (1) {
        // 년월일시분초에 100분의 1초 두자리를 추가함 (1/100 초 앞에 자리가 모자르면 0으로 채움)
        $key = date('ymdHis', time()) . str_pad((int)(microtime()*100), 2, "0", STR_PAD_LEFT);

        $result = sql_query(" insert into {$gml['uniqid_table']} set uq_id = '$key', uq_ip = '".get_real_client_ip()."' ", false);
        if ($result) break; // 쿼리가 정상이면 빠진다.

        // insert 하지 못했으면 일정시간 쉰다음 다시 유일키를 만든다.
        usleep(10000); // 100분의 1초를 쉰다
    }
    sql_query(" UNLOCK TABLES ");

    return $key;
}


// CHARSET 변경 : euc-kr -> utf-8
function iconv_utf8($str)
{
    return iconv('euc-kr', 'utf-8', $str);
}


// CHARSET 변경 : utf-8 -> euc-kr
function iconv_euckr($str)
{
    return iconv('utf-8', 'euc-kr', $str);
}


// PC 또는 모바일 사용인지를 검사
function check_device($device)
{
    global $is_admin;

    if ($is_admin) return;

    if ($device=='pc' && GML_IS_MOBILE) {
        alert(__('PC view only bulletin board.'), GML_URL);
    } else if ($device=='mobile' && !GML_IS_MOBILE) {
        alert(__('Mobile view only bulletin board.'), GML_URL);
    }
}


// 게시판 최신글 캐시 파일 삭제
function delete_cache_latest($bo_table)
{
    if (!preg_match("/^([A-Za-z0-9_]{1,20})$/", $bo_table)) {
        return;
    }

    /*
    $files = glob(GML_DATA_PATH.'/cache/latest-'.$bo_table.'-*');
    if (is_array($files)) {
        foreach ($files as $filename)
            unlink($filename);
    }
    */

    if( function_exists('gml_delete_cache_by_prefix') ){
        gml_delete_cache_by_prefix('latest-'.$bo_table.'-');
    }
}

// 게시판 첨부파일 썸네일 삭제
function delete_board_thumbnail($bo_table, $file)
{
    if(!$bo_table || !$file)
        return;

    $fn = preg_replace("/\.[^\.]+$/i", "", basename($file));
    $files = glob(GML_DATA_PATH.'/file/'.$bo_table.'/thumb-'.$fn.'*');
    if (is_array($files)) {
        foreach ($files as $filename)
            unlink($filename);
    }
}

// 에디터 이미지 얻기
function get_editor_image($contents, $view=true)
{
    if(!$contents)
        return false;

    // $contents 중 img 태그 추출
    if ($view)
        $pattern = "/<img([^>]*)>/iS";
    else
        $pattern = "/<img[^>]*src=[\'\"]?([^>\'\"]+[^>\'\"]+)[\'\"]?[^>]*>/i";
    preg_match_all($pattern, $contents, $matchs);

    return $matchs;
}

// 에디터 썸네일 삭제
function delete_editor_thumbnail($contents)
{
    if(!$contents)
        return;

    // $contents 중 img 태그 추출
    $matchs = get_editor_image($contents);

    if(!$matchs)
        return;

    for($i=0; $i<count($matchs[1]); $i++) {
        // 이미지 path 구함
        $imgurl = @parse_url($matchs[1][$i]);
        $srcfile = $_SERVER['DOCUMENT_ROOT'].$imgurl['path'];

        $filename = preg_replace("/\.[^\.]+$/i", "", basename($srcfile));
        $filepath = dirname($srcfile);
        $files = glob($filepath.'/thumb-'.$filename.'*');
        if (is_array($files)) {
            foreach($files as $filename)
                unlink($filename);
        }
    }
}

// 1:1문의 첨부파일 썸네일 삭제
function delete_qa_thumbnail($file)
{
    if(!$file)
        return;

    $fn = preg_replace("/\.[^\.]+$/i", "", basename($file));
    $files = glob(GML_DATA_PATH.'/qa/thumb-'.$fn.'*');
    if (is_array($files)) {
        foreach ($files as $filename)
            unlink($filename);
    }
}

// 스킨 style sheet 파일 얻기
function get_skin_stylesheet($skin_path, $dir='')
{
    if(!$skin_path)
        return "";

    $str = "";
    $files = array();

    if($dir)
        $skin_path .= '/'.$dir;

    $skin_url = GML_URL.str_replace("\\", "/", str_replace(GML_PATH, "", $skin_path));

    if(is_dir($skin_path)) {
        if($dh = opendir($skin_path)) {
            while(($file = readdir($dh)) !== false) {
                if($file == "." || $file == "..")
                    continue;

                if(is_dir($skin_path.'/'.$file))
                    continue;

                if(preg_match("/\.(css)$/i", $file))
                    $files[] = $file;
            }
            closedir($dh);
        }
    }

    if(!empty($files)) {
        sort($files);

        foreach($files as $file) {
            $str .= '<link rel="stylesheet" href="'.$skin_url.'/'.$file.'?='.date("md").'">'."\n";
        }
    }

    return $str;

    /*
    // glob 를 이용한 코드
    if (!$skin_path) return '';
    $skin_path .= $dir ? '/'.$dir : '';

    $str = '';
    $skin_url = GML_URL.str_replace('\\', '/', str_replace(GML_PATH, '', $skin_path));

    foreach (glob($skin_path.'/*.css') as $filepath) {
        $file = str_replace($skin_path, '', $filepath);
        $str .= '<link rel="stylesheet" href="'.$skin_url.'/'.$file.'?='.date('md').'">'."\n";
    }
    return $str;
    */
}

// 스킨 javascript 파일 얻기
function get_skin_javascript($skin_path, $dir='')
{
    if(!$skin_path)
        return "";

    $str = "";
    $files = array();

    if($dir)
        $skin_path .= '/'.$dir;

    $skin_url = GML_URL.str_replace("\\", "/", str_replace(GML_PATH, "", $skin_path));

    if(is_dir($skin_path)) {
        if($dh = opendir($skin_path)) {
            while(($file = readdir($dh)) !== false) {
                if($file == "." || $file == "..")
                    continue;

                if(is_dir($skin_path.'/'.$file))
                    continue;

                if(preg_match("/\.(js)$/i", $file))
                    $files[] = $file;
            }
            closedir($dh);
        }
    }

    if(!empty($files)) {
        sort($files);

        foreach($files as $file) {
            $str .= '<script src="'.$skin_url.'/'.$file.'"></script>'."\n";
        }
    }

    return $str;
}

// HTML 마지막 처리
function html_end()
{
    global $html_process;

    return $html_process->run();
}

function add_stylesheet($stylesheet, $order=0)
{
    global $html_process;

    if(trim($stylesheet))
        $html_process->merge_stylesheet($stylesheet, $order);
}

function add_javascript($javascript, $order=0)
{
    global $html_process;

    if(trim($javascript))
        $html_process->merge_javascript($javascript, $order);
}

class html_process {
    protected $css = array();
    protected $js  = array();

    function merge_stylesheet($stylesheet, $order)
    {
        $links = $this->css;
        $is_merge = true;

        foreach($links as $link) {
            if($link[1] == $stylesheet) {
                $is_merge = false;
                break;
            }
        }

        if($is_merge)
            $this->css[] = array($order, $stylesheet);
    }

    function merge_javascript($javascript, $order)
    {
        $scripts = $this->js;
        $is_merge = true;

        foreach($scripts as $script) {
            if($script[1] == $javascript) {
                $is_merge = false;
                break;
            }
        }

        if($is_merge)
            $this->js[] = array($order, $javascript);
    }

    function run()
    {
        global $config, $gml, $member;

        // 현재접속자 처리
        $tmp_sql = " select count(*) as cnt from {$gml['login_table']} where lo_ip = '{$_SERVER['REMOTE_ADDR']}' ";
        $tmp_row = sql_fetch($tmp_sql);

        if ($tmp_row['cnt']) {
            $tmp_sql = " update {$gml['login_table']} set mb_id = '{$member['mb_id']}', lo_datetime = '".GML_TIME_YMDHIS."', lo_location = '{$gml['lo_location']}', lo_url = '{$gml['lo_url']}' where lo_ip = '{$_SERVER['REMOTE_ADDR']}' ";
            sql_query($tmp_sql, FALSE);
        } else {
            $tmp_sql = " insert into {$gml['login_table']} ( lo_ip, mb_id, lo_datetime, lo_location, lo_url ) values ( '{$_SERVER['REMOTE_ADDR']}', '{$member['mb_id']}', '".GML_TIME_YMDHIS."', '{$gml['lo_location']}',  '{$gml['lo_url']}' ) ";
            sql_query($tmp_sql, FALSE);

            // 시간이 지난 접속은 삭제한다
            sql_query(" delete from {$gml['login_table']} where lo_datetime < '".date("Y-m-d H:i:s", GML_SERVER_TIME - (60 * $config['cf_login_minutes']))."' ");

            // 부담(overhead)이 있다면 테이블 최적화
            //$row = sql_fetch(" SHOW TABLE STATUS FROM `$mysql_db` LIKE '$gml['login_table']' ");
            //if ($row['Data_free'] > 0) sql_query(" OPTIMIZE TABLE $gml['login_table'] ");
        }

        $buffer = ob_get_contents();
        ob_end_clean();

        $stylesheet = '';
        $links = $this->css;

        if(!empty($links)) {
            foreach ($links as $key => $row) {
                $order[$key] = $row[0];
                $index[$key] = $key;
                $style[$key] = $row[1];
            }

            array_multisort($order, SORT_ASC, $index, SORT_ASC, $links);

            $links = apply_replace('html_process_css_files', $links);

            foreach($links as $link) {
                if(!trim($link[1]))
                    continue;

                $link[1] = preg_replace('#\.css([\'\"]?>)$#i', '.css?ver='.GML_CSS_VER.'$1', $link[1]);

                $stylesheet .= PHP_EOL.$link[1];
            }
        }

        $javascript = '';
        $scripts = $this->js;
        $php_eol = '';

        unset($order);
        unset($index);

        if(!empty($scripts)) {
            foreach ($scripts as $key => $row) {
                $order[$key] = $row[0];
                $index[$key] = $key;
                $script[$key] = $row[1];
            }

            array_multisort($order, SORT_ASC, $index, SORT_ASC, $scripts);

            $scripts = apply_replace('html_process_script_files', $scripts);

            foreach($scripts as $js) {
                if(!trim($js[1]))
                    continue;

                $js[1] = preg_replace('#\.js([\'\"]?>)$#i', '.js?ver='.GML_JS_VER.'$1', $js[1]);

                $javascript .= $php_eol.$js[1];
                $php_eol = PHP_EOL;
            }
        }

        /*
        </title>
        <link rel="stylesheet" href="default.css">
        밑으로 스킨의 스타일시트가 위치하도록 하게 한다.
        */
        $buffer = preg_replace('#(</title>[^<]*<link[^>]+>)#', "$1$stylesheet", $buffer);

        /*
        </head>
        <body>
        전에 스킨의 자바스크립트가 위치하도록 하게 한다.
        */
        $nl = '';
        if($javascript)
            $nl = "\n";
        $buffer = preg_replace('#(</head>[^<]*<body[^>]*>)#', "$javascript{$nl}$1", $buffer);

        $meta_tag = apply_replace('html_process_add_meta', '');

        if( $meta_tag ){
            /*
            </title>content<body>
            전에 메타태그가 위치 하도록 하게 한다.
            */
            $nl = "\n";
            $buffer = preg_replace('#(<title[^>]*>.*?</title>)#', "$meta_tag{$nl}$1", $buffer);
        }

        return $buffer;
    }
}

// 휴대폰번호의 숫자만 취한 후 중간에 하이픈(-)을 넣는다.
function hyphen_hp_number($hp)
{
    $hp = preg_replace("/[^0-9]/", "", $hp);
    return preg_replace("/([0-9]{3})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $hp);
}


// 로그인 후 이동할 URL
function login_url($url='')
{
    if (!$url) $url = GML_URL;

    return urlencode(clean_xss_tags(urldecode($url)));
}


// $dir 을 포함하여 https 또는 http 주소를 반환한다.
function https_url($dir, $https=true)
{
    if ($https) {
        if (GML_HTTPS_DOMAIN) {
            $url = GML_HTTPS_DOMAIN.'/'.$dir;
        } else {
            $url = GML_URL.'/'.$dir;
        }
    } else {
        if (GML_DOMAIN) {
            $url = GML_DOMAIN.'/'.$dir;
        } else {
            $url = GML_URL.'/'.$dir;
        }
    }

    return $url;
}


// 게시판의 공지사항을 , 로 구분하여 업데이트 한다.
function board_notice($bo_notice, $wr_id, $insert=false)
{
    $notice_array = explode(",", trim($bo_notice));

    if($insert && in_array($wr_id, $notice_array))
        return $bo_notice;

    $notice_array = array_merge(array($wr_id), $notice_array);
    $notice_array = array_unique($notice_array);
    foreach ($notice_array as $key=>$value) {
        if (!trim($value))
            unset($notice_array[$key]);
    }
    if (!$insert) {
        foreach ($notice_array as $key=>$value) {
            if ((int)$value == (int)$wr_id)
                unset($notice_array[$key]);
        }
    }
    return implode(",", $notice_array);
}


// goo.gl 짧은주소 만들기
function googl_short_url($longUrl)
{
    global $config;

    // Get API key from : http://code.google.com/apis/console/
    // URL Shortener API ON
    $apiKey = $config['cf_googl_shorturl_apikey'];

    $postData = array('longUrl' => $longUrl);
    $jsonData = json_encode($postData);

    $curlObj = curl_init();

    curl_setopt($curlObj, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url?key='.$apiKey);
    curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curlObj, CURLOPT_HEADER, 0);
    curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
    curl_setopt($curlObj, CURLOPT_POST, 1);
    curl_setopt($curlObj, CURLOPT_POSTFIELDS, $jsonData);

    $response = curl_exec($curlObj);

    //change the response json string to object
    $json = json_decode($response);

    curl_close($curlObj);

    return $json->id;
}


// 임시 저장된 글 수
function autosave_count($mb_id)
{
    global $gml;

    if ($mb_id) {
        $row = sql_fetch(" select count(*) as cnt from {$gml['autosave_table']} where mb_id = '$mb_id' ");
        return (int)$row['cnt'];
    } else {
        return 0;
    }
}

// 본인확인내역 기록
function insert_cert_history($mb_id, $company, $method)
{
    global $gml;

    $sql = " insert into {$gml['cert_history_table']}
                set mb_id = '$mb_id',
                    cr_company = '$company',
                    cr_method = '$method',
                    cr_ip = '{$_SERVER['REMOTE_ADDR']}',
                    cr_date = '".GML_TIME_YMD."',
                    cr_time = '".GML_TIME_HIS."' ";
    sql_query($sql);
}

// 인증시도회수 체크
function certify_count_check($mb_id, $type)
{
    global $gml, $config;

    if($config['cf_cert_use'] != 2)
        return;

    if($config['cf_cert_limit'] == 0)
        return;

    $sql = " select count(*) as cnt from {$gml['cert_history_table']} ";

    if($mb_id) {
        $sql .= " where mb_id = '$mb_id' ";
    } else {
        $sql .= " where cr_ip = '{$_SERVER['REMOTE_ADDR']}' ";
    }

    $sql .= " and cr_method = '".$type."' and cr_date = '".GML_TIME_YMD."' ";

    $row = sql_fetch($sql);

    switch($type) {
        case 'hp':
            $cert = __('Phone');
            break;
        case 'ipin':
            $cert = __('I-PIN');
            break;
        default:
            break;
    }

    if((int)$row['cnt'] >= (int)$config['cf_cert_limit'])
        alert_close(sprintf(__("Your %s identification has been used %s times today, so it's no longer available."), $cert, $row['cnt']));
}

// get_sock 함수 대체
if (!function_exists("get_sock")) {
    function get_sock($url)
    {
        // host 와 uri 를 분리
        //if (ereg("http://([a-zA-Z0-9_\-\.]+)([^<]*)", $url, $res))
        if (preg_match("/http:\/\/([a-zA-Z0-9_\-\.]+)([^<]*)/", $url, $res))
        {
            $host = $res[1];
            $get  = $res[2];
        }

        // 80번 포트로 소캣접속 시도
        $fp = fsockopen ($host, 80, $errno, $errstr, 30);
        if (!$fp)
        {
            //die("$errstr ($errno)\n");

            echo "$errstr ($errno)\n";
            return null;
        }
        else
        {
            fputs($fp, "GET $get HTTP/1.0\r\n");
            fputs($fp, "Host: $host\r\n");
            fputs($fp, "\r\n");

            // header 와 content 를 분리한다.
            while (trim($buffer = fgets($fp,1024)) != "")
            {
                $header .= $buffer;
            }
            while (!feof($fp))
            {
                $buffer .= fgets($fp,1024);
            }
        }
        fclose($fp);

        // content 만 return 한다.
        return $buffer;
    }
}

// 주소출력
function print_address($addr1, $addr2, $addr3, $addr4='')
{
    $address = get_text(trim($addr1));
    $addr2   = get_text(trim($addr2));
    $addr3   = get_text(trim($addr3));

    if($addr4 == 'N') {
        if($addr2)
            $address .= ' '.$addr2;
    } else {
        if($addr2)
            $address .= ', '.$addr2;
    }

    if($addr3)
        $address .= ' '.$addr3;

    return $address;
}

// input vars 체크
function check_input_vars()
{
    $max_input_vars = ini_get('max_input_vars');

    if($max_input_vars) {
        $post_vars = count($_POST, COUNT_RECURSIVE);
        $get_vars = count($_GET, COUNT_RECURSIVE);
        $cookie_vars = count($_COOKIE, COUNT_RECURSIVE);

        $input_vars = $post_vars + $get_vars + $cookie_vars;

        if($input_vars > $max_input_vars) {
            alert(__('The number of variables sent on the form is greater than the max_input_vars value.').'\\n'.__('Some of the transfer points may be lost and recorded in the DB.').'\\n\\n'.__('To resolve the problem, change the max_input_vars value in server php.ini'));
        }
    }
}

// HTML 특수문자 변환 htmlspecialchars
function htmlspecialchars2($str)
{
    $trans = array("\"" => "&#034;", "'" => "&#039;", "<"=>"&#060;", ">"=>"&#062;");
    $str = strtr($str, $trans);
    return $str;
}

// date 형식 변환
function conv_date_format($format, $date, $add='')
{
    if($add)
        $timestamp = strtotime($add, strtotime($date));
    else
        $timestamp = strtotime($date);

    return date($format, $timestamp);
}

// 검색어 특수문자 제거
function get_search_string($stx)
{
    $stx_pattern = array();
    $stx_pattern[] = '#\.*/+#';
    $stx_pattern[] = '#\\\*#';
    $stx_pattern[] = '#\.{2,}#';
    $stx_pattern[] = '#[/\'\"%=*\#\(\)\|\+\&\!\$~\{\}\[\]`;:\?\^\,]+#';

    $stx_replace = array();
    $stx_replace[] = '';
    $stx_replace[] = '';
    $stx_replace[] = '.';
    $stx_replace[] = '';

    $stx = preg_replace($stx_pattern, $stx_replace, strip_tags($stx));

    return $stx;
}

// XSS 관련 태그 제거
function clean_xss_tags($str)
{
    $str = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $str);

    return $str;
}

// unescape nl 얻기
function conv_unescape_nl($str)
{
    $search = array('\\r', '\r', '\\n', '\n');
    $replace = array('', '', "\n", "\n");

    return str_replace($search, $replace, $str);
}

// 회원 삭제
function member_delete($mb_id)
{
    global $config;
    global $gml;

    $sql = " select mb_name, mb_nick, mb_ip, mb_recommend, mb_memo, mb_level from {$gml['member_table']} where mb_id= '".$mb_id."' ";
    $mb = sql_fetch($sql);

    // 이미 삭제된 회원은 제외
    if(preg_match('#^[0-9]{8}.*삭제함#', $mb['mb_memo']))
        return;

    if ($mb['mb_recommend']) {
        $row = sql_fetch(" select count(*) as cnt from {$gml['member_table']} where mb_id = '".addslashes($mb['mb_recommend'])."' ");
        if ($row['cnt'])
            insert_point($mb['mb_recommend'], $config['cf_recommend_point'] * (-1), sprintf(__('Return of recommendation point due to %s member data deletion'), $mb_id), "@member", $mb['mb_recommend'], sprintf(__('Delete %s recommendation'), $mb_id));
    }

    // 회원자료는 정보만 없앤 후 아이디는 보관하여 다른 사람이 사용하지 못하도록 함 : 061025
    $sql = " update {$gml['member_table']} set mb_password = '', mb_level = 1, mb_email = '', mb_homepage = '', mb_tel = '', mb_hp = '', mb_zip = '', mb_country = '', mb_addr1 = '', mb_addr2 = '', mb_birth = '', mb_sex = '', mb_signature = '', mb_memo = '".date('Ymd', GML_SERVER_TIME)." ".__('Deleted')."\n{$mb['mb_memo']}' where mb_id = '{$mb_id}' ";
    sql_query($sql);

    // 포인트 테이블에서 삭제
    sql_query(" delete from {$gml['point_table']} where mb_id = '$mb_id' ");

    // 그룹접근가능 삭제
    sql_query(" delete from {$gml['group_member_table']} where mb_id = '$mb_id' ");

    // 쪽지 삭제
    sql_query(" delete from {$gml['memo_table']} where me_recv_mb_id = '$mb_id' or me_send_mb_id = '$mb_id' ");

    // 스크랩 삭제
    sql_query(" delete from {$gml['scrap_table']} where mb_id = '$mb_id' ");

    // 관리권한 삭제
    sql_query(" delete from {$gml['auth_table']} where mb_id = '$mb_id' ");

    // 그룹관리자인 경우 그룹관리자를 공백으로
    sql_query(" update {$gml['group_table']} set gr_admin = '' where gr_admin = '$mb_id' ");

    // 게시판관리자인 경우 게시판관리자를 공백으로
    sql_query(" update {$gml['board_table']} set bo_admin = '' where bo_admin = '$mb_id' ");

    //소셜로그인에서 삭제 또는 해제
    if(function_exists('social_member_link_delete')){
        social_member_link_delete($mb_id);
    }

    // 아이콘 삭제
    @unlink(GML_DATA_PATH.'/member/'.substr($mb_id,0,2).'/'.$mb_id.'.gif');
}

// 이메일 주소 추출
function get_email_address($email)
{
    preg_match("/[0-9a-z._-]+@[a-z0-9._-]{4,}/i", $email, $matches);

    return $matches[0];
}

// 파일명에서 특수문자 제거
function get_safe_filename($name)
{
    $pattern = '/["\'<>=#&!%\\\\(\)\*\+\?]/';
    $name = preg_replace($pattern, '', $name);

    return $name;
}

// 파일명 치환
function replace_filename($name)
{
    @session_start();
    $ss_id = session_id();
    $usec = get_microtime();
    $file_path = pathinfo($name);
    $ext = $file_path['extension'];
    $return_filename = sha1($ss_id.$_SERVER['REMOTE_ADDR'].$usec);
    if( $ext )
        $return_filename .= '.'.$ext;

    return $return_filename;
}

// 인기검색어 입력
function insert_popular($field, $str)
{
    global $gml;

    if(!in_array('mb_id', $field)) {
        $sql = " insert into {$gml['popular_table']} set pp_word = '{$str}', pp_date = '".GML_TIME_YMD."', pp_ip = '".get_real_client_ip()."' ";
        sql_query($sql, FALSE);
    }
}

// 문자열 암호화
function get_encrypt_string($str)
{
    if(defined('GML_STRING_ENCRYPT_FUNCTION') && GML_STRING_ENCRYPT_FUNCTION) {
        $encrypt = call_user_func(GML_STRING_ENCRYPT_FUNCTION, $str);
    } else {
        $encrypt = sql_password($str);
    }

    return $encrypt;
}

// 비밀번호 비교
function check_password($pass, $hash)
{
    $password = get_encrypt_string($pass);

    return ($password === $hash);
}

// 동일한 host url 인지
function check_url_host($url, $msg='', $return_url=GML_URL, $is_redirect=false)
{
    if(!$msg)
        $msg = __('Can not specify another domain at url.');

    $p = @parse_url($url);
    $host = preg_replace('/:[0-9]+$/', '', $_SERVER['HTTP_HOST']);
    $is_host_check = false;

    // url을 urlencode 를 2번이상하면 parse_url 에서 scheme와 host 값을 가져올수 없는 취약점이 존재함
    if ( $is_redirect && !isset($p['host']) && urldecode($url) != $url ){
        $i = 0;
        while($i <= 3){
            $url = urldecode($url);
            if( urldecode($url) == $url ) break;
            $i++;
        }

        if( urldecode($url) == $url ){
            $p = @parse_url($url);
        } else {
            $is_host_check = true;
        }
    }

    if(stripos($url, 'http:') !== false) {
        if(!isset($p['scheme']) || !$p['scheme'] || !isset($p['host']) || !$p['host'])
            alert(__('Invalid url information.'), $return_url);
    }

    //php 5.6.29 이하 버전에서는 parse_url 버그가 존재함
    //php 7.0.1 ~ 7.0.5 버전에서는 parse_url 버그가 존재함
    if ( $is_redirect && (isset($p['host']) && $p['host']) ) {
        $bool_ch = false;
        foreach( array('user','host') as $key) {
            if ( isset( $p[ $key ] ) && strpbrk( $p[ $key ], ':/?#@' ) ) {
                $bool_ch = true;
            }
        }
        if( $bool_ch ){
            $regex = '/https?\:\/\/'.$host.'/i';
            if( ! preg_match($regex, $url) ){
                $is_host_check = true;
            }
        }
    }

    if ((isset($p['scheme']) && $p['scheme']) || (isset($p['host']) && $p['host']) || $is_host_check) {
        //if ($p['host'].(isset($p['port']) ? ':'.$p['port'] : '') != $_SERVER['HTTP_HOST']) {
        if ( ($p['host'] != $host) || $is_host_check ) {
            echo '<script>'.PHP_EOL;
            echo 'alert("'.__('Can not specify another domain at url.').'");'.PHP_EOL;
            echo 'document.location.href = "'.$return_url.'";'.PHP_EOL;
            echo '</script>'.PHP_EOL;
            echo '<noscript>'.PHP_EOL;
            echo '<p>'.$msg.'</p>'.PHP_EOL;
            echo '<p><a href="'.$return_url.'">'.__('Back').'</a></p>'.PHP_EOL;
            echo '</noscript>'.PHP_EOL;
            exit;
        }
    }
}

// QUERY STRING 에 포함된 XSS 태그 제거
function clean_query_string($query, $amp=true)
{
    $qstr = trim($query);

    parse_str($qstr, $out);

    if(is_array($out)) {
        $q = array();

        foreach($out as $key=>$val) {
            $key = strip_tags(trim($key));
            $val = trim($val);

            switch($key) {
                case 'wr_id':
                    $val = (int)preg_replace('/[^0-9]/', '', $val);
                    $q[$key] = $val;
                    break;
                case 'sca':
                    $val = clean_xss_tags($val);
                    $q[$key] = $val;
                    break;
                case 'sfl':
                    $val = preg_replace("/[\<\>\'\"\\\'\\\"\%\=\(\)\s]/", "", $val);
                    $q[$key] = $val;
                    break;
                case 'stx':
                    $val = get_search_string($val);
                    $q[$key] = $val;
                    break;
                case 'sst':
                    $val = preg_replace("/[\<\>\'\"\\\'\\\"\%\=\(\)\s]/", "", $val);
                    $q[$key] = $val;
                    break;
                case 'sod':
                    $val = preg_match("/^(asc|desc)$/i", $val) ? $val : '';
                    $q[$key] = $val;
                    break;
                case 'sop':
                    $val = preg_match("/^(or|and)$/i", $val) ? $val : '';
                    $q[$key] = $val;
                    break;
                case 'spt':
                    $val = (int)preg_replace('/[^0-9]/', '', $val);
                    $q[$key] = $val;
                    break;
                case 'page':
                    $val = (int)preg_replace('/[^0-9]/', '', $val);
                    $q[$key] = $val;
                    break;
                case 'w':
                    $val = substr($val, 0, 2);
                    $q[$key] = $val;
                    break;
                case 'bo_table':
                    $val = preg_replace('/[^a-z0-9_]/i', '', $val);
                    $val = substr($val, 0, 20);
                    $q[$key] = $val;
                    break;
                case 'gr_id':
                    $val = preg_replace('/[^a-z0-9_]/i', '', $val);
                    $q[$key] = $val;
                    break;
                default:
                    $val = clean_xss_tags($val);
                    $q[$key] = $val;
                    break;
            }
        }

        if($amp)
            $sep = '&amp;';
        else
            $sep ='&';

        $str = http_build_query($q, '', $sep);
    } else {
        $str = clean_xss_tags($qstr);
    }

    return $str;
}

function get_params_merge_url($params){
    $p = @parse_url(GML_URL);
    $href = $p['scheme'].'://'.$p['host'];
    if(isset($p['port']) && $p['port'])
        $href .= ':'.$p['port'];
    //$href .= explode('?', $_SERVER['REQUEST_URI'])[0];
    if( $tmp = explode('?', $_SERVER['REQUEST_URI']) ){
        if( isset($tmp[0]) && $tmp[0] )
            $href .= $tmp[0];
    }
    $q = array();
    if($_SERVER['QUERY_STRING']) {
        foreach($_GET as $key=>$val) {
            $key = strip_tags($key);
            $val = strip_tags($val);

            if($key && $val)
                $q[$key] = $val;
        }
    }

    if( is_array($params) ){
        $q = array_merge($q, $params);
    }

    $query = http_build_query($q, '', '&amp;');
    $href .= '?'.$query;

    return $href;
}

function get_device_change_url()
{
    $q = array();
    $device = (GML_IS_MOBILE ? 'pc' : 'mobile');
    $q['device'] = $device;

    return get_params_merge_url($q);
}

// 스킨 path
function get_skin_path($dir, $skin)
{
    global $config;

    $theme_path = '';
    $cf_theme = trim($config['cf_theme']);

    $theme_path = GML_PATH.'/'.GML_THEME_DIR.'/'.$cf_theme;
    $upper_skin_dir = GML_IS_MOBILE ? (GML_MOBILE_DIR.'/'.GML_SKIN_DIR) : GML_SKIN_DIR;

    $skin_path = $theme_path.'/'.$upper_skin_dir.'/'.$dir.'/'.$skin;

    if(!is_dir($skin_path)) {
        $skin_path_pc = $theme_path.'/'.GML_SKIN_DIR.'/'.$dir.'/'.$skin;
		if(!is_dir($skin_path_pc)) {
			$skin_path = $theme_path.'/'.$upper_skin_dir.'/'.$dir.'/'.'basic';
		} else {
			$skin_path = $skin_path_pc;
		}
    }

    return $skin_path;
}

// 스킨 url
function get_skin_url($dir, $skin)
{
    $skin_path = get_skin_path($dir, $skin);

    return str_replace(GML_PATH, GML_URL, $skin_path);
}

// 발신번호 유효성 체크
function check_vaild_callback($callback){
   $_callback = preg_replace('/[^0-9]/','', $callback);

   /**
   * 1588 로시작하면 총8자리인데 7자리라 차단
   * 02 로시작하면 총9자리 또는 10자리인데 11자리라차단
   * 1366은 그자체가 원번호이기에 다른게 붙으면 차단
   * 030으로 시작하면 총10자리 또는 11자리인데 9자리라차단
   */

   if( substr($_callback,0,4) == '1588') if( strlen($_callback) != 8) return false;
   if( substr($_callback,0,2) == '02')   if( strlen($_callback) != 9  && strlen($_callback) != 10 ) return false;
   if( substr($_callback,0,3) == '030')  if( strlen($_callback) != 10 && strlen($_callback) != 11 ) return false;

   if( !preg_match("/^(02|0[3-6]\d|01(0|1|3|5|6|7|8|9)|070|080|007)\-?\d{3,4}\-?\d{4,5}$/",$_callback) &&
       !preg_match("/^(15|16|18)\d{2}\-?\d{4,5}$/",$_callback) ){
             return false;
   } else if( preg_match("/^(02|0[3-6]\d|01(0|1|3|5|6|7|8|9)|070|080)\-?0{3,4}\-?\d{4}$/",$_callback )) {
             return false;
   } else {
             return true;
   }
}

function is_url_base64_encoded($s)
{
    $s_encode = strtr($s, '._-', '+/=');

    if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s_encode)) return false;

    $decoded = base64_decode($s_encode, true);
    if(false === $decoded) return false;

    if(base64_encode($decoded) != $s_encode) return false;

    return true;
}

// 문자열 암복호화
class str_encrypt
{
    var $salt;
    var $lenght;

    function __construct($salt='')
    {
        if(!$salt)
            $this->salt = md5(preg_replace('/[^0-9A-Za-z]/', substr(GML_MYSQL_USER, -1), GML_ENCRYPT_ADD_STRING.$_SERVER['SERVER_SOFTWARE'].$_SERVER['DOCUMENT_ROOT'] ));
        else
            $this->salt = $salt;

        $this->length = strlen($this->salt);
    }

    function encrypt($str)
    {
        $length = strlen($str);
        $result = '';

        for($i=0; $i<$length; $i++) {
            $char    = substr($str, $i, 1);
            $keychar = substr($this->salt, ($i % $this->length) - 1, 1);
            $char    = chr(ord($char) + ord($keychar));
            $result .= $char;
        }

        return strtr(base64_encode($result) , '+/=', '._-');
    }

    function decrypt($str) {
        $result = '';
        $str    = base64_decode(strtr($str, '._-', '+/='));
        $length = strlen($str);

        for($i=0; $i<$length; $i++) {
            $char    = substr($str, $i, 1);
            $keychar = substr($this->salt, ($i % $this->length) - 1, 1);
            $char    = chr(ord($char) - ord($keychar));
            $result .= $char;
        }

        return $result;
    }
}

// 불법접근을 막도록 토큰을 생성하면서 토큰값을 리턴
function get_write_token($bo_table)
{
    $token = md5(uniqid(rand(), true));
    set_session('ss_write_'.$bo_table.'_token', $token);

    return $token;
}


// POST로 넘어온 토큰과 세션에 저장된 토큰 비교
function check_write_token($bo_table)
{
    if(!$bo_table)
        alert(__('Please use the correct method.'), GML_URL);

    $token = get_session('ss_write_'.$bo_table.'_token');
    set_session('ss_write_'.$bo_table.'_token', '');

    if(!$token || !$_REQUEST['token'] || $token != $_REQUEST['token'])
        alert(__('Please use the correct method.'), GML_URL);

    return true;
}

function get_head_title($title){
    global $gml;

    if( isset($gml['board_title']) && $gml['board_title'] ){
        $title = $gml['board_title'];
    }

    return $title;
}

function is_use_email_certify(){
    global $config;

    if( $config['cf_use_email_certify'] && function_exists('social_is_login_check') ){
        if( $config['cf_social_login_use'] && (get_session('ss_social_provider') || social_is_login_check()) ){      //소셜 로그인을 사용한다면
            $tmp = (defined('GML_SOCIAL_CERTIFY_MAIL') && GML_SOCIAL_CERTIFY_MAIL) ? 1 : 0;
            return $tmp;
        }
    }

    return $config['cf_use_email_certify'];
}

function get_real_client_ip(){

    if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        return $_SERVER['HTTP_X_FORWARDED_FOR'];

    return $_SERVER['REMOTE_ADDR'];
}

function get_call_func_cache($func, $args=array()){

    static $cache = array();

    $key = md5(serialize($args));

    if( isset($cache[$func]) && isset($cache[$func][$key]) ){
        return $cache[$func][$key];
    }

    $result = null;

    try{
        $cache[$func][$key] = $result = call_user_func_array($func, $args);
    } catch (Exception $e) {
        return null;
    }

    return $result;
}

// include 하는 경로에 data file 경로가 포함되어 있는지 체크합니다.
function is_include_path_check($path='', $is_input='')
{
    if( $path ){

        if( ! in_array($path, get_allow_head_filename()) && ! in_array($path, get_allow_tail_filename()) ){
            return false;
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);

        if($extension && preg_match('/(jpg|jpeg|png|gif|bmp|conf)$/i', $extension)) {
            return false;
        }
    }

    return true;
}

function option_array_checked($option, $arr=array()){
    $checked = '';

    if( !is_array($arr) ){
        $arr = explode(',', $arr);
    }

    if ( !empty($arr) && in_array($option, (array) $arr) ){
        $checked = 'checked="checked"';
    }

    return $checked;
}

// 짧은 주소 형식으로 만들어서 가져온다.
function get_pretty_url($folder, $no='', $query_string='')
{
    global $gml, $config;

    static $boards = array();

    if( ! $boards ){
        $sql = " select bo_table from {$gml['board_table']} ";
        $result = sql_query($sql);

        while ($row = sql_fetch_array($result)) {
            $boards[] = $row['bo_table'];
        }
    }

	// use shortten url
	if($config['cf_bbs_rewrite']) {
		if(in_array($folder, $boards)) {
			$url = GML_URL. '/'. $folder;
			if($no) {
				$url .= '/'. $no;
			}

		} else {
            $url = GML_URL. '/'.$folder;
			if($no) {
				$no_array = explode("=", $no);
				$no_value = end($no_array);
				$url .= '/'. $no_value;
			}
		}
        if($query_string) {
            // If the first character of the query string is '&', replace it with '?'.
            if(substr($query_string, 0, 1) == '&') {
                $url .= preg_replace("/\&amp;/", "?", $query_string, 1);
            } else {
                $url .= '?'. $query_string;
            }
        }
	} else { // don't use shortten url
		if(in_array($folder, $boards)) {
			$url = GML_BBS_URL. '/board.php?bo_table='. $folder;
			if($no) {
				$url .= '&amp;wr_id='. $no;
			}
			if($query_string) {
				$url .= '&amp;'. $query_string;
			}
		} else {
			$url = GML_BBS_URL. '/'.$folder.'.php';
            if($no) {
				$url .= '?'. $no;
			}
            if($query_string) {
                $url .= ($no ? '?' : '&amp;'). $query_string;
			}
		}
	}

	return $url;
}

// convert url to config
function adjust_url_to_config($url)
{
	global $config;

    // convert original url to shortten url
	if(strpos($url, '.php') !== false && $config['cf_bbs_rewrite']) {
		$url_element = explode('=', $url);
		$id = end($url_element);

		if(strpos($url, 'bo_table') !== false) {
			$url = GML_URL. '/'. $id;
		} else if(strpos($url, 'gr_id') !== false) {
			$url = GML_URL. '/group/'. $id;
		} else if(strpos($url, 'co_id') !== false) {
			$url = GML_URL. '/content/'. $id;
		}
    // convert shortten url to original url
    } else if(strpos($url, '.php') === false && !$config['cf_bbs_rewrite']) {
		$url_element = str_replace(GML_URL, '', $url);
		$url_element2 = explode('/', $url_element);
		$id = str_replace('/', '', end($url_element2));

		if(strpos($url_element, 'content') !== false) {
			$url = GML_BBS_URL. '/content.php?co_id='. $id;
		} else if(strpos($url_element, 'group') !== false) {
			$url = GML_BBS_URL. '/group.php?gr_id='. $id;
		} else if(!$url_element && $id) {
			$url = GML_BBS_URL. '/board.php?bo_table='. $id;
		}
	}

	return $url;
}

// 데이터 용량 단위 구분 함수
function exchange_data($val, $type=null, $float=0) {
    if(empty($val)) return;
    $cal = 1024;
    $type = strtoupper($type);  // 대문자로 처리

    $arr = array("B", "KB", "MB", "GB", "TB", "PB");  // 데이터 단위 배열화
    $index = array_search($type, $arr); // 입력받은 출력 단위가 배열에 있는지 체크
    // 출력 단위에 맞추어 처리
    if($index > 0) {
        // 해당하는 단위 만큼 처리
        for($i = 0; $i < $index; $i++) {
            $val /= $cal;
        }
        // 선택된 단위의 1 미만이면 소수점 자리 강제 노출
        if(intval($val) < 1) {
            $float = 2;
        }
    } else {
        $index = 0;
        // KB 단위 이상 일때 만 처리
        if($val >= $cal) {
            while(true) {
                // 루프횟수 체크
                $index++;
                // 단위 분할처리
                $val = $val / $cal;
                // 더이상 분할되지 않거나 지정된 분할 단위에 도달하면 중단
                if($val < $cal || $index >= count($arr)-1) break;
            }
        }
    }
    // 출력 단위 가져옴
    $unit = $arr[$index];

    return number_format($val, $float)." ".$unit;
}
?>
