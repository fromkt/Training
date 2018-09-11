<?php
$sub_menu = "100200";
include_once('./_common.php');

if ($is_admin != 'super')
    alert(__('Only the Super administrator can access it.'));     //최고관리자만 접근 가능합니다.

$sql_common = " from {$gml['auth_table']} a left join {$gml['member_table']} b on (a.mb_id=b.mb_id) ";

$sql_search = " where (1) ";
if ($stx) {
    $sql_search .= " and ( ";
    switch ($sfl) {
        default :
            $sql_search .= " ({$sfl} like '%{$stx}%') ";
            break;
    }
    $sql_search .= " ) ";
}

if (!$sst) {
    $sst  = "a.mb_id, au_menu";
    $sod = "";
}
$sql_order = " order by $sst $sod ";

$sql = " select count(*) as cnt
            {$sql_common}
            {$sql_search}
            {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select *
            {$sql_common}
            {$sql_search}
            {$sql_order}
            limit {$from_record}, {$rows} ";
$result = sql_query($sql);

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall btn_ov02">'.__('ALL list').'</a>';  //전체목록

$gml['title'] = __('Set administrative permissions');    //관리권한설정
include_once('./admin.head.php');

$colspan = 5;
?>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <a href="?sst=mb_leave_date&amp;sod=desc&amp;sfl=<?php echo $sfl ?>&amp;stx=<?php echo $stx ?>" class="btn_ov01"> <span class="ov_txt"><?php e__('Administrative rights set');   //설정된 관리권한 ?></span><span class="ov_num"><?php echo sprintf(__('Total %s'), number_format($total_count)); ?></span></a>
</div>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
<input type="hidden" name="sfl" value="a.mb_id" id="sfl">

<label for="stx" class="sound_only"><?php e__('Member ID');  //회원아이디 ?><strong class="sound_only"><?php e__('required'); ?></strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="required frm_input">
<input type="submit" value="<?php e__('Search'); ?>" id="fsearch_submit" class="btn_submit">

</form>

<form name="fauthlist" id="fauthlist" method="post" action="./auth_list_delete.php" onsubmit="return fauthlist_submit(this);">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $gml['title']; ?> <?php e__('List'); ?></caption>
    <thead>
    <tr>
        <th scope="col">
            <label for="chkall" class="sound_only"><?php e__('All current page members');    //현재 페이지 회원 전체 ?></label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col"><?php echo subject_sort_link('a.mb_id') ?><?php e__('Member ID');  //회원아이디 ?></a></th>
        <th scope="col"><?php echo subject_sort_link('mb_nick') ?><?php e__('Nickname'); ?></a></th>
        <th scope="col"><?php e__('Menu'); ?></th>
        <th scope="col"><?php e__('Permissions'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $count = 0;
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
        $is_continue = false;
        // 회원아이디가 없는 메뉴는 삭제함
        if($row['mb_id'] == '' && $row['mb_nick'] == '') {
            sql_query(" delete from {$gml['auth_table']} where au_menu = '{$row['au_menu']}' ");
            $is_continue = true;
        }

        // 메뉴번호가 바뀌는 경우에 현재 없는 저장된 메뉴는 삭제함
        if (!isset($auth_menu[$row['au_menu']]))
        {
            sql_query(" delete from {$gml['auth_table']} where au_menu = '{$row['au_menu']}' ");
            $is_continue = true;
        }

        if($is_continue)
            continue;

        $mb_nick = get_sideview($row['mb_id'], $row['mb_nick'], $row['mb_email'], $row['mb_homepage']);

        $bg = 'bg'.($i%2);
    ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_chk">
            <input type="hidden" name="au_menu[<?php echo $i ?>]" value="<?php echo $row['au_menu'] ?>">
            <input type="hidden" name="mb_id[<?php echo $i ?>]" value="<?php echo $row['mb_id'] ?>">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo sprintf(__('Authority of %s'), $row['mb_nick']); ?></label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
        </td>
        <td class="td_mbid"><a href="?sfl=a.mb_id&amp;stx=<?php echo $row['mb_id'] ?>"><?php echo $row['mb_id'] ?></a></td>
        <td class="td_auth_mbnick td_left"><?php echo $mb_nick ?></td>
        <td class="td_menu td_left">
            <?php echo $row['au_menu'] ?>
            <?php echo $auth_menu[$row['au_menu']] ?>
        </td>
        <td class="td_auth"><?php echo $row['au_auth'] ?></td>
    </tr>
    <?php
        $count++;
    }

    if ($count == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">'.__('No Data').'</td></tr>';
    ?>
    </tbody>
    </table>
</div>

<div class="btn_list01 btn_list">
    <input type="submit" name="act_button" value="<?php e__('Delete selected');  //선택삭제 ?>" onclick="document.pressed=this.value" class="btn btn_02">
</div>

<?php
if (strstr($sfl, 'mb_id'))
    $mb_id = $stx;
else
    $mb_id = '';
?>
</form>

<?php
$pagelist = get_paging(GML_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'].'?'.$qstr.'&amp;page=');
echo $pagelist;
?>

<form name="fauthlist2" id="fauthlist2" action="./auth_update.php" method="post" autocomplete="off">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">

<div class="local_desc01 local_desc">
    <p>
        <?php e__('You can give the members administrative rights in the following form.'); //다음 양식에서 회원에게 관리권한을 부여하실 수 있습니다. ?>
        <br>
        <?php e__('Permissions <strong> r </ strong> are read permissions, <strong> w </ strong> are write permissions, and <strong> d </ strong> are delete permissions.'); //권한 <strong>r</strong>은 읽기권한, <strong>w</strong>는 쓰기권한, <strong>d</strong>는 삭제권한입니다. ?>
    </p>
</div>
<section id="add_admin" class="panel">
    <h2 class="panel_tit"><?php e__('Add administrative privileges');    //관리권한 추가 ?></h2>


    <div class="panel_con">

        <ul class="frm_ul">
            <li class="li_50">
                <label for="mb_id" class="lb_block"><?php e__('Member ID'); ?><strong class="sound_only"><?php e__('required'); ?></strong></label>
                <strong id="msg_mb_id" class="msg_sound_only"></strong>
                <input type="text" name="mb_id" value="<?php echo $mb_id ?>" id="mb_id" required class="required frm_input frm_input_full">
            </li>
            <li class="li_50 bd_0">
                <label for="au_menu" class="lb_block"><?php e__('Access menu');  //접근가능메뉴 ?><strong class="sound_only"><?php e__('required'); ?></strong></label>
                    <select id="au_menu" name="au_menu" required class="required frm_input_full">
                        <option value=''><?php e__('Select'); ?></option>
                        <?php
                        foreach($auth_menu as $key=>$value)
                        {
                            if (!(substr($key, -3) == '000' || $key == '-' || !$key))
                                echo '<option value="'.$key.'">'.$key.' '.$value.'</option>';
                        }
                        ?>
                    </select>
            </li>
            <li class="li_clear">
                <span  class="lb_block"><?php e__('Specify Permissions');    //권한지정 ?></span>
                <input type="checkbox" name="r" value="r" id="r" checked>
                <label for="r">r (<?php e__('Read'); ?>)</label>
                <input type="checkbox" name="w" value="w" id="w">
                <label for="w">w (<?php e__('Write'); ?>)</label>
                <input type="checkbox" name="d" value="d" id="d">
                <label for="d">d (<?php e__('Delete'); ?>)</label>
            </li>
        </ul>
    </div>

    <div class="panel_btn">
        <input type="submit" value="<?php e__('Add'); ?>" class="btn_submit btn">
    </div>
</section>

</form>

<?php
get_localize_script('auth_list',
array(
'check_msg'=>__('Please select at least one item to %s.'),  // %s 하실 항목을 하나 이상 선택하세요.
'delete_msg'=>__('Are you sure you want to delete it?'),    // 정말 삭제하시겠습니까?
'delete_pressed' => __('Delete selected'),  //선택삭제
),
true);
?>
<script>
function fauthlist_submit(f)
{
    if (!is_checked("chk[]")) {
        alert( js_sprintf(auth_list.check_msg, document.pressed) );
        return false;
    }

    if(document.pressed == auth_list.delete_pressed ) {
        if(!confirm( auth_list.delete_msg )) {
            return false;
        }
    }

    return true;
}
</script>

<?php
include_once ('./admin.tail.php');
?>
