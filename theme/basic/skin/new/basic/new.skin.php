<?php
if (!defined("_GNUBOARD_")) exit; // Unable to access direct pages

$colspan = 5;

if ($is_admin) $colspan++;

for ($i=0; $i<count($list); $i++) {
    $list[$i]['num'] = $total_count - ($page - 1) * $config['cf_page_rows'] - $i;
    $list[$i]['board_url'] = get_pretty_url($list[$i]['bo_table']);
    $list[$i]['gr_subject'] = cut_str($list[$i]['gr_subject'], 20);
    $list[$i]['bo_subject'] = cut_str($list[$i]['bo_subject'], 20);
    $list[$i]['wr_subject'] = get_text(cut_str($list[$i]['wr_subject'], 80));
}

if ($i == 0) $no_list = '<tr><td colspan="'.$colspan.'" class="empty_table">'.__('No posts found.').'</td></tr>';

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$new_skin_url.'/style.css">', 0);
?>

<!-- Start search new posts { -->
<fieldset id="new_sch">
    <legend><?php e__('Search Details'); ?></legend>
    <form name="fnew" method="get">
    <?php echo $group_select ?>
    <label for="view" class="sound_only"><?php e__('Search target'); ?></label>
    <select name="view" id="view">
        <option value=""><?php e__('All posts'); ?></option>
        <option value="w"><?php e__('post'); ?></option>
        <option value="c"><?php e__('Comment'); ?></option>
    </select>
    <label for="mb_id" class="sound_only"><?php e__('search terms'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label>
    <?php
        if ( $is_admin ){
            echo '<input type="text" name="mb_id" value="'.$mb_id.'" id="mb_id" required class="frm_input" size="40">';
        } else {
            echo '<input type="text" name="mb_nick" value="'.get_text($mb_nick).'" id="mb_id" required class="frm_input" size="40">';
        }
    ?>
    <button type="submit" class="btn_submit"><i class="fa fa-search" aria-hidden="true"></i> <?php e__('Search'); ?></button>
    <p><?php echo $is_admin ? __('Only member ID can be searched') : __('Only Member NickName can be searched'); ?></p>
    </form>
    <script>
    /* In the Select box, automatic movement release.
    function select_change()
    {
        document.fnew.submit();
    }
    */
    document.getElementById("gr_id").value = "<?php echo $gr_id ?>";
    document.getElementById("view").value = "<?php echo $view ?>";
    </script>
</fieldset>
<!-- } End search new posts -->

<!-- Start list new posts { -->
<form name="fnewlist" id="fnewlist" method="post" action="#" onsubmit="return fnew_submit(this);">
<input type="hidden" name="sw"       value="move">
<input type="hidden" name="view"     value="<?php echo $view; ?>">
<input type="hidden" name="sfl"      value="<?php echo $sfl; ?>">
<input type="hidden" name="stx"      value="<?php echo $stx; ?>">
<input type="hidden" name="bo_table" value="<?php echo $bo_table; ?>">
<input type="hidden" name="page"     value="<?php echo $page; ?>">
<input type="hidden" name="pressed"  value="">

<div class="tbl_head01 tbl_wrap">
    <table>
    <thead>
    <tr>
        <?php if ($is_admin) { ?>
        <th scope="col" class="td_chk_all all_chk">
            <label for="all_chk" class="sound_only"><?php e__('List All'); ?></label>
            <input type="checkbox" id="all_chk">
        </th>
        <?php } ?>
        <th scope="col"><?php e__('Group'); ?></th>
        <th scope="col"><?php e__('Board'); ?></th>
        <th scope="col"><?php e__('Subject'); ?></th>
        <th scope="col"><?php e__('Name'); ?></th>
        <th scope="col"><?php e__('Date'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php for ($i=0; $i<count($list); $i++) { ?>
    <tr>
        <?php if ($is_admin) { ?>
        <td class="td_chk li_chk">
            <label for="chk_bn_id_<?php echo $i; ?>" class="sound_only"><?php echo $num?></label>
            <input type="checkbox" name="chk_bn_id[]" value="<?php echo $i; ?>" id="chk_bn_id_<?php echo $i; ?>">
            <input type="hidden" name="bo_table[<?php echo $i; ?>]" value="<?php echo $list[$i]['bo_table']; ?>">
            <input type="hidden" name="wr_id[<?php echo $i; ?>]" value="<?php echo $list[$i]['wr_id']; ?>">
        </td>
        <?php } ?>
        <td class="td_group"><a href="./new.php?gr_id=<?php echo $list[$i]['gr_id'] ?>"><?php echo $list[$i]['gr_subject'] ?></a></td>
        <td class="td_board"><a href="<?php echo $list[$i]['board_url'] ?>"><?php echo $list[$i]['bo_subject'] ?></a></td>
        <td><a href="<?php echo $list[$i]['href'] ?>" class="new_tit">
        	<span class="new_li_status <?php echo $list[$i]['comment_class'][0] ?>"><i class="fa <?php echo $list[$i]['comment_class'][1] ?>" aria-hidden="true"></i></span><?php echo $list[$i]['wr_subject'] ?></a></td>
        <td class="td_name"><?php echo $list[$i]['name'] ?></td>
        <td class="td_date"><?php echo $list[$i]['datetime2'] ?></td>
    </tr>
    <?php }  ?>

    <?php echo $no_list; // No posts found. ?>
    </tbody>
    </table>
</div>

<?php if ($is_admin) { ?>
<div class="sir_bw02 sir_bw">
    <button type="submit" onclick="document.pressed=this.value" value="delete_selection" class="btn_b01 btn"><?php e__('Delete Selection'); ?></button>
</div>
<?php } ?>
</form>

<?php if ($is_admin) {

get_localize_script('new_skin',
array(
'check_msg'=>__('Please select at least one item to %s.'),  // 할 게시물을 하나 이상 선택하세요.
'delete_msg'=>__('Are you sure you want to %s it?'),    // 선택한 게시물을 정말 %s 하시겠습니까?
'delete2_msg'=>__('Once deleted, data can not be recovered.'),   //한번 삭제한 자료는 복구할 수 없습니다
),
true);

?>
<script>
jQuery(function($){
    $('#all_chk').click(function(){
        $('[name="chk_bn_id[]"]').attr('checked', this.checked);
    });
});

function fnew_submit(f)
{
    f.pressed.value = document.pressed;

    if( document.pressed === "delete_selection" ){
        document.pressed = "<?php e__('Delete Selection'); ?>";
    }

    var cnt = 0;
    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_bn_id[]" && f.elements[i].checked)
            cnt++;
    }

    if (!cnt) {
        alert( js_sprintf(new_skin.check_msg, document.pressed) );
        return false;
    }

    if (!confirm( js_sprintf(new_skin.delete_msg, document.pressed) + "\n\n" + new_skin.delete2_msg )) {
        return false;
    }

    f.action = "./new_delete.php";

    return true;
}
</script>
<?php } ?>

<?php echo $write_pages ?>
<!-- } End list new posts -->
