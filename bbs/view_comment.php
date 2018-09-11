<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages
include_once(GML_CAPTCHA_PATH.'/captcha.lib.php');

$captcha_html = "";
if ($is_guest && $board['bo_comment_level'] < 2) {
    $captcha_html = captcha_html('_comment');
}

@include_once($board_skin_path.'/view_comment.head.skin.php');

$list = array();

$is_comment_write = false;
if ($member['mb_level'] >= $board['bo_comment_level'])
    $is_comment_write = true;

// Clean up php variables common to skin
$depth_length = GML_IS_MOBILE ? 20 : 50;

// Comment Print
//$sql = " select * from {$write_table} where wr_parent = '{$wr_id}' and wr_is_comment = 1 order by wr_comment desc, wr_comment_reply ";
$sql = " select * from $write_table where wr_parent = '$wr_id' and wr_is_comment = 1 order by wr_comment, wr_comment_reply ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++)
{
    $list[$i] = $row;
    $list[$i]['profile_img'] = get_member_profile_img($row['mb_id']);

    //$list[$i]['name'] = get_sideview($row['mb_id'], cut_str($row['wr_name'], 20, ''), $row['wr_email'], $row['wr_homepage']);

    $tmp_name = get_text(cut_str($row['wr_name'], $config['cf_cut_name'])); // Print name only by the number of digits set
    if ($board['bo_use_sideview'])
        $list[$i]['name'] = get_sideview($row['mb_id'], $tmp_name, $row['wr_email'], $row['wr_homepage']);
    else
        $list[$i]['name'] = '<span class="'.($row['mb_id']?'member':'guest').'">'.$tmp_name.'</span>';



    // Cut consecutive typed characters without spaces (Reference way Board . way.co.kr)
    //$list[$i]['content'] = eregi_replace("[^ \n<>]{130}", "\\0\n", $row['wr_content']);

    $list[$i]['content'] = $list[$i]['content1']= __('This is a secret Comment.');
    if (!strstr($row['wr_option'], 'secret') ||
        $is_admin ||
        ($write['mb_id']===$member['mb_id'] && $member['mb_id']) ||
        ($row['mb_id']===$member['mb_id'] && $member['mb_id'])) {
        $list[$i]['content1'] = $row['wr_content'];
        $list[$i]['content'] = conv_content($row['wr_content'], 0, 'wr_content');
        $list[$i]['content'] = search_font($stx, $list[$i]['content']);
    } else {
        $ss_name = 'ss_secret_comment_'.$bo_table.'_'.$list[$i]['wr_id'];

        if(!get_session($ss_name))
            $list[$i]['content'] = '<a href='.GML_BBS_URL.'"/password.php?w=sc&amp;bo_table='.$bo_table.'&amp;wr_id='.$list[$i]['wr_id'].$qstr.'" class="s_cmt">'.__('Confirm comments').'</a>';
        else {
            $list[$i]['content'] = conv_content($row['wr_content'], 0, 'wr_content');
            $list[$i]['content'] = search_font($stx, $list[$i]['content']);
        }
    }

    $list[$i]['datetime'] = substr($row['wr_datetime'],2,14);

    // If you are not an administrator, hide the intermediate IP addresses and show them.
    $list[$i]['ip'] = $row['wr_ip'];
    if (!$is_admin)
        $list[$i]['ip'] = preg_replace("/([0-9]+).([0-9]+).([0-9]+).([0-9]+)/", GML_IP_DISPLAY, $row['wr_ip']);

    $list[$i]['is_reply'] = false;
    $list[$i]['is_edit'] = false;
    $list[$i]['is_del']  = false;
    if ($is_comment_write || $is_admin)
    {
        $token = '';

        if ($member['mb_id'])
        {
            if ($row['mb_id'] === $member['mb_id'] || $is_admin)
            {
                set_session('ss_delete_comment_'.$row['wr_id'].'_token', $token = uniqid(time()));
                $list[$i]['del_link']  = GML_BBS_URL.'/delete_comment.php?bo_table='.$bo_table.'&amp;comment_id='.$row['wr_id'].'&amp;token='.$token.'&amp;page='.$page.$qstr;
                $list[$i]['is_edit']   = true;
                $list[$i]['is_del']    = true;
            }
        }
        else
        {
            if (!$row['mb_id']) {
                $list[$i]['del_link'] = GML_BBS_URL.'/password.php?w=x&amp;bo_table='.$bo_table.'&amp;comment_id='.$row['wr_id'].'&amp;page='.$page.$qstr;
                $list[$i]['is_del']   = true;
            }
        }

        if (strlen($row['wr_comment_reply']) < 5)
            $list[$i]['is_reply'] = true;
    }

    // 05.05.22
    // Responsive comments can not be modified or deleted
    if ($i > 0 && !$is_admin)
    {
        if ($row['wr_comment_reply'])
        {
            $tmp_comment_reply = substr($row['wr_comment_reply'], 0, strlen($row['wr_comment_reply']) - 1);
            if ($tmp_comment_reply == $list[$i-1]['wr_comment_reply'])
            {
                $list[$i-1]['is_edit'] = false;
                $list[$i-1]['is_del'] = false;
            }
        }
    }

    // Display comments
    $list[$i]['comment_id'] = $list[$i]['wr_id'];
    $list[$i]['cmt_depth'] = strlen($list[$i]['wr_comment_reply']) * $depth_length;
    /*
    if (strstr($list[$i]['wr_option'], "secret")) {
        $str = $str;
    }
    */
    $list[$i]['comment'] = preg_replace("/\[\<a\s.*href\=\"(http|https|ftp|mms)\:\/\/([^[:space:]]+)\.(mp3|wma|wmv|asf|asx|mpg|mpeg)\".*\<\/a\>\]/i", "<script>doc_write(obj_movie('$1://$2.$3'));</script>", $list[$i]['content']);
    $list[$i]['cmt_sv'] = count($list) - $i + 1; // Resetting the Comment header z-index ie Troubleshooting side view overlap below 8
    $list[$i]['wr_name'] = get_text($list[$i]['wr_name']);
    $list[$i]['format_datetime'] = date('Y-m-d\TH:i:s+09:00', strtotime($list[$i]['datetime']));
    $list[$i]['is_secret'] = strstr($list[$i]['wr_option'], "secret") == true ? true : false;

    if($list[$i]['is_reply'] || $list[$i]['is_edit'] || $list[$i]['is_del']) {
        $query_string = clean_query_string($_SERVER['QUERY_STRING']);

        if($w == 'cu') {
            $sql = " select wr_id, wr_content, mb_id from $write_table where wr_id = '$c_id' and wr_is_comment = '1' ";
            $cmt = sql_fetch($sql);
            if (!($is_admin || ($member['mb_id'] == $cmt['mb_id'] && $cmt['mb_id'])))
                $cmt['wr_content'] = '';
            $c_wr_content = $cmt['wr_content'];
        }

        $list[$i]['c_reply_href'] = GML_BBS_URL. '/board.php?'.$query_string.'&amp;c_id='.$list[$i]['comment_id'].'&amp;w=c#bo_vc_w';
        $list[$i]['c_edit_href'] = GML_BBS_URL. '/board.php?'.$query_string.'&amp;c_id='.$list[$i]['comment_id'].'&amp;w=cu#bo_vc_w';
    }

    $list[$i]['comment_url'] = GML_URL. '/'. $bo_table. '/'. $wr_id. '?#c_'. $list[$i]['comment_id'];
}

//  Set Comment limit length
if ($is_admin)
{
    $comment_min = $comment_max = 0;
}
else
{
    $comment_min = (int)$board['bo_comment_min'];
    $comment_max = (int)$board['bo_comment_max'];
}

$comment_action_url = https_url(GML_BBS_DIR)."/write_comment_update.php";

$no_comment = $i < 1 ? '<p id="bo_vc_empty">'.__('No comments').'</p>' : '';

// 댓글 쓰기
if ($is_comment_write && $w == '') $w = 'c';
$get_cookie_sns_name = get_cookie("ck_sns_name");
$use_sns = $board['bo_use_sns'] && ($config['cf_facebook_appid'] || $config['cf_twitter_key']);

include_once($board_skin_path.'/view_comment.skin.php');

include_once(GML_THEME_JS_PATH.'/view_comment_js.php');

if (!$member['mb_id']) // If non-members
    echo '<script src="'.GML_JS_URL.'/md5.js"></script>'."\n";

@include_once($board_skin_path.'/view_comment.tail.skin.php');
?>
