<?php
include_once('./_common.php');

@include_once($board_skin_path.'/good.head.skin.php');

// Enable Javascript 자바스크립트 사용가능할 때
if($_POST['js'] == "on") {
    $error = $count = "";

    function print_result($error, $count)
    {
        echo '{ "error": "' . $error . '", "count": "' . $count . '" }';
        if($error)
            exit;
    }

    if (!$is_member)
    {
        $error = __('Only members are allowed.');
        print_result($error, $count);
    }

    if (!($bo_table && $wr_id)) {
        $error = __('The parameters is wrong.');
        print_result($error, $count);
    }

    $ss_name = 'ss_view_'.$bo_table.'_'.$wr_id;
    if (!get_session($ss_name)) {
        $error = __('You can only Good or Bad from the corresponding posts.');
        print_result($error, $count);
    }

    $row = sql_fetch(" select count(*) as cnt from {$gml['write_prefix']}{$bo_table} ", FALSE);
    if (!$row['cnt']) {
        $error = __('This Board does not exist.');
        print_result($error, $count);
    }

    if ($good == 'good' || $good == 'nogood')
    {
        if($write['mb_id'] == $member['mb_id']) {
            $error = __('You can not Good or Bad in your own post.');
            print_result($error, $count);
        }

        if (!$board['bo_use_good'] && $good == 'good') {
            $error = __('This Board does not use the Good feature.');
            print_result($error, $count);
        }

        if (!$board['bo_use_nogood'] && $good == 'nogood') {
            $error = __('This Board does not use the Bad feature.');
            print_result($error, $count);
        }

        $sql = " select bg_flag from {$gml['board_good_table']}
                    where bo_table = '{$bo_table}'
                    and wr_id = '{$wr_id}'
                    and mb_id = '{$member['mb_id']}'
                    and bg_flag in ('good', 'nogood') ";
        $row = sql_fetch($sql);
        if ($row['bg_flag'])
        {
            if ($row['bg_flag'] == 'good')
                $status = __('Good');
            else
                $status = __('Bad');

            $error = sprintf(__('This post already %s.'), $status);
            print_result($error, $count);
        }
        else
        {
            // Good or Bad count increase
            sql_query(" update {$gml['write_prefix']}{$bo_table} set wr_{$good} = wr_{$good} + 1 where wr_id = '{$wr_id}' ");
            // Insert Good or Bad data
            sql_query(" insert {$gml['board_good_table']} set bo_table = '{$bo_table}', wr_id = '{$wr_id}', mb_id = '{$member['mb_id']}', bg_flag = '{$good}', bg_datetime = '".GML_TIME_YMDHIS."' ");

            // Add notification 알림 추가
            add_notice($good, $member['mb_id'], $write['mb_id'], $bo_table, $wr_id);

            $sql = " select wr_{$good} as count from {$gml['write_prefix']}{$bo_table} where wr_id = '$wr_id' ";
            $row = sql_fetch($sql);

            $count = $row['count'];

            print_result($error, $count);
        }
    }
} else {
    include_once(GML_PATH.'/head.sub.php');

    if (!$is_member)
    {
        $href = './login.php?'.$qstr.'&amp;url='.urlencode(get_pretty_url($bo_table, $wr_id));

        alert(__('Only members are allowed.'), $href);
    }

    if (!($bo_table && $wr_id))
        alert(__('The parameters is wrong.'));

    $ss_name = 'ss_view_'.$bo_table.'_'.$wr_id;
    if (!get_session($ss_name))
        alert(__('You can only Good or Bad from the corresponding posts.'));

    $row = sql_fetch(" select count(*) as cnt from {$gml['write_prefix']}{$bo_table} ", FALSE);
    if (!$row['cnt'])
        alert(__('This Board does not exist.'));

    if ($good == 'good' || $good == 'nogood')
    {
        if($write['mb_id'] == $member['mb_id'])
            alert(__('You can not Good or Bad in your own post.'));

        if (!$board['bo_use_good'] && $good == 'good')
            alert(__('This Board does not use the Good feature.'));

        if (!$board['bo_use_nogood'] && $good == 'nogood')
            alert(__('This Board does not use the Bad feature.'));

        $sql = " select bg_flag from {$gml['board_good_table']}
                    where bo_table = '{$bo_table}'
                    and wr_id = '{$wr_id}'
                    and mb_id = '{$member['mb_id']}'
                    and bg_flag in ('good', 'nogood') ";
        $row = sql_fetch($sql);
        if ($row['bg_flag'])
        {
            if ($row['bg_flag'] == 'good')
                $status = __('Good');
            else
                $status = __('Bad');

            alert(sprintf(__('This post already %s.'), $status));
        }
        else
        {
            // Good or Bad count increase
            sql_query(" update {$gml['write_prefix']}{$bo_table} set wr_{$good} = wr_{$good} + 1 where wr_id = '{$wr_id}' ");
            // Insert Good or Bad data
            sql_query(" insert {$gml['board_good_table']} set bo_table = '{$bo_table}', wr_id = '{$wr_id}', mb_id = '{$member['mb_id']}', bg_flag = '{$good}', bg_datetime = '".GML_TIME_YMDHIS."' ");

            // Add notification 알림 추가
            add_notice($good, $member['mb_id'], $write['mb_id'], $bo_table, $wr_id);

            if ($good == 'good')
                $status = __('Good');
            else
                $status = __('Bad');

            $href = get_pretty_url($bo_table, $wr_id);

            alert(sprintf(__('This post has %s'), $status), '', false);
        }
    }
}

@include_once($board_skin_path.'/good.tail.skin.php');
?>
