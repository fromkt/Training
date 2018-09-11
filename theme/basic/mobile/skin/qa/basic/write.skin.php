<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

$option = '';
$option_hidden = '';

if ($is_dhtml_editor) {
    $option_hidden .= '<input type="hidden" name="qa_html" value="1">';
} else {
    $option .= "\n".'<input type="checkbox" id="qa_html" name="qa_html" onclick="html_auto_br(this);" value="'.$html_value.'" '.$html_checked.'>'."\n".'<label for="qa_html">html</label>';
}

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$qa_skin_url.'/style.css">', 0);
?>

<section id="bo_w">
    <!-- Start Write/Update 1:1 Contact { -->
    <form name="fwrite" id="fwrite" action="<?php echo $action_url ?>" onsubmit="return fwrite_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
    <input type="hidden" name="w" value="<?php echo $w ?>">
    <input type="hidden" name="qa_id" value="<?php echo $qa_id ?>">
    <input type="hidden" name="sca" value="<?php echo $sca ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <?php echo $option_hidden ?>

    <div class="form_01">
        <?php if ($category_option) { ?>
        <div class="bo_w_select">
            <label for="qa_category" class="sound_only"><?php e__('Category'); ?><strong><?php e__('Required'); ?></strong></label>
            <select name="qa_category" id="qa_category" required>
                <option value=""><?php e__('Select a Category'); ?></option>
                <?php echo $category_option ?>
            </select>
        </div>
        <?php } ?>
		<ul>
            <?php if ($option) { ?>
            <li>
                <span class="sound_only"><?php e__('Option'); ?></span>
                <?php echo $option; ?>
            </li>
            <?php } ?>

            <?php if ($is_email) { ?>
            <li class="bo_w_mail">
                <label for="qa_email" class="sound_only"><?php e__('Email'); ?></label>
                <input type="email" name="qa_email" value="<?php echo get_text($write['qa_email']); ?>" id="qa_email" <?php echo $req_email; ?> class="<?php echo $req_email.' '; ?> frm_input full_input email" maxlength="100" placeholder="<?php e__('Email'); ?>">
                <div class="bo_w_mail_ck">
                	<input type="checkbox" name="qa_email_recv" value="1" id="qa_email_recv" <?php if($write['qa_email_recv']) echo 'checked="checked"'; ?>>
                	<label for="qa_email_recv" class="frm_info"><?php e__('Receive by email'); ?></label>
                </div>
            </li>
            <?php } ?>

            <?php if ($is_hp) { ?>
            <li class="bo_w_hp">
                <label for="qa_hp" class="sound_only"><?php e__('Mobile phone'); ?></label>
                <input type="text" name="qa_hp" value="<?php echo get_text($write['qa_hp']); ?>" id="qa_hp" <?php echo $req_hp; ?> class="<?php echo $req_hp.' '; ?>frm_input full_input" size="30" placeholder="<?php e__('Mobile phone'); ?>">
                <?php if($qaconfig['qa_use_sms']) { ?>
                <div class="bo_w_hp_ck">	
                	<input type="checkbox" id="qa_sms_recv" name="qa_sms_recv" value="1" <?php if($write['qa_sms_recv']) echo 'checked="checked"'; ?>>
                	<label for="qa_sms_recv" class="frm_info"><?php e__('Receive by SMS'); ?></label>
                </div>
                <?php } ?>
            </li>
            <?php } ?>

            <li>
                <label for="qa_subject" class="sound_only"><?php e__('Subject'); ?><strong><?php e__('Required'); ?></strong></label>
                <input type="text" name="qa_subject" value="<?php echo get_text($write['qa_subject']); ?>" id="qa_subject" required class="frm_input required" maxlength="255" placeholder="<?php e__('Subject'); ?>">
            </li>

            <li>
               <label for="qa_content" class="sound_only"><?php e__('Content'); ?><strong><?php e__('Required'); ?></strong></label>
                <div class="wr_content">
                    <?php echo $editor_html; // When using the editor, expose it as an editor or as a textarea ?>
                </div>
            </li>

            <li class="bo_w_flie">
                <div class="file_wr">
                    <span class="lb_icon"><i class="fa fa-download" aria-hidden="true"></i><span class="sound_only"><?php e__('File #1'); ?></span></span>
                    <input type="file" name="bf_file[1]" title="<?php echo sprintf(__('File Attachment %s : Upload capacity less than %s'), 1, $upload_max_filesize); ?>" class="frm_file">
                    <?php if($w == 'u' && $write['qa_file1']) { ?>
                    <input type="checkbox" id="bf_file_del1" name="bf_file_del[1]" value="1"> <label for="bf_file_del1"><?php echo $write['qa_source1']; ?> <?php e__('Delete File'); ?></label>
                    <?php } ?>
                </div>
            </li>

            <li class="bo_w_flie">
                <div class="file_wr">
                    <span class="lb_icon"><i class="fa fa-download" aria-hidden="true"></i><span class="sound_only"><?php e__('File #2'); ?></span></span>
                    <input type="file" name="bf_file[2]" title="<?php echo sprintf(__('File Attachment %s : Upload capacity less than %s'), 1, $upload_max_filesize); ?>" class="frm_file">
                    <?php if($w == 'u' && $write['qa_file2']) { ?>
                    <input type="checkbox" id="bf_file_del2" name="bf_file_del[2]" value="1"> <label for="bf_file_del2"><?php echo $write['qa_source2']; ?> <?php e__('Delete File'); ?></label>
                    <?php } ?>
                </div>
            </li>

        </ul>
    </div>

    <div class="bo_w_btn">
        <a href="<?php echo $list_href; ?>" class="btn_cancel"><?php e__('List'); ?></a>
        <button type="submit" value="<?php e__('Save'); ?>" id="btn_submit" accesskey="s" class="btn_submit"><?php e__('Save'); ?></button>
    </div>
    </form>

    <?php include_once(GML_THEME_JS_PATH. '/qa_write_js.php'); ?>
</section>
<!-- } End Write/Update 1:1 Contact -->
