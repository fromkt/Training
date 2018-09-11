<?php
$sub_menu = '300700';
include_once('./_common.php');

if ($w == "u" || $w == "d")
    check_demo();

if ($W == 'd')
    auth_check($auth[$sub_menu], "d");
else
    auth_check($auth[$sub_menu], "w");

check_admin_token();

@mkdir(GML_DATA_PATH."/faq", GML_DIR_PERMISSION);
@chmod(GML_DATA_PATH."/faq", GML_DIR_PERMISSION);

if ($fm_himg_del)  @unlink(GML_DATA_PATH."/faq/{$fm_id}_h");
if ($fm_timg_del)  @unlink(GML_DATA_PATH."/faq/{$fm_id}_t");

$fm_subject = strip_tags($fm_subject);

$sql_common = " set fm_subject = '$fm_subject',
                    fm_head_html = '$fm_head_html',
                    fm_tail_html = '$fm_tail_html',
                    fm_mobile_head_html = '$fm_mobile_head_html',
                    fm_mobile_tail_html = '$fm_mobile_tail_html',
                    fm_order = '$fm_order' ";

if ($w == "")
{
    $sql = " alter table {$gml['faq_master_table']} auto_increment=1 ";
    sql_query($sql);

    $sql = " insert {$gml['faq_master_table']} $sql_common ";
    sql_query($sql);

    $fm_id = sql_insert_id();
}
else if ($w == "u")
{
    if(!$config['cf_use_multi_lang_data']) {
        $sql = " update {$gml['faq_master_table']} $sql_common where fm_id = '$fm_id' ";
        sql_query($sql);
    }
}
else if ($w == "d")
{
    @unlink(GML_DATA_PATH."/faq/{$fm_id}_h");
    @unlink(GML_DATA_PATH."/faq/{$fm_id}_t");

    // FAQ삭제
	$sql = " delete from {$gml['faq_master_table']} where fm_id = '$fm_id' ";
    sql_query($sql);

    // FAQ상세삭제
	$sql = " delete from {$gml['faq_table']} where fm_id = '$fm_id' ";
    sql_query($sql);

    delete_multi_lang_data($fm_id);
}

if ($w == "" || $w == "u")
{
    if($config['cf_use_multi_lang_data']) {
        $key_array = array('fm_subject', 'fm_head_html', 'fm_tail_html', 'fm_mobile_head_html', 'fm_mobile_tail_html');
        $value_array = array($fm_subject, $fm_head_html, $fm_tail_html, $fm_mobile_head_html, $fm_mobile_tail_html);

        $sql_common = " ml_case = 'faqmaster',
                        ml_lang = '{$_POST['lang']}',
                        ml_target_id = '{$fm_id}' ";

        for($i=0;$i<count($key_array);$i++) {
            $sql = " select * from {$gml['multi_lang_data_table']}
                     where ml_case='faqmaster'
                       and ml_lang='{$_POST['lang']}'
                       and ml_target_id='{$fm_id}'
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

    if ($_FILES['fm_himg']['name']){
        $dest_path = GML_DATA_PATH."/faq/".$fm_id."_h";
        @move_uploaded_file($_FILES['fm_himg']['tmp_name'], $dest_path);
        @chmod($dest_path, GML_FILE_PERMISSION);
    }
    if ($_FILES['fm_timg']['name']){
        $dest_path = GML_DATA_PATH."/faq/".$fm_id."_t";
        @move_uploaded_file($_FILES['fm_timg']['tmp_name'], $dest_path);
        @chmod($dest_path, GML_FILE_PERMISSION);
    }

    goto_url("./faqmasterform.php?w=u&amp;fm_id=$fm_id");
}
else
    goto_url("./faqmasterlist.php");
?>
