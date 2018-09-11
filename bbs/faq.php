<?php
include_once('./_common.php');

// FAQ MASTER
$faq_master_list = array();
$sql = " select * from {$gml['faq_master_table']} order by fm_order,fm_id ";
$result = sql_query($sql);
while ($row=sql_fetch_array($result))
{
    $key = $row['fm_id'];
    if (!$fm_id) $fm_id = $key;
    $faq_master_list[$key] = $row;
}

if ($fm_id){
    $qstr .= '&amp;fm_id=' . $fm_id; // Master faq key_id
}

$fm = $faq_master_list[$fm_id];
if($config['cf_use_multi_lang_data']) {
    $fm = get_faq_by_lang($fm, 'faqmaster');
}
if (!$fm['fm_id'])
    alert(__('No content registered.'));

$gml['title'] = $fm['fm_subject'];

// load language for l10n
bind_lang_domain( 'default', get_path_lang_dir('skin', $faq_skin_path.'/'.GML_LANG_DIR) );

$skin_file = $faq_skin_path.'/list.skin.php';

include_once('./_head.php');

if(is_file($skin_file)) {
    $admin_href = '';
    $himg_src = '';
    $timg_src = '';
    if($is_admin)
        $admin_href = GML_ADMIN_URL.'/faqmasterform.php?w=u&amp;fm_id='.$fm_id;

    if(!GML_IS_MOBILE) {
        $himg = GML_DATA_PATH.'/faq/'.$fm_id.'_h';
        if (is_file($himg)){
            $himg_src = GML_DATA_URL.'/faq/'.$fm_id.'_h';
        }

        $timg = GML_DATA_PATH.'/faq/'.$fm_id.'_t';
        if (is_file($timg)){
            $timg_src = GML_DATA_URL.'/faq/'.$fm_id.'_t';
        }
    }

    $category_href = GML_BBS_URL.'/faq.php';
    $category_stx = '';
    $faq_list = array();

    $stx = trim($stx);
    $sql_search = '';

    if($stx) {
       $sql_search = " and ( INSTR(fa_subject, '$stx') > 0 or INSTR(fa_content, '$stx') > 0 ) ";
    }

    if ($page < 1) { $page = 1; } // If no page exists, first page (page 1) 페이지가 없으면 첫 페이지 (1 페이지)

    $page_rows = GML_IS_MOBILE ? $config['cf_mobile_page_rows'] : $config['cf_page_rows'];

    $sql = " select count(*) as cnt
                from {$gml['faq_table']}
                where fm_id = '$fm_id'
                  $sql_search ";
    $total = sql_fetch($sql);
    $total_count = $total['cnt'];

    $total_page  = ceil($total_count / $page_rows);  // Calculate Page Totals 전체 페이지 계산
    $from_record = ($page - 1) * $page_rows; // 시작 열을 구함

    $sql = " select *
                from {$gml['faq_table']}
                where fm_id = '$fm_id'
                  $sql_search
                order by fa_order , fa_id
                limit $from_record, $page_rows ";
    $result = sql_query($sql);
    for ($i=0;$row=sql_fetch_array($result);$i++){
        if($config['cf_use_multi_lang_data']) {
            $row = get_faq_by_lang($row, 'faq');
        }
        $faq_list[] = $row;
        if($stx) {
            $faq_list[$i]['fa_subject'] = search_font($stx, conv_content($faq_list[$i]['fa_subject'], 1));
            $faq_list[$i]['fa_content'] = search_font($stx, conv_content($faq_list[$i]['fa_content'], 1));
        }
    }

    $fm_head_html = conv_content(GML_IS_MOBILE ? $fm['fm_mobile_head_html'] : $fm['fm_head_html'], 1);
    $fm_tail_html = conv_content(GML_IS_MOBILE ? $fm['fm_mobile_tail_html'] : $fm['fm_tail_html'], 1);

    foreach($faq_master_list as $key => $value) {
        if($config['cf_use_multi_lang_data']) {
            $value = get_faq_by_lang($value, 'faqmaster');
        }
        if($value['fm_id'] == $fm_id) { // The currently selected category is : 현재 선택된 카테고리라면
            $faq_master_list[$key]['category_option'] = ' id="bo_cate_on"';
            $faq_master_list[$key]['category_msg_and_subject'] = '<span class="sound_only">'.__('Current Category').' </span>'.$value['fm_subject'];
        } else {
            $faq_master_list[$key]['category_msg_and_subject'] = $value['fm_subject'];
        }
    }

    $no_faq_list = '';
    if( count($faq_list) ){
        for($i=0; $i<count($faq_list); $i++) {
            $faq_list[$i]['fa_subject'] = conv_content($faq_list[$i]['fa_subject'], 1);
            $faq_list[$i]['fa_content'] = conv_content($faq_list[$i]['fa_content'], 1);
        }
    } else {
        if($stx){
            $no_faq_list = '<p class="empty_list">'.__('No post found.').'</p>';
        } else {
            $no_faq_list = '<div class="empty_list">'.__('No FAQ is registered.');
            if($is_admin)
                $no_faq_list .= '<br><a href="'.GML_ADMIN_URL.'/faqmasterlist.php">'.__('To register a new FAQ, use the FAQ Admin menu.').'</a>';
            $no_faq_list .= '</div>';
        }
    }

    $get_pagination = get_paging($page_rows, $page, $total_page, $_SERVER['SCRIPT_NAME'].'?'.$qstr.'&amp;page=');

    include_once($skin_file);
} else {
    echo '<p>'.str_replace(GML_PATH.'/', '', $skin_file).__('File does not exist.').'</p>';
}

include_once('./_tail.php');
?>
