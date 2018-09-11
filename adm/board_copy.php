<?php
$sub_menu = "300100";
include_once("./_common.php");

auth_check($auth[$sub_menu], 'w');

$gml['title'] = __('Copy Board');
include_once(GML_PATH.'/head.sub.php');
print_l10n_js_text('admin_js');     // print js object l10n text
?>
<script src="<?php echo GML_ADMIN_URL ?>/admin.js?ver=<?php echo GML_JS_VER; ?>"></script>

<div class="new_win">
    <h1><?php echo $gml['title']; ?></h1>

    <form name="fboardcopy" id="fboardcopy" action="./board_copy_update.php" onsubmit="return fboardcopy_check(this);" method="post">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>" id="bo_table">
    <input type="hidden" name="token" value="">
    <div class=" new_win_con">
        <div class="tbl_frm01 tbl_wrap">
            <table>
            <caption><?php echo $gml['title']; ?></caption>
            <tbody>
            <tr>
                <th scope="col"><?php __('Original table name'); ?></th>
                <td><?php echo $bo_table ?></td>
            </tr>
            <tr>
                <th scope="col"><label for="target_table"><?php e__('Copy table name'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label></th>
                <td><input type="text" name="target_table" id="target_table" required class="required alnum_ frm_input" maxlength="20"><?php e__('Alphabetic, numeric, _ only (no spaces)'); ?></td>
            </tr>
            <tr>
                <th scope="col"><label for="target_subject"><?php e__('Board Subject'); ?><strong class="sound_only"><?php e__('Required'); ?></strong></label></th>
                <td><input type="text" name="target_subject" value="[<?php e__('Copy'); ?>] <?php echo get_board_gettext_titles($board['bo_subject']); ?>" id="target_subject" required class="required frm_input" maxlength="120"></td>
            </tr>
            <tr>
                <th scope="col"><?php e__('Copy Type'); ?></th>
                <td>
                    <input type="radio" name="copy_case" value="schema_only" id="copy_case" checked>
                    <label for="copy_case"><?php e__('Structure only'); ?></label>
                    <input type="radio" name="copy_case" value="schema_data_both" id="copy_case2">
                    <label for="copy_case2"><?php e__('Structure and data'); ?></label>
                </td>
            </tr>
            </tbody>
            </table>
        </div>
    </div>
    <div class="win_btn ">
        <input type="submit" class="btn_submit btn" value="<?php e__('Copy'); ?>">
        <input type="button" class="btn_close btn" value="<?php e__('Close Window'); ?>" onclick="window.close();">
    </div>

    </form>

</div>

<?php
get_localize_script('fboard_copy',
array(
'check_msg'=>__('The original table name and the table name to copy must be different.'),  // 원본 테이블명과 복사할 테이블명이 달라야 합니다.
'bo_table_check_msg'=>__('Board TABLE Name can not be used. Please use a different name.'),
),
true);
?>
<script>
function fboardcopy_check(f)
{

    <?php
    if(!$w){
    $js_array = get_bo_table_banned_word();
    echo "var banned_array = ". json_encode($js_array) . ";\n";
    }
    ?>

    // 게시판명이 금지된 단어로 되어 있으면
    if( (typeof banned_array != 'undefined') && jQuery.inArray(f.target_table.value, banned_array) !== -1 ){
        alert( fboard_copy.bo_table_check_msg );
        return false;
    }

    if (f.bo_table.value == f.target_table.value) {
        alert(fboard_copy.check_msg);
        return false;
    }

    return true;
}
</script>


<?php
include_once(GML_PATH.'/tail.sub.php');
?>
