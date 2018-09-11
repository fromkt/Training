<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
?>

<script>
$(function() {
    $("a.view_image").click(function() {
        window.open(this.href, "large_image", "location=yes,links=no,toolbar=no,top=10,left=10,width=10,height=10,resizable=yes,scrollbars=no,status=no");
        return false;
    });

    // Resize Image
    $("#bo_v_atc").viewimageresize();
});
</script>
