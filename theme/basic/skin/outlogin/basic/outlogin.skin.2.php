<?php
if (!defined("_GNUBOARD_")) exit; // Unable to access direct pages

$get_member_profile_img = get_member_profile_img($member['mb_id']);

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$outlogin_skin_url.'/style.css">', 0);
?>

<!-- Start outlogin html { -->
<section id="ol_after" class="ol">
    <header id="ol_after_hd">
        <h2><?php e__('My profile'); ?></h2>
        <?php echo $get_member_profile_img ?>
        <div class="ol_btn">
        	<strong><?php echo $nick ?></strong>
        	<?php if ($is_admin == 'super' || $is_auth) {  ?><a href="<?php echo GML_ADMIN_URL ?>" class="btn_admin btn_04"><?php e__('Admin'); ?></a><?php }  ?>
    		<a href="<?php echo GML_BBS_URL ?>/member_confirm.php?url=register_form.php" id="ol_after_info" title="<?php e__('Edit profile'); ?>" class="btn_b04"><?php e__('Edit profile'); ?></a>
    		<a href="<?php echo GML_BBS_URL ?>/logout.php" id="ol_after_logout" class="btn_b04"><?php e__('Logout'); ?></a>
        </div>
    </header>
    <ul id="ol_after_private">
    	<li class="li_point">
            <a href="<?php echo GML_BBS_URL ?>/point.php" target="_blank" id="ol_after_pt" class="win_point">
            	<span><i class="fa fa-database" aria-hidden="true"></i> <?php e__('Point'); ?></span>
            	<span class="mm_value"><?php echo $point ?></span>
            </a>
        </li>
    	<li class="li_scrap">
            <a href="<?php echo GML_BBS_URL ?>/scrap.php" target="_blank" id="ol_after_scrap" class="win_scrap">
            	<span><i class="fa fa-thumb-tack" aria-hidden="true"></i> <?php e__('Scrap'); ?></span>
            	<span class="mm_value"><?php echo $scrap_cnt ?></span>
            </a>
        </li>
    	<li class="li_memo">
            <a href="<?php echo GML_BBS_URL ?>/memo.php" target="_blank" id="ol_after_memo" class="win_memo">
                <span><i class="fa fa-envelope-o" aria-hidden="true"></i><span class="sound_only"><?php e__('Unread'); ?></span><?php e__('Memo'); ?></span>
                <span class="mm_value"><?php echo $memo_not_read ?></span>
            </a>
        </li>
        <li class="li_alarm">
        	<button class="win_alarm">
        		<span><i class="fa fa-bell-o"></i> <?php e__('Alarm'); ?></span>
        		<span class="mm_value"><?php echo $notice_cnt ?></span>
        	</button>
        </li>
    </ul>

</section>

<div id="my_alarm_list">
	<h3><?php e__('Alarm') ?></h3>
	<a href="<?php echo GML_BBS_URL ?>/notice.php" class="all_arm"><?php e__('View all') ?></a>
	<ul>
	</ul>
</div>


<?php
get_localize_script('outlogin_skin2',
array(
'leave_msg'=>__('Are you sure you want to leave the membership?'),  // 정말 회원에서 탈퇴 하시겠습니까?
'no_notices' => __('No new notices found'),   // 새로운 알림이 없습니다.
),
true);
?>
<script>
// In case of withdrawal, please link the following code.
function member_leave()
{
    if (confirm(outlogin_skin2.leave_msg))
        location.href = "<?php echo GML_BBS_URL ?>/member_confirm.php?url=member_leave.php";
}

$(document).ready(function(){
    var open = 0;
    // toggle notice preview tab
	$(".win_alarm").click(function(){
        if(!open) {
            $.ajax({
                url: gml_bbs_url + "/ajax.notice_preview.php",
                type: 'post',
                datatype: 'json',
                async: false,
                cache: false,
                success: function(data) {
                    $("#my_alarm_list ul").html(data);
                }
            });
            open = 1;
            $("#my_alarm_list").show();
        } else {
            open = 0;
            $("#my_alarm_list").hide();
        }
	});

    // When click 'X' button, mark as read and delete
    $(document).on('click', '.list_del', function(){
        var no_id = $(this).attr("id");
        var data = {
            chk_no_id : no_id
        };

        var $li = $(this).parents("li");
        var idx = $("li").index($li);

        $.ajax({
            url: gml_bbs_url + "/ajax.notice_read.php",
            type: "post",
            data: data,
            dataType: "json",
            async: false,
            cache: false,
            success: function(data) {
                $("li").eq(idx).remove();
                if($("#my_alarm_list ul li").size() == 0) {
                    html = "<li>"+outlogin_skin2.no_notices+"</li>";
                    $("#my_alarm_list ul").html(html);
                }
            }
        });
    });

    // When you click on a notice sentence, the link is marked as read and move to the link.
    $(document).on('click', '#my_alarm_list ul li a', function(){
        event.preventDefault();

        var no_id = $(this).siblings(".list_del").attr("id");
        var data = {
            chk_no_id : no_id
        };

        var $li = $(this).parents("li");
        var idx = $("li").index($li);

        var href = this.href;
        var onclick = $(this).attr('onclick');

        $.ajax({
            url: gml_bbs_url + "/ajax.notice_read.php",
            type: "post",
            data: data,
            dataType: "json",
            async: false,
            cache: false,
            success: function(data) {
                if(onclick) {
                    $("li").eq(idx).remove();
                    if($("#my_alarm_list ul li").size() == 0) {
                        html = "<li>"+outlogin_skin2.no_notices+"</li>";
                        $("#my_alarm_list ul").html(html);
                    }
                    window[onclick]();
                } else {
                    document.location.href = href;
                }
            }
        });
    });

});
</script>
<!-- } End outlogin html -->
