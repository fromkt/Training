<?php
$sub_menu = '300700';
include_once('./_common.php');
include_once(GML_EDITOR_LIB);

auth_check($auth[$sub_menu], "w");

$fm_id = (int) $fm_id;
$fa_id = isset($fa_id) ? (int) $fa_id : 0;

$sql = " select * from {$gml['faq_master_table']} where fm_id = '$fm_id' ";
$fm = sql_fetch($sql);

$html_title = 'FAQ '.$fm['fm_subject'];

if ($w == "u")
{
    $html_title .= ' '.__('Edit');
    $readonly = " readonly";

    $sql = " select * from {$gml['faq_table']} where fa_id = '$fa_id' ";
    $fa = sql_fetch($sql);
    if (!$fa['fa_id']) alert(__('No Data'));
    if($config['cf_use_multi_lang_data']) {
        $fa = get_faq_by_lang($fa, 'faq');
    }
}
else
    $html_title .= ' '.__('Enter FAQ');

$gml['title'] = sprintf(__('Manage %s'), $html_title);

include_once (GML_ADMIN_PATH.'/admin.head.php');
?>

<form name="frmfaqform" action="./faqformupdate.php" onsubmit="return frmfaqform_check(this);" method="post">
<input type="hidden" name="w" value="<?php echo $w; ?>">
<input type="hidden" name="fm_id" value="<?php echo $fm_id; ?>">
<input type="hidden" name="fa_id" value="<?php echo $fa_id; ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="token" value="">

<div class="frm_wr">

    <ul class="frm_ul">
       <li>
            <span class="lb_block"><label for="fa_order"><?php e__('Output order'); ?></label>
            <?php echo help(__('Smaller numbers will be printed first from the FAQ page.')); ?></span>
            <input type="text" name="fa_order" value="<?php echo $fa['fa_order']; ?>" id="fa_order" class="frm_input" maxlength="10" size="10">
            <?php if ($w == 'u') { ?><a href="<?php echo GML_BBS_URL; ?>/faq.php?fm_id=<?php echo $fm_id; ?>" class="btn_frmline"><?php e__('View Content'); ?></a><?php } ?>

        </li>
        <li>
            <span class="lb_block"><?php e__('Question'); ?></span>
            <?php echo editor_html('fa_subject', get_text($fa['fa_subject'], 0)); ?>
        </li>
        <li>
            <span class="lb_block"><?php e__('Answer'); ?></span>
            <?php echo editor_html('fa_content', get_text($fa['fa_content'], 0)); ?>
        </li>
    </ul>
</div>

<div class="btn_fixed_top">
    <?php
    if($config['cf_use_multi_lang_data']) {
        echo get_lang_select_html('theme_lang_bar', $lang, 'class="theme_select_lang"', true);
    }
    ?>
    <a href="./faqlist.php?fm_id=<?php echo $fm_id; ?>" class="btn btn_02"><?php e__('List'); ?></a>
    <input type="submit" value="<?php e__('Save'); ?>" class="btn_submit btn" accesskey="s">
</div>

</form>

<?php
get_localize_script('faq_form',
array(
'title_msg'=>__('Please enter a title.'),    // 제목을 입력하세요.
'content_msg' => __('Enter contents.'),  // 내용을 입력하세요.
),
true);
?>
<script>
function frmfaqform_check(f)
{
    errmsg = "";
    errfld = "";

    //check_field(f.fa_subject, faq_form.title_msg);
    //check_field(f.fa_content, faq_form.content_msg);

    if (errmsg != "")
    {
        alert(errmsg);
        errfld.focus();
        return false;
    }

    <?php echo get_editor_js('fa_subject'); ?>
    <?php echo get_editor_js('fa_content'); ?>

    return true;
}

// document.getElementById('fa_order').focus(); 포커스 해제
</script>

<?php
include_once (GML_ADMIN_PATH.'/admin.tail.php');
?>
