<?php
include_once('./_common.php');

if (!$board['bo_table']) {
   alert(__('This bulletin board does not exist.'), GML_URL);
}

check_device($board['bo_device']);

if (isset($write['wr_is_comment']) && $write['wr_is_comment']) {
    goto_url(get_pretty_url($bo_table, $wr_id, '#c_'.$wr_id));
}

if (!$bo_table) {
    $msg = __('Parameter bo_table is empty.');
    alert($msg);
}

$gml['board_title'] = ((GML_IS_MOBILE && $board['bo_mobile_subject']) ? get_board_gettext_titles($board['bo_mobile_subject']) : get_board_gettext_titles($board['bo_subject']));

$qstr = apply_replace('get_board_qstr_params', $qstr, $board, $wr_id);

// Read the text if the wr_id value exists
if ($wr_id) {
    // If there is no post, go to the corresponding bulletin board list
    if (!$write['wr_id']) {
        $msg = __('The post does not exist.').'\\n\\n'.__('The post has been deleted or moved.');
        alert($msg, get_pretty_url($bo_table));
    }

    // Enable Group Access
    if (isset($group['gr_use_access']) && $group['gr_use_access']) {
        if ($is_guest) {
            $msg = __('No non-members have access to this bulletin board.')."\\n\\n".__('If you are a member, log in and try it.');
            alert($msg, GML_BBS_URL.'/login.php?wr_id='.$wr_id.$qstr.'&amp;url='.urlencode(get_pretty_url($bo_table, $wr_id, $qstr)));
        }

        // Pass if group manager or higher
        if ($is_admin == "super" || $is_admin == "group") {
            ;
        } else {
            // Group Access
            $sql = " select count(*) as cnt from {$gml['group_member_table']} where gr_id = '{$board['gr_id']}' and mb_id = '{$member['mb_id']}' ";
            $row = sql_fetch($sql);
            if (!$row['cnt']) {
                alert(__('You do not have access, so you can not read the post.')."\\n\\n".__('If you have any questions, please contact the administrator.'), GML_URL);
            }
        }
    }

    // If the logged in member's level is less than the set read level
    if ($member['mb_level'] < $board['bo_read_level']) {
        if ($is_member)
            alert(__('You do not have permission to read.'), GML_URL);
        else
            alert(__('You do not have permission to read.').'\\n\\n'.__('If you are a member, log in and try it.'), GML_BBS_URL.'/login.php?wr_id='.$wr_id.$qstr.'&amp;url='.urlencode(get_pretty_url($bo_table, $wr_id, $qstr)));
    }

    // If use self-Authentication
    if ($config['cf_cert_use'] && !$is_admin) {
        // Only authorized members
        if ($board['bo_use_cert'] != '' && $is_guest) {
            alert(__('This bulletin board can only be read by the member who has confirmed self-Authentication.').'\\n\\n'.__('If you are a member, log in and try it.'), GML_BBS_URL.'/login.php?wr_id='.$wr_id.$qstr.'&amp;url='.urlencode(get_pretty_url($bo_table, $wr_id, $qstr)));
        }

        if ($board['bo_use_cert'] == 'cert' && !$member['mb_certify']) {
            alert(__('This bulletin board can only be read by the member who has confirmed self-Authentication.').'\\n\\n'.__('Please confirm your Authentication in the member profile edit.'), GML_URL);
        }

        if ($board['bo_use_cert'] == 'adult' && !$member['mb_adult']) {
            alert(__('This bulletin board can only be read by a member certified as an adult by self-Authentication.').'\\n\\n'.__('If you are an adult and can not read the post, please confirm your Authentication in the member profile edit.'), GML_URL);
        }

        if ($board['bo_use_cert'] == 'hp-cert' && $member['mb_certify'] != 'hp') {
            alert(__('This bulletin board can only be read by a member who has confirmed Phone-Authentication.').'\\n\\n'.__('Please check the identity of the Phone-Authentication in the member profile edit.'), GML_URL);
        }

        if ($board['bo_use_cert'] == 'hp-adult' && (!$member['mb_adult'] || $member['mb_certify'] != 'hp')) {
            alert(__('This bulletin board can only be read by a member certified as an adult by Phone-Authentication.').'\\n\\n'.__('If you are an adult and can not read the post, please confirm your Phone-Authentication in the member profile edit.'), GML_URL);
        }
    }

    // Pass if writing it's self or admin
    if (($write['mb_id'] && $write['mb_id'] === $member['mb_id']) || $is_admin) {
        ;
    } else {
        // if secret
        if (strstr($write['wr_option'], "secret"))
        {
            // In case the member posted the secret post and the Admin posted the answer
            $is_owner = false;
            if ($write['wr_reply'] && $member['mb_id'])
            {
                $sql = " select mb_id from {$write_table}
                            where wr_num = '{$write['wr_num']}'
                            and wr_reply = ''
                            and wr_is_comment = 0 ";
                $row = sql_fetch($sql);
                if ($row['mb_id'] === $member['mb_id'])
                    $is_owner = true;
            }

            $ss_name = 'ss_secret_'.$bo_table.'_'.$write['wr_num'];

            if (!$is_owner)
            {
                //$ss_name = "ss_secret_{$bo_table}_{$wr_id}";
                // The number of a post you have read is stored in your session and will not be asked for a password again if you read the same post.
                // If this post is not a saved post and is not an administrator
                //if ("$bo_table|$write['wr_num']" != get_session("ss_secret"))
                if (!get_session($ss_name))
                    goto_url(GML_BBS_URL.'/password.php?w=s&amp;bo_table='.$bo_table.'&amp;wr_id='.$wr_id.$qstr);
            }

            set_session($ss_name, TRUE);
        }
    }

    // Once read does not increase hits until browser is closed
    $ss_name = 'ss_view_'.$bo_table.'_'.$wr_id;
    if (!get_session($ss_name))
    {
        sql_query(" update {$write_table} set wr_hit = wr_hit + 1 where wr_id = '{$wr_id}' ");

        // Pass with Self own writings
        if ($write['mb_id'] && $write['mb_id'] === $member['mb_id']) {
            ;
        } else if ($is_guest && $board['bo_read_level'] == 1 && $write['wr_ip'] == $_SERVER['REMOTE_ADDR']) {
            // If you are a non-members and have the same reading level 1 and registered IP, go by self own post.
            ;
        } else {
            // If setting a reading point
            if ($config['cf_use_point'] && $board['bo_read_point'] && $member['mb_point'] + $board['bo_read_point'] < 0)
                alert(sprintf(__('Your point (%s) is missing or enough points, so you can not read (%s).'), number_format($member['mb_point']), number_format($board['bo_read_point'])).'\\n\\n'.__('Collect points and read again.'));

            insert_point($member['mb_id'], $board['bo_read_point'], ((GML_IS_MOBILE && $board['bo_mobile_subject']) ? $board['bo_mobile_subject'] : $board['bo_subject']).' '.$wr_id.' '.__('Read post'), $bo_table, $wr_id, 'read');
        }

        set_session($ss_name, TRUE);
    }

    $gml['title'] = strip_tags(conv_subject($write['wr_subject'], 255))." > ".$gml['board_title'];
} else {
    if ($member['mb_level'] < $board['bo_list_level']) {
        if ($member['mb_id'])
            alert(__('You do not have permission to view the list.'), GML_URL);
        else
            alert(__('You do not have permission to view the list.').'\\n\\n'.__('If you are a member, log in and try it.'), GML_BBS_URL.'/login.php?'.$qstr.'&url='.urlencode(get_pretty_url($bo_table, '', $qstr)));
    }

    // If use self-Authentication
    if ($config['cf_cert_use'] && !$is_admin) {
        // Only authorized members
        if ($board['bo_use_cert'] != '' && $is_guest) {
            alert(__('This bulletin board can only be read by the member who has confirmed self-Authentication.').'\\n\\n'.__('If you are a member, log in and try it.'), GML_BBS_URL.'/login.php?wr_id='.$wr_id.$qstr.'&amp;url='.urlencode(get_pretty_url($bo_table, $wr_id, $qstr)));
        }

        if ($board['bo_use_cert'] == 'cert' && !$member['mb_certify']) {
            alert(__('This bulletin board can only be read by the member who has confirmed self-Authentication.').'\\n\\n'.__('Please confirm your Authentication in the member profile edit.'), GML_URL);
        }

        if ($board['bo_use_cert'] == 'adult' && !$member['mb_adult']) {
            alert(__('This bulletin board can only be read by a member certified as an adult by self-Authentication.').'\\n\\n'.__('If you are an adult and can not read the post, please confirm your Authentication in the member profile edit.'), GML_URL);
        }

        if ($board['bo_use_cert'] == 'hp-cert' && $member['mb_certify'] != 'hp') {
            alert(__('This bulletin board can only be read by a member who has confirmed Phone-Authentication.').'\\n\\n'.__('Please check the identity of the Phone-Authentication in the member profile edit.'), GML_URL);
        }

        if ($board['bo_use_cert'] == 'hp-adult' && (!$member['mb_adult'] || $member['mb_certify'] != 'hp')) {
            alert(__('This bulletin board can only be read by a member certified as an adult by Phone-Authentication.').'\\n\\n'.__('If you are an adult and can not read the post, please confirm your Phone-Authentication in the member profile edit.'), GML_URL);
        }
    }

    if (!isset($page) || (isset($page) && $page == 0)) $page = 1;

    $gml['title'] = $gml['board_title'].' '.$page.' '.__('Page');
}

