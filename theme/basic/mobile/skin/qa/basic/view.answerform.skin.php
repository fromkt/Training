<?php
if (!defined("_GNUBOARD_")) exit; // Unable to access direct pages

$option = '';
$option_hidden = '';

if ($is_dhtml_editor) {
    $option_hidden .= '<input type="hidden" name="qa_html" value="1">';
} else {
    $option .= "\n".'<input type="checkbox" id="qa_html" name="qa_html" onclick="html_auto_br(this);" value="'.$html_value.'" '.$html_checked.'>'."\n".'<label for="qa_html">html</label>';
}
?>

<section id="bo_v_ans" class="bo_v_frm">
    <?php if($is_admin) { // Write answer if an admin  ?>
    <h2><?php e__('Write Answer'); ?></h2>

    <form name="fanswer" method="post" action="./qawrite_update.php" autocomplete="off">
    <input type="hidden" name="qa_id" value="<?php echo $view['qa_id']; ?>">
    <input type="hidden" name="w" value="a">
    <input type="hidden" name="sca" value="<?php echo $sca ?>">
    <input type="hidden" name="stx" value="<?php echo $stx; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <?php echo $option_hidden ?>

    <div class="form_01">
        <ul>
            <?php if ($option) { ?>
            <li>
                <span class="sound_only"><?php e__('Option'); ?></span>
                <?php echo $option; ?>
            </li>
            <?php } ?>
            <li>
                <label for="qa_subject" class="sound_only"><?php e__('Subject'); ?></label>
                <input type="text" name="qa_subject" value="" id="qa_subject" required class="frm_input required" size="50" maxlength="255" placeholder="<?php e__('Subject'); ?>">
            </li>
            <li>
                <label for="qa_content" class="sound_only"><?php e__('Content'); ?><strong><?php e__('Required'); ?></strong></label>
                <?php echo $editor_html; // When using the editor, expose it as an editor or as a textarea ?>
            </li>
        </ul>
    </div>

    <div class="btn_confirm">
        <button type="submit" id="btn_submit" accesskey="s" class="btn_submit"><?php e__('Write'); ?></button>
    </div>
    </form>

    <?php include_once(GML_THEME_JS_PATH. '/qa_write_js.php'); } else { ?>
    <p id="ans_msg"><?php e__('We are preparing an answer to your inquiry.'); ?></p>
    <?php } ?>
</section>
