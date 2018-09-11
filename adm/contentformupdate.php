<?php
$sub_menu = '300600';
include_once('./_common.php');

if ($w == "u" || $w == "d")
    check_demo();

if ($w == 'd')
    auth_check($auth[$sub_menu], "d");
else
    auth_check($auth[$sub_menu], "w");

check_admin_token();

if ($co_id)
{
    if(preg_match("/[^a-z0-9_]/i", $co_id)) alert(__('ID can only be alphabetic, numeric, and _.'));

    $co_row = get_content_db($co_id);
}

@mkdir(GML_DATA_PATH."/content", GML_DIR_PERMISSION);
@chmod(GML_DATA_PATH."/content", GML_DIR_PERMISSION);

if ($co_himg_del)  @unlink(GML_DATA_PATH."/content/{$co_id}_h");
if ($co_timg_del)  @unlink(GML_DATA_PATH."/content/{$co_id}_t");

$error_msg = '';

if( $co_include_head ){

    $file_ext = pathinfo($co_include_head, PATHINFO_EXTENSION);

    if( ! $file_ext || ! in_array($file_ext, array('php', 'htm', 'html')) ) {
        alert(__('The extension of the header file path only accepts php, htm, html.'));
    }
}

if( $co_include_tail ){

    $file_ext = pathinfo($co_include_tail, PATHINFO_EXTENSION);

    if( ! $file_ext || ! in_array($file_ext, array('php', 'htm', 'html')) ) {
        alert(__('The extension of the footer file path only accepts php, htm, html.'));
    }
}

if( $co_include_head && ! is_include_path_check($co_include_head) ){
    $co_include_head = '';
    $error_msg = __('Header file path contains a string that can not be included.');
}

if( $co_include_tail && ! is_include_path_check($co_include_tail) ){
    $co_include_tail = '';
    $error_msg = __('Footer file path contains a string that can not be included.');
}

$sql_common = " co_include_head     = '$co_include_head',
                co_include_tail     = '$co_include_tail',
                co_html             = '$co_html',
                co_tag_filter_use   = '$co_tag_filter_use',
                co_subject          = '$co_subject',
                co_content          = '$co_content',
                co_mobile_content   = '$co_mobile_content',
                co_skin             = '$co_skin',
                co_mobile_skin      = '$co_mobile_skin' ";

if ($w == "")
{
    $row = $co_row;
    if ($row['co_id'])
        alert(__('Is already a content registered with the same ID.'));

    $sql = " insert {$gml['content_table']}
                set co_id = '$co_id',
                    $sql_common ";
    sql_query($sql);
}
else if ($w == "u")
{
    $sql = " update {$gml['content_table']}
                set $sql_common
              where co_id = '$co_id' ";
    sql_query($sql);

}
else if ($w == "d")
{
    @unlink(GML_DATA_PATH."/content/{$co_id}_h");
    @unlink(GML_DATA_PATH."/content/{$co_id}_t");

    if( $co_row ){
        $sql = " delete from {$gml['content_table']} where co_id = '".$co_row['co_id']."' ";
        sql_query($sql);
        $sql = " delete from {$gml['multi_lang_data_table']} where ml_case = 'content' and ml_target_id = '".$co_row['co_id']."' ";
        sql_query($sql, false);
    }
}

if ($w == "" || $w == "u")
{
    if($config['cf_use_multi_lang_data']) {
        $key_array = array('co_subject', 'co_content', 'co_mobile_content');
        $value_array = array($co_subject, $co_content, $co_mobile_content);

        $sql_common = " ml_case = 'content',
                        ml_lang = '{$_POST['lang']}',
                        ml_target_id = '{$co_id}' ";

        for($i=0;$i<count($key_array);$i++) {
            $sql = " select * from {$gml['multi_lang_data_table']}
                     where ml_case='content'
                       and ml_lang='{$_POST['lang']}'
                       and ml_target_id='{$co_id}'
                       and ml_target_column='{$key_array[$i]}' ";

            $row = sql_fetch($sql);
            $ml_id = $row['ml_id'];
            if($ml_id) {
                // update
                $sql = " update {$gml['multi_lang_data_table']}
                            set ml_target_value = '{$value_array[$i]}'
                          where ml_id = '{$ml_id}' ";
                sql_query($sql);
            } else {
                // insert
                $sql = " insert {$gml['multi_lang_data_table']}
                            set ml_id = '{$ml_id}',
                                $sql_common,
                                ml_target_column = '{$key_array[$i]}',
                                ml_target_value = '{$value_array[$i]}' ";
                sql_query($sql);
            }
        }
    }
    if ($_FILES['co_himg']['name'])
    {
        $dest_path = GML_DATA_PATH."/content/".$co_id."_h";
        @move_uploaded_file($_FILES['co_himg']['tmp_name'], $dest_path);
        @chmod($dest_path, GML_FILE_PERMISSION);
    }
    if ($_FILES['co_timg']['name'])
    {
        $dest_path = GML_DATA_PATH."/content/".$co_id."_t";
        @move_uploaded_file($_FILES['co_timg']['tmp_name'], $dest_path);
        @chmod($dest_path, GML_FILE_PERMISSION);
    }

    if( $error_msg ){
        alert($error_msg, "./contentform.php?w=u&amp;co_id=$co_id");
    } else {
        goto_url("./contentform.php?w=u&amp;co_id=$co_id");
    }
}
else
{
    goto_url("./contentlist.php");
}
?>
