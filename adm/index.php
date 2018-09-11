<?php
$sub_menu = '100000';
include_once('./_common.php');

@include_once('./safe_check.php');
if(function_exists('social_log_file_delete')){
    social_log_file_delete(86400);      //소셜로그인 디버그 파일 24시간 지난것은 삭제
}

$gml['title'] = __('Admin Main');    //관리자메인
include_once ('./admin.head.php');

$new_member_rows = 5;
$new_point_rows = 5;
$new_write_rows = 5;

$sql_common = " from {$gml['member_table']} ";

$sql_search = " where (1) ";

if ($is_admin != 'super')
    $sql_search .= " and mb_level <= '{$member['mb_level']}' ";

if (!$sst) {
    $sst = "mb_datetime";
    $sod = "desc";
}

$sql_order = " order by {$sst} {$sod} ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

// 탈퇴회원수
$sql = " select count(*) as cnt {$sql_common} {$sql_search} and mb_leave_date <> '' {$sql_order} ";
$row = sql_fetch($sql);
$leave_count = $row['cnt'];

// 차단회원수
$sql = " select count(*) as cnt {$sql_common} {$sql_search} and mb_intercept_date <> '' {$sql_order} ";
$row = sql_fetch($sql);
$intercept_count = $row['cnt'];

$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$new_member_rows} ";
$result = sql_query($sql);

$colspan = 12;
?>

<div class="col_50">
    <section id="total_member">
        <h2 class="sound_only"><?php e__('Members');  //회원수 ?></h2>
        <div class="info_box bg_01">
            <a href="./member_list.php"><i class="fa fa-user" aria-hidden="true"></i><?php e__('Totals of member'); //총회원수 ?><br class="m_none"><strong><?php echo number_format($total_count) ?> <?php echo n__('people', 'peoples', $total_count);     // 명 ?></strong></a>
        </div>
        <div class="info_box bg_02 ">
            <a href="./member_list.php?sst=mb_intercept_date&sod=desc&sfl=&stx="><i class="fa fa-ban" aria-hidden="true"></i><?php e__('Block');  //차단 ?><br class="m_none"><strong><?php echo number_format($intercept_count) ?> <?php echo n__('people', 'peoples', $total_count);     // 명 ?></strong> </a>
        </div>
        <div class="info_box bg_03">
           <a href="./member_list.php?sst=mb_leave_date&sod=desc&sfl=&stx="> <i class="fa fa-user-times" aria-hidden="true"></i><?php e__('Withdrawal');   //탈퇴 ?><br class="m_none"><strong><?php echo number_format($leave_count) ?> <?php echo n__('people', 'peoples', $total_count);     // 명 ?></strong> </a>
        </div>
    </section>
    <section class="panel" id="new_member">
        <h2 class="panel_tit"><?php echo sprintf(__('List of %s new members'), $new_member_rows); //신규가입회원 %s 건 목록 ?> <a href="./member_list.php" title="<?php e__('View all members'); //회원 전체보기 ?>" class="more_btn"><i class="fa fa-plus" aria-hidden="true"></i></a></h2>
        <div class="panel_con">

            <ul>
            <?php
            for ($i=0; $row=sql_fetch_array($result); $i++)
            {
                // 접근가능한 그룹수
                $sql2 = " select count(*) as cnt from {$gml['group_member_table']} where mb_id = '{$row['mb_id']}' ";
                $row2 = sql_fetch($sql2);
                $group = "";
                if ($row2['cnt'])
                    $group = '<a href="./boardgroupmember_form.php?mb_id='.$row['mb_id'].'">'.$row2['cnt'].'</a>';

                if ($is_admin == 'group')
                {
                    $s_mod = '';
                    $s_del = '';
                }
                else
                {
                    $s_mod = '<a href="./member_form.php?$qstr&amp;w=u&amp;mb_id='.$row['mb_id'].'">'.__('edit').'</a>';
                    $s_del = '<a href="./member_delete.php?'.$qstr.'&amp;w=d&amp;mb_id='.$row['mb_id'].'&amp;url='.$_SERVER['SCRIPT_NAME'].'" onclick="return delete_confirm(this);">'.__('delete').'</a>';
                }
                $s_grp = '<a href="./boardgroupmember_form.php?mb_id='.$row['mb_id'].'">'.__('group').'</a>';

                $leave_date = $row['mb_leave_date'] ? $row['mb_leave_date'] : date("Ymd", GML_SERVER_TIME);
                $intercept_date = $row['mb_intercept_date'] ? $row['mb_intercept_date'] : date("Ymd", GML_SERVER_TIME);

                $mb_nick = get_sideview($row['mb_id'], get_text($row['mb_nick']), $row['mb_email'], $row['mb_homepage']);

                $mb_id = $row['mb_id'];
            ?>
            <li>
                <div class="li_top">
                    <strong class="li_tit fl_left"><i class="fa fa-user" aria-hidden="true"></i> <?php echo $mb_id ?></strong>
                    <span class="mb_point fl_right"><span class="sound_only"><?php e__('Retention Point');   //보유포인트 ?></span><i class="fa fa-product-hunt" aria-hidden="true"></i> <a href="./point_list.php?sfl=mb_id&amp;stx=<?php echo $row['mb_id'] ?>"><?php echo number_format($row['mb_point']) ?></a></span>
                </div>
                <dl class="li_info">
                    <dt><?php e__('Name');   //이름 ?></dt><dd><?php echo get_text($row['mb_name']); ?></dd>
                    <dt><?php e__('Nickname');   //닉네임 ?></dt><dd><?php echo $mb_nick ?></dd>
                    <dt><?php e__('Level');   //레벨 ?></dt><dd><?php echo $row['mb_level'] ?></dd>
                    <?php if ($group) { ?><dt><?php e__('Group'); ?></dt><dd><?php echo $group ?></dd><?php } ?>
                </dl>
                <dl class="mb_agree">
                    <dt><?php e__('reception');  //수신 ?></dt>
                    <dd><?php echo $row['mb_mailling']?'<i class="fa fa-check-square" aria-hidden="true"></i><span class="sound_only">'.__('Yes').'</span>':'<i class="fa fa-square-o" aria-hidden="true"></i><span class="sound_only">'.__('No').'</span>'; ?></dd>
                    <dt><?php e__('Public'); //공개 ?></dt>
                    <dd><?php echo $row['mb_open']?'<i class="fa fa-check-square" aria-hidden="true"></i><span class="sound_only">'.__('Yes').'</span>':'<i class="fa fa-square-o" aria-hidden="true"></i><span class="sound_only">'.__('No').'</span>'; ?></dd>
                    <dt><?php e__('Authentication'); //인증 ?></dt>
                    <dd><?php echo preg_match('/[1-9]/', $row['mb_email_certify'])?'<i class="fa fa-check-square" aria-hidden="true"></i><span class="sound_only">'.__('Yes').'</span>':'<i class="fa fa-square-o" aria-hidden="true"></i><span class="sound_only">'.__('No').'</span>'; ?></dd>
                    <dt><?php e__('block');   //차단 ?></dt>
                    <dd><?php echo $row['mb_intercept_date']?'<i class="fa fa-check-square" aria-hidden="true"></i><span class="sound_only">'.__('Yes').'</span>':'<i class="fa fa-square-o" aria-hidden="true"></i><span class="sound_only">'.__('No').'</span>'; ?></dd>
                </dl>
            </li>
            <?php
                }
            if ($i == 0)
                echo '<li class="empty_li">'.__('No Data').'</li>';
            ?>
            </ul>
        </div>
    </section>

