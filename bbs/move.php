<?php
include_once('./_common.php');

if ($sw == 'move')
    $act = __('Move');
else if ($sw == 'copy')
    $act = __('Copy');
else
    alert(sprintf(__('%s parameter is wrong'), 'sw'));

// Board admin or higher copy, removable
if ($is_admin != 'board' && $is_admin != 'group' && $is_admin != 'super')
    alert_close(__('Board admin or higher access is available.'));

$gml['title'] = __('Post').' ' . $act;
include_once(GML_PATH.'/head.sub.php');

$wr_id_list = '';
if ($wr_id)
    $wr_id_list = $wr_id;
else {
    $comma = '';
    for ($i=0; $i<count($_POST['chk_wr_id']); $i++) {
        $wr_id_list .= $comma . $_POST['chk_wr_id'][$i];
        $comma = ',';
    }
}

//$sql = " select * from {$gml['board_table']} a, {$gml['group_table']} b where a.gr_id = b.gr_id and bo_table <> '$bo_table' ";
// 원본 게시판을 선택 할 수 있도록 함.
$sql = " select * from {$gml['board_table']} a, {$gml['group_table']} b where a.gr_id = b.gr_id ";
if ($is_admin == 'group')
    $sql .= " and b.gr_admin = '{$member['mb_id']}' ";
else if ($is_admin == 'board')
    $sql .= " and a.bo_admin = '{$member['mb_id']}' ";
$sql .= " order by a.gr_id, a.bo_order, a.bo_table ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++)
{
    $list[$i] = $row;
}
?>

<div id="copymove" class="new_win">
    <h1 id="win_title"><?php echo $gml['title'] ?></h1>
    <form name="fboardmoveall" method="post" action="./move_update.php" onsubmit="return fboardmoveall_submit(this);">
    <input type="hidden" name="sw" value="<?php echo $sw ?>">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="wr_id_list" value="<?php echo $wr_id_list ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="act" value="<?php echo $act ?>">
    <input type="hidden" name="url" value="<?php echo get_text(clean_xss_tags($_SERVER['HTTP_REFERER'])); ?>">

    <div class="tbl_head01 tbl_wrap">
        <table>
        <caption><?php echo sprintf(__('Please select at least one bulletin board to %s.'), $act); ?></caption>
        <thead>
        <tr>
            <th scope="col" class="td_chk_all all_chk">
                <label for="chkall"><span class="sound_only"><?php e__('All current page Boards'); ?></span></label>
                <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);">
            </th>
            <th scope="col"><?php e__('Board'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php for ($i=0; $i<count($list); $i++) {
            $atc_mark = '';
            $atc_bg = '';
            if ($list[$i]['bo_table'] == $bo_table) { // 게시물이 현재 속해 있는 게시판이라면
                $atc_mark = '<span class="copymove_current">'.__('Current').'<span class="sound_only">'.__('Board').'</span></span>';
                $atc_bg = 'copymove_currentbg';
            }
        ?>
        <tr class="<?php echo $atc_bg; ?>">
            <td class="td_chk">
                <label for="chk<?php echo $i ?>"><span class="sound_only"><?php echo $list[$i]['bo_table'] ?></span></label>
                <input type="checkbox" value="<?php echo $list[$i]['bo_table'] ?>" id="chk<?php echo $i ?>" name="chk_bo_table[]">
            </td>
            <td>
                <label for="chk<?php echo $i ?>">
                    <?php
                    echo $list[$i]['gr_subject'] . ' &gt; ';
                    $save_gr_subject = $list[$i]['gr_subject'];
                    ?>
                    <?php echo get_board_gettext_titles($list[$i]['bo_subject']); ?> (<?php echo $list[$i]['bo_table'] ?>)
                    <?php echo $atc_mark; ?>
                </label>
            </td>
        </tr>
        <?php } ?>
        </tbody>
        </table>
    </div>

    <div class="win_btn">
        <button type="submit" id="btn_submit" class="btn_submit"><?php echo $act ?></button>
    </div>
    </form>

</div>

<?php
get_localize_script('move_js',
array(
'close_msg'=>__('Close Window'),  // 창닫기
'choose_msg'=>__('Please select at least one bulletin board for %s.'),    // 게시물을 %s 할 게시판을 한개 이상 선택해 주십시오.
),
true);
?>
<script>
jQuery(function($) {
    $(".win_btn").append("<button type=\"button\" class=\"btn_cancel btn_close\">"+ move_js.close_msg +"</button>");

    $(".win_btn button.btn_close").click(function() {
        window.close();
    });
});

function all_checked(sw) {
    var f = document.fboardmoveall;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_bo_table[]")
            f.elements[i].checked = sw;
    }
}

function fboardmoveall_submit(f)
{
    var check = false;

    if (typeof(f.elements['chk_bo_table[]']) == 'undefined')
        ;
    else {
        if (typeof(f.elements['chk_bo_table[]'].length) == 'undefined') {
            if (f.elements['chk_bo_table[]'].checked)
                check = true;
        } else {
            for (i=0; i<f.elements['chk_bo_table[]'].length; i++) {
                if (f.elements['chk_bo_table[]'][i].checked) {
                    check = true;
                    break;
                }
            }
        }
    }

    if (!check) {
        alert( js_sprintf(move_js.choose_msg, f.act.value) );
        return false;
    }

    document.getElementById('btn_submit').disabled = true;

    f.action = './move_update.php';
    return true;
}
</script>

<?php
start_event('move_html_footer');
include_once(GML_PATH.'/tail.sub.php');
?>
