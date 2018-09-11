<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

get_localize_script('list_l10n',
array(
'copy_pressed'=>__('Copy Selection'),  // 선택복사
'move_pressed'=>__('Move Selection'),    // 선택이동
'delete_pressed' => __('Delete Selection'),  //선택삭제
'copy_msg'=>__('Copy'),
'move_msg'=>__('Move'),
'select_msg'=>__('Please select at least one item to %s.'),   //%s 할 게시물을 하나 이상 선택하세요.
'delete_check_msg1'=>__('Are you sure you want to delete it?'),  //정말 삭제하시겠습니까?
'delete_check_msg2'=>__('If you selected a post with a reply, you must also select the reply post to delete the post.')
),
true);
?>
<script>
function all_checked(sw) {
    var f = document.fboardlist;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]")
            f.elements[i].checked = sw;
    }
}

function fboardlist_submit(f) {
    var chk_count = 0;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked)
            chk_count++;
    }

    var todo = '';
    if(document.pressed == 'delete_selection') {
        todo = list_l10n.delete_pressed;
    } else if(document.pressed == 'copy_selection') {
        todo = list_l10n.copy_pressed;
    } else if(document.pressed == 'move_selection') {
        todo = list_l10n.move_pressed;
    }

    if (!chk_count) {
        alert(js_sprintf(list_l10n.select_msg, todo));
        return false;
    }

    if(todo == list_l10n.copy_pressed) {
        select_copy("copy");
        return;
    }

    if(todo == list_l10n.move_pressed) {
        select_copy("move");
        return;
    }

    if(todo == list_l10n.delete_pressed) {
        if (!confirm(list_l10n.delete_check_msg1+"\n\n"+list_l10n.delete_check_msg2))
            return false;

        f.removeAttribute("target");
        f.action = gml_bbs_url + "/board_list_update.php";
    }

    return true;
}

// 선택한 게시물 복사 및 이동
function select_copy(sw) {
    var f = document.fboardlist;

    if (sw == "copy")
        str = list_l10n.copy_msg;
    else
        str = list_l10n.move_msg;

    var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");

    f.sw.value = sw;
    f.target = "move";
    f.action = gml_bbs_url + "/move.php";
    f.submit();
}
</script>
