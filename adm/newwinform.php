<?php
$sub_menu = '100310';
include_once('./_common.php');
include_once(GML_EDITOR_LIB);

auth_check($auth[$sub_menu], "w");

$nw_id = preg_replace('/[^0-9]/', '', $nw_id);

$html_title = __('Popup Layer');
if ($w == "u")
{
    $html_title .= ' '.__('Edit');
    $sql = " select * from {$gml['new_win_table']} where nw_id = '$nw_id' ";
    $nw = sql_fetch($sql);
    if (!$nw['nw_id']) alert(__('No Data'));
}
else
{
    $html_title .= ' '.__('Enter');
    $nw['nw_device'] = 'both';
    $nw['nw_disable_hours'] = 24;
    $nw['nw_left']   = 10;
    $nw['nw_top']    = 10;
    $nw['nw_width']  = 450;
    $nw['nw_height'] = 500;
    $nw['nw_content_html'] = 2;
}

$gml['title'] = $html_title;
include_once (GML_ADMIN_PATH.'/admin.head.php');
?>

<form name="frmnewwin" action="./newwinformupdate.php" onsubmit="return frmnewwin_check(this);" method="post">
<input type="hidden" name="w" value="<?php echo $w; ?>">
<input type="hidden" name="nw_id" value="<?php echo $nw_id; ?>">
<input type="hidden" name="token" value="">

<div class="local_desc01 local_desc">
    <p><?php e__('Sets the pop-up layer to open automatically when the main page is connected.'); ?></p>
</div>

<div class="frm_wr">
    <ul class="frm_ul">

        <li class="li_50">
            <span class="lb_block"><label for="nw_device"><?php e__('Browser_device'); ?></label> <?php echo help(__('Set up the connector to display the pop-up layer.')); ?></span>
            <select name="nw_device" id="nw_device">
                <option value="both"<?php echo get_selected($nw['nw_device'], 'both', true); ?>><?php e__('PC and mobile'); ?></option>
                <option value="pc"<?php echo get_selected($nw['nw_device'], 'pc'); ?>><?php e__('PC'); ?></option>
                <option value="mobile"<?php echo get_selected($nw['nw_device'], 'mobile'); ?>><?php e__('MOBILE'); ?></option>
            </select>
        </li>
        <li class="li_50 bd_0">
            <span class="lb_block"><label for="nw_disable_hours"><?php e__('Hours'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label>
            
            <?php echo help(__('Set how many hours you do not want the pop-up layer to be shown when the customer chooses never to view again.')); ?></span>
            <input type="text" name="nw_disable_hours" value="<?php echo $nw['nw_disable_hours']; ?>" id="nw_disable_hours" required class="frm_input required" size="5"> <?php e__('Hours'); ?>
            
        </li>
        <li class="li_50 li_clear">
            <span class="lb_block"><label for="nw_begin_time"><?php e__('Start Date'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label></span>
            <input type="text" name="nw_begin_time" value="<?php echo $nw['nw_begin_time']; ?>" id="nw_begin_time" required class="frm_input m_full_input required  date_input" size="20" maxlength="19"><br>
            <input type="checkbox" name="nw_begin_chk" value="<?php echo date("Y-m-d 00:00:00", GML_SERVER_TIME); ?>" id="nw_begin_chk" onclick="if (this.checked == true) this.form.nw_begin_time.value=this.form.nw_begin_chk.value; else this.form.nw_begin_time.value = this.form.nw_begin_time.defaultValue;">
            <label for="nw_begin_chk"><?php e__('Start date to today'); ?></label>
            
        </li>
        <li class="li_50">
            <span class="lb_block"><label for="nw_end_time"><?php e__('End date'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label></span>
            <input type="text" name="nw_end_time" value="<?php echo $nw['nw_end_time']; ?>" id="nw_end_time" required class="frm_input m_full_input required date_input" size="20" maxlength="19"><br>
            <input type="checkbox" name="nw_end_chk" value="<?php echo date("Y-m-d 23:59:59", GML_SERVER_TIME+(60*60*24*7)); ?>" id="nw_end_chk" onclick="if (this.checked == true) this.form.nw_end_time.value=this.form.nw_end_chk.value; else this.form.nw_end_time.value = this.form.nw_end_time.defaultValue;">
            <label for="nw_end_chk"><?php e__('The end date will be changed to seven days from today.'); ?></label>
            
        </li>
        <li class="li_50 li_clear">
            <span class="lb_block"><label for="nw_left"><?php e__('Popup Layer Left Position'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label></span>
            <input type="text" name="nw_left" value="<?php echo $nw['nw_left']; ?>" id="nw_left" required class="frm_input required" size="5"> px
            
        </li>
        <li class="li_50">
            <span class="lb_block"><label for="nw_top"><?php e__('Popup Layer Top Position'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label></span>
            <input type="text" name="nw_top" value="<?php echo $nw['nw_top']; ?>" id="nw_top" required class="frm_input required"  size="5"> px
            
        </li>
        <li class="li_50 li_clear">
            <span class="lb_block"><label for="nw_width"><?php e__('Popup Layer Width'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label></span>
            <input type="text" name="nw_width" value="<?php echo $nw['nw_width'] ?>" id="nw_width" required class="frm_input required" size="5"> px
            
        </li>
        <li class="li_50">
            <span class="lb_block"><label for="nw_height"><?php e__('Popup Layer Height'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label></span>
            <input type="text" name="nw_height" value="<?php echo $nw['nw_height'] ?>" id="nw_height" required class="frm_input required" size="5"> px
            
        </li>
        <li class="li_clear">
            <span class="lb_block"><label for="nw_subject"><?php e__('Popup Layer Title'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label></span>
            <input type="text" name="nw_subject" value="<?php echo get_sanitize_input($nw['nw_subject']) ?>" id="nw_subject" required class="frm_input frm_input_full required" size="80">
            
        </li>
        <li>
            <span class="lb_block"><label for="nw_content"><?php e__('Content'); ?></label></span>
            <?php echo editor_html('nw_content', get_text($nw['nw_content'], 0)); ?>
        </li>
    </ul>
</div>

<div class="btn_fixed_top">
<a href="./newwinlist.php" class="btn btn_02"><?php ep__('List', 'Go to List page'); ?></a>
<input type="submit" value="<?php e__('Save'); ?>" class="btn_submit btn" accesskey="s">
</div>
</form>

<?php
get_localize_script('newwin_form',
array(
'title_msg'=>__('Please enter a title.'),  // 제목을 입력하세요.
),
true);
?>
<script>
function frmnewwin_check(f)
{
    errmsg = "";
    errfld = "";

    <?php echo get_editor_js('nw_content'); ?>

    check_field(f.nw_subject, newwin_form.title_msg);

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
