<?php
define('GML_CAPTCHA', true);
include_once('./_common.php');
include_once(GML_CAPTCHA_PATH.'/captcha.lib.php');

// 토큰체크
$comment_token = trim(get_session('ss_comment_token'));
set_session('ss_comment_token', '');
if(!trim($_POST['token']) || !$comment_token || $comment_token != $_POST['token'])
    alert(__('Please use the correct method.'));

// 090710
if (substr_count($wr_content, "&#") > 50) {
    alert(__('Content contains many invalid codes.'));
    exit;
}

@include_once($board_skin_path.'/write_comment_update.head.skin.php');

$w = $_POST["w"];
$wr_name  = trim($_POST['wr_name']);
$wr_email = '';
if (!empty($_POST['wr_email']))
    $wr_email = get_email_address(trim($_POST['wr_email']));

// 비회원의 경우 이름이 누락되는 경우가 있음
if ($is_guest) {
    if ($wr_name == '')
        alert(__('You must enter a name.'));
    if(!chk_captcha())
        alert(__('The Captcha certification is invalid.'));
}

if ($w == "c" || $w == "cu") {
    if ($member['mb_level'] < $board['bo_comment_level'])
        alert(__('You do not have permission to write comments.'));
}
else
    alert(sprintf(__('%s parameter is wrong'), 'w'));

// 세션의 시간 검사
// 4.00.15 - 댓글 수정시 연속 게시물 등록 메시지로 인한 오류 수정
if ($w == 'c' && $_SESSION['ss_datetime'] >= (GML_SERVER_TIME - $config['cf_delay_sec']) && !$is_admin)
    alert(__('The post can not be writed in succession too soon.'));

set_session('ss_datetime', GML_SERVER_TIME);

$wr = get_write($write_table, $wr_id);
if (empty($wr['wr_id']))
    alert(__('The post does not exist.').'\\n'.__('The post has been deleted or moved.'));


// "인터넷옵션 > 보안 > 사용자정의수준 > 스크립팅 > Action 스크립팅 > 사용 안 함" 일 경우의 오류 처리
// 이 옵션을 사용 안 함으로 설정할 경우 어떤 스크립트도 실행 되지 않습니다.
//if (!trim($_POST["wr_content"])) die ("내용을 입력하여 주십시오.");

if ($is_member)
{
    $mb_id = $member['mb_id'];
    // 4.00.13 - 실명 사용일때 댓글에 닉네임으로 입력되던 오류를 수정
    $wr_name = addslashes(clean_xss_tags($board['bo_use_name'] ? $member['mb_name'] : $member['mb_nick']));
    $wr_password = $member['mb_password'];
    $wr_email = addslashes($member['mb_email']);
    $wr_homepage = addslashes(clean_xss_tags($member['mb_homepage']));
}
else
{
    $mb_id = '';
    $wr_password = get_encrypt_string($wr_password);
}

