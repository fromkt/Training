<?php
if (!defined("_GNUBOARD_")) exit; // Unable to access direct pages

$get_member_profile_img = get_member_profile_img($member['mb_id']);

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$outlogin_skin_url.'/style.css">', 0);
?>

<!-- Start outlogin html { -->
<aside id="ol_after" class="ol">
    <h2><?php e__('My profile'); ?></h2>
    <div id="ol_after_hd">
    	<div class="ol_after_nick">
	        <span class="profile_img"><?php echo $get_member_profile_img ?></span>
	        	<!-- <a href="<?php echo GML_BBS_URL ?>/member_confirm.php?url=register_form.php" id="ol_after_info" title="<?php e__('Edit profile'); ?>"><?php e__('Edit profile'); ?></a> -->
	        <strong><?php echo $nick ?></strong>
        </div>
        
	    <div class="ol_after_btn">
	    	<a href="<?php echo GML_BBS_URL ?>/notice.php" class="btn_log btn_arm"><i class="fa fa-bell-o"></i><span class="sound_only"><?php e__('Alarm'); ?></span><span class="arm_cnt"><?php echo $member['mb_notice_cnt'] ?></span></a>
	        <?php if ($is_admin == 'super' || $is_auth) { ?><a href="<?php echo GML_ADMIN_URL ?>" class="btn_log"><i class="fa fa-1x fa-cog"></i><span class="sound_only"><?php e__('Admin'); ?></span></a><?php } ?>
	        <a href="<?php echo GML_BBS_URL ?>/logout.php" id="ol_after_logout" class="btn_log"><?php e__('Logout'); ?></a>
	    </div>
	</div>
	
    <ul id="ol_after_private">
    	<li id="ol_after_pt">
            <a href="<?php echo GML_BBS_URL ?>/point.php" target="_blank">
            	<div class="ms_point">
					<strong><?php echo $point ?></strong>  
				</div>
                <span><?php e__('Point'); ?></span>
            </a>
        </li>
        <li id="ol_after_memo">
            <a href="<?php echo GML_BBS_URL ?>/memo.php" target="_blank">
            	<div class="my_service ms_memo">
            		<i class="fa fa-envelope-o" aria-hidden="true"></i>
	            	<strong><?php echo $memo_not_read ?></strong>
            	</div>
                <span class="sound_only"><?php e__('Unread'); ?></span><span><?php e__('Memo'); ?></span>
            </a>
        </li>       
        <li id="ol_after_scrap">
            <a href="<?php echo GML_BBS_URL ?>/scrap.php" target="_blank">
            	<div class="my_service ms_scrap">
					<i class="fa fa-thumb-tack" aria-hidden="true"></i>
					<strong><?php echo $scrap_cnt ?></strong>
				</div>
        	<span><?php e__('Scrap'); ?></span>
        	</a>
        </li>
    </ul>
</aside>

<?php
get_localize_script('outlogin_skin2',
array(
'leave_msg'=>__('Are you sure you want to leave the membership?'),  // 정말 회원에서 탈퇴 하시겠습니까?
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
</script>
<!-- } End outlogin html -->
