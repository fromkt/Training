<?php
$sub_menu = "300200";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

$mb = get_member($mb_id);
if (!$mb['mb_id'])
    alert(__('This member does not exist.'));

$gml['title'] = __('Accessible groups');
include_once('./admin.head.php');

$colspan = 4;
?>

<form name="fboardgroupmember_form" id="fboardgroupmember_form" action="./boardgroupmember_update.php" onsubmit="return boardgroupmember_form_check(this)" method="post">
<input type="hidden" name="mb_id" value="<?php echo $mb['mb_id'] ?>" id="mb_id">
<input type="hidden" name="token" value="" id="token">
<div class="local_sch03">
    <p><?php e__('ID'); ?> <b><?php echo $mb['mb_id'] ?></b>, <?php e__('Name'); ?> <b><?php echo get_text($mb['mb_name']); ?></b>, <?php e__('Nickname'); ?> <b><?php echo $mb['mb_nick'] ?></b></p>
    <label for="gr_id" class="sch_tit"><?php e__('Assign Group'); ?></label>
    <select name="gr_id" id="gr_id">
        <option value=""><?php e__('Please select an accessible group.'); ?></option>
        <?php
        $sql = " select *
                    from {$gml['group_table']}
                    where gr_use_access = 1 ";
        //if ($is_admin == 'group') {
        if ($is_admin != 'super')
            $sql .= " and gr_admin = '{$member['mb_id']}' ";
        $sql .= " order by gr_id ";
        $result = sql_query($sql);
        for ($i=0; $row=sql_fetch_array($result); $i++) {
            echo "<option value=\"".$row['gr_id']."\">".$row['gr_subject']."</option>";
        }
        ?>
    </select>
    <input type="submit" value="<?php e__('Select'); ?>" class="btn_submit btn" accesskey="s">
</div>
</form>

<form name="fboardgroupmember" id="fboardgroupmember" action="./boardgroupmember_update.php" onsubmit="return fboardgroupmember_submit(this);" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>" id="sst">
<input type="hidden" name="sod" value="<?php echo $sod ?>" id="sod">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>" id="sfl">
<input type="hidden" name="stx" value="<?php echo $stx ?>" id="stx">
<input type="hidden" name="page" value="<?php echo $page ?>" id="page">
<input type="hidden" name="token" value="<?php echo $token ?>" id="token">
<input type="hidden" name="mb_id" value="<?php echo $mb['mb_id'] ?>" id="mb_id">
<input type="hidden" name="w" value="d" id="w">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $gml['title']; ?> <?php e__('List'); ?></caption>
    <thead>
    <tr>
        <th scope="col">
            <label for="chkall" class="sound_only"><?php e__('All accessible groups'); ?></label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col"><?php e__('Group ID'); ?></th>
        <th scope="col"><?php e__('Group'); ?></th>
        <th scope="col"><?php e__('Processing date'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $sql = " select * from {$gml['group_member_table']} a, {$gml['group_table']} b
                where a.mb_id = '{$mb['mb_id']}'
                and a.gr_id = b.gr_id ";
    if ($is_admin != 'super')
        $sql .= " and b.gr_admin = '{$member['mb_id']}' ";
    $sql .= " order by a.gr_id desc ";
    $result = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
    $bg = 'bg'.($i%2);
    ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_chk">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo $row['gr_subject'] ?> <?php e__('Group'); ?></label>
            <input type="checkbox" name="chk[]" value="<?php echo $row['gml_id'] ?>" id="chk_<?php echo $i ?>">
        </td>
        <td><a href="<?php echo get_pretty_url('group', $row['gr_id']); ?>"><?php echo $row['gr_id'] ?></a></td>
        <td><?php echo $row['gr_subject'] ?></td>
        <td class="td_datetime"><?php echo $row['gml_datetime'] ?></td>
    </tr>
    <?php
    }

    if ($i == 0) {
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">'.__('No groups are accessible.').'</td></tr>';
    }
    ?>
    </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <input type="submit" name="" value="<?php e__('Delete Selection'); ?>" class="btn btn_02">
</div>
</form>

<?php
get_localize_script('boardgroupmember_form',
array(
'check_msg'=>__('Select at least one item to delete.'),  // 선택삭제 하실 항목을 하나 이상 선택하세요.
'select_msg'=>__('Please select an accessible group.'),    // 접근가능 그룹을 선택하세요.
),
true);
?>
<script>
function fboardgroupmember_submit(f)
{
    if (!is_checked("chk[]")) {
        alert(boardgroupmember_form.check_msg);
        return false;
    }

    return true;
}

function boardgroupmember_form_check(f)
{
    if (f.gr_id.value == '') {
        alert(boardgroupmember_form.select_msg);
        return false;
    }

    return true;
}
</script>

<?php
include_once('./admin.tail.php');
?>
