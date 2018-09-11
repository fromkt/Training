<?php
$sub_menu = "200900";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

$po_id = isset($po_id) ? (int) $po_id : 0;

$html_title = __('Poll');
if ($w == '')
    $html_title .= ' '.__('Add');
else if ($w == 'u')  {
    $html_title .= ' '.__('Edit');
    $po = get_poll_db($po_id);
} else
    alert(__('Incorrect \'w\' value.'));

$gml['title'] = $html_title;
include_once('./admin.head.php');
?>

<form name="fpoll" id="fpoll" action="./poll_form_update.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="po_id" value="<?php echo $po_id ?>">
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
            <label for="po_subject" class="lb_block"><?php e__('Poll Title'); ?><strong class="sound_only"><?php e__('required'); ?></strong></label>
            <input type="text" name="po_subject" value="<?php echo $po['po_subject'] ?>" id="po_subject" required class="required frm_input frm_input_full" size="80" maxlength="125">
        </li>

        <?php
        for ($i=1; $i<=9; $i++) {
            $required = '';
            if ($i==1 || $i==2) {
                $required = 'required';
                $sound_only = '<strong class="sound_only">'.__('required').'</strong>';
            }

            $po_poll = get_text($po['po_poll'.$i]);
        ?>

        <li class="li_50">
            <label for="po_poll<?php echo $i ?>" class="lb_block"><?php e__('topic'); ?> <?php echo $i ?><?php echo $sound_only ?></label>
            <input type="text" name="po_poll<?php echo $i ?>" value="<?php echo $po_poll ?>" id="po_poll<?php echo $i ?>" <?php echo $required ?> class="frm_input input_mg m_full_input <?php echo $required ?>" maxlength="125">
            <label for="po_cnt<?php echo $i ?>"><span class="sound_only"><?php e__('topic'); ?> <?php echo $i ?> </span><?php e__('Number of votes'); ?></label>
            <input type="text" name="po_cnt<?php echo $i ?>" value="<?php echo $po['po_cnt'.$i] ?>" id="po_cnt<?php echo $i ?>" class="frm_input" size="3">
           
        </li>

        <?php } ?>

        <li class="li_clear">
            <span class="lb_block"><label for="po_etc"><?php e__('Other opinions'); ?></label>
            <?php echo help(__('Enter a simple question to allow you to leave other opinions.')) ?></span>
            <input type="text" name="po_etc" value="<?php echo get_text($po['po_etc']) ?>" id="po_etc" class="frm_input" size="80" maxlength="125">
            
        </li>
        <li>
            <span class="lb_block"><label for="po_level"><?php e__('Voting membership level'); ?></label>
            <?php echo help(__('You can vote by setting the level to 1.')); ?></span>
            <?php echo get_member_level_select('po_level', 1, 10, $po['po_level']) ?> <?php e__('More voting is possible.'); ?>
            
        </li>
        <li>
            <span class="lb_block"><label for="po_point"><?php e__('Point'); ?></label>
            <?php echo help(__('Points will be awarded to the members who vote.')) ?></span>
            <input type="text" name="po_point" value="<?php echo $po['po_point'] ?>" id="po_point" class="frm_input"> <?php e__('Point'); ?>
            
        </li>

        <?php if ($w == 'u') { ?>
        <li>
            <span class="lb_block"><?php e__('Voting registration date'); ?><span>
            <?php echo $po['po_date']; ?>
        </li>
        <li>
            <label for="po_ips" class="lb_block"><?php e__('IP to vote'); ?></label>
            <textarea name="po_ips" id="po_ips" readonly rows="10"><?php echo preg_replace("/\n/", " / ", $po['po_ips']) ?></textarea>
        </li>
        <li>
            <label for="mb_ids" class="lb_block"><?php e__('A voting member'); ?></label>
            <textarea name="mb_ids" id="mb_ids" readonly rows="10"><?php echo preg_replace("/\n/", " / ", $po['mb_ids']) ?></textarea>
        </li>
        <?php } ?>
    </ul>

</div>

<div class="btn_fixed_top">
    <a href="./poll_list.php?<?php echo $qstr ?>" class="btn_02 btn"><?php e__('List'); ?></a>
    <input type="submit" value="<?php e__('Save'); ?>" class="btn_submit btn" accesskey="s">
</div>

</form>


<?php
include_once('./admin.tail.php');
?>
