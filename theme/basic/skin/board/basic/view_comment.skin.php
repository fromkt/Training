<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages
?>

<button class="cmt_btn"><?php e__('List of comments'); ?></button>

<!-- Start Comment { -->
<section id="bo_vc">
    <h2><?php e__('List of comments'); ?></h2>

    <?php for($i=0; $i<count($list); $i++) { ?>
    <article id="c_<?php echo $list[$i]['comment_id'] ?>" <?php if ($list[$i]['cmt_depth']) { ?>style="margin-left:<?php echo $list[$i]['cmt_depth'] ?>px;border-top-color:#e0e0e0"<?php } ?>>
        <header style="z-index:<?php echo $list[$i]['cmt_sv']; ?>">
            <h2><?php echo sprintf($list[$i]['cmt_depth'] ? __('Comment on comments by %s') : __('Comment by %s') , $list[$i]['wr_name']); ?></h2>
            <?php echo $list[$i]['name'] ?>
            <?php if ($is_ip_view) { ?>
            <span class="sound_only"><?php e__('IP'); ?></span>
            <span>(<?php echo $list[$i]['ip']; ?>)</span>
            <?php } ?>
            <span class="sound_only"><?php e__('Date'); ?></span>
            <span class="bo_vc_hdinfo"><i class="fa fa-clock-o" aria-hidden="true"></i> <time datetime="<?php echo $list[$i]['format_datetime'] ?>"><?php echo $list[$i]['datetime'] ?></time></span>
            <?php
            include(GML_SNS_PATH.'/view_comment_list.sns.skin.php');
            ?>
        </header>

        <!-- Comment Print -->
        <div class="cmt_contents">
            <p>
                <?php if ($list[$i]['is_secret']) { ?>
                <img src="<?php echo $board_skin_url; ?>/img/icon_secret.gif" alt="<?php e__('Secret'); ?>">
                <?php } ?>
                <?php echo $list[$i]['comment'] ?>
            </p>
        </div>
        <span id="edit_<?php echo $list[$i]['comment_id'] ?>" class="bo_vc_w"></span><!-- Edit -->
        <span id="reply_<?php echo $list[$i]['comment_id'] ?>" class="bo_vc_w"></span><!-- Reply -->

        <input type="hidden" value="<?php echo strstr($list[$i]['wr_option'],"secret") ?>" id="secret_comment_<?php echo $list[$i]['comment_id'] ?>">
        <textarea id="save_comment_<?php echo $list[$i]['comment_id'] ?>" style="display:none"><?php echo get_text($list[$i]['content1'], 0) ?></textarea>

		<div class="bo_vl_opt">
            <?php if($is_member) { ?>
			<button type="button" class="cmt_opt"><span class="sound_only"><?php e__('Comment Option Button'); ?></span><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>
            <?php if($list[$i]['is_reply'] || $list[$i]['is_edit'] || $list[$i]['is_del']) { ?>
            <ul class="bo_vl_act">
                <?php if ($list[$i]['is_reply']) { ?><li><a href="<?php echo $list[$i]['c_reply_href'];  ?>" onclick="comment_box('<?php echo $list[$i]['comment_id'] ?>', 'c'); return false;"><?php e__('Reply'); ?></a></li><?php } ?>
                <?php if ($list[$i]['is_edit']) { ?><li><a href="<?php echo $list[$i]['c_edit_href'];  ?>" onclick="comment_box('<?php echo $list[$i]['comment_id'] ?>', 'cu'); return false;"><?php e__('Edit'); ?></a></li><?php } ?>
                <?php if ($list[$i]['is_del'])  { ?><li><a href="<?php echo $list[$i]['del_link'];  ?>" onclick="return comment_delete();"><?php e__('Delete'); ?></a></li><?php } ?>
                <li><a href="<?php echo $list[$i]['comment_url'] ?>" onclick="copy_comment(this.href)"><?php e__('Copy URL'); ?></a></li>
            </ul>
            <?php }
            } ?>
		</div>
    </article>
    <?php } ?>
    <?php echo $no_comment; // No Comment ?>
</section>
<!-- } End Comment -->

<?php if ($is_comment_write) { ?>
<!-- Start writing comments { -->
<aside id="bo_vc_w" class="bo_vc_w">
    <h2><?php e__('Writing comments'); ?></h2>
    <form name="fviewcomment" id="fviewcomment" action="<?php echo $comment_action_url; ?>" onsubmit="return fviewcomment_submit(this);" method="post" autocomplete="off">
    <input type="hidden" name="w" value="<?php echo $w ?>" id="w">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">
    <input type="hidden" name="comment_id" value="<?php echo $c_id ?>" id="comment_id">
    <input type="hidden" name="sca" value="<?php echo $sca ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="is_good" value="">

    <span class="sound_only"><?php e__('Comments'); ?></span>
    <?php if ($comment_min || $comment_max) { ?><strong id="char_cnt"><span id="char_count"></span><?php e__('length'); ?></strong><?php } ?>
    <textarea id="wr_content" name="wr_content" maxlength="10000" required class="required" title="<?php e__('Comments'); ?>" placeholder="<?php e__('Enter comments'); ?>"
    <?php if ($comment_min || $comment_max) { ?>onkeyup="check_byte('wr_content', 'char_count');"<?php } ?>><?php echo $c_wr_content; ?></textarea>
    <div class="bo_vc_w_wr">
        <div class="bo_vc_w_info">
            <?php if ($is_guest) { ?>
            <label for="wr_name" class="sound_only"><?php e__('Name'); ?><strong> <?php e__('Required'); ?></strong></label>
            <input type="text" name="wr_name" value="<?php echo $get_cookie_sns_name ?>" id="wr_name" required class="frm_input required" size="25" placeholder="<?php e__('Name'); ?>">
            <label for="wr_password" class="sound_only"><?php e__('Password'); ?><strong> <?php e__('Required'); ?></strong></label>
            <input type="password" name="wr_password" id="wr_password" required class="frm_input required" size="25"  placeholder="<?php e__('Password'); ?>">
            <?php
            }
            ?>
            <?php
            if($use_sns) {
            ?>
            <span class="sound_only"><?php e__('Write SNS at the same'); ?></span>
            <span id="bo_vc_send_sns"></span>
            <?php } ?>
            <?php if ($is_guest) { ?>
                <?php echo $captcha_html; ?>
            <?php } ?>
        </div>
        <div class="btn_confirm">
            <label for="wr_secret" class="wr_secret_ck"><?php e__('Use Secret'); ?></label>
            <input type="checkbox" name="wr_secret" value="secret" id="wr_secret">
            <button type="submit" id="btn_submit" class="btn_submit"><?php e__('Add comment'); ?></button>
        </div>
        <script>
	        $(document).on('click', '#wr_secret', function(){
				$(".wr_secret_ck").toggleClass("click_on");
			});
        </script>
    </div>
    </form>
</aside>
<?php } ?>
<!-- } End writing comments -->
