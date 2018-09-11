<?php
$sub_menu = "300100";
include_once('./_common.php');
include_once(GML_EDITOR_LIB);

auth_check($auth[$sub_menu], 'w');

$sql = " select count(*) as cnt from {$gml['group_table']} ";
$row = sql_fetch($sql);
if (!$row['cnt'])
    alert(__('At least one bulletin Board group must be created.'), './boardgroup_form.php');

$html_title = __('Board');

$required = "";
$readonly = "";
if ($w == '') {

    $html_title .= ' '.__('Create');

    $required = 'required';
    $required_valid = 'alnum_';
    $sound_only = '<strong class="sound_only">'.__('Required').'</strong>';

    $board['bo_count_delete'] = 1;
    $board['bo_count_modify'] = 1;
    $board['bo_read_point'] = $config['cf_read_point'];
    $board['bo_write_point'] = $config['cf_write_point'];
    $board['bo_comment_point'] = $config['cf_comment_point'];
    $board['bo_download_point'] = $config['cf_download_point'];

    $board['bo_gallery_cols'] = 4;
    $board['bo_gallery_width'] = 202;
    $board['bo_gallery_height'] = 150;
    $board['bo_mobile_gallery_width'] = 125;
    $board['bo_mobile_gallery_height'] = 100;
    $board['bo_table_width'] = 100;
    $board['bo_page_rows'] = $config['cf_page_rows'];
    $board['bo_mobile_page_rows'] = $config['cf_page_rows'];
    $board['bo_subject_len'] = 60;
    $board['bo_mobile_subject_len'] = 30;
    $board['bo_new'] = 24;
    $board['bo_hot'] = 100;
    $board['bo_image_width'] = 600;
    $board['bo_upload_count'] = 2;
    $board['bo_upload_size'] = 1048576;
    $board['bo_reply_order'] = 1;
    $board['bo_use_search'] = 1;
    $board['bo_skin'] = 'basic';
    $board['bo_mobile_skin'] = 'basic';
    $board['gr_id'] = $gr_id;
    $board['bo_use_secret'] = 0;
    $board['bo_include_head'] = '_head.php';
    $board['bo_include_tail'] = '_tail.php';

} else if ($w == 'u') {

    $html_title .= ' '.__('Edit');

    if (!$board['bo_table'])
        alert(__('This bulletin board does not exist.'));

    if ($is_admin == 'group') {
        if ($member['mb_id'] != $group['gr_admin'])
            alert(__('Invalid group.'));
    }

    $readonly = 'readonly';

}

if ($is_admin != 'super') {
    $group = get_group($board['gr_id']);
    $is_admin = is_admin($member['mb_id']);
}

$gml['title'] = $html_title;
include_once ('./admin.head.php');

add_javascript('<script src="'.GML_ADMIN_URL.'/js/horizon-swiper.min.js"></script>', 10);
add_stylesheet('<link rel="stylesheet" href="'.GML_ADMIN_URL.'/js/horizon-swiper.min.css">', 1);

$frm_submit = '<div class="btn_fixed_top">'.PHP_EOL;
if ($w == 'u') $frm_submit .= '<a href="./board_copy.php?bo_table='.$bo_table.'" id="board_copy" target="win_board_copy" class="btn btn_02">게시판복사</a>'.PHP_EOL;
$frm_submit .= '<input type="submit" value="'.__('Save').'" class="btn_submit btn" accesskey="s"></div>';
?>

<form name="fboardform" id="fboardform" action="./board_form_update.php" onsubmit="return fboardform_submit(this)" method="post" enctype="multipart/form-data">
<input type="hidden" name="w" value="<?php echo $w ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">

<div class="anchor horizon-swiper">
    <div class="horizon-item"><a href="#anc_bo_basic"><?php e__('Default Settings'); ?></a></div><div class="horizon-item"><a href="#anc_bo_auth"><?php e__('Set Permissions'); ?></a></div><div class="horizon-item"><a href="#anc_bo_function"><?php e__('Set Features'); ?></a></div><div class="horizon-item"><a href="#anc_bo_design"><?php e__('DesignForms'); ?></a></div><div class="horizon-item"><a href="#anc_bo_point"><?php e__('Point Settings'); ?></a></div><div class="horizon-item"><a href="#anc_bo_extra"><?php e__('Extra field'); ?></a></div>
    <?php start_event('admin_board_form_anchor'); ?>
</div>

<button type="button" class="<?php echo GML_ADMIN_HTML_TAB_CLASS; ?>"><?php e__('Default Settings'); ?></button>