include_once(GML_PATH.'/head.sub.php');

$width = $board['bo_table_width'];
if ($width <= 100)
    $width .= '%';
else
    $width .='px';

// Use IP Display
$ip = "";
$is_ip_view = $board['bo_use_ip_view'];
if ($is_admin) {
    $is_ip_view = true;
    if (array_key_exists('wr_ip', $write)) {
        $ip = $write['wr_ip'];
    }
} else {
    // If you are not an admin, hide the IP address and show it.
    if (isset($write['wr_ip'])) {
        $ip = preg_replace("/([0-9]+).([0-9]+).([0-9]+).([0-9]+)/", GML_IP_DISPLAY, $write['wr_ip']);
    }
}

// Use category
$is_category = false;
$category_name = '';
if ($board['bo_use_category']) {
    $is_category = true;
    if (array_key_exists('ca_name', $write)) {
        $category_name = $write['ca_name']; // Category name
    }
}

// Use good
$is_good = false;
if ($board['bo_use_good'])
    $is_good = true;

// Use bad
$is_nogood = false;
if ($board['bo_use_nogood'])
    $is_nogood = true;

$admin_href = "";
// IF Super Admin OR Group Admin
if ($member['mb_id'] && ($is_admin === 'super' || $group['gr_admin'] === $member['mb_id']))
    $admin_href = GML_ADMIN_URL.'/board_form.php?w=u&amp;bo_table='.$bo_table;

include_once(GML_BBS_PATH.'/board_head.php');

// If you have a post ID( wr_id ), this is view page 
if (isset($wr_id) && $wr_id) {
    include_once(GML_BBS_PATH.'/view.php');
}

// Show entire list If Show All List is not available with " Yes " or wr_id value
//if ($board['bo_use_list_view'] || empty($wr_id))
if ($member['mb_level'] >= $board['bo_list_level'] && $board['bo_use_list_view'] || empty($wr_id))
    include_once (GML_BBS_PATH.'/list.php');

include_once(GML_BBS_PATH.'/board_tail.php');

echo "\n<!-- ".__('Skin used')." : ".(GML_IS_MOBILE ? $board['bo_mobile_skin'] : $board['bo_skin'])." -->\n";

include_once(GML_PATH.'/tail.sub.php');
?>
