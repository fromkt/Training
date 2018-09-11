<?php
include_once('./_common.php');

if ($is_admin != 'super')
    die(__('Only the Super administrator can access it.'));

switch($type) {
    case 'group':
        $sql = " select gr_id as id, gr_subject as subject
                    from {$gml['group_table']}
                    order by gr_order, gr_id ";
        break;
    case 'board':
        $sql = " select bo_table as id, bo_subject as subject, gr_id
                    from {$gml['board_table']}
                    order by bo_order, bo_table ";
        break;
    case 'content':
        $sql = " select co_id as id, co_subject as subject
                    from {$gml['content_table']}
                    order by co_id ";
        break;
    default:
        $sql = '';
        break;
}
?>

<?php
if($sql) {
    $result = sql_query($sql);

    for($i=0; $row=sql_fetch_array($result); $i++) {
        if($i == 0) {

    $bbs_subject_title = ($type == 'board') ? __('Board Title') : __('Subject');
?>

<div class="tbl_head01 tbl_wrap">
    <table>
    <thead>
    <tr>
        <th scope="col"><?php echo $bbs_subject_title; ?></th>
        <?php if($type == 'board'){ ?>
            <th scope="col"><?php e__('Board Group'); ?></th>
        <?php } ?>
        <th scope="col"><?php e__('Select'); ?></th>
    </tr>
    </thead>
    <tbody>

<?php }
        switch($type) {
            case 'group':
                $link = get_pretty_url('group', 'gr_id='.$row['id']);
                break;
            case 'board':
                $link = get_pretty_url($row['id']);
                break;
            case 'content':
                $link = get_pretty_url('content', 'co_id='.$row['id']);
                break;
            default:
                $link = '';
                break;
        }
?>

    <tr>
        <td class="td_left"><?php echo $row['subject']; ?></td>
        <?php
        if($type == 'board'){
        $group = get_call_func_cache('get_group', array($row['gr_id']));
        ?>
        <td class="td_left"><?php echo $group['gr_subject']; ?></td>
        <?php } ?>
        <td class="td_mng td_mng_s">
            <input type="hidden" name="subject[]" value="<?php echo preg_replace('/[\'\"]/', '', $row['subject']); ?>">
            <input type="hidden" name="link[]" value="<?php echo $link; ?>">
            <button type="button" class="add_select btn_03"><span class="sound_only"><?php echo $row['subject']; ?> </span><?php e__('Select'); ?></button>
        </td>
    </tr>

<?php } ?>

    </tbody>
    </table>
</div>

<div class="local_desc01 menu_exists_tip" style="display:none">
    <p>* <?php e__('The title of the <strong>RED</strong> color appears if you are already connected to the menu.'); ?></p>
</div>

<div class="btn_win02 btn_win">
    <button type="button" class="btn_cancel btn btn_02" onclick="window.close();"><?php e__('Close Window'); ?></button>
</div>

<?php } else { ?>

<div class="tbl_frm01 tbl_wrap">
    <table>
    <tbody>
    <tr>
        <th scope="row"><label for="me_name"><?php e__('Menu'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label></th>
        <td><input type="text" name="me_name" id="me_name" required class="frm_input required"></td>
    </tr>
    <tr>
        <th scope="row"><label for="me_link"><?php e__('Link'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label> <?php echo help(__('Please enter a link including http://')); ?></th>
        <td>
            <input type="text" name="me_link" id="me_link" required class="frm_input full_input required">
        </td>
    </tr>
    </tbody>
    </table>
</div>

<div class="btn_win02 btn_win">
    <button type="button" id="add_manual" class="btn_submit btn"><?php e__('Add'); ?></button>
    <button type="button" class="btn_cancel btn btn_02" onclick="window.close();"><?php e__('Close'); ?></button>
</div>
<?php } ?>
