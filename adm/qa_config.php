<?php
$sub_menu = "300500";
include_once('./_common.php');
include_once(GML_EDITOR_LIB);

auth_check($auth[$sub_menu], 'r');

$gml['title'] = __('1:1 Contact Settings');
include_once ('./admin.head.php');

$qaconfig = get_qa_config();
?>

<form name="fqaconfigform" id="fqaconfigform" method="post" onsubmit="return fqaconfigform_submit(this);" autocomplete="off">
<input type="hidden" name="token" value="" id="token">

<section id="anc_cf_qa_config">
    <h2 class="h2_frm"><?php e__('1:1 Contact Settings'); ?></h2>

    <div class="frm_wr">
        <ul class="frm_ul">

            <li>
                <span class="lb_block"><label for="qa_title"><?php e__('Title'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label></span>
               
                <input type="text" name="qa_title" value="<?php echo $qaconfig['qa_title'] ?>" id="qa_title" required class="required frm_input" size="40">
                <a href="<?php echo GML_BBS_URL; ?>/qalist.php" class="btn_frmline"><?php e__('Redirect 1:1 Contact'); ?></a>
                
            </li>
            <li>
                <span class="lb_block"><label for="qa_category"><?php _('Category'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label>
               
                <?php echo help(__('Divide the classification by | between the classification. (Example : Question | answer) Do not enter the first letter #. (Example : # Question | # Answer [X])')); ?></span>

                <input type="text" name="qa_category" value="<?php echo $qaconfig['qa_category'] ?>" id="qa_category" required class="required frm_input frm_input_full" size="70">

            </li>
            <li class="li_50">
                <span class="lb_block"><label for="qa_skin"><?php e__('Skin Directory'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label></span>

                <?php echo get_skin_select('qa', 'qa_skin', 'qa_skin', $qaconfig['qa_skin'], 'required'); ?>

            </li>
            <li class="li_50">
                <span class="lb_block"><label for="qa_mobile_skin"><?php e__('Mobile Skin Directory'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label></span>

                <?php echo get_mobile_skin_select('qa', 'qa_mobile_skin', 'qa_mobile_skin', $qaconfig['qa_mobile_skin'], 'required'); ?>

            </li>
            <li class="li_50">
                <span class="lb_block"><?php e__('Enter Email'); ?></span>
               
                <input type="checkbox" name="qa_use_email" value="1" id="qa_use_email" <?php echo $qaconfig['qa_use_email']?'checked':''; ?>> <label for="qa_use_email"><?php e__('Show'); ?></label>
                <input type="checkbox" name="qa_req_email" value="1" id="qa_req_email" <?php echo $qaconfig['qa_req_email']?'checked':''; ?>> <label for="qa_req_email"><?php e__('Required'); ?></label>
                
            </li>
            <li class="li_50">
                <span class="lb_block"><?php e__('Input mobile number'); ?></span>
               
                <input type="checkbox" name="qa_use_hp" value="1" id="qa_use_hp" <?php echo $qaconfig['qa_use_hp']?'checked':''; ?>> <label for="qa_use_hp"><?php e__('Show'); ?></label>
                <input type="checkbox" name="qa_req_hp" value="1" id="qa_req_hp" <?php echo $qaconfig['qa_req_hp']?'checked':''; ?>> <label for="qa_req_hp"><?php e__('Required'); ?></label>
                
            </li>
            <li class="li_clear">
                <span class="lb_block"><label for="qa_admin_email"><?php e__('Administrator Email'); ?></label>
                <?php echo help(__('If you enter an administrator email, the notification will be sent to the registered email when you register your inquiry.')); ?></span>

                <input type="text" name="qa_admin_email" value="<?php echo $qaconfig['qa_admin_email'] ?>" id="qa_admin_email" class="frm_input  frm_input_full"  size="50">

            </li>
            <li class="li_clear">
                <span class="lb_block"><label for="qa_use_editor"><?php e__('Enable DHTML Editor'); ?></label>
                <?php echo help(__('Set whether content is used as DHTML Editor function when writing. Depending on the skin, it may not apply.')); ?></span>
                <select name="qa_use_editor" id="qa_use_editor">
                    <?php echo option_selected(0, $qaconfig['qa_use_editor'], __('Disable') ); ?>
                    <?php echo option_selected(1, $qaconfig['qa_use_editor'], __('Enable') ); ?>
                </select>

            </li>
            <li class="li_50">
                <span class="lb_block"><label for="qa_subject_len"><?php e__('Title Length'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label>
                <?php echo help(__('Number of title letters in the list')); ?></span>
                <input type="text" name="qa_subject_len" value="<?php echo $qaconfig['qa_subject_len'] ?>" id="qa_subject_len" required class="required numeric frm_input" size="4">
                
            </li>
            <li class="li_50">
                <span class="lb_block"><label for="qa_mobile_subject_len"><?php e__('Title Length in Mobile'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label>
                <?php echo help(__('Number of title letters in the list')); ?></span>
                <input type="text" name="qa_mobile_subject_len" value="<?php echo $qaconfig['qa_mobile_subject_len'] ?>" id="qa_mobile_subject_len" required class="required numeric frm_input" size="4">
                
            </li>
            <li class="li_50">
                <span class="lb_block"><label for="qa_page_rows"><?php e__('List per page'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label></span>
                <input type="text" name="qa_page_rows" value="<?php echo $qaconfig['qa_page_rows'] ?>" id="qa_page_rows" required class="required numeric frm_input"  size="4">

            </li>
            <li class="li_50">
                <span class="lb_block"><label for="qa_mobile_page_rows"><?php e__('List per page in Mobile'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label></span>
                <input type="text" name="qa_mobile_page_rows" value="<?php echo $qaconfig['qa_mobile_page_rows'] ?>" id="qa_mobile_page_rows" required class="required numeric frm_input" size="4">
                
            </li>
            <li class="li_50">
                <span class="lb_block"><label for="qa_image_width"><?php e__('Image Width Size'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label>
                <?php echo help(__('Width size of the image that is output from the Board')); ?></span>
                <input type="text" name="qa_image_width" value="<?php echo $qaconfig['qa_image_width'] ?>" id="qa_image_width" required class="required numeric frm_input" size="4"> <?php e__('Pixel'); ?>
                
            </li>
            <li class="li_50">
                <span class="lb_block"><label for="qa_upload_size"><?php e__('File upload Size'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label>
                <?php echo help(sprintf(__('Up to %s uploadable'), ini_get("upload_max_filesize")).', 1 MB = 1,048,576 bytes'); ?></span>
                <?php e__('Per upload file'); ?> <input type="text" name="qa_upload_size" value="<?php echo $qaconfig['qa_upload_size'] ?>" id="qa_upload_size" required class="required numeric frm_input" size="10"> <?php e__('Bytes below'); ?>

            </li>
            <li class="li_50">
                <span class="lb_block"><label for="qa_include_head"><?php e__('Header File Path'); ?></label></span>
                <?php echo get_include_head_select('qa_include_head', 'qa_include_head', $qaconfig['qa_include_head']); ?>
            </li>
            <li class="li_50">
                <span class="lb_block"><label for="qa_include_tail"><?php e__('Footer File Path'); ?></label></span>
                <?php echo get_include_tail_select('qa_include_tail', 'qa_include_tail', $qaconfig['qa_include_tail']); ?>
            </li>
            <li class="li_clear">
                <span class="lb_block"><label for="qa_content_head"><?php e__('Header Content'); ?></label></span>
                <?php echo editor_html("qa_content_head", get_text($qaconfig['qa_content_head'], 0)); ?>

            </li>
            <li>
                <span class="lb_block"><label for="qa_content_tail"><?php e__('Footer Content'); ?></label></span>
                <?php echo editor_html("qa_content_tail", get_text($qaconfig['qa_content_tail'], 0)); ?>

            </li>
            <li>
                <span class="lb_block"><label for="qa_mobile_content_head"><?php e__('Header of Mobile File Path'); ?></label></span>
                <?php echo editor_html("qa_mobile_content_head", get_text($qaconfig['qa_mobile_content_head'], 0)); ?>

            </li>
            <li>
                <span class="lb_block"><label for="qa_mobile_content_tail"><?php e__('Footer of Mobile Content'); ?></label></span>
                <?php echo editor_html("qa_mobile_content_tail", get_text($qaconfig['qa_mobile_content_tail'], 0)); ?>

            </li>
            <li>
                <span class="lb_block"><label for="qa_insert_content"><?php e__('Writing Basics'); ?></label></span>
                <textarea id="qa_insert_content" name="qa_insert_content" rows="5"><?php echo $qaconfig['qa_insert_content'] ?></textarea>

            </li>
            <?php for ($i=1; $i<=5; $i++) { ?>
            <li class="extra_ul">
                <span class="lb_block"><?php echo sprintf(__('Extra field %d'), $i); ?></span>
                <label for="qa_<?php echo $i ?>_subj" class="extra_lb"><?php echo sprintf(__('Extra field %d Title'), $i); ?></label>
                <input type="text" name="qa_<?php echo $i ?>_subj" id="qa_<?php echo $i ?>_subj" value="<?php echo get_text($qaconfig['qa_'.$i.'_subj']) ?>" class="frm_input m_full_input">
                <label for="qa_<?php echo $i ?>" class="extra_lb"><?php echo sprintf(__('Extra field %d Value'), $i); ?></label>
                <input type="text" name="qa_<?php echo $i ?>" value="<?php echo get_text($qaconfig['qa_'.$i]) ?>" id="qa_<?php echo $i ?>" class="frm_input m_full_input">
            </li>
            <?php } ?>
        </ul>
    </div>
</section>

<div class="btn_fixed_top">
    <input type="submit" value="<?php e__('Save'); ?>" class="btn_submit btn" accesskey="s">
</div>

</form>

<script>


function fqaconfigform_submit(f)
{
    <?php echo get_editor_js("qa_content_head"); ?>
    <?php echo get_editor_js("qa_content_tail"); ?>
    <?php echo get_editor_js("qa_mobile_content_head"); ?>
    <?php echo get_editor_js("qa_mobile_content_tail"); ?>

    f.action = "./qa_config_update.php";
    return true;
}
</script>

<?php
include_once ('./admin.tail.php');
?>
