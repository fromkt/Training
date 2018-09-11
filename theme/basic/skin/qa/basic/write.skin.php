<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

$option = '';
$option_hidden = '';

if ($is_dhtml_editor) {
    $option_hidden .= '<input type="hidden" name="qa_html" value="1">';
} else {
    $option .= "\n".'<input type="checkbox" id="qa_html" name="qa_html" onclick="html_auto_br(this);" value="'.$html_value.'" '.$html_checked.'>'."\n".'<label for="qa_html">html</label>';
}

$qa_content_li_class = $is_dhtml_editor ? $config['cf_editor'] : '';

// add_stylesheet('css file path', Output order); Smaller numbers printed first
add_stylesheet('<link rel="stylesheet" href="'.$qa_skin_url.'/style.css">', 0);
?>

<section id="bo_w">
    <h2><?php e__('Write 1:1 Contact'); ?></h2>
    <!-- Start Write 1:1 Contact { -->
    <form name="fwrite" id="fwrite" action="<?php echo $action_url ?>" onsubmit="return fwrite_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
    <input type="hidden" name="w" value="<?php echo $w ?>">
    <input type="hidden" name="qa_id" value="<?php echo $qa_id ?>">
    <input type="hidden" name="sca" value="<?php echo $sca ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <?php echo $option_hidden ?>

    <div class="form_01">
        <ul>
            <?php if ($category_option) { ?>
            <li>
                <label for="qa_category" class="sound_only"><?php e__('Category'); ?><strong><?php e__('Required'); ?></strong></label>
                <select name="qa_category" id="qa_category" required >
                    <option value=""><?php e__('Select a Category'); ?></option>
                    <?php echo $category_option ?>
                </select>
            </li>
            <?php } ?>

            <?php if ($is_email) { ?>
            <li class="bo_w_mail">
                <label for="qa_email" class="sound_only"><?php e__('Email'); ?></label>
                <input type="text" name="qa_email" value="<?php echo get_text($write['qa_email']); ?>" id="qa_email" <?php echo $req_email; ?> class="<?php echo $req_email.' '; ?>frm_input full_input email" size="50" maxlength="100" placeholder="<?php e__('email'); ?>">
                <div class="bo_w_mail_ck">
                	<input type="checkbox" name="qa_email_recv" id="qa_email_recv" value="1" <?php if($write['qa_email_recv']) echo 'checked="checked"'; ?>>
                	<label for="qa_email_recv" class="frm_info"><?php e__('Receive by email'); ?></label>
                </div>
            </li>
            <?php } ?>

            <?php if ($is_hp) { ?>
            <li class="bo_w_hp">
                <label for="qa_hp" class="sound_only"><?php e__('Mobile phone'); ?></label>
                <input type="text" name="qa_hp" value="<?php echo get_text($write['qa_hp']); ?>" id="qa_hp" <?php echo $req_hp; ?> class="<?php echo $req_hp.' '; ?>frm_input full_input" size="30" placeholder="휴대폰">
                <?php if($qaconfig['qa_use_sms']) { ?>
                <input type="checkbox" name="qa_sms_recv" id="qa_sms_recv" value="1" <?php if($write['qa_sms_recv']) echo 'checked="checked"'; ?>> <label for="qa_sms_recv" class="frm_info"><?php e__('Receive by SMS'); ?></label>
                <?php } ?>
            </li>
            <?php } ?>

            <li class="bo_w_sbj">
                <label for="qa_subject" class="sound_only"><?php e__('Subject'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label>
                <input type="text" name="qa_subject" value="<?php echo get_text($write['qa_subject']); ?>" id="qa_subject" required class="frm_input full_input required" size="50" maxlength="255" placeholder="<?php e__('Subject'); ?>">
            </li>

            <li class="qa_content_wrap <?php echo $qa_content_li_class ?>">
                <label for="qa_content" class="sound_only"><?php e__('Content'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label>
                <?php echo $editor_html; // When using the editor, expose it as an editor or as a textarea ?>
            </li>

            <?php if ($option) { ?>
            <li>
                <?php e__('Option'); ?>
                <?php echo $option; ?>
            </li>
            <?php } ?>

            <li class="bo_w_flie">
                <div class="file_wr">
                    <label for="bf_file_1" class="lb_icon"><i class="fa fa-download" aria-hidden="true"></i><span class="sound_only"> <?php e__('File #1'); ?></span></label>
                    <input type="file" name="bf_file[1]" id="bf_file_1" title="<?php echo sprintf(__('File Attachment %s : Upload capacity less than %s'), 1, $upload_max_filesize); ?>" class="frm_file">
                    <?php if($w == 'u' && $write['qa_file1']) { ?>
                    <input type="checkbox" id="bf_file_del1" name="bf_file_del[1]" value="1"> <label for="bf_file_del1"><?php echo $write['qa_source1']; ?> <?php e__('Delete File'); ?></label>
                    <?php } ?>
                </div>
            </li>

            <li class="bo_w_flie">
                <div class="file_wr">
                    <label for="bf_file_2" class="lb_icon"><i class="fa fa-download" aria-hidden="true"></i><span class="sound_only"> <?php e__('File #2'); ?></span></label>
                    <input type="file" name="bf_file[2]" id="bf_file_2" title="<?php echo sprintf(__('File Attachment %s : Upload capacity less than %s'), 1, $upload_max_filesize); ?>" class="frm_file">
                    <?php if($w == 'u' && $write['qa_file2']) { ?>
                    <input type="checkbox" id="bf_file_del2" name="bf_file_del[2]" value="1"> <label for="bf_file_del2"><?php echo $write['qa_source2']; ?> <?php e__('Delete File'); ?></label>
                    <?php } ?>
                </div>
            </li>
        </ul>
    </div>

    <div class="btn_confirm">
        <a href="<?php echo $list_href; ?>" class="btn_cancel btn"><?php e__('List'); ?></a>
        <button type="submit" value="<?php e__('Save'); ?>" id="btn_submit" accesskey="s" class="btn_submit btn"><?php e__('Write'); ?></button>
    </div>
    </form>

    <?php include_once(GML_THEME_JS_PATH. '/qa_write_js.php'); ?>
</section>
<!-- } End Write 1:1 Contact -->
