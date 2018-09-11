<?php
$sub_menu = '300700';
include_once('./_common.php');

if ($w == "u" || $w == "d")
    check_demo();

if ($w == 'd')
    auth_check($auth[$sub_menu], "d");
else
    auth_check($auth[$sub_menu], "w");

check_admin_token();

$sql_common = " fa_subject = '$fa_subject',
                fa_content = '$fa_content',
                fa_order = '$fa_order' ";

if ($w == "")
{
    $sql = " insert {$gml['faq_table']}
                set fm_id ='$fm_id',
                    $sql_common ";
    sql_query($sql);

    $fa_id = sql_insert_id();
}
else if ($w == "u")
{
    if(!$config['cf_use_multi_lang_data']) {
        $sql = " update {$gml['faq_table']}
                    set $sql_common
                  where fa_id = '$fa_id' ";
        sql_query($sql);
    }
}
else if ($w == "d")
{
	$sql = " delete from {$gml['faq_table']} where fa_id = '$fa_id' ";
    sql_query($sql);

    delete_multi_lang_data($fa_id);
}

if ($w == "" || $w == "u")
{
    if($config['cf_use_multi_lang_data']) {
        $key_array = array('fa_subject', 'fa_content');
        $value_array = array($fa_subject, $fa_content);

        $sql_common = " ml_case = 'faq',
                        ml_lang = '{$_POST['lang']}',
                        ml_target_id = '{$fa_id}' ";

        for($i=0;$i<count($key_array);$i++) {
            $sql = " select * from {$gml['multi_lang_data_table']}
                     where ml_case='faq'
                       and ml_lang='{$_POST['lang']}'
                       and ml_target_id='{$fa_id}'
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
}

if ($w == 'd')
    goto_url("./faqlist.php?fm_id=$fm_id");
else
    goto_url("./faqform.php?w=u&amp;fm_id=$fm_id&amp;fa_id=$fa_id");
?>
