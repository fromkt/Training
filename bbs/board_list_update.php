<?php
include_once('./_common.php');

$count = count($_POST['chk_wr_id']);

if(!$count) {
    alert(sprintf(__('Please select at least one item to %s.'), $_POST['btn_submit']));
}

if($_POST['btn_submit'] == 'delete_selection') {    // Delete Selection 선택삭제
    include './delete_all.php';
} else if($_POST['btn_submit'] == 'copy_selection') {     // Copy Selection 선택복사
    $sw = 'copy';
    include './move.php';
} else if($_POST['btn_submit'] == 'move_Selection') {     // Move Selection 선택이동
    $sw = 'move';
    include './move.php';
} else {
    alert(__('Please use the correct method.'));
}
?>