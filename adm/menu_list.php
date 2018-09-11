<?php
$sub_menu = "100290";
include_once('./_common.php');

if ($is_admin != 'super')
    alert(__('Only the Super administrator can access it.'));

$sql = " select * from {$gml['menu_table']} order by me_id ";
$result = sql_query($sql);

$gml['title'] = __('Menu settings'); //메뉴 설정
include_once('./admin.head.php');

$colspan = 7;
?>

<div class="local_desc01 local_desc">
    <p><?php e__('<strong>Caution!</strong> After setting the menu, press <strong>OK</strong> to save.');    //주의! 메뉴설정 작업 후 반드시 확인을 누르셔야 저장됩니다. ?></p>
</div>

<form name="fmenulist" id="fmenulist" method="post" action="./menu_list_update.php" onsubmit="return fmenulist_submit(this);">
<input type="hidden" name="token" value="">

<div id="menulist" class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $gml['title']; ?> <?php e__('List'); ?></caption>
    <thead>
    <tr>
        <th scope="col" rowspan="2" style="width:50%"><?php e__('Menu'); ?></th>
        <th scope="col" colspan="4" style="width:45%"><?php e__('Link'); ?></th>
        <th scope="col" rowspan="2" style="width:5%"><?php e__('Setting'); ?></th>
    </tr>
    <tr>
		<th scope="col"><?php e__('New Window'); ?></th>
		<th scope="col"><?php e__('Sequence'); ?></th>
        <th scope="col"><?php e__('Use PC'); ?></th>
        <th scope="col"><?php e__('Use Mobile'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
        $bg = 'bg'.($i%2);
        $sub_menu_class = '';
        if(strlen($row['me_code']) == 4) {
            $sub_menu_class = ' sub_menu_class';
            $sub_menu_info = '<span class="sound_only">'.sprintf('Sub on %s' ,$row['me_name']).'</span>';
            $sub_menu_ico = '<span class="sub_menu_ico"></span>';
        }

        $search  = array('"', "'");
        $replace = array('&#034;', '&#039;');
        $me_name = str_replace($search, $replace, $row['me_name']);
    ?>
    <tr class="<?php echo $bg; ?> menu_list menu_group_<?php echo substr($row['me_code'], 0, 2); ?>">

        <td class="<?php echo $sub_menu_class; ?>">
            <input type="hidden" name="code[]" value="<?php echo substr($row['me_code'], 0, 2) ?>">
            <label for="me_name_<?php echo $i; ?>" class="sound_only"><?php echo $sub_menu_info; ?> <?php e__('Menu'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label>
            <input type="text" name="me_name[]" value="<?php echo $me_name; ?>" id="me_name_<?php echo $i; ?>" required class="required tbl_input full_input">
        </td>

        <td colspan="4">
            <label for="me_link_<?php echo $i; ?>" class="sound_only"><?php e__('Link'); ?><strong class="sound_only"> <?php e__('Required'); ?></strong></label>
            <input type="text" name="me_link[]" value="<?php echo adjust_url_to_config($row['me_link']) ?>" id="me_link_<?php echo $i; ?>" required class="required frm_input full_input">
        </td>

        <td class="td_mng td_mng_s" rowspan="2">
            <?php if(strlen($row['me_code']) == 2) { ?>
            <button type="button" class="btn_add_submenu btn_03 "><?php e__('Add'); ?></button><br>
            <?php } ?>
            <button type="button" class="btn_del_menu btn_02 "><?php e__('delete'); ?></button>
        </td>

    </tr>
    <tr class="<?php echo $bg; ?> menu_list menu_group_<?php echo substr($row['me_code'], 0, 2); ?>">
    	<td></td>
		<td class="td_select">
            <label for="me_target_<?php echo $i; ?>" class="sound_only"><?php e__('New Window'); ?></label>
            <select name="me_target[]" id="me_target_<?php echo $i; ?>">
                <option value="self"<?php echo get_selected($row['me_target'], 'self', true); ?>><?php e__('Disable'); ?></option>
                <option value="blank"<?php echo get_selected($row['me_target'], 'blank', true); ?>><?php e__('Enable'); ?></option>
            </select>
        </td>

        <td class="td_select">
            <label for="me_order_<?php echo $i; ?>" class="sound_only"><?php e__('Sequence'); ?></label>
            <input type="text" name="me_order[]" value="<?php echo $row['me_order'] ?>" id="me_order_<?php echo $i; ?>" class="frm_input" size="5">
        </td>

        <td class="td_select">
            <label for="me_use_<?php echo $i; ?>" class="sound_only"><?php e__('Use PC'); ?></label>
            <select name="me_use[]" id="me_use_<?php echo $i; ?>">
                <option value="1"<?php echo get_selected($row['me_use'], '1', true); ?>><?php e__('Enable'); ?></option>
                <option value="0"<?php echo get_selected($row['me_use'], '0', true); ?>><?php e__('Disable'); ?></option>
            </select>
        </td>

        <td class="td_mng">
            <label for="me_mobile_use_<?php echo $i; ?>" class="sound_only"><?php e__('Use Mobile'); ?></label>
            <select name="me_mobile_use[]" id="me_mobile_use_<?php echo $i; ?>">
                <option value="1"<?php echo get_selected($row['me_mobile_use'], '1', true); ?>><?php e__('Enable'); ?></option>
                <option value="0"<?php echo get_selected($row['me_mobile_use'], '0', true); ?>><?php e__('Disable'); ?></option>
            </select>
        </td>
    </tr>
    <?php
    }

    if ($i==0)
        echo '<tr id="empty_menu_list"><td colspan="'.$colspan.'" class="empty_table">'.__('No Data').'</td></tr>';
    ?>
    </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <button type="button" onclick="return add_menu();" class="btn btn_03"><?php e__('Add Menu'); ?><span class="sound_only"> <?php e__('New Window'); ?></span></button>
    <input type="submit" name="act_button" value="<?php e__('Save'); ?>" class="btn_submit btn">
</div>

</form>

<?php
get_localize_script('menu_list',
array(
'del_menu_msg'=>__('Are you sure you want to delete the menu?'),  // 메뉴를 삭제하시겠습니까?
'no_data_msg'=>__('No Data'),    // 자료가 없습니다.
),
true);
?>
<script>
jQuery(function($) {
    $(document).on("click", ".btn_add_submenu", function() {
        var code = $(this).closest("tr").find("input[name='code[]']").val().substr(0, 2);
        add_submenu(code);
    });

    $(document).on("click", ".btn_del_menu", function() {
        if(!confirm( menu_list.del_menu_msg ))
            return false;

        var $tr = $(this).closest("tr");
        if($tr.find("td.sub_menu_class").size() > 0) {
            $tr.next().remove();
            $tr.remove();
        } else {
            var code = $(this).closest("tr").find("input[name='code[]']").val().substr(0, 2);
            $("tr.menu_group_"+code).remove();
        }

        if($("#menulist tr.menu_list").size() < 1) {
            var list = "<tr id=\"empty_menu_list\"><td colspan=\"<?php echo $colspan; ?>\" class=\"empty_table\">"+ menu_list.no_data_msg +"</td></tr>\n";
            $("#menulist table tbody").append(list);
        } else {
            $("#menulist tr.menu_list").each(function(index) {
                $(this).removeClass("bg0 bg1")
                    .addClass("bg"+(index % 2));
            });
        }
    });
});

function add_menu()
{
    var max_code = base_convert(0, 10, 36);
    $("#menulist tr.menu_list").find("input[name='code[]']").each(function() {
        var me_code = $(this).val().substr(0, 2);
        if(max_code < me_code)
            max_code = me_code;
    });

    var url = "./menu_form.php?code="+max_code+"&new=new";
    window.open(url, "add_menu", "left=100,top=100,width=550,height=650,scrollbars=yes,resizable=yes");
    return false;
}

function add_submenu(code)
{
    var url = "./menu_form.php?code="+code;
    window.open(url, "add_menu", "left=100,top=100,width=550,height=650,scrollbars=yes,resizable=yes");
    return false;
}

function base_convert(number, frombase, tobase) {
  //  discuss at: http://phpjs.org/functions/base_convert/
  // original by: Philippe Baumann
  // improved by: Rafał Kukawski (http://blog.kukawski.pl)
  //   example 1: base_convert('A37334', 16, 2);
  //   returns 1: '101000110111001100110100'

  return parseInt(number + '', frombase | 0)
    .toString(tobase | 0);
}

function fmenulist_submit(f)
{
    return true;
}
</script>

<?php
include_once ('./admin.tail.php');
?>
