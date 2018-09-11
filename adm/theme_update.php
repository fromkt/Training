<?php
$sub_menu = "100280";
include_once('./_common.php');

if ($is_admin != 'super')
    die(__('Only the Super administrator can access it.'));

admin_referer_check();

$theme = trim($_POST['theme']);
$theme_dir = get_theme_dir();

if($_POST['type'] == 'reset') {
    $sql = " update {$gml['config_table']} set cf_theme = '' ";
    sql_query($sql);
    die('');
}

if(!in_array($theme, $theme_dir))
    die(__('The selected theme is not installed.'));

// 테마적용
$sql = " update {$gml['config_table']} set cf_theme = '$theme' ";
sql_query($sql);

// 테마 설정 스킨 적용
if($_POST['set_default_skin'] == 1) {
    $keys = 'set_default_skin, cf_member_skin, cf_mobile_member_skin, cf_new_skin, cf_mobile_new_skin, cf_search_skin, cf_mobile_search_skin, cf_connect_skin, cf_mobile_connect_skin, cf_faq_skin, cf_mobile_faq_skin, qa_skin, qa_mobile_skin';

    $tconfig = get_theme_config_value($theme, $keys);

    if($tconfig['set_default_skin']) {
        $sql_common = array();
        $qa_sql_common = array();

        foreach($tconfig as $key => $val) {
            if(preg_match('#^qa_.+$#', $key)) {
                if($val) {
                    if(!preg_match('#^theme/.+$#', $val))
                        $val = 'theme/'.$val;

                    $qa_sql_common[] = " $key = '$val' ";
                }

                continue;
            }

            if(!isset($config[$key]))
                continue;

            if($val) {
                if(!preg_match('#^theme/.+$#', $val))
                    $val = 'theme/'.$val;

                $sql_common[] = " $key = '$val' ";
            }
        }

        if(!empty($sql_common)) {
            $sql = " update {$gml['config_table']} set " . implode(', ', $sql_common);
            sql_query($sql);
        }

        if(!empty($qa_sql_common)) {
            $sql = " update {$gml['qa_config_table']} set " . implode(', ', $qa_sql_common);
            sql_query($sql);
        }
    }
}

die('');
?>