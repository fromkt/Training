<?php
$sub_menu = "300200";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

if ($is_admin != 'super' && $w == '') alert(__('Only the Super administrator can access it.'));

$html_title = __('Board Group');
$gr_id_attr = '';
$sound_only = '';
if ($w == '') {
    $gr_id_attr = 'required';
    $sound_only = '<strong class="sound_only"> '.__('required').'</strong>';
    $gr = array('gr_use_access' => 0);
    $html_title .= ' '.__('Create');
} else if ($w == 'u') {
    $gr_id_attr = 'readonly';
    $gr = get_group($gr_id);
    $html_title .= ' '.__('Edit');
}
else
    alert(__('The parameters is wrong.'));

$gml['title'] = $html_title;
include_once('./admin.head.php');
?>

<div class="local_desc01 local_desc">
    <p>
        <?php e__('You need at least one board group to create a board.'); ?><br>
        <?php e__('Using the bulletin board group will help you manage the bulletin board more effectively.'); ?>
    </p>
</div>

<form name="fboardgroup" id="fboardgroup" action="./boardgroup_form_update.php" onsubmit="return fboardgroup_check(this);" method="post" autocomplete="off">
<input type="hidden" name="w" value="<?php echo $w ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">

<div class="frm_wr">
    <ul class="frm_ul">
        <li>
            <span class="lb_block"><label for="gr_id"><?php e__('Group ID'); ?><?php echo $sound_only ?></label></span>
            <input type="text" name="gr_id" value="<?php echo $group['gr_id'] ?>" id="gr_id" <?php echo $gr_id_attr; ?> class="<?php echo $gr_id_attr; ?> alnum_ frm_input m_full_input" maxlength="10">
            <?php
            if ($w=='')
                echo __('Alphabetic, numeric, _ only (without spaces)');
            else
                echo '<a href="'.get_pretty_url('group', $group['gr_id']).'" class="btn_frmline">'.__('Redirect Board Group').'</a>';
            ?>
        </li>
        <li>
            <span class="lb_block"><label for="gr_subject"><?php e__('Group Subject'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label></span>

            <input type="text" name="gr_subject" value="<?php echo get_text($group['gr_subject']) ?>" id="gr_subject" required class="required frm_input frm_input_full" size="80">
            <?php
            if ($w == 'u')
                echo '<a href="./board_form.php?gr_id='.$gr_id.'" class="btn_frmline">'.__('Create Board').'</a>';
            ?>

        </li>
        <li>
            <span class="lb_block"><label for="gr_device"><?php e__('Browser_device'); ?></label>

            <?php echo help(__('Distinguish your PC from your mobile use.')) ?></span>
            <select id="gr_device" name="gr_device">
                <option value="both"<?php echo get_selected($group['gr_device'], 'both', true); ?>><?php e__('Use on both PC and mobile'); ?></option>
                <option value="pc"<?php echo get_selected($group['gr_device'], 'pc'); ?>><?php e__('PC only'); ?></option>
                <option value="mobile"<?php echo get_selected($group['gr_device'], 'mobile'); ?>><?php e__('MOBILE only'); ?></option>
            </select>

        </li>
        <li>
            <span class="lb_block"><?php if ($is_admin == 'super') { ?><label for="gr_admin"><?php } ?><?php e__('Group Admin'); ?><?php if ($is_admin == 'super') { ?></label><?php } ?></span>

            <?php
            if ($is_admin == 'super')
                echo '<input type="text" id="gr_admin" name="gr_admin" class="frm_input m_full_input" value="'.$gr['gr_admin'].'" maxlength="20">';
            else
                echo '<input type="hidden" id="gr_admin" name="gr_admin" value="'.$gr['gr_admin'].'">'.$gr['gr_admin'];
            ?>

        </li>
        <li>
            <span class="lb_block"><label for="gr_use_access"><?php e__('Access Member Usage'); ?></label>

            <?php echo help(__('If you check the use, only accessible members can access the bulletin boards belonging to this group.')) ?></span>
            <input type="checkbox" name="gr_use_access" value="1" id="gr_use_access" <?php echo $gr['gr_use_access']?'checked':''; ?>>
            <?php e__('Enable'); ?>

        </li>
        <li>
            <span class="lb_block"><?php e__('Access members'); ?></span>

            <?php
            // 접근회원수
            $sql1 = " select count(*) as cnt from {$gml['group_member_table']} where gr_id = '{$gr_id}' ";
            $row1 = sql_fetch($sql1);
            echo '<a href="./boardgroupmember_list.php?gr_id='.$gr_id.'">'.$row1['cnt'].'</a>';
            ?>

        </li>
        <?php for ($i=1;$i<=10;$i++) { ?>
        <li class="extra_ul">
            <span class="lb_block"><?php echo sprintf(__('Extra field %d'), $i); ?></span>
            <label for="gr_<?php echo $i ?>_subj" class="extra_lb"><?php echo sprintf(__('Extra field %d Title'), $i); ?></label>
            <input type="text" name="gr_<?php echo $i ?>_subj" value="<?php echo get_text($group['gr_'.$i.'_subj']) ?>" id="gr_<?php echo $i ?>_subj" class="frm_input m_full_input">
            <label for="gr_<?php echo $i ?>" class="extra_lb"><?php echo sprintf(__('Extra field %d Value'), $i); ?></label>
            <input type="text" name="gr_<?php echo $i ?>" value="<?php echo $gr['gr_'.$i] ?>" id="gr_<?php echo $i ?>" class="frm_input m_full_input">

        </li>
        <?php } ?>
    </ul>
</div>

<div class="btn_fixed_top">
    <a href="./boardgroup_list.php?<?php echo $qstr ?>" class="btn btn_02"><?php e__('List'); ?></a>
    <input type="submit" class="btn_submit btn" accesskey="s" value="<?php e__('Save'); ?>">
</div>

</form>


<script>

function fboardgroup_check(f)
{
    f.action = './boardgroup_form_update.php';
    return true;
}
</script>

<?php
include_once ('./admin.tail.php');
?>
