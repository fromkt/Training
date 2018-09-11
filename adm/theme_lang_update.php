<?php
$sub_menu = "100550";
include_once('./_common.php');

$is_ajax = false;

if( isset($_POST['is_ajax']) && !empty($_POST['is_ajax']) ){
    $is_ajax = true;
    put_event('alert', 'alert_json_print', 4 );
}

if ($is_admin != 'super') {
    alert(__('Only the Super administrator can access it.'));  //최고관리자만 접근 가능합니다.
}

check_demo();

if( $downfile ){
    $downfile = preg_replace('/[^0-9a-z._]/i', '', $downfile);

    $zip_path = GML_DATA_PATH.'/tmp/'.$downfile;
    
    if( file_exists($zip_path) ){

        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename='.$downfile);
        header('Content-Length: ' . filesize($zip_path));
        readfile($zip_path);
        exit;

    } else {
        alert(__('File missing or invalid request.'));
    }

}

check_admin_token();

include_once GML_LIB_PATH."/Gettext/src/autoloader.php";

use Gettext\Translations;

$is_theme = $is_skin = false;
$skin_name = $theme_lang_file = $save_po_file = $save_mo_file = $save_lang_path = '';
$t_phps = array();
$load_theme_lang = (isset($load_theme_lang) && array_key_exists($load_theme_lang, allow_locale_langs()) ) ? $load_theme_lang : '';
$strip_allowable_tags = '<a><b><strong><span><i><em><u><label><br><hr>';
$file_regex = "#\.php$#";

$load_theme_folder = isset($load_theme_folder) ? preg_replace('/[^0-9a-z_.-]/i', '', $load_theme_folder) : '';
$load_theme_param = isset($load_theme_param) ? preg_replace('/[^0-9a-z_.-\/]/i', '', $load_theme_param) : '';

$post_originals = isset($_POST['originals']) ? (array) $_POST['originals'] : array();
$poedit_keyword_list = 'e__;__;p__:1,2c;ep__:1,2c;n__:1,2';

if( preg_match('#^theme/#i', $load_theme_param) ){   // IF THEME

    $is_theme = true;
    $load_theme_folder = preg_replace('/[^0-9a-z_.-]/i', '', preg_replace('#^theme/#i', '', $load_theme_param));

    $theme_skin_path = GML_PATH.'/'.GML_THEME_DIR.'/'.$load_theme_folder;
    
    $save_po_file = 'theme-'.$load_theme_lang.'.po';
    $save_mo_file = 'theme-'.$load_theme_lang.'.mo';
    $save_lang_path = $theme_skin_path.'/'.GML_LANG_DIR.'/'.$load_theme_lang;

    $theme_lang_file = $load_theme_lang ? $save_lang_path.'/'.$save_po_file : '';

} else if( preg_match('#^skin/#i', $load_theme_param) ){   // IF THEME SKIN

    $skin_name = preg_replace('#^skin/#i', '', $load_theme_param);

    $theme_skin_path = GML_PATH.'/'.GML_THEME_DIR.'/'.$load_theme_folder.'/'.GML_SKIN_DIR.'/'.$skin_name;

    $save_po_file = 'skin-'.$load_theme_lang.'.po';
    $save_mo_file = 'skin-'.$load_theme_lang.'.mo';
    $save_lang_path = $theme_skin_path.'/'.GML_LANG_DIR.'/'.$load_theme_lang;

    $theme_lang_file = $load_theme_lang ? $save_lang_path.'/'.$save_po_file : '';
    
} else if( preg_match('#^mobile/skin/#i', $load_theme_param) ){   // IF THEME MOBILE SKIN

    $skin_name = preg_replace('#^mobile/skin/#i', '', $load_theme_param);

    $theme_skin_path = GML_PATH.'/'.GML_THEME_DIR.'/'.$load_theme_folder.'/'.GML_MOBILE_DIR.'/'.GML_SKIN_DIR.'/'.$skin_name;

    $save_po_file = 'skin-'.$load_theme_lang.'.po';
    $save_mo_file = 'skin-'.$load_theme_lang.'.mo';
    $save_lang_path = $theme_skin_path.'/'.GML_LANG_DIR.'/'.$load_theme_lang;

    $theme_lang_file = $load_theme_lang ? $save_lang_path.'/'.$save_po_file : '';
}

if( $theme_skin_path && $load_theme_lang ){
    try {
        if( !is_dir($theme_skin_path.'/'.GML_LANG_DIR) ){
            @mkdir($theme_skin_path.'/'.GML_LANG_DIR, GML_DIR_PERMISSION);
            @chmod($theme_skin_path.'/'.GML_LANG_DIR, GML_DIR_PERMISSION);
        }
        if( !is_dir($theme_skin_path.'/'.GML_LANG_DIR.'/'.$load_theme_lang) ){
            @mkdir($theme_skin_path.'/'.GML_LANG_DIR.'/'.$load_theme_lang, GML_DIR_PERMISSION);
            @chmod($theme_skin_path.'/'.GML_LANG_DIR.'/'.$load_theme_lang, GML_DIR_PERMISSION);
        }
    } catch (Exception $e) {
    }
}