if ($w == 'c') // 댓글 입력
{
    /*
    if ($member[mb_point] + $board[bo_comment_point] < 0 && !$is_admin)
        alert('보유하신 포인트('.number_format($member[mb_point]).')가 없거나 모자라서 댓글쓰기('.number_format($board[bo_comment_point]).')가 불가합니다.\\n\\n포인트를 적립하신 후 다시 댓글을 써 주십시오.');
    */
    // 댓글쓰기 포인트설정시 회원의 포인트가 음수인 경우 댓글을 쓰지 못하던 버그를 수정 (곱슬최씨님)
    $tmp_point = ($member['mb_point'] > 0) ? $member['mb_point'] : 0;
    if ($tmp_point + $board['bo_comment_point'] < 0 && !$is_admin)
        alert(sprintf(__('Your point (%s) is missing or enough points, so you can not comment (%s).'), number_format($member['mb_point']), number_format($board['bo_comment_point'])).'\\n\\n'.__('Collect points and comment again.'));

    // 댓글 답변
    if ($comment_id)
    {
        $sql = " select wr_id, wr_parent, wr_comment, wr_comment_reply from $write_table
                    where wr_id = '$comment_id' ";
        $reply_array = sql_fetch($sql);
        if (!$reply_array['wr_id'])
            alert(__('There are no comments to answer.').'\\n\\n'.__('The comment might have been deleted while replying.'));

        if($wr['wr_parent'] != $reply_array['wr_parent'])
            alert(__('Unable to register comments.'));

        $tmp_comment = $reply_array['wr_comment'];

        if (strlen($reply_array['wr_comment_reply']) == 5)
            alert(__("You can't answer any more.").'\\n\\n'.__('You can answer only in five stages.'));

        $reply_len = strlen($reply_array['wr_comment_reply']) + 1;
        if ($board['bo_reply_order']) {
            $begin_reply_char = 'A';
            $end_reply_char = 'Z';
            $reply_number = +1;
            $sql = " select MAX(SUBSTRING(wr_comment_reply, $reply_len, 1)) as reply
                        from $write_table
                        where wr_parent = '$wr_id'
                        and wr_comment = '$tmp_comment'
                        and SUBSTRING(wr_comment_reply, $reply_len, 1) <> '' ";
        }
        else
        {
            $begin_reply_char = 'Z';
            $end_reply_char = 'A';
            $reply_number = -1;
            $sql = " select MIN(SUBSTRING(wr_comment_reply, $reply_len, 1)) as reply
                        from $write_table
                        where wr_parent = '$wr_id'
                        and wr_comment = '$tmp_comment'
                        and SUBSTRING(wr_comment_reply, $reply_len, 1) <> '' ";
        }
        if ($reply_array['wr_comment_reply'])
            $sql .= " and wr_comment_reply like '{$reply_array['wr_comment_reply']}%' ";
        $row = sql_fetch($sql);

        if (!$row['reply'])
            $reply_char = $begin_reply_char;
        else if ($row['reply'] == $end_reply_char) // A~Z은 26 입니다.
            alert(__("You can't answer any more.").'\\n\\n'.__('You can answer only in 26 questions.'));
        else
            $reply_char = chr(ord($row['reply']) + $reply_number);

        $tmp_comment_reply = $reply_array['wr_comment_reply'] . $reply_char;
    }
    else
    {
        $sql = " select max(wr_comment) as max_comment from $write_table
                    where wr_parent = '$wr_id' and wr_is_comment = 1 ";
        $row = sql_fetch($sql);
        //$row[max_comment] -= 1;
        $row['max_comment'] += 1;
        $tmp_comment = $row['max_comment'];
        $tmp_comment_reply = '';
    }

    $wr_subject = get_text(stripslashes($wr['wr_subject']));

    $sql = " insert into $write_table
                set ca_name = '{$wr['ca_name']}',
                     wr_option = '$wr_secret',
                     wr_num = '{$wr['wr_num']}',
                     wr_reply = '',
                     wr_parent = '$wr_id',
                     wr_is_comment = 1,
                     wr_comment = '$tmp_comment',
                     wr_comment_reply = '$tmp_comment_reply',
                     wr_subject = '',
                     wr_content = '$wr_content',
                     mb_id = '$mb_id',
                     wr_password = '$wr_password',
                     wr_name = '$wr_name',
                     wr_email = '$wr_email',
                     wr_homepage = '$wr_homepage',
                     wr_datetime = '".GML_TIME_YMDHIS."',
                     wr_last = '',
                     wr_ip = '{$_SERVER['REMOTE_ADDR']}',
                     wr_1 = '$wr_1',
                     wr_2 = '$wr_2',
                     wr_3 = '$wr_3',
                     wr_4 = '$wr_4',
                     wr_5 = '$wr_5',
                     wr_6 = '$wr_6',
                     wr_7 = '$wr_7',
                     wr_8 = '$wr_8',
                     wr_9 = '$wr_9',
                     wr_10 = '$wr_10' ";
    sql_query($sql);

    $comment_id = sql_insert_id();

    // 원글에 댓글수 증가 & 마지막 시간 반영
    sql_query(" update $write_table set wr_comment = wr_comment + 1, wr_last = '".GML_TIME_YMDHIS."' where wr_id = '$wr_id' ");

    // 새글 INSERT
    sql_query(" insert into {$gml['board_new_table']} ( bo_table, wr_id, wr_parent, bn_datetime, mb_id ) values ( '$bo_table', '$comment_id', '$wr_id', '".GML_TIME_YMDHIS."', '{$member['mb_id']}' ) ");

    // 댓글 1 증가
    sql_query(" update {$gml['board_table']} set bo_count_comment = bo_count_comment + 1 where bo_table = '$bo_table' ");

    // 포인트 부여
    insert_point($member['mb_id'], $board['bo_comment_point'], "{$board['bo_subject']} {$wr_id}-{$comment_id} ".__('Write Comment'), $bo_table, $comment_id, 'comment');

    // 알림
    // 1뎁스의 댓글이면 원글 작성자에게 알림을 보냄
    // 2뎁스이상 댓글이면 바로 상위의 댓글 작성자에게 알림을 보냄
    $insert_comment = get_write($write_table, $comment_id);
    $wr_comment_reply = $insert_comment['wr_comment_reply'];
    $cmt_depth = strlen(trim($wr_comment_reply));
    $rel_mb_id = $wr['mb_id'];
    $rel_wr_id = $wr_id;
    if($cmt_depth > 0) {
        $cmt_upper_level = substr($wr_comment_reply, 0, -1);
        $sql = " select * from {$write_table}
                    where wr_parent = {$insert_comment['wr_parent']}
                    and wr_comment = {$insert_comment['wr_comment']}
                    and wr_comment_reply = '{$cmt_upper_level}' ";
        $upper_row = sql_fetch($sql);
        $rel_mb_id = $upper_row['mb_id'];
        $rel_wr_id = $upper_row['wr_id'];
    }
    add_notice("comment", $mb_id, $rel_mb_id, $bo_table, $comment_id, $rel_wr_id);

    // 메일발송 사용
    if ($config['cf_email_use'] && $board['bo_use_email'])
    {
        // 관리자의 정보를 얻고
        $super_admin = get_admin('super');
        $group_admin = get_admin('group');
        $board_admin = get_admin('board');

        $wr_content = nl2br(get_text(stripslashes(__('Original writing')."\n{$wr['wr_subject']}\n\n\n".__('Comment')."\n$wr_content")));

        $warr = array( ''=>__('Enter'), 'u'=>__('Edit'), 'r'=>__('Reply'), 'c'=>__('Comment'), 'cu'=>__('Edit Comment') );
        $str = $warr[$w];

        $subject = '['.$config['cf_title'].'] '.get_board_gettext_titles($board['bo_subject']).' '.sprintf(__('Board, %s message posts.'), $str);
        // 4.00.15 - 메일로 보내는 댓글의 바로가기 링크 수정
        $link_url = get_pretty_url($bo_table, $wr_id, $qstr."#c_".$comment_id);

        include_once(GML_LIB_PATH.'/mailer.lib.php');

        ob_start();
        include_once ('./write_update_mail.php');
        $content = ob_get_contents();
        ob_end_clean();

        $array_email = array();
        // 게시판관리자에게 보내는 메일
        if ($config['cf_email_wr_board_admin']) $array_email[] = $board_admin['mb_email'];
        // 게시판그룹관리자에게 보내는 메일
        if ($config['cf_email_wr_group_admin']) $array_email[] = $group_admin['mb_email'];
        // 최고관리자에게 보내는 메일
        if ($config['cf_email_wr_super_admin']) $array_email[] = $super_admin['mb_email'];

        // 원글게시자에게 보내는 메일
        if ($config['cf_email_wr_write']) $array_email[] = $wr['wr_email'];

        // 댓글 쓴 모든이에게 메일 발송이 되어 있다면 (자신에게는 발송하지 않는다)
        if ($config['cf_email_wr_comment_all']) {
            $sql = " select distinct wr_email from {$write_table}
                        where wr_email not in ( '{$wr['wr_email']}', '{$member['mb_email']}', '' )
                        and wr_parent = '$wr_id' ";
            $result = sql_query($sql);
            while ($row=sql_fetch_array($result))
                $array_email[] = $row['wr_email'];
        }

        // 중복된 메일 주소는 제거
        $unique_email = array_unique($array_email);
        $unique_email = array_values($unique_email);
        for ($i=0; $i<count($unique_email); $i++) {
            mailer($wr_name, $wr_email, $unique_email[$i], $subject, $content, 1);
        }
    }

    // SNS 등록
    include_once("./write_comment_update.sns.php");
    if($wr_facebook_user || $wr_twitter_user) {
        $sql = " update $write_table
                    set wr_facebook_user = '$wr_facebook_user',
                        wr_twitter_user  = '$wr_twitter_user'
                    where wr_id = '$comment_id' ";
        sql_query($sql);
    }
}
else if ($w == 'cu') // Edit comment
{
    $sql = " select mb_id, wr_password, wr_comment, wr_comment_reply from $write_table
                where wr_id = '$comment_id' ";
    $comment = $reply_array = sql_fetch($sql);
    $tmp_comment = $reply_array['wr_comment'];

    $len = strlen($reply_array['wr_comment_reply']);
    if ($len < 0) $len = 0;
    $comment_reply = substr($reply_array['wr_comment_reply'], 0, $len);
    //print_r2($GLOBALS); exit;

    if ($is_admin == 'super') // 최고관리자 통과
        ;
    else if ($is_admin == 'group') { // 그룹관리자
        $mb = get_member($comment['mb_id']);
        if ($member['mb_id'] === $group['gr_admin']) { // 자신이 관리하는 그룹인가?
            if ($member['mb_level'] >= $mb['mb_level']) // 자신의 레벨이 크거나 같다면 통과
                ;
            else
                alert(__('This comment is higher than the group admin authority and can not be modified.'));
        } else
            alert(__('You can not modify the comment because it is not a bulletin board for the group you manage.'));
    } else if ($is_admin == 'board') { // 게시판관리자이면
        $mb = get_member($comment['mb_id']);
        if ($member['mb_id'] === $board['bo_admin']) { // 자신이 관리하는 게시판인가?
            if ($member['mb_level'] >= $mb['mb_level']) // 자신의 레벨이 크거나 같다면 통과
                ;
            else
                alert(__("You can not modify this comment because it is a comment from a member who is above the board manager's authority."));
        } else
            alert(__("You can not modify the comment because it is not a bulletin you manage."));
    } else if ($member['mb_id']) {
        if ($member['mb_id'] !== $comment['mb_id'])
            alert(__("Can not modify because it is not your own post."));
    } else {
        if($comment['wr_password'] != $wr_password)
            alert(__("You do not have permission to modify comments."));
    }

    $sql = " select count(*) as cnt from $write_table
                where wr_comment_reply like '$comment_reply%'
                and wr_id <> '$comment_id'
                and wr_parent = '$wr_id'
                and wr_comment = '$tmp_comment'
                and wr_is_comment = 1 ";
    $row = sql_fetch($sql);
    if ($row['cnt'] && !$is_admin)
        alert(__("This comment can not be modified because there is an answer comment associated with it."));

    $sql_ip = "";
    if (!$is_admin)
        $sql_ip = " , wr_ip = '{$_SERVER['REMOTE_ADDR']}' ";

    $sql_secret = "";
    if ($wr_secret)
        $sql_secret = " , wr_option = '$wr_secret' ";

    $sql = " update $write_table
                set wr_subject = '$wr_subject',
                     wr_content = '$wr_content',
                     wr_1 = '$wr_1',
                     wr_2 = '$wr_2',
                     wr_3 = '$wr_3',
                     wr_4 = '$wr_4',
                     wr_5 = '$wr_5',
                     wr_6 = '$wr_6',
                     wr_7 = '$wr_7',
                     wr_8 = '$wr_8',
                     wr_9 = '$wr_9',
                     wr_10 = '$wr_10',
                     wr_option = '$wr_option'
                     $sql_ip
                     $sql_secret
              where wr_id = '$comment_id' ";
    sql_query($sql);
}

// 사용자 코드 실행
@include_once($board_skin_path.'/write_comment_update.skin.php');
@include_once($board_skin_path.'/write_comment_update.tail.skin.php');

delete_cache_latest($bo_table);

$redirect_url = apply_replace('comment_update_move_url', get_pretty_url($bo_table, $wr['wr_parent'], $qstr.'&amp;#c_'.$comment_id), $bo_table, $wr['wr_parent'], $qstr, $comment_id);

start_event('comment_update_after', $board, $wr_id, $w, $qstr, $redirect_url);

goto_url($redirect_url);
?>