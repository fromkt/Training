<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

get_localize_script('view_comment_l10n',
array(
'delete_check_msg'=>__('Are you sure you want to delete it?'),  //정말 삭제하시겠습니까?
'copy_comment_url_msg'=>__('Comment Url Copied.'),
'content_check_msg'=>__('The content contains the forbidden word (%s)'),     //내용에 금지단어(%s)가 포함되어있습니다
'comment_min_msg'=>__('Comment must be at least %s characters long.'),    //댓글은 %s글자 이상 쓰셔야 합니다.
'comment_max_msg'=>__('Comment must be written in not more than %s characters.'),
'enter_comment_msg'=>__('Please enter a comment.'),
'enter_name_msg'=>__('Please enter your name.'),
'enter_password_msg'=>__('Please enter your password.'),
),
true);
?>

<script>
var save_before = '';
var save_html = document.getElementById('bo_vc_w').innerHTML;
// 글자수 제한
var char_min = parseInt(<?php echo $comment_min ?>); // 최소
var char_max = parseInt(<?php echo $comment_max ?>); // 최대

if(char_min > 0 || char_max > 0) {
    check_byte('wr_content', 'char_count');
}

$(function() {
    //댓글열기
    $(".cmt_btn").click(function(){
        $(this).toggleClass("cmt_btn_op");
        $("#bo_vc").toggle();
    });

    $(document).on("keyup change", "textarea#wr_content[maxlength]", function() {
        var str = $(this).val()
        var mx = parseInt($(this).attr("maxlength"))
        if (str.length > mx) {
            $(this).val(str.substr(0, mx));
            return false;
        }
    });

    // 댓글 옵션창 열기
    $(".cmt_opt").on("click", function(){
        $(this).parent("div").children(".bo_vl_act").show();
    });

    // 댓글 옵션창 닫기
    $(document).mouseup(function (e){
        var container = $(".bo_vl_act");
        if( container.has(e.target).length === 0)
        container.hide();
    });
});

function good_and_write()
{
    var f = document.fviewcomment;
    if (fviewcomment_submit(f)) {
        f.is_good.value = 1;
        f.submit();
    } else {
        f.is_good.value = 0;
    }
}

function copy_comment(url)
{
    event.preventDefault();

    copyToClipboard(url);
    alert(view_comment_l10n.copy_comment_url_msg);
}

function fviewcomment_submit(f)
{
    var pattern = /(^\s*)|(\s*$)/g; // \s 공백 문자

    f.is_good.value = 0;

    var subject = "";
    var content = "";
    $.ajax({
        url: gml_bbs_url+"/ajax.filter.php",
        type: "POST",
        data: {
            "subject": "",
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

    if (content) {
        alert(js_sprintf(view_comment_l10n.content_check_msg, content));
        f.wr_content.focus();
        return false;
    }

    // 양쪽 공백 없애기
    var pattern = /(^\s*)|(\s*$)/g; // \s 공백 문자
    document.getElementById('wr_content').value = document.getElementById('wr_content').value.replace(pattern, "");
    if (char_min > 0 || char_max > 0)
    {
        check_byte('wr_content', 'char_count');
        var cnt = parseInt(document.getElementById('char_count').innerHTML);
        if (char_min > 0 && char_min > cnt)
        {
            alert(js_sprintf(view_comment_l10n.comment_min_msg, char_min));
            return false;
        } else if (char_max > 0 && char_max < cnt)
        {
            alert(js_sprintf(view_comment_l10n.comment_max_msg, char_max));
            return false;
        }
    }
    else if (!document.getElementById('wr_content').value)
    {
        alert(view_comment_l10n.enter_comment_msg);
        return false;
    }

    if (typeof(f.wr_name) != 'undefined')
    {
        f.wr_name.value = f.wr_name.value.replace(pattern, "");
        if (f.wr_name.value == '')
        {
            alert(view_comment_l10n.enter_name_msg);
            f.wr_name.focus();
            return false;
        }
    }

    if (typeof(f.wr_password) != 'undefined')
    {
        f.wr_password.value = f.wr_password.value.replace(pattern, "");
        if (f.wr_password.value == '')
        {
            alert(view_comment_l10n.enter_password_msg);
            f.wr_password.focus();
            return false;
        }
    }

    <?php if($is_guest) echo chk_captcha_js();  ?>

    set_comment_token(f);

    document.getElementById("btn_submit").disabled = "disabled";

    return true;
}

function comment_box(comment_id, work)
{
    var el_id,
        form_el = 'fviewcomment',
        respond = document.getElementById(form_el);

    // 댓글 아이디가 넘어오면 답변, 수정
    if (comment_id)
    {
        if (work == 'c')
            el_id = 'reply_' + comment_id;
        else
            el_id = 'edit_' + comment_id;
    }
    else
        el_id = 'bo_vc_w';

    if (save_before != el_id)
    {
        if (save_before)
        {
            document.getElementById(save_before).style.display = 'none';
        }

        document.getElementById(el_id).style.display = '';
        document.getElementById(el_id).appendChild(respond);
        //입력값 초기화
        document.getElementById('wr_content').value = '';

        // 댓글 수정
        if (work == 'cu')
        {
            document.getElementById('wr_content').value = document.getElementById('save_comment_' + comment_id).value;
            if (typeof char_count != 'undefined')
                check_byte('wr_content', 'char_count');
            if (document.getElementById('secret_comment_'+comment_id).value)
                document.getElementById('wr_secret').checked = true;
            else
                document.getElementById('wr_secret').checked = false;
        }

        document.getElementById('comment_id').value = comment_id;
        document.getElementById('w').value = work;

        if(save_before)
            $("#captcha_reload").trigger("click");

        save_before = el_id;
    }
}

function comment_delete()
{
    return confirm( view_comment_l10n.delete_check_msg );
}

comment_box('', 'c'); // 댓글 입력폼이 보이도록 처리하기위해서 추가 (root님)

<?php if($use_sns) { ?>
$(function() {
    // sns 등록
    $("#bo_vc_send_sns").load(
        "<?php echo GML_SNS_URL; ?>/view_comment_write.sns.skin.php?bo_table=<?php echo $bo_table; ?>",
        function() {
            save_html = document.getElementById('bo_vc_w').innerHTML;
        }
    );
});
<?php } ?>
</script>
