<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

get_localize_script('write_l10n',
array(
'wrap_msg' => __('Do you want to wrap up the wrap?'),  //자동 줄바꿈을 하시겠습니까?
'convert_tag_msg'=>__('Word wrap is a function to convert a line from a post to a tag called <br>.'),   //자동 줄바꿈은 게시물 내용중 줄바뀐 곳을<br>태그로 변환하는 기능입니다.
'subject_check_msg'=>__('The subject contains the forbidden word (%s)'),  //제목에 금지단어(%s)가 포함되어있습니다
'content_check_msg'=>__('The content contains the forbidden word (%s)'),     //내용에 금지단어(%s)가 포함되어있습니다
'content_min_msg'=>__('content must be at least %s characters long.'),
'content_max_msg'=>__('content must be written in not more than %s characters.'),
),
true);
?>

<script>
<?php if($use_character_number) { ?>
// 글자수 제한
var char_min = parseInt(<?php echo $write_min; ?>); // 최소
var char_max = parseInt(<?php echo $write_max; ?>); // 최대
check_byte("wr_content", "char_count");

$(function() {
    $("#wr_content").on("keyup", function() {
        check_byte("wr_content", "char_count");
    });
});

<?php } ?>
function html_auto_br(obj)
{
    if (obj.checked) {
        result = confirm( write_l10n.wrap_msg+"\n\n"+write_l10n.convert_tag_msg );
        if (result)
            obj.value = "html2";
        else
            obj.value = "html1";
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
            "subject": f.wr_subject.value,
            "content": f.wr_content.value
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
        alert(js_sprintf(write_l10n.subject_check_msg, subject));
        f.wr_subject.focus();
        return false;
    }

    if (content) {
        alert(js_sprintf(write_l10n.content_check_msg, content));
        if (typeof(ed_wr_content) != "undefined")
            ed_wr_content.returnFalse();
        else
            f.wr_content.focus();
        return false;
    }

    if (document.getElementById("char_count")) {
        if (char_min > 0 || char_max > 0) {
            var cnt = parseInt(check_byte("wr_content", "char_count"));
            if (char_min > 0 && char_min > cnt) {
                alert(js_sprintf(write_l10n.content_min_msg, char_min));
                return false;
            }
            else if (char_max > 0 && char_max < cnt) {
                alert(js_sprintf(write_l10n.content_max_msg, char_max));
                return false;
            }
        }
    }

    <?php echo $captcha_js; // 캡챠 사용시 자바스크립트에서 입력된 캡챠를 검사함  ?>

    document.getElementById("btn_submit").disabled = "disabled";

    return true;
}
</script>
