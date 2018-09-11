<?php
$sub_menu = '300600';
include_once('./_common.php');
include_once(GML_EDITOR_LIB);

auth_check($auth[$sub_menu], "w");

$html_title = __('Content');
$gml['title'] = sprintf(__('Manage %s'), $html_title);

if ($w == "u")
{
    $html_title .=  ' '.__('Edit');
    $readonly = " readonly";

    $co = get_content_db($co_id);
    if (!$co['co_id'])
        alert('');
    if($config['cf_use_multi_lang_data']) {
        $co = get_content_by_lang($co);
    }
}
else
{
    $html_title .= ' '.__('Enter');
    $co = array(
        'co_html' => 2,
        'co_skin' => 'basic',
        'co_mobile_skin' => 'basic'
        );
}

include_once (GML_ADMIN_PATH.'/admin.head.php');
?>

<form name="frmcontentform" action="./contentformupdate.php" onsubmit="return frmcontentform_check(this);" method="post" enctype="MULTIPART/FORM-DATA" >
<input type="hidden" name="w" value="<?php echo $w; ?>">
<input type="hidden" name="co_html" value="1">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="token" value="">

<div class="frm_wr">

    <ul class="frm_ul">
        <li>
            <span class="lb_block"><label for="co_id">ID</label>
            <?php echo help(__('ID available only letters, numbers, _ with no spaces. (20 characters)')); ?></span>
            <input type="text" value="<?php echo $co['co_id']; ?>" name="co_id" id ="co_id" required <?php echo $readonly; ?> class="required <?php echo $readonly; ?> frm_input" size="20" maxlength="20">
            <?php if ($w == 'u') { ?><a href="<?php echo get_pretty_url('content', $co_id); ?>" class="btn_frmline" target="_blank"><?php e__('View content'); ?></a><?php } ?>

        </li>
        <li>
            <span class="lb_block"><label for="co_subject"><?php e__('Subject'); ?></label></span>
            <input type="text" name="co_subject" value="<?php echo htmlspecialchars2($co['co_subject']); ?>" id="co_subject" required class="frm_input required frm_input_full" size="90">
        </li>
        <li>
            <span class="lb_block"><?php e__('Content'); ?></span>
            <?php echo editor_html('co_content', get_text($co['co_content'], 0)); ?>
        </li>
        <li>
            <span class="lb_block"><?php e__('Mobile Content'); ?></span>
            <?php echo editor_html('co_mobile_content', get_text($co['co_mobile_content'], 0)); ?>
        </li>
        <li class="li_50">
            <span class="lb_block"><label for="co_skin"><?php e__('Skin Directory'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label></span>
            <?php echo get_skin_select('content', 'co_skin', 'co_skin', $co['co_skin'], 'required'); ?></span>

        </li>
        <li class="li_50">
            <span class="lb_block"><label for="co_mobile_skin"><?php e__('Mobile Skin Directory'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label></span>
            <?php echo get_mobile_skin_select('content', 'co_mobile_skin', 'co_mobile_skin', $co['co_mobile_skin'], 'required'); ?>

        </li>
        <li class="li_clear">
            <span class="lb_block"><label for="co_tag_filter_use"><?php e__('Enable Tag Filtering'); ?></label>
            <?php echo help(__('If you want to use a tag in the content, for example, IFrame, select Disable.')); ?></span>
            <select name="co_tag_filter_use" id="co_tag_filter_use">
                <option value="1"<?php echo get_selected(1, $co['co_tag_filter_use']); ?>><?php e__('Enable'); ?></option>
                <option value="0"<?php echo get_selected(0, $co['co_tag_filter_use']); ?>><?php e__('Disabled'); ?></option>
            </select>

        </li>
        <li class="li_50">
            <span class="lb_block"><label for="co_include_head"><?php e__('Header File Path'); ?></label>
            <?php echo help(__('If no set value exists, use the default Header File.')); ?></span>
            <?php echo get_include_head_select('co_include_head', 'co_include_head', $co['co_include_head']); ?>

        </li>
        <li class="li_50">
            <span class="lb_block"><label for="co_include_tail"><?php e__('Footer File Path'); ?></label>
            <?php echo help(__('If no set value exists, use the default Footer File.')); ?></span>
            <?php echo get_include_tail_select('co_include_tail', 'co_include_tail', $co['co_include_tail']); ?>

        </li>
        <li class="li_50 li_clear">
            <span class="lb_block"><label for="co_himg"><?php e__('Header images'); ?></label></span>
            <input type="file" name="co_himg" id="co_himg">
            <?php
            $himg = GML_DATA_PATH.'/content/'.$co['co_id'].'_h';
            if (file_exists($himg)) {
                $size = @getimagesize($himg);
                if($size[0] && $size[0] > 750)
                    $width = 750;
                else
                    $width = $size[0];

                echo '<input type="checkbox" name="co_himg_del" value="1" id="co_himg_del"> <label for="co_himg_del">'.__('Delete').'</label>';
                $himg_str = '<img src="'.GML_DATA_URL.'/content/'.$co['co_id'].'_h" width="'.$width.'" alt="">';
            }
            if ($himg_str) {
                echo '<div class="banner_or_img">';
                echo $himg_str;
                echo '</div>';
            }
            ?>

        </li>
        <li class="li_50">
            <span class="lb_block"><label for="co_timg"><?php e__('Footer images'); ?></label></span>
            <input type="file" name="co_timg" id="co_timg">
            <?php
            $timg = GML_DATA_PATH.'/content/'.$co['co_id'].'_t';
            if (file_exists($timg)) {
                $size = @getimagesize($timg);
                if($size[0] && $size[0] > 750)
                    $width = 750;
                else
                    $width = $size[0];

                echo '<input type="checkbox" name="co_timg_del" value="1" id="co_timg_del"> <label for="co_timg_del">'.__('Delete').'</label>';
                $timg_str = '<img src="'.GML_DATA_URL.'/content/'.$co['co_id'].'_t" width="'.$width.'" alt="">';
            }
            if ($timg_str) {
                echo '<div class="banner_or_img">';
                echo $timg_str;
                echo '</div>';
            }
            ?>

        </li>
    </ul>
</div>

<div class="btn_fixed_top">
    <?php
    if($config['cf_use_multi_lang_data']) {
        echo get_lang_select_html('theme_lang_bar', $lang, 'class="theme_select_lang"', true);
    }
    ?>
    <a href="./contentlist.php" class="btn_02 btn" ><?php e__('List'); ?></a>
    <input type="submit" value="<?php e__('Save'); ?>" class="btn_submit btn" accesskey="s">
</div>

</form>

<?php
get_localize_script('content_form',
array(
'id_msg'=>__('Please enter your ID.'),  // ID를 입력하세요.
'title_msg'=>__('Please enter a title.'),    // 제목을 입력하세요.
'content_msg' => __('Enter contents.'),  // 내용을 입력하세요.
),
true);
?>
<script>
function frmcontentform_check(f)
{
    errmsg = "";
    errfld = "";

    <?php echo get_editor_js('co_content'); ?>
    <?php echo chk_editor_js('co_content'); ?>
    <?php echo get_editor_js('co_mobile_content'); ?>

    check_field(f.co_id, content_form.id_msg);
    check_field(f.co_subject, content_form.title_msg);
    check_field(f.co_content, content_form.content_msg);

    if (errmsg != "") {
        alert(errmsg);
        errfld.focus();
        return false;
    }
    return true;
}
</script>

<?php
include_once (GML_ADMIN_PATH.'/admin.tail.php');
?>
