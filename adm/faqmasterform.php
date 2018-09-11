<?php
$sub_menu = '300700';
include_once('./_common.php');
include_once(GML_EDITOR_LIB);

auth_check($auth[$sub_menu], "w");

$html_title = 'FAQ';

$fm_id = preg_replace('/[^0-9]/', '', $fm_id);

if ($w == "u")
{
    $html_title .= ' - '.__('Edit');
    $readonly = ' readonly';

    $sql = " select * from {$gml['faq_master_table']} where fm_id = '$fm_id' ";
    $fm = sql_fetch($sql);
    if (!$fm['fm_id']) alert(__('No Data'));
    if($config['cf_use_multi_lang_data']) {
        $fm = get_faq_by_lang($fm, 'faqmaster');
    }
}
else
{
    $html_title .= ' '.__('Add');
    $fm = array();
}

$gml['title'] = sprintf(__('Manage %s'), $html_title);

include_once (GML_ADMIN_PATH.'/admin.head.php');
?>

<form name="frmfaqmasterform" action="./faqmasterformupdate.php" onsubmit="return frmfaqmasterform_check(this);" method="post" enctype="MULTIPART/FORM-DATA">
<input type="hidden" name="w" value="<?php echo $w; ?>">
<input type="hidden" name="fm_id" value="<?php echo $fm_id; ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="token" value="">

<div class="frm_wr">

    <ul class="frm_ul">
        <li>
            <span class="lb_block"><label for="fm_order"><?php e__('Output order'); ?></label>
            <?php echo help(__('Smaller numbers will be printed first in FAQ category.')); ?></span>
            <input type="text" name="fm_order" value="<?php echo $fm['fm_order']; ?>" id="fm_order" class="frm_input" maxlength="10" size="10">

        </li>
        <li>
            <span class="lb_block"><label for="fm_subject"><?php e__('Subject'); ?></label></span>
            <input type="text" value="<?php echo get_text($fm['fm_subject']); ?>" name="fm_subject" id="fm_subject" required class="frm_input required"  size="70">
            <?php if ($w == 'u') { ?>
            <a href="<?php echo GML_BBS_URL; ?>/faq.php?fm_id=<?php echo $fm_id; ?>" class="btn_frmline"><?php e__('View'); ?></a>
            <a href="./faqlist.php?fm_id=<?php echo $fm_id; ?>" class="btn_frmline"><?php e__('View Details'); ?></a>
            <?php } ?>

        </li>
        <li>
            <span class="lb_block"><label for="fm_himg"><?php e__('Header IMAGE'); ?></label></span>

            <input type="file" name="fm_himg" id="fm_himg">
            <?php
            $himg = GML_DATA_PATH.'/faq/'.$fm['fm_id'].'_h';
            if (file_exists($himg)) {
                $size = @getimagesize($himg);
                if($size[0] && $size[0] > 750)
                    $width = 750;
                else
                    $width = $size[0];

                echo '<input type="checkbox" name="fm_himg_del" value="1" id="fm_himg_del"> <label for="fm_himg_del">삭제</label>';
                $himg_str = '<img src="'.GML_DATA_URL.'/faq/'.$fm['fm_id'].'_h" width="'.$width.'" alt="">';
            }
            if ($himg_str) {
                echo '<div class="banner_or_img">';
                echo $himg_str;
                echo '</div>';
            }
            ?>

        </li>
        <li>
            <span class="lb_block"><label for="fm_timg"><?php e__('Footer IMAGE'); ?></label></span>

            <input type="file" name="fm_timg" id="fm_timg">
            <?php
            $timg = GML_DATA_PATH.'/faq/'.$fm['fm_id'].'_t';
            if (file_exists($timg)) {
                $size = @getimagesize($timg);
                if($size[0] && $size[0] > 750)
                    $width = 750;
                else
                    $width = $size[0];

                echo '<input type="checkbox" name="fm_timg_del" value="1" id="fm_timg_del"><label for="fm_timg_del">'.__('Delete').'</label>';
                $timg_str = '<img src="'.GML_DATA_URL.'/faq/'.$fm['fm_id'].'_t" width="'.$width.'" alt="">';
            }
            if ($timg_str) {
                echo '<div class="banner_or_img">';
                echo $timg_str;
                echo '</div>';
            }
            ?>

        </li>
        <li>
            <span class="lb_block"><?php e__('Header Content'); ?></span>

                <?php echo editor_html('fm_head_html', get_text($fm['fm_head_html'], 0)); ?>

        </li>
        <li>
            <span class="lb_block"><?php e__('Footer Content'); ?></span>
            <?php echo editor_html('fm_tail_html', get_text($fm['fm_tail_html'], 0)); ?>

        </li>
        <li>
            <span class="lb_block"><?php e__('Header of Mobile Content'); ?></span>
            <?php echo editor_html('fm_mobile_head_html', get_text($fm['fm_mobile_head_html'], 0)); ?>

        </li>
        <li>
            <span class="lb_block"><?php e__('Footer of Mobile Content'); ?></span>
            <?php echo editor_html('fm_mobile_tail_html', get_text($fm['fm_mobile_tail_html'], 0)); ?>

        </li>
    </ul>
</div>

<div class="btn_fixed_top">
    <?php
    if($config['cf_use_multi_lang_data']) {
        echo get_lang_select_html('theme_lang_bar', $lang, 'class="theme_select_lang"', true);
    }
    ?>
    <a href="./faqmasterlist.php" class="btn_02 btn"><?php e__('List'); ?></a>
    <input type="submit" value="<?php e__('Save'); ?>" class="btn_submit btn" accesskey="s">
</div>

</form>

<script>
function frmfaqmasterform_check(f)
{
    <?php echo get_editor_js('fm_head_html'); ?>
    <?php echo get_editor_js('fm_tail_html'); ?>
    <?php echo get_editor_js('fm_mobile_head_html'); ?>
    <?php echo get_editor_js('fm_mobile_tail_html'); ?>
}

// document.frmfaqmasterform.fm_subject.focus();
</script>

<?php
include_once (GML_ADMIN_PATH.'/admin.tail.php');
?>