<section id="anc_bo_basic" class="<?php echo GML_ADMIN_HTML_CON_CLASS; ?>">
    <h2 class="h2_frm"><?php e__('Default Settings'); ?></h2>

    <div class="frm_ul">
        <ul>
            <li>
                <span class="lb_block"><label for="bo_table">TABLE<?php echo $sound_only ?></label>
                <button type="button" class="tooltip_btn">?</button><span class="tooltip"><?php e__('Alphabetic, numeric, _ only (no more than 20 characters without spaces)'); ?></span></span>
                <input type="text" name="bo_table" value="<?php echo $board['bo_table'] ?>" id="bo_table" <?php echo $required ?> <?php echo $readonly ?> class="frm_input <?php echo $reaonly ?> <?php echo $required ?> <?php echo $required_valid ?>" maxlength="20">
                <?php if ($w == '') { ?>
                <?php } else { ?>
                    <a href="<?php echo get_pretty_url($board['bo_table']) ?>" class="btn_frmline"><?php e__('Redirect Board'); ?></a>

                    <a href="./board_thumbnail_delete.php?bo_table=<?php echo $board['bo_table'].'&amp;'.$qstr;?>" onclick="return delete_confirm2('<?php e__('Are you sure you want to delete the bulletin board thumbnail file?'); ?>');" class="btn_frmline"><?php e__('Delete board thumbnail'); ?></a>
                    <a href="./board_list.php?<?php echo $qstr;?>" class="btn_frmline"><?php e__('Go to List'); ?></a>
                <?php } ?>

            </li>
            <li>
                <span class="lb_block"><label for="gr_id"><?php e__('Group'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label></span>

                <?php echo get_group_select('gr_id', $board['gr_id'], 'required'); ?>
                <?php if ($w=='u') { ?><a href="javascript:document.location.href='./board_list.php?sfl=a.gr_id&stx='+document.fboardform.gr_id.value;" class="btn_frmline"><?php e__('List of Boards in the same group'); ?></a><?php } ?>

            </li>
            <li class="li_50">
                <span class="lb_block"><label for="bo_subject"><?php e__('Board Title'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label></span>
                <input type="text" name="bo_subject" value="<?php echo get_text($board['bo_subject']); ?>" id="bo_subject" required class="required frm_input frm_input_full" size="80" maxlength="120">

            </li>
            <li class="li_50">
                <span class="lb_block"><label for="bo_mobile_subject"><?php e__('Mobile Board Title'); ?></label>
                <?php echo help(__('Enter a different title for the bulletin board you see in mobile. If no input is made, the default bulletin board title is output.')); ?></span>
                <input type="text" name="bo_mobile_subject" value="<?php echo get_text($board['bo_mobile_subject']) ?>" id="bo_mobile_subject" class="frm_input frm_input_full" size="80" maxlength="120">

            </li>
            <li class="li_clear">
                <span class="lb_block"><label for="bo_device"><?php e__('Browser_device'); ?></label>
                <?php echo help(__('Distinguish your PC from your mobile use.')); ?></span>
                <select id="bo_device" name="bo_device">
                    <option value="both"<?php echo get_selected($board['bo_device'], 'both'); ?>><?php e__('Use on both PC and mobile'); ?></option>
                    <option value="pc"<?php echo get_selected($board['bo_device'], 'pc'); ?>><?php e__('PC only'); ?></option>
                    <option value="mobile"<?php echo get_selected($board['bo_device'], 'mobile'); ?>><?php e__('MOBILE only'); ?></option>
                </select>

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_device" value="1" id="chk_grp_device">
                    <label for="chk_grp_device"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_device" value="1" id="chk_all_device">
                    <label for="chk_all_device"><?php e__('Apply All'); ?></label>
                </div>

            </li>
            <li>
                <span class="lb_block"><label for="bo_category_list"><?php e__('Category'); ?></label>
                <?php echo help(__('Divide the classification by | between the classification. (Example : Question | answer) Do not enter the first letter #. (Example : # Question | # Answer [X])')); ?></span>
                <input type="text" name="bo_category_list" value="<?php echo get_text($board['bo_category_list']) ?>" id="bo_category_list" class="frm_input" size="70">
                <input type="checkbox" name="bo_use_category" value="1" id="bo_use_category" <?php echo $board['bo_use_category']?'checked':''; ?>>
                <label for="bo_use_category"><?php e__('Enable'); ?></label>

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_category_list" value="1" id="chk_grp_category_list">
                    <label for="chk_grp_category_list"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_category_list" value="1" id="chk_all_category_list">
                    <label for="chk_all_category_list"><?php e__('Apply All'); ?></label>
                </div>

            </li>
            <?php if ($w == 'u') { ?>
            <li>
                <span class="lb_block"><label for="proc_count"><?php e__('Adjust Count'); ?></label>
                <?php echo help(sprintf(n__('Number of article : %s', 'Number of articles : %s', $board['bo_count_write']).', '.
                n__('Number of Comment : %s', 'Number of Comments : %s', $board['bo_count_comment'])." \n".
                __('Check if the number in the bulletin board list does not match'), number_format($board['bo_count_write']), number_format($board['bo_count_comment'])));   //게시판 목록에서 글의 번호가 맞지 않을 경우에 체크하십시오. ?></span>
                <input type="checkbox" name="proc_count" value="1" id="proc_count"> <?php e__('Adjust'); ?>
            </li>
            <?php } ?>
        </ul>
    </div>
</section>

<button type="button" class="<?php echo GML_ADMIN_HTML_TAB_CLASS; ?>"><?php e__('Setting Board Permissions'); ?></button>
<section id="anc_bo_auth" class="<?php echo GML_ADMIN_HTML_CON_CLASS; ?>">
    <h2 class="h2_frm"><?php e__('Setting Board Permissions'); ?></h2>

    <div class="frm_ul">
        <ul>
            <li>
                <span class="lb_block"><label for="bo_admin"><?php e__('Board Admin'); ?></label></span>
                <input type="text" name="bo_admin" value="<?php echo $board['bo_admin'] ?>" id="bo_admin" class="frm_input" maxlength="20">

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_admin" value="1" id="chk_grp_admin">
                    <label for="chk_grp_admin"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_admin" value="1" id="chk_all_admin">
                    <label for="chk_all_admin"><?php e__('Apply All'); ?></label>
                </div>

            </li>
            <li>
                <span class="lb_block"><label for="bo_list_level"><?php e__('List view permissions'); ?></label>
                <?php echo help(__('Permission 1 is a non-members, 2 or more member. The privilege is highest with 10.')); ?></span>
                <?php echo get_member_level_select('bo_list_level', 1, 10, $board['bo_list_level']) ?>

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_list_level" value="1" id="chk_grp_list_level">
                    <label for="chk_grp_list_level"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_list_level" value="1" id="chk_all_list_level">
                    <label for="chk_all_list_level"><?php e__('Apply All'); ?></label>
                </div>

            </li>
            <li>
                <span class="lb_block"><label for="bo_read_level"><?php e__('Read permission'); ?></label></span>
                <?php echo get_member_level_select('bo_read_level', 1, 10, $board['bo_read_level']) ?>
                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_read_level" value="1" id="chk_grp_read_level">
                    <label for="chk_grp_read_level"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_read_level" value="1" id="chk_all_read_level">
                    <label for="chk_all_read_level"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_write_level"><?php e__('Write permission'); ?></label></span>
                <?php echo get_member_level_select('bo_write_level', 1, 10, $board['bo_write_level']) ?>
                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_write_level" value="1" id="chk_grp_write_level">
                    <label for="chk_grp_write_level"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_write_level" value="1" id="chk_all_write_level">
                    <label for="chk_all_write_level"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_reply_level"><?php e__('Reply permission'); ?></label></span>
                <?php echo get_member_level_select('bo_reply_level', 1, 10, $board['bo_reply_level']) ?>
                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_reply_level" value="1" id="chk_grp_reply_level">
                    <label for="chk_grp_reply_level"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_reply_level" value="1" id="chk_all_reply_level">
                    <label for="chk_all_reply_level"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_comment_level"><?php e__('Comments permission'); ?></label></span>
                <?php echo get_member_level_select('bo_comment_level', 1, 10, $board['bo_comment_level']) ?>
                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_comment_level" value="1" id="chk_grp_comment_level">
                    <label for="chk_grp_comment_level"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_comment_level" value="1" id="chk_all_comment_level">
                    <label for="chk_all_comment_level"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_link_level"><?php ep__('Link permission', 'Writing Link permission'); ?></label></span>
                <?php echo get_member_level_select('bo_link_level', 1, 10, $board['bo_link_level']) ?>
                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_link_level" value="1" id="chk_grp_link_level">
                    <label for="chk_grp_link_level"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_link_level" value="1" id="chk_all_link_level">
                    <label for="chk_all_link_level"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_upload_level"><?php e__('Upload Permission'); ?></label></span>
                <?php echo get_member_level_select('bo_upload_level', 1, 10, $board['bo_upload_level']) ?>
                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_upload_level" value="1" id="chk_grp_upload_level">
                    <label for="chk_grp_upload_level"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_upload_level" value="1" id="chk_all_upload_level">
                    <label for="chk_all_upload_level"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_download_level"><?php e__('Download Permission'); ?></label></span>
                <?php echo get_member_level_select('bo_download_level', 1, 10, $board['bo_download_level']) ?>
                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_download_level" value="1" id="chk_grp_download_level">
                    <label for="chk_grp_download_level"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_download_level" value="1" id="chk_all_download_level">
                    <label for="chk_all_download_level"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_html_level"><?php e__('HTML Write Permission'); ?></label></span>
                <?php echo get_member_level_select('bo_html_level', 1, 10, $board['bo_html_level']) ?>
                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_html_level" value="1" id="chk_grp_html_level">
                    <label for="chk_grp_html_level"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_html_level" value="1" id="chk_all_html_level">
                    <label for="chk_all_html_level"><?php e__('Apply All'); ?></label>
                </div>
            </li>
        </ul>
    </div>
</section>


<button type="button" class="<?php echo GML_ADMIN_HTML_TAB_CLASS; ?>"><?php e__('Setting board features'); ?></button>
<section id="anc_bo_function" class="<?php echo GML_ADMIN_HTML_CON_CLASS; ?>">
    <h2 class="h2_frm"><?php e__('Setting board features'); ?></h2>

    <div class="frm_ul">
        <ul>
            <li>
                <span class="lb_block"><label for="bo_count_modify"><?php e__('Unable to modify original text'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label>
                 <?php echo help(__('If the number of comments is greater than the number of settings, the original text can not be modified. Set to 0 to modify regardless of the number of comments.')); ?></span>
                 <input type="text" name="bo_count_modify" value="<?php echo $board['bo_count_modify'] ?>" id="bo_count_modify" required class="required numeric frm_input" size="3"> <?php e__('If the comments run more than the entered number, they can not be modified.'); ?>

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_count_modify" value="1" id="chk_grp_count_modify">
                    <label for="chk_grp_count_modify"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_count_modify" value="1" id="chk_all_count_modify">
                    <label for="chk_all_count_modify"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_count_delete"><?php e__('Unable to delete original text'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label></span>

                <input type="text" name="bo_count_delete" value="<?php echo $board['bo_count_delete'] ?>" id="bo_count_delete" required class="required numeric frm_input" size="3"> <?php e__('If the comments run more than the entered number, they can not be deleted.'); ?>

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_count_delete" value="1" id="chk_grp_count_delete">
                    <label for="chk_grp_count_delete"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_count_delete" value="1" id="chk_all_count_delete">
                    <label for="chk_all_count_delete"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_use_sideview"><?php e__('Author sideview'); ?></label></span>

                <input type="checkbox" name="bo_use_sideview" value="1" id="bo_use_sideview" <?php echo $board['bo_use_sideview']?'checked':''; ?>>
                <?php e__('Enable (Layer menu when the author clicks)'); ?>

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_use_sideview" value="1" id="chk_grp_use_sideview">
                    <label for="chk_grp_use_sideview"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_use_sideview" value="1" id="chk_all_use_sideview">
                    <label for="chk_all_use_sideview"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_use_secret"><?php e__('Use secret'); ?></label>

                <?php echo help('"Checkbox" is a non-sensitive check when writing ; "unconditional" writes all that are created in secret. (The administrator outputs the check box.) May not apply to all skins.') ?></span>
                <select id="bo_use_secret" name="bo_use_secret">
                    <?php echo option_selected(0, $board['bo_use_secret'], __('Disabled')); ?>
                    <?php echo option_selected(1, $board['bo_use_secret'], __('Checkbox')); ?>
                    <?php echo option_selected(2, $board['bo_use_secret'], __('Unconditional')); ?>
                </select>

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_use_secret" value="1" id="chk_grp_use_secret">
                    <label for="chk_grp_use_secret"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_use_secret" value="1" id="chk_all_use_secret">
                    <label for="chk_all_use_secret"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_use_dhtml_editor"><?php e__('Enable DHTML Editor'); ?></label>

                <?php echo help(__('Set whether content is used as DHTML Editor function when writing. Depending on the skin, it may not apply.')); ?></span>
                <input type="checkbox" name="bo_use_dhtml_editor" value="1" <?php echo $board['bo_use_dhtml_editor']?'checked':''; ?> id="bo_use_dhtml_editor">
                <?php e__('Enable'); ?>

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_use_dhtml_editor" value="1" id="chk_grp_use_dhtml_editor">
                    <label for="chk_grp_use_dhtml_editor"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_use_dhtml_editor" value="1" id="chk_all_use_dhtml_editor">
                    <label for="chk_all_use_dhtml_editor"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_use_rss_view"><?php e__('Enable RSS Display'); ?></label>

                <?php echo help(__('You must be able to read non-members and check to use RSS Show.')); ?></span>
                <input type="checkbox" name="bo_use_rss_view" value="1" <?php echo $board['bo_use_rss_view']?'checked':''; ?> id="bo_use_rss_view">
                <?php e__('Enable'); ?>

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_use_rss_view" value="1" id="chk_grp_use_rss_view">
                    <label for="chk_grp_use_rss_view"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_use_rss_view" value="1" id="chk_all_use_rss_view">
                    <label for="chk_all_use_rss_view"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_use_good"><?php e__('Using Good'); ?></label></span>
                <input type="checkbox" name="bo_use_good" value="1" <?php echo $board['bo_use_good']?'checked':''; ?> id="bo_use_good">
                <?php e__('Enable'); ?>

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_use_good" value="1" id="chk_grp_use_good">
                    <label for="chk_grp_use_good"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_use_good" value="1" id="chk_all_use_good">
                    <label for="chk_all_use_good"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_use_nogood"><?php e__('Using Bad'); ?></label></span>

                <input type="checkbox" name="bo_use_nogood" value="1" id="bo_use_nogood" <?php echo $board['bo_use_nogood']?'checked':''; ?>>
                <?php e__('Enable'); ?>

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_use_nogood" value="1" id="chk_grp_use_nogood">
                    <label for="chk_grp_use_nogood"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_use_nogood" value="1" id="chk_all_use_nogood">
                    <label for="chk_all_use_nogood"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_use_name"><?php e__('Use name'); ?></label></span>

                <input type="checkbox" name="bo_use_name" value="1" id="bo_use_name" <?php echo $board['bo_use_name']?'checked':''; ?>>
                <?php e__('Enable'); ?>

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_use_name" value="1" id="chk_grp_use_name">
                    <label for="chk_grp_use_name"><?php e__('Apply Group'); ?></label>

                    <input type="checkbox" name="chk_all_use_name" value="1" id="chk_all_use_name">
                    <label for="chk_all_use_name"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_use_signature"><?php e__('Enable signature display'); ?></label></span>

                <input type="checkbox" name="bo_use_signature" value="1" id="bo_use_signature" <?php echo $board['bo_use_signature']?'checked':''; ?>>
                <?php e__('Enable'); ?>

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_use_signature" value="1" id="chk_grp_use_signature">
                    <label for="chk_grp_use_signature"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_use_signature" value="1" id="chk_all_use_signature">
                    <label for="chk_all_use_signature"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_use_ip_view"><?php e__('Enable IP View'); ?></label></span>

                <input type="checkbox" name="bo_use_ip_view" value="1" id="bo_use_ip_view" <?php echo $board['bo_use_ip_view']?'checked':''; ?>>
                <?php e__('Enable'); ?>

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_use_ip_view" value="1" id="chk_grp_use_ip_view">
                    <label for="chk_grp_use_ip_view"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_use_ip_view" value="1" id="chk_all_use_ip_view">
                    <label for="chk_all_use_ip_view"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_use_list_content"><?php e__('Load Content in List'); ?></label>

                <?php echo help(__('This option is set if you need to retrieve content from the list in addition to the bulletin board title. Default is disabled.')); ?></span>
                <input type="checkbox" name="bo_use_list_content" value="1" id="bo_use_list_content" <?php echo $board['bo_use_list_content']?'checked':''; ?>>
                <?php e__('Enable'); ?>

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_use_list_content" value="1" id="chk_grp_use_list_content">
                    <label for="chk_grp_use_list_content"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_use_list_content" value="1" id="chk_all_use_list_content">
                    <label for="chk_all_use_list_content"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_use_list_file"><?php e__('Use file in list'); ?></label>

                    <?php echo help(__('This option is set when you need to retrieve bulletin board attachments from the list. Default is disabled.')); ?></span>
                    <input type="checkbox" name="bo_use_list_file" value="1" id="bo_use_list_file" <?php echo $board['bo_use_list_file']?'checked':''; ?>>
                    <?php e__('Enable'); ?>

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_use_list_file" value="1" id="chk_grp_use_list_file">
                    <label for="chk_grp_use_list_file"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_use_list_file" value="1" id="chk_all_use_list_file">
                    <label for="chk_all_use_list_file"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_use_list_view"><?php e__('Show list in view'); ?></label></span>

                <input type="checkbox" name="bo_use_list_view" value="1" id="bo_use_list_view" <?php echo $board['bo_use_list_view']?'checked':''; ?>>
                <?php e__('Enable'); ?>

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_use_list_view" value="1" id="chk_grp_use_list_view">
                    <label for="chk_grp_use_list_view"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_use_list_view" value="1" id="chk_all_use_list_view">
                    <label for="chk_all_use_list_view"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_use_email"><?php e__('Enable mail sending'); ?></label></span>

                <input type="checkbox" name="bo_use_email" value="1" id="bo_use_email" <?php echo $board['bo_use_email']?'checked':''; ?>>
                <?php e__('Enable'); ?>

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_use_email" value="1" id="chk_grp_use_email">
                    <label for="chk_grp_use_email"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_use_email" value="1" id="chk_all_use_email">
                    <label for="chk_all_use_email"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_upload_count"><?php e__('File Upload Count'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label>
                <?php echo help(__('Maximum number of files that can be uploaded per post (0 does not use file attachments)')); ?></span>
                <input type="text" name="bo_upload_count" value="<?php echo $board['bo_upload_count'] ?>" id="bo_upload_count" required class="required numeric frm_input" size="4">

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_upload_count" value="1" id="chk_grp_upload_count">
                    <label for="chk_grp_upload_count"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_upload_count" value="1" id="chk_all_upload_count">
                    <label for="chk_all_upload_count"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_upload_size"><?php e__('File Upload Size'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label>
                <?php echo help(sprintf(__('Up to %s uploadable'), ini_get("upload_max_filesize")).', 1 MB = 1,048,576 bytes'); ?></span>
                <?php e__('Per upload file'); ?> <input type="text" name="bo_upload_size" value="<?php echo $board['bo_upload_size'] ?>" id="bo_upload_size" required class="required numeric frm_input"  size="10"> <?php e__('Below'); ?>

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_upload_size" value="1" id="chk_grp_upload_size">
                    <label for="chk_grp_upload_size"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_upload_size" value="1" id="chk_all_upload_size">
                    <label for="chk_all_upload_size"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_use_file_content"><?php e__('Enable File Description'); ?></label>
                </span>
                    <input type="checkbox" name="bo_use_file_content" value="1" id="bo_use_file_content" <?php echo $board['bo_use_file_content']?'checked':''; ?>> <?php e__('Enable'); ?>

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_use_file_content" value="1" id="chk_grp_use_file_content">
                    <label for="chk_grp_use_file_content"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_use_file_content" value="1" id="chk_all_use_file_content">
                    <label for="chk_all_use_file_content"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_write_min"><?php e__('Limit the minimum number of posts'); ?></label>
                <?php echo help(__('Set minimum number of characters when writing. Do not check when entering 0 or when using DHTML Editor')); ?></span>
                <input type="text" name="bo_write_min" value="<?php echo $board['bo_write_min'] ?>" id="bo_write_min" class="numeric frm_input" size="4">

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_write_min" value="1" id="chk_grp_write_min">
                    <label for="chk_grp_write_min"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_write_min" value="1" id="chk_all_write_min">
                    <label for="chk_all_write_min"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_write_max"><?php e__('Limit Maximum Number of posts'); ?></label>
                <?php echo help(__('Set the maximum number of characters when writing. Do not check when entering 0 or when using DHTML Editor')); ?></span>
                <input type="text" name="bo_write_max" value="<?php echo $board['bo_write_max'] ?>" id="bo_write_max" class="numeric frm_input" size="4">

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_write_max" value="1" id="chk_grp_write_max">
                    <label for="chk_grp_write_max"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_write_max" value="1" id="chk_all_write_max">
                    <label for="chk_all_write_max"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_comment_min"><?php e__('Limit the length of comments'); ?></label>
                <?php echo help(__('Set the minimum length of text characters when entering comments.')); ?></span>
                <input type="text" name="bo_comment_min" value="<?php echo $board['bo_comment_min'] ?>" id="bo_comment_min" class="numeric frm_input" size="4">

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_comment_min" value="1" id="chk_grp_comment_min">
                    <label for="chk_grp_comment_min"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_comment_min" value="1" id="chk_all_comment_min">
                    <label for="chk_all_comment_min"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_comment_max"><?php e__('Limit the maximum length of comments'); ?></label>
                <?php echo help(__('Set the maximum number of text characters when entering comments.')); ?></span>
                <input type="text" name="bo_comment_max" value="<?php echo $board['bo_comment_max'] ?>" id="bo_comment_max" class="numeric frm_input" size="4">

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_comment_max" value="1" id="chk_grp_comment_max">
                    <label for="chk_grp_comment_max"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_comment_max" value="1" id="chk_all_comment_max">
                    <label for="chk_all_comment_max"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_use_sns"><?php e__('Using SNS'); ?></label>
                <?php echo help(__('Check Enable to post comments on social network services or register comments at the same time.<br>You can use it only when you set the basic configuration SNS settings.')); ?></span>
                <input type="checkbox" name="bo_use_sns" value="1" id="bo_use_sns" <?php echo $board['bo_use_sns']?'checked':''; ?>>
                <?php e__('Enable'); ?>

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_use_sns" value="1" id="chk_grp_use_sns">
                    <label for="chk_grp_use_sns"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_use_sns" value="1" id="chk_all_use_sns">
                    <label for="chk_all_use_sns"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_use_search"><?php e__('Use all search'); ?></label></span>
                <input type="checkbox" name="bo_use_search" value="1" id="bo_use_search" <?php echo $board['bo_use_search']?'checked':''; ?>>
                <?php e__('Enable'); ?>
                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_use_search" value="1" id="chk_grp_use_search">
                    <label for="chk_grp_use_search"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_use_search" value="1" id="chk_all_use_search">
                    <label for="chk_all_use_search"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_order"><?php e__('Output order'); ?></label>
                <?php echo help(__('The lower number bulletin is first printed on the menu or search.')); ?></span>
                <input type="text" name="bo_order" value="<?php echo $board['bo_order'] ?>" id="bo_order" class="frm_input" size="4">

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_order" value="1" id="chk_grp_order">
                    <label for="chk_grp_order"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_order" value="1" id="chk_all_order">
                    <label for="chk_all_order"><?php e__('Apply All'); ?></label>
                </div>

            </li>
            <li>
                <span class="lb_block"><label for="bo_use_captcha"><?php e__('Use captcha'); ?></label>
                <?php echo help(__('When checked, Enable CAPTCHA when writing.(Both members and non-members)<br>If you do not check, use CAPTCHA only for non-members.')); ?>
                </span>
                <input type="checkbox" name="bo_use_captcha" value="1" <?php echo $board['bo_use_captcha']?'checked':''; ?> id="bo_use_captcha">
                <?php e__('Enable'); ?>
                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_use_captcha" value="1" id="chk_grp_use_captcha">
                    <label for="chk_grp_use_captcha"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_use_captcha" value="1" id="chk_all_use_captcha">
                    <label for="chk_all_use_captcha"><?php e__('Apply All'); ?></label>
                </div>
            </li>

        </ul>
    </div>
</section>

<button type="button" class="<?php echo GML_ADMIN_HTML_TAB_CLASS; ?>"><?php e__('Board designforms'); ?></button>
<section id="anc_bo_design" class="<?php echo GML_ADMIN_HTML_CON_CLASS; ?>">
    <h2 class="h2_frm"><?php e__('Board designforms'); ?></h2>

    <div class="frm_ul">
        <ul>
            <li>
                <span class="lb_block"><label for="bo_skin"><?php e__('Skin Directory'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label></span>
                <?php echo get_skin_select('board', 'bo_skin', 'bo_skin', $board['bo_skin'], 'required'); ?>
                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_skin" value="1" id="chk_grp_skin">
                    <label for="chk_grp_skin"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_skin" value="1" id="chk_all_skin">
                    <label for="chk_all_skin"><?php e__('Apply All'); ?></label>
                </div>

            </li>
            <li>
                <span class="lb_block"><label for="bo_mobile_skin"><?php e__('Mobile Skin Directory'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label></span>
                <?php echo get_mobile_skin_select('board', 'bo_mobile_skin', 'bo_mobile_skin', $board['bo_mobile_skin'], 'required'); ?>
                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_mobile_skin" value="1" id="chk_grp_mobile_skin">
                    <label for="chk_grp_mobile_skin"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_mobile_skin" value="1" id="chk_all_mobile_skin">
                    <label for="chk_all_mobile_skin"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_include_head"><?php e__('Header File Path'); ?></label></span>
                <?php echo get_include_head_select('bo_include_head', 'bo_include_head', $board['bo_include_head']); ?>
                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_include_head" value="1" id="chk_grp_include_head">
                    <label for="chk_grp_include_head"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_include_head" value="1" id="chk_all_include_head">
                    <label for="chk_all_include_head"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_include_tail"><?php e__('Footer File Path'); ?></label></span>
                <?php echo get_include_tail_select('bo_include_tail', 'bo_include_tail', $board['bo_include_tail']); ?>
                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_include_tail" value="1" id="chk_grp_include_tail">
                    <label for="chk_grp_include_tail"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_include_tail" value="1" id="chk_all_include_tail">
                    <label for="chk_all_include_tail"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_content_head"><?php e__('Header Content'); ?></label></span>

                <?php echo editor_html("bo_content_head", get_text($board['bo_content_head'], 0)); ?>
                    <input type="checkbox" name="chk_grp_content_head" value="1" id="chk_grp_content_head">
                    <label for="chk_grp_content_head"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_content_head" value="1" id="chk_all_content_head">
                    <label for="chk_all_content_head"><?php e__('Apply All'); ?></label>

            </li>
            <li>
                <span class="lb_block"><label for="bo_content_tail"><?php e__('Footer Content'); ?></label></span>

                <?php echo editor_html("bo_content_tail", get_text($board['bo_content_tail'], 0)); ?>
                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_content_tail" value="1" id="chk_grp_content_tail">
                    <label for="chk_grp_content_tail"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_content_tail" value="1" id="chk_all_content_tail">
                    <label for="chk_all_content_tail"><?php e__('Apply All'); ?></label>
                </div>

            </li>
            <li>
                <span class="lb_block"><label for="bo_mobile_content_head"><?php e__('Header of Mobile File Path'); ?></label></span>

                <?php echo editor_html("bo_mobile_content_head", get_text($board['bo_mobile_content_head'], 0)); ?>

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_mobile_content_head" value="1" id="chk_grp_mobile_content_head">
                    <label for="chk_grp_mobile_content_head"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_mobile_content_head" value="1" id="chk_all_mobile_content_head">
                    <label for="chk_all_mobile_content_head"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_mobile_content_tail"><?php e__('Footer of Mobile Content'); ?></label></span>

                <?php echo editor_html("bo_mobile_content_tail", get_text($board['bo_mobile_content_tail'], 0)); ?>

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_mobile_content_tail" value="1" id="chk_grp_mobile_content_tail">
                    <label for="chk_grp_mobile_content_tail"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_mobile_content_tail" value="1" id="chk_all_mobile_content_tail">
                    <label for="chk_all_mobile_content_tail"><?php e__('Apply All'); ?></label>
                </div>
            </li>
             <li>
                <span class="lb_block"><label for="bo_insert_content"><?php e__('Writing Basics'); ?></label></span>
                <textarea id="bo_insert_content" name="bo_insert_content" rows="5"><?php echo $board['bo_insert_content'] ?></textarea>

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_insert_content" value="1" id="chk_grp_insert_content">
                    <label for="chk_grp_insert_content"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_insert_content" value="1" id="chk_all_insert_content">
                    <label for="chk_all_insert_content"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_subject_len"><?php e__('Subject length'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label>
                <?php echo help(__('The number of titles in a list Mark the truncated text as …')); ?></span>
                <input type="text" name="bo_subject_len" value="<?php echo $board['bo_subject_len'] ?>" id="bo_subject_len" required class="required numeric frm_input" size="4">

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_subject_len" value="1" id="chk_grp_subject_len">
                    <label for="chk_grp_subject_len"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_subject_len" value="1" id="chk_all_subject_len">
                    <label for="chk_all_subject_len"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_mobile_subject_len"><?php e__('Mobile Subject length'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label>
                <?php echo help('The number of titles in a list Mark the truncated text as …') ?></span>
                <input type="text" name="bo_mobile_subject_len" value="<?php echo $board['bo_mobile_subject_len'] ?>" id="bo_mobile_subject_len" required class="required numeric frm_input" size="4">
                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_mobile_subject_len" value="1" id="chk_grp_mobile_subject_len">
                    <label for="chk_grp_mobile_subject_len"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_mobile_subject_len" value="1" id="chk_all_mobile_subject_len">
                    <label for="chk_all_mobile_subject_len"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_page_rows"><?php e__('List per page'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label></span>

                <input type="text" name="bo_page_rows" value="<?php echo $board['bo_page_rows'] ?>" id="bo_page_rows" required class="required numeric frm_input" size="4">

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_page_rows" value="1" id="chk_grp_page_rows">
                    <label for="chk_grp_page_rows"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_page_rows" value="1" id="chk_all_page_rows">
                    <label for="chk_all_page_rows"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_mobile_page_rows"><?php e__('List per mobile page'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label></span>

                <input type="text" name="bo_mobile_page_rows" value="<?php echo $board['bo_mobile_page_rows'] ?>" id="bo_mobile_page_rows" required class="required numeric frm_input" size="4">

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_mobile_page_rows" value="1" id="chk_grp_mobile_page_rows">
                    <label for="chk_grp_mobile_page_rows"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_mobile_page_rows" value="1" id="chk_all_mobile_page_rows">
                    <label for="chk_all_mobile_page_rows"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_gallery_cols"><?php e__('Gallery Images'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label>

                <?php echo help(__('The value that sets the number of images per line in the list of bulletin boards in gallery format.')); ?></span>
                <input type="text" name="bo_gallery_cols" value="<?php echo $board['bo_gallery_cols'] ?>" id="bo_gallery_cols" required class="required numeric frm_input" size="4">

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_gallery_cols" value="1" id="chk_grp_gallery_cols">
                    <label for="chk_grp_gallery_cols"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_gallery_cols" value="1" id="chk_all_gallery_cols">
                    <label for="chk_all_gallery_cols"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_gallery_width"><?php e__('Gallery Image Width'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label>

                <?php echo help(__('The value that sets the width of the thumbnail image in the list of bulletin boards in gallery format.')); ?></span>
                <input type="text" name="bo_gallery_width" value="<?php echo $board['bo_gallery_width'] ?>" id="bo_gallery_width" required class="required numeric frm_input" size="4">

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_gallery_width" value="1" id="chk_grp_gallery_width">
                    <label for="chk_grp_gallery_width"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_gallery_width" value="1" id="chk_all_gallery_width">
                    <label for="chk_all_gallery_width"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
               <span class="lb_block"> <label for="bo_gallery_height"><?php e__('Gallery Image Height'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label>

                <?php echo help(__('The value that sets the height of the thumbnail image in the list of bulletin boards in gallery format.')); ?></span>
                <input type="text" name="bo_gallery_height" value="<?php echo $board['bo_gallery_height'] ?>" id="bo_gallery_height" required class="required numeric frm_input" size="4">

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_gallery_height" value="1" id="chk_grp_gallery_height">
                    <label for="chk_grp_gallery_height"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_gallery_height" value="1" id="chk_all_gallery_height">
                    <label for="chk_all_gallery_height"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_mobile_gallery_width"><?php e__('Mobile<br>Gallery Image Width'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label>

                <?php echo help(__('The value that sets the width of the thumbnail image in the list of bulletin boards in gallery format when connecting via mobile.')); ?></span>
                <input type="text" name="bo_mobile_gallery_width" value="<?php echo $board['bo_mobile_gallery_width'] ?>" id="bo_mobile_gallery_width" required class="required numeric frm_input" size="4">
                <div class="al_ap">

                    <input type="checkbox" name="chk_grp_mobile_gallery_width" value="1" id="chk_grp_mobile_gallery_width">
                    <label for="chk_grp_mobile_gallery_width"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_mobile_gallery_width" value="1" id="chk_all_mobile_gallery_width">
                    <label for="chk_all_mobile_gallery_width"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_mobile_gallery_height"><?php e__('Mobile<br>Gallery Image Height'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label>

                <?php echo help(__('The value that sets the height of the thumbnail image in the list of bulletin boards in gallery format when connecting via mobile.')); ?></span>
                <input type="text" name="bo_mobile_gallery_height" value="<?php echo $board['bo_mobile_gallery_height'] ?>" id="bo_mobile_gallery_height" required class="required numeric frm_input" size="4">

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_mobile_gallery_height" value="1" id="chk_grp_mobile_gallery_height">
                    <label for="chk_grp_mobile_gallery_height"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_mobile_gallery_height" value="1" id="chk_all_mobile_gallery_height">
                    <label for="chk_all_mobile_gallery_height"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_table_width"><?php e__('Board Width'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label>

                <?php echo help(__('Below 100 is %')) ?></span>
                <input type="text" name="bo_table_width" value="<?php echo $board['bo_table_width'] ?>" id="bo_table_width" required class="required numeric frm_input" size="4">

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_table_width" value="1" id="chk_grp_table_width">
                    <label for="chk_grp_table_width"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_table_width" value="1" id="chk_all_table_width">
                    <label for="chk_all_table_width"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_image_width"><?php e__('IMAGE Width'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label>

                <?php echo help(__('Width size of the image that is output from the bulletin board')); ?></span>
                <input type="text" name="bo_image_width" value="<?php echo $board['bo_image_width'] ?>" id="bo_image_width" required class="required numeric frm_input" size="4"> <?php e__('Pixel'); ?>

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_image_width" value="1" id="chk_grp_image_width">
                    <label for="chk_grp_image_width"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_image_width" value="1" id="chk_all_image_width">
                    <label for="chk_all_image_width"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_new"><?php ep__('New icon', 'New text icon'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label>

                <?php echo help(__('The time to print a new image after entering the text. If you enter 0, no icon will be output.')); ?></span>
                <input type="text" name="bo_new" value="<?php echo $board['bo_new'] ?>" id="bo_new" required class="required numeric frm_input" size="4">

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_new" value="1" id="chk_grp_new">
                    <label for="chk_grp_new"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_new" value="1" id="chk_all_new">
                    <label for="chk_all_new"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_hot"><?php ep__('Popular text icon', 'Popular icon'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label>
                <?php echo help(__('If the number of queries is greater than the set value, output hot image. Enter 0 to not print the icon.')); ?></span>
                <input type="text" name="bo_hot" value="<?php echo $board['bo_hot'] ?>" id="bo_hot" required class="required numeric frm_input" size="4">

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_hot" value="1" id="chk_grp_hot">
                    <label for="chk_grp_hot"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_hot" value="1" id="chk_all_hot">
                    <label for="chk_all_hot"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_reply_order"><?php e__('Reply sort'); ?></label></span>

                <select id="bo_reply_order" name="bo_reply_order">
                    <option value="1"<?php echo get_selected($board['bo_reply_order'], 1, true); ?>><?php e__('Below the reply you wrote later (default)'); ?>
                    <option value="0"<?php echo get_selected($board['bo_reply_order'], 0); ?>><?php e__('Restate Later Responses'); ?>
                </select>

                <div class="al_ap">
                    <input type="checkbox" id="chk_grp_reply_order" name="chk_grp_reply_order" value="1">
                    <label for="chk_grp_reply_order"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" id="chk_all_reply_order" name="chk_all_reply_order" value="1">
                    <label for="chk_all_reply_order"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_sort_field"><?php e__('List sort fields'); ?></label>
                <?php echo help(__('Select the fields in the list to use for sorting by defaults.If you do not use them as "default", the speed may be slow.')); ?></span>

                <select id="bo_sort_field" name="bo_sort_field">
                    <option value="" <?php echo get_selected($board['bo_sort_field'], ""); ?>>wr_num, wr_reply : <?php e__('default'); ?></option>
                    <option value="wr_datetime asc" <?php echo get_selected($board['bo_sort_field'], "wr_datetime asc"); ?>>wr_datetime asc : <?php e__('Before date'); ?></option>
                    <option value="wr_datetime desc" <?php echo get_selected($board['bo_sort_field'], "wr_datetime desc"); ?>>wr_datetime desc : <?php e__('From the latest'); ?></option>
                    <option value="wr_hit asc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "wr_hit asc, wr_num, wr_reply"); ?>>wr_hit asc : <?php e__('From low hit of view'); ?></option>
                    <option value="wr_hit desc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "wr_hit desc, wr_num, wr_reply"); ?>>wr_hit desc : <?php e__('From High Hits'); ?></option>
                    <option value="wr_last asc" <?php echo get_selected($board['bo_sort_field'], "wr_last asc"); ?>>wr_last asc : <?php e__('From the previous posts'); ?></option>
                    <option value="wr_last desc" <?php echo get_selected($board['bo_sort_field'], "wr_last desc"); ?>>wr_last desc : <?php e__('A recent posts'); ?></option>
                    <option value="wr_comment asc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "wr_comment asc, wr_num, wr_reply"); ?>>wr_comment asc : <?php e__('Comments from Low'); ?></option>
                    <option value="wr_comment desc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "wr_comment desc, wr_num, wr_reply"); ?>>wr_comment desc : <?php e__('Comments from High'); ?></option>
                    <option value="wr_good asc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "wr_good asc, wr_num, wr_reply"); ?>>wr_good asc : <?php e__('From the low goods'); ?></option>
                    <option value="wr_good desc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "wr_good desc, wr_num, wr_reply"); ?>>wr_good desc : <?php e__('From the high goods'); ?></option>
                    <option value="wr_nogood asc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "wr_nogood asc, wr_num, wr_reply"); ?>>wr_nogood asc : <?php e__('From the low bads'); ?></option>
                    <option value="wr_nogood desc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "wr_nogood desc, wr_num, wr_reply"); ?>>wr_nogood desc : <?php e__('From the high bads'); ?></option>
                    <option value="wr_subject asc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "wr_subject asc, wr_num, wr_reply"); ?>>wr_subject asc : <?php e__('Subject Ascending'); ?></option>
                    <option value="wr_subject desc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "wr_subject desc, wr_num, wr_reply"); ?>>wr_subject desc : <?php e__('Subject Descending'); ?></option>
                    <option value="wr_name asc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "wr_name asc, wr_num, wr_reply"); ?>>wr_name asc : <?php e__('Author Ascending'); ?></option>
                    <option value="wr_name desc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "wr_name desc, wr_num, wr_reply"); ?>>wr_name desc : <?php e__('Author Descending'); ?></option>
                    <option value="ca_name asc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "ca_name asc, wr_num, wr_reply"); ?>>ca_name asc : <?php e__('Category name Ascending'); ?></option>
                    <option value="ca_name desc, wr_num, wr_reply" <?php echo get_selected($board['bo_sort_field'], "ca_name desc, wr_num, wr_reply"); ?>>ca_name desc : <?php e__('Category name Descending'); ?></option>
                </select>
                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_sort_field" value="1" id="chk_grp_sort_field">
                    <label for="chk_grp_sort_field"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_sort_field" value="1" id="chk_all_sort_field">
                    <label for="chk_all_sort_field"><?php e__('Apply All'); ?></label>
                </div>
            </li>
        </ul>
    </div>
    <div class="btn_confirm btn_confirm01"><button type="button" class="get_theme_galc"><?php e__('Get theme image settings'); ?></button></div>
</section>



<button type="button" class="<?php echo GML_ADMIN_HTML_TAB_CLASS; ?>"><?php e__('Setting board points'); ?></button>
<section id="anc_bo_point" class="<?php echo GML_ADMIN_HTML_CON_CLASS; ?>">
    <h2 class="h2_frm"><?php e__('Setting board points'); ?></h2>

    <div class="frm_ul">
        <ul>

            <li>
                <span class="lb_block"><label for="chk_grp_point"><?php e__('Set as default'); ?></label>

                <?php echo help(__('Set as point entered in Default Preferences')); ?></span>
                <input type="checkbox" name="chk_grp_point" id="chk_grp_point" onclick="set_point(this.form)">
            </li>
            <li>
                <span class="lb_block"><label for="bo_read_point"><?php e__('Read Point'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label></span>

                <input type="text" name="bo_read_point" value="<?php echo $board['bo_read_point'] ?>" id="bo_read_point" required class="required frm_input" size="5">

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_read_point" value="1" id="chk_grp_read_point">
                    <label for="chk_grp_read_point"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_read_point" value="1" id="chk_all_read_point">
                    <label for="chk_all_read_point"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_write_point"><?php e__('Write Point'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label></span>

                <input type="text" name="bo_write_point" value="<?php echo $board['bo_write_point'] ?>" id="bo_write_point" required class="required frm_input" size="5">

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_write_point" value="1" id="chk_grp_write_point">
                    <label for="chk_grp_write_point"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_write_point" value="1" id="chk_all_write_point">
                    <label for="chk_all_write_point"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_comment_point"><?php e__('Comments Point'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label></span>

                <input type="text" name="bo_comment_point" value="<?php echo $board['bo_comment_point'] ?>" id="bo_comment_point" required class="required frm_input" size="5">

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_comment_point" value="1" id="chk_grp_comment_point">
                    <label for="chk_grp_comment_point"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_comment_point" value="1" id="chk_all_comment_point">
                    <label for="chk_all_comment_point"><?php e__('Apply All'); ?></label>
                </div>
            </li>
            <li>
                <span class="lb_block"><label for="bo_download_point"><?php e__('Download Point'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label></span>

                <input type="text" name="bo_download_point" value="<?php echo $board['bo_download_point'] ?>" id="bo_download_point" required class="required frm_input" size="5">

                <div class="al_ap">
                    <input type="checkbox" name="chk_grp_download_point" value="1" id="chk_grp_download_point">
                    <label for="chk_grp_download_point"><?php e__('Apply Group'); ?></label>
                    <input type="checkbox" name="chk_all_download_point" value="1" id="chk_all_download_point">
                    <label for="chk_all_download_point"><?php e__('Apply All'); ?></label>
                </div>
            </li>
        </ul>
    </div>
</section>

<button type="button" class="<?php echo GML_ADMIN_HTML_TAB_CLASS; ?>"><?php e__('Set Board Extra Fields'); ?></button>
<section id="anc_bo_extra" class="<?php echo GML_ADMIN_HTML_CON_CLASS; ?>">
    <h2 class="h2_frm"><?php e__('Set Board Extra Fields'); ?></h2>

    <div class="frm_ul extra_ul">

        <ul>
        <?php for ($i=1; $i<=10; $i++) { ?>
        <li>
            <span class="lb_block"><?php echo sprintf(__('Extra field %d'), $i); ?></span>
            <label for="bo_<?php echo $i ?>_subj" class="extra_lb"><?php echo sprintf(__('Extra field %d Title'), $i); ?></label>
            <input type="text" name="bo_<?php echo $i ?>_subj" id="bo_<?php echo $i ?>_subj" value="<?php echo get_text($board['bo_'.$i.'_subj']) ?>" class="frm_input m_full_input">
            <label for="bo_<?php echo $i ?>" class="extra_lb"><?php echo sprintf(__('Extra field %d Value'), $i); ?></label>
            <input type="text" name="bo_<?php echo $i ?>" value="<?php echo get_text($board['bo_'.$i]) ?>" id="bo_<?php echo $i ?>" class="frm_input m_full_input">

            <div class="al_ap">
                <input type="checkbox" name="chk_grp_<?php echo $i ?>" value="1" id="chk_grp_<?php echo $i ?>">
                <label for="chk_grp_<?php echo $i ?>"><?php e__('Apply Group'); ?></label>
                <input type="checkbox" name="chk_all_<?php echo $i ?>" value="1" id="chk_all_<?php echo $i ?>">
                <label for="chk_all_<?php echo $i ?>"><?php e__('Apply All'); ?></label>
            </div>
        </li>
        <?php } ?>
        </ul>
    </div>
</section>

<?php start_event('admin_board_form_tag'); ?>

<div class="btn_fixed_top">
    <?php if( $bo_table && $w ){ ?>
        <a href="./board_copy.php?bo_table=<?php echo $board['bo_table']; ?>" id="board_copy" target="win_board_copy" class=" btn_02 btn"><?php e__('Copy Board'); ?></a>
        <a href="<?php echo get_pretty_url($board['bo_table']) ?>" class=" btn_02 btn"><?php e__('Redirect Board'); ?></a>
        <a href="./board_thumbnail_delete.php?bo_table=<?php echo $board['bo_table']; ?>'&amp;'<?php echo $qstr; ?>" onclick="return delete_confirm2('<?php e__('Are you sure you want to delete the board thumbnail file?'); ?>');" class="btn_02 btn thumb_delete_btn"><?php e__('Delete Board thumbnail'); ?></a>
    <?php } ?>
    <input type="submit" value="<?php e__('Save'); ?>" class="btn_submi btn btn_01" accesskey="s">
</div>

</form>

<?php
get_localize_script('board_form',
array(
'image_check_msg'=>__('Do you want to apply bulletin board image settings for the current theme?'),  // 현재 테마의 게시판 이미지 설정을 적용하시겠습니까?
'comments_number_msg'=>__('The number of non-modifiable comments must be at least 0.'),     //원글 수정 불가 댓글수는 0 이상 입력하셔야 합니다.
'post_check_msg'=>__('You must enter at least 1 for the number of original comments that can not be deleted.'),    //원글 삭제 불가 댓글수는 1 이상 입력하셔야 합니다.
'bo_table_check_msg'=>__('Board TABLE Name can not be used. Please use a different name.'),
),
true);
?>
<script>
jQuery(function(){
    $('.horizon-swiper').horizonSwiper();

    /*
    setTimeout(function(){

        $("#fboardform .tab_tit").each( function( index, element ){
            $(this).removeClass("close").next(".tab_con").hide();
        });

    }, 1);
    */

    $("#fboardform").on("click", ".tab_tit", function(e){
        $(this).next().slideToggle("slow", function() {
            $(this).toggleClass("hide");

            if( $(this).is(":visible") ){
            <?php if( 'smarteditor2' === $config['cf_editor'] ){ ?>
            var othis = $(this);
            if( othis.find("textarea.smarteditor2").length ){
                othis.find("textarea.smarteditor2").each( function(index){
                    var name_attr = $(this).attr("name");

                    if( ! $(this).next("iframe").height() ){
                        oEditors.getById[name_attr].exec("SE_FIT_IFRAME", []);
                        oEditors.getById[name_attr].exec("CHANGE_EDITING_MODE", ["WYSIWYG", true]);
                    }
                });
            }
            <?php } ?>
            }
        });

        $(this).toggleClass("close");
//        $(".tab_con").not($(this).next()).slideUp('');
    });

    $("#board_copy").on("click", function(e){
        window.open(this.href, "win_board_copy", "left=10,top=10,width=500,height=400");
        return false;
    });

    $(".get_theme_galc").on("click", function(e) {
        if(!confirm( board_form.image_check_msg ))
            return false;

        $.ajax({
            type: "POST",
            url: "./theme_config_load.php",
            cache: false,
            async: false,
            data: { type: "board" },
            dataType: "json",
            success: function(data) {
                if(data.error) {
                    alert(data.error);
                    return false;
                }

                var field = Array('bo_gallery_cols', 'bo_gallery_width', 'bo_gallery_height', 'bo_mobile_gallery_width', 'bo_mobile_gallery_height', 'bo_image_width');
                var count = field.length;
                var key;

                for(i=0; i<count; i++) {
                    key = field[i];

                    if(data[key] != undefined && data[key] != "")
                        $("input[name="+key+"]").val(data[key]);
                }
            }
        });
    });
});

function board_copy(bo_table) {
    window.open("./board_copy.php?bo_table="+bo_table, "BoardCopy", "left=10,top=10,width=500,height=200");
}

function set_point(f) {
    if (f.chk_grp_point.checked) {
        f.bo_read_point.value = "<?php echo $config['cf_read_point'] ?>";
        f.bo_write_point.value = "<?php echo $config['cf_write_point'] ?>";
        f.bo_comment_point.value = "<?php echo $config['cf_comment_point'] ?>";
        f.bo_download_point.value = "<?php echo $config['cf_download_point'] ?>";
    } else {
        f.bo_read_point.value     = f.bo_read_point.defaultValue;
        f.bo_write_point.value    = f.bo_write_point.defaultValue;
        f.bo_comment_point.value  = f.bo_comment_point.defaultValue;
        f.bo_download_point.value = f.bo_download_point.defaultValue;
    }
}

function fboardform_submit(f)
{

    <?php
    if(!$w){
    $js_array = get_bo_table_banned_word();
    echo "var banned_array = ". json_encode($js_array) . ";\n";
    }
    ?>

    // 게시판명이 금지된 단어로 되어 있으면
    if( (typeof banned_array != 'undefined') && jQuery.inArray(f.bo_table.value, banned_array) !== -1 ){
        alert( board_form.bo_table_check_msg );
        return false;
    }

    <?php echo get_editor_js("bo_content_head"); ?>
    <?php echo get_editor_js("bo_content_tail"); ?>
    <?php echo get_editor_js("bo_mobile_content_head"); ?>
    <?php echo get_editor_js("bo_mobile_content_tail"); ?>

    if (parseInt(f.bo_count_modify.value) < 0) {
        alert( board_form.comments_number_msg );
        f.bo_count_modify.focus();
        return false;
    }

    if (parseInt(f.bo_count_delete.value) < 1) {
        alert( board_form.post_check_msg );
        f.bo_count_delete.focus();
        return false;
    }
    
    <?php start_event('admin_board_form_sumit', $w); ?>

    return true;
}

<?php start_event('admin_board_form_script', $w); ?>

</script>

<?php
include_once ('./admin.tail.php');
?>