</div>



<div class="col_50">
    <?php
    $sql_common = " from {$gml['board_new_table']} a, {$gml['board_table']} b, {$gml['group_table']} c where a.bo_table = b.bo_table and b.gr_id = c.gr_id ";

    if ($gr_id)
        $sql_common .= " and b.gr_id = '$gr_id' ";
    if ($view) {
        if ($view == 'w')
            $sql_common .= " and a.wr_id = a.wr_parent ";
        else if ($view == 'c')
            $sql_common .= " and a.wr_id <> a.wr_parent ";
    }
    $sql_order = " order by a.bn_id desc ";

    $sql = " select count(*) as cnt {$sql_common} ";
    $row = sql_fetch($sql);
    $total_count = $row['cnt'];

    $colspan = 5;
    ?>

    <section class="panel" id="new_board">
        <h2 class="panel_tit"><?php e__('Recent post');  //최근게시물 ?><a href="<?php echo GML_BBS_URL ?>/new.php" title="<?php e__('More recent posts'); //최근게시물 더보기 ?>" class="more_btn"><i class="fa fa-plus" aria-hidden="true"></i></a></h2>

        <div class="panel_con">
            <ul>
            <?php
            $sql = " select a.*, b.bo_subject, c.gr_subject, c.gr_id {$sql_common} {$sql_order} limit {$new_write_rows} ";
            $result = sql_query($sql);
            for ($i=0; $row=sql_fetch_array($result); $i++)
            {
                $tmp_write_table = $gml['write_prefix'] . $row['bo_table'];

                if ($row['wr_id'] == $row['wr_parent']) // 원글
                {
                    $comment = "";
                    $comment_link = "";
                    $row2 = sql_fetch(" select * from $tmp_write_table where wr_id = '{$row['wr_id']}' ");

                    $name = get_sideview($row2['mb_id'], get_text(cut_str($row2['wr_name'], $config['cf_cut_name'])), $row2['wr_email'], $row2['wr_homepage']);
                    // 당일인 경우 시간으로 표시함
                    $datetime = substr($row2['wr_datetime'],0,10);
                    $datetime2 = $row2['wr_datetime'];
                    if ($datetime == GML_TIME_YMD)
                        $datetime2 = substr($datetime2,11,5);
                    else
                        $datetime2 = substr($datetime2,5,5);

                }
                else // 코멘트
                {
                    $comment = '<span class="cmt_icon">'.__('Comment').'</span> ';
                    $comment_link = '#c_'.$row['wr_id'];
                    $row2 = sql_fetch(" select * from {$tmp_write_table} where wr_id = '{$row['wr_parent']}' ");
                    $row3 = sql_fetch(" select mb_id, wr_name, wr_email, wr_homepage, wr_datetime from {$tmp_write_table} where wr_id = '{$row['wr_id']}' ");

                    $name = get_sideview($row3['mb_id'], get_text(cut_str($row3['wr_name'], $config['cf_cut_name'])), $row3['wr_email'], $row3['wr_homepage']);
                    // 당일인 경우 시간으로 표시함
                    $datetime = substr($row3['wr_datetime'],0,10);
                    $datetime2 = $row3['wr_datetime'];
                    if ($datetime == GML_TIME_YMD)
                        $datetime2 = substr($datetime2,11,5);
                    else
                        $datetime2 = substr($datetime2,5,5);
                }
            ?>

            <li>
                <span class="li_cate">
                    <a href="<?php echo GML_BBS_URL ?>/new.php?gr_id=<?php echo $row['gr_id'] ?>"><?php echo cut_str($row['gr_subject'],10) ?></a>
                    <a href="<?php echo get_pretty_url($row['bo_table']) ?>"><?php echo cut_str($row['bo_subject'],20) ?></a>
                </span>
                <a href="<?php echo get_pretty_url($row['bo_table']) ?> ?>&amp;wr_id=<?php echo $row2['wr_id'] ?><?php echo $comment_link ?>" class="li_tit"><?php echo $comment ?><?php echo conv_subject($row2['wr_subject'], 100) ?></a>
                <div class="li_info">
                    <span class="sound_only"><?php e__('Name'); ?></span>
                    <span><?php echo $name ?></span>
                    <span class="sound_only"><?php e__('Date'); ?></span>
                    <span><?php echo $datetime ?></span>
                </div>
            </li>

            <?php
            }
            if ($i == 0)
                echo '<li class="empty_li">'.__('No Data').'</li>';
            ?>
            </ul>
        </div>

    </section>

    <?php
    $sql_common = " from {$gml['point_table']} ";
    $sql_search = " where (1) ";
    $sql_order = " order by po_id desc ";

    $sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
    $row = sql_fetch($sql);
    $total_count = $row['cnt'];

    $sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$new_point_rows} ";
    $result = sql_query($sql);

    ?>

    <section class="panel" id="new_point">
        <h2 class="panel_tit"><?php e__('Recent Point History'); //최근 포인트 내역 ?><span class="li_total">
           (<?php echo sprintf(n__('%s total', '%s totals', $total_count), number_format($total_count)); ?>)
        </span> <a href="./point_list.php" title="<?php e__('View all points history');  //포인트내역 전체보기 ?>" class="more_btn"><i class="fa fa-plus" aria-hidden="true"></i></a></h2>


        <div class="panel_con">

            <ul>
            <?php
            $row2['mb_id'] = '';
            for ($i=0; $row=sql_fetch_array($result); $i++)
            {
                if ($row2['mb_id'] != $row['mb_id'])
                {
                    $sql2 = " select mb_id, mb_name, mb_nick, mb_email, mb_homepage, mb_point from {$gml['member_table']} where mb_id = '{$row['mb_id']}' ";
                    $row2 = sql_fetch($sql2);
                }

                $mb_nick = get_sideview($row['mb_id'], $row2['mb_nick'], $row2['mb_email'], $row2['mb_homepage']);

                $link1 = $link2 = "";
                if (!preg_match("/^\@/", $row['po_rel_table']) && $row['po_rel_table'])
                {
                    $link1 = '<a href="'.get_pretty_url($row['po_rel_table'], $row['po_rel_id']).'" target="_blank">';
                    $link2 = '</a>';
                }
            ?>

            <li>
                <div class="li_top">
                    <span class="li_tit fl_left"><?php echo $link1.$row['po_content'].$link2 ?></span>
                    <span class="li_point2 fl_right"> <?php echo number_format($row['po_point']) ?></span>
                </div>
                <div class="li_info">
                    <?php echo $mb_nick ?>
                    <span><?php echo $row['po_datetime'] ?></span>
                    <strong class="mb_point fl_right"><i class="fa fa-product-hunt" aria-hidden="true"></i><?php echo number_format($row['po_mb_point']) ?></strong>
                </div>
            </li>

            <?php
            }

            if ($i == 0)
                echo '<li class="empty_li">'.__('No Data').'</li>';
            ?>
            </ul>
        </div>

    </section>
</div>
<?php
include_once ('./admin.tail.php');
?>
