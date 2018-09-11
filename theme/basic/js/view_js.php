<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

get_localize_script('view_l10n',
array(
'copy_post_msg'=>__('You copied the post!'),
'download_not_msg'=>__('You do not have download privileges.'),     //다운로드 권한이 없습니다.
'member_login_msg'=>__('If you are a member, please Log in and use it.'),   //회원이시라면 로그인 후 이용해 보십시오.
'point_msg1'=>__('Download the file and the point will be deducted (%s points).'),
'point_msg2'=>__('Points will be deducted only once per post and will not be deducted from the next download.'),
'point_msg3'=>__('Do you still want to download?'),
'good_msg'=>__('This post has Good.'),
'bad_msg'=>__('This post has Bad.'),
),
true);
?>

<script>
<?php if ($board['bo_download_point'] < 0) { ?>
$(function() {
    $("a.view_file_download").click(function() {
        if(!gml_is_member) {
            alert(view_l10n.download_not_msg+"\n"+view_l10n.member_login_msg);
            return false;
        }

        var msg = js_sprintf(view_l10n.point_msg1, <?php echo number_format($board['bo_download_point']) ?>)+"\n\n"+view_l10n.point_msg2+"\n\n"+view_l10n.point_msg3;

        if(confirm(msg)) {
            var href = $(this).attr("href")+"&js=on";
            $(this).attr("href", href);

            return true;
        } else {
            return false;
        }
    });
});
<?php } ?>

function board_move(href)
{
    window.open(href, "boardmove", "left=50, top=50, width=500, height=550, scrollbars=1");
}

$(function() {
    $("a.view_image").click(function() {
        window.open(this.href, "large_image", "location=yes,links=no,toolbar=no,top=10,left=10,width=10,height=10,resizable=yes,scrollbars=no,status=no");
        return false;
    });

    // 추천, 비추천
    $("#good_button, #nogood_button").click(function() {
        var $tx;
        if(this.id == "good_button")
            $tx = $("#bo_v_act_good");
        else
            $tx = $("#bo_v_act_nogood");

        excute_good(this.href, $(this), $tx);
        return false;
    });

    // 이미지 리사이즈
    $("#bo_v_atc").viewimageresize();

    //sns공유
    $("#btn_share").click(function(){
        $("#bo_v_sns").fadeIn();
    });

    $(document).mouseup(function (e) {
        var container = $("#bo_v_sns");
        if (!container.is(e.target) && container.has(e.target).length === 0){
        container.css("display","none");
        }
    });

    $(document).mouseup(function (e) {
        var container = $("#bo_v_opt");
        if (!container.is(e.target) && container.has(e.target).length === 0){
            container.css("display","none");
        }
    });

    $(document).mouseup(function (e){
        var container = $(".bo_vl_act");
        if( container.has(e.target).length === 0)
        container.hide();
    });

    //게시글 옵션
    $(".bo_v_opt").click(function(){
        $("#bo_v_opt").fadeIn();
    });

    // Copy Post
    $("#copy_post").click(function() {
        var copy_arr = new Array();
        var post_title = $(".bo_v_tit").text().trim();
        var post_contents = $("#bo_v_con").text().trim();
        if(post_title.length > 0) {
            copy_arr.push(post_title);
        }
        if(post_contents.length > 0) {
            copy_arr.push(post_contents);
        }
        var copy_str = copy_arr.join("\n\n");
        copyToClipboard(copy_str);
        alert(view_l10n.copy_post_msg);
    });
});

function excute_good(href, $el, $tx)
{
    $.post(
        href,
        { js: "on" },
        function(data) {
            if(data.error) {
                alert(data.error);
                return false;
            }

            if(data.count) {
                $el.find("strong").text(number_format(String(data.count)));
                if($tx.attr("id").search("nogood") > -1) {
                    $tx.text(view_l10n.bad_msg);
                    $tx.fadeIn(200).delay(2500).fadeOut(200);
                } else {
                    $tx.text(view_l10n.good_msg);
                    $tx.fadeIn(200).delay(2500).fadeOut(200);
                }
            }
        }, "json"
    );
}
</script>