if( file_exists($theme_lang_file) ){
    //import from a .po file:
    $translations = Translations::fromPoFile($theme_lang_file);

    foreach( $post_originals as $i=>$original ){
        if( empty($original) ) continue;

        $context = (isset($_POST['contexts'][$i]) && !empty($_POST['contexts'][$i])) ? strip_tags($_POST['contexts'][$i], $strip_allowable_tags) : null;
        $trans_txt = (isset($_POST['trans_txt'][$i]) && !empty($_POST['trans_txt'][$i])) ? strip_tags($_POST['trans_txt'][$i], $strip_allowable_tags) : '';

        //edit some translations:
        $translation = $translations->find($context, $original);

        if ($translation) {
            $translation->setTranslation($trans_txt);
        }
    }

} else {

    $translations = new Gettext\Translations();

    foreach( $post_originals as $i=>$original ){
        if( empty($original) ) continue;

        $context = (isset($_POST['contexts'][$i]) && !empty($_POST['contexts'][$i])) ? strip_tags($_POST['contexts'][$i], $strip_allowable_tags) : null;
        $trans_txt = (isset($_POST['trans_txt'][$i]) && !empty($_POST['trans_txt'][$i])) ? strip_tags($_POST['trans_txt'][$i], $strip_allowable_tags) : '';
        $plural = (isset($_POST['plurals'][$i]) && !empty($_POST['plurals'][$i])) ? strip_tags($_POST['plurals'][$i], $strip_allowable_tags) : '';

        $insertedTranslation = $translations->insert($context, $original, $plural);

        //Find a specific translation
        $translation = $translations->find($context, $original);

        if ($translation) {
            $translation->setTranslation($trans_txt);
        }
    }

    //Edit headers, domain, etc
    $translations->setHeader('Last-Translator', 'SIR');
    $translations->setHeader('Language', $load_theme_lang);
    $translations->setHeader('X-Poedit-KeywordsList', $poedit_keyword_list);
    $translations->setDomain('default');
}

//Save to a file

$real_pofile_path = $theme_lang_file;

$tmp_save_result = $po_save_result = $mo_save_result = false;
$msg = $pre_msg = '';

$po_save_tmp_path = $mo_save_tmp_path = '';

if( $load_theme_lang && $load_theme_param ){

    if(is_writeable($save_lang_path)){
        $po_save_result = Gettext\Generators\Po::toFile($translations, $real_pofile_path);
        $mo_save_result = $translations->toMoFile($save_lang_path.'/'.$save_mo_file);
    } else {
        $data_tmp_path = GML_DATA_PATH.'/tmp/';

        $files = glob("{$data_tmp_path}/*.{po, mo}", GLOB_BRACE);
        $files2 = glob("{$data_tmp_path}/lang_*.zip", GLOB_BRACE);

        $files = array_merge( $files, $files2 );
        if (is_array($files)) {
            $before_time  = time() - 21600;     // 6 hours
            foreach ($files as $lang_file) {
                $modification_time = filemtime($log_file); // 파일접근시간

                if ($before_time && $modification_time > $before_time) continue;

                unlink($lang_file);
            }
        }

        $po_save_tmp_path = $data_tmp_path.$save_po_file;
        $mo_save_tmp_path = $data_tmp_path.$save_mo_file;

        $po_save_result = Gettext\Generators\Po::toFile($translations, $po_save_tmp_path);
        $mo_save_result = $translations->toMoFile($mo_save_tmp_path);
        $tmp_save_result = true;
    }
}

if( !$tmp_save_result && $po_save_result ){
    $msg = __('Save translation file succeeded.');
} else if ( $tmp_save_result && $po_save_result ){
    $files = array($po_save_tmp_path, $mo_save_tmp_path);

    $tmp_file_name = ( preg_match('#^theme/#i', $load_theme_param) ) ? preg_replace('/[^0-9a-z_.-]/i', '', str_replace('/', '_', $load_theme_param)) : 
    GML_THEME_DIR.'_'.$load_theme_folder.'_'.preg_replace('/[^0-9a-z_.-]/i', '', str_replace('/', '_', $load_theme_param));
    
    $zip_name = 'lang_'.$tmp_file_name.'.zip';
    $zip_path = GML_DATA_PATH.'/tmp/'.$zip_name;

    require_once GML_LIB_PATH.'/Zip/Zip.php';

    $zip = new Zip();
    $result = $zip->zip_start($zip_path);

    if ($result === true) {
        foreach ($files as $file) {
            $zip->zip_add($file);
        }
        $zip->zip_end(false, true);
        
        $pre_msg = '<div class="download_zipfile">';
        $pre_msg .= '<a href="./theme_lang_update.php?downfile='.$zip_name.'">'.__('Download Translation File').'</a>';
        $pre_msg .= '<br>';
        $pre_msg .= __('If you have downloaded a zip_file, After Uncompress it, Save the file .po and .mo file to the path below.');
        $pre_msg .= '<br>';
        $pre_msg .= preg_replace('#^'.preg_quote(GML_PATH.'/', '/').'#i', '', $save_lang_path).'/';
        $pre_msg .= '</div>';
    } else {

        alert(__('Failed to save translation file.'), GML_URL);
    }

} else {
    alert(__('Failed to save translation file.'), GML_URL);
}

if( $is_ajax ){
    die( json_encode(array('msg'=>$msg, 'pre_msg'=>$pre_msg)) );
}

echo $msg;
echo $pre_msg;