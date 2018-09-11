<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

get_localize_script('qa_write_l10n',
array(
'wrap_msg' => __('Do you want to wrap up the wrap?'),  //자동 줄바꿈을 하시겠습니까?
'convert_tag_msg'=>__('Word wrap is a function to convert a line from a post to a tag called <br>.'),   //자동 줄바꿈은 게시물 내용중 줄바뀐 곳을<br>태그로 변환하는 기능입니다.
'subject_check_msg'=>__('The subject contains the forbidden word (%s)'),  //제목에 금지단어(%s)가 포함되어있습니다
'content_check_msg'=>__('The content contains the forbidden word (%s)'),     //내용에 금지단어(%s)가 포함되어있습니다
),
true);
?>
<script>
function html_auto_br(obj)
{
    if (obj.checked) {
        result = confirm( qa_write_l10n.wrap_msg+"\n\n"+qa_write_l10n.convert_tag_msg );
        if (result)
            obj.value = "2";
        else
            obj.value = "1";
    }
    else
        obj.value = "";
}

function fwrite_submit(f)
{
    <?php echo $editor_js; // 에디터 사용시 자바스크립트에서 내용을 폼필드로 넣어주며 내용이 입력되었는지 검사함   ?>

    var subject = "";
    var content = "";
    $.ajax({
        url: gml_bbs_url+"/ajax.filter.php",
        type: "POST",
        data: {
            "subject": f.qa_subject.value,
            "content": f.qa_content.value
        },
        dataType: "json",
        async: false,
        cache: false,
        success: function(data, textStatus) {
            subject = data.subject;
            content = data.content;
        }
    });

    if (subject) {
        alert(js_sprintf(qa_write_l10n.subject_check_msg, subject));
        f.qa_subject.focus();
        return false;
    }

    if (content) {
        alert(js_sprintf(qa_write_l10n.content_check_msg, content));
        if (typeof(ed_qa_content) != "undefined")
            ed_qa_content.returnFalse();
        else
            f.qa_content.focus();
        return false;
    }

    document.getElementById("btn_submit").disabled = "disabled";

    return true;
}

$(document).ready(function(){
    $("#qa_sms_recv").click(function(){
        $(".bo_w_hp_ck").toggleClass("click_on");
    });

    $("#qa_email_recv").click(function(){
        $(".bo_w_mail_ck").toggleClass("click_on");
    });
});
</script>
