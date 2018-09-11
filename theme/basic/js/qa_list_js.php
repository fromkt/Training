<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

get_localize_script('qa_list_l10n',
array(
'delete_pressed' => __('Delete Selection'),  //선택삭제
'select_msg'=>__('Please select at least one item to %s.'),   //%s 할 게시물을 하나 이상 선택하세요.
'delete_check_msg'=>__('Are you sure you want to delete it?'),  //정말 삭제하시겠습니까?
),
true);
?>

<script>
function all_checked(sw) {
    var f = document.fqalist;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_qa_id[]")
            f.elements[i].checked = sw;
    }
}

function fqalist_submit(f) {
    var chk_count = 0;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_qa_id[]" && f.elements[i].checked)
            chk_count++;
    }

    var todo = (document.pressed == 'delete_selection') ? qa_list_l10n.delete_pressed : '';

    if (!chk_count) {
        alert(js_sprintf(qa_list_l10n.select_msg, todo));
        return false;
    }

    if(todo) {
        if (!confirm(qa_list_l10n.delete_check_msg))
            return false;
    }

    return true;
}
</script>
