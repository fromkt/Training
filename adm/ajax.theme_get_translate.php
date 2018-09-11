<?php
include_once('./_common.php');
include_once(GML_LIB_PATH.'/json.lib.php');

$error = admin_referer_check(true);
if($error) {
    die(json_encode(array('error'=>$error, 'url'=>GML_URL)));
}

if ($is_admin != 'super') {
    die(json_encode(array('error'=>__('Only the Super administrator can access it.'), 'url'=>GML_URL)));  //최고관리자만 접근 가능합니다.
}

$theme_param = preg_replace('/[^0-9a-z\/]/i', '', $theme_param);

$file_regex = "#\.php$#";
$is_theme = $is_skin = false;
$skin_name = $theme_lang_file = '';
$t_phps = array();
$select_theme_lang = (isset($select_theme_lang) && array_key_exists($select_theme_lang, allow_locale_langs()) ) ? $select_theme_lang : '';
$get_theme_info = (isset($get_theme_info) && $get_theme_info) ? 1 : 0;
$theme_folder = (isset($theme_folder) && $theme_folder) ? preg_replace('/[^0-9a-z_.-]/i', '', $theme_folder) : '';

if( $get_theme_info && $theme_folder ){

    $select_theme_path = GML_PATH.'/'.GML_THEME_DIR.'/'.$theme_folder;

    if( is_dir($select_theme_path) ){

        $info = get_theme_info($theme_folder);

        $theme_name = get_text($info['theme_name']);

        $theme_skin_dirs = array_merge(get_theme_skin_dir_list($select_theme_path), get_theme_skin_dir_list($select_theme_path, 1));

        ob_start();
            ?>
        <select id="select_theme_lang_file" data-theme="<?php echo $theme_folder; ?>" name="theme_param">
            <option value=""><?php e__('select'); ?></option>
            <option value="theme/<?php echo $theme_folder; ?>"><?php echo sprintf(__('Language files for theme %s'), $theme_name); ?></option>
            <?php
            foreach( $theme_skin_dirs as $skin_dir ){
                if( empty($skin_dir) ) continue;
                
                if( preg_match('/^'.preg_quote(GML_MOBILE_DIR.'/', '/').'/i', $skin_dir) ){
                    
                    $s = sprintf(__('Theme %s, Mobile Skin %s Language Files'), $theme_name, $skin_dir);
                } else {
                    
                    $s = sprintf(__('Theme %s, Skin %s Language Files'), $theme_name, $skin_dir);
                }

                echo '<option value="'.$skin_dir.'">'.$s.'</option>';
            }
            ?>
        </select>
        <button type="button" class="btn theme_load_lang_info"><?php e__('Open'); ?></button>
            <?php

        $content = ob_get_contents();
        ob_end_clean();
    
        die(json_encode(array('error'=>'', 'content'=>$content)));
    }

    die(json_encode(array('error'=>__('Invalid request'), 'url'=>GML_URL)));
    exit;
}

include_once GML_LIB_PATH."/Gettext/src/autoloader.php";

use Gettext\Translations;
use Gettext\Merge;
use Gettext\Generators;

if( preg_match('#^theme/#i', $theme_param) ){   // IF THEME

    $is_theme = true;
    $theme_folder = preg_replace('/[^0-9a-z_.-]/i', '', preg_replace('#^theme/#i', '', $theme_param));

    $theme_root_path = GML_PATH.'/'.GML_THEME_DIR.'/'.$theme_folder;

    if( ! is_dir($theme_root_path) ){
        die(json_encode(array('error'=>__('Invalid request'), 'url'=>GML_URL)));
    }

    $php_files = scanDirRecursive($theme_root_path, $file_regex, 'file', true);

    $theme_mobile_skin_regx = $theme_root_path.DIRECTORY_SEPARATOR.'mobile'.DIRECTORY_SEPARATOR.GML_SKIN_DIR;
    $theme_skin_regx = $theme_root_path.DIRECTORY_SEPARATOR.GML_SKIN_DIR;

    foreach( $php_files as $php_file ){
        if ( preg_match('/^'.preg_quote($theme_skin_regx, '/').'/i', $php_file) ) continue;
        if ( preg_match('/^'.preg_quote($theme_mobile_skin_regx, '/').'/i', $php_file) ) continue;

        $t_phps[] = $php_file;
    }

    $theme_lang_file = $select_theme_lang ? $theme_root_path.'/'.GML_LANG_DIR.'/'.$select_theme_lang.'/theme-'.$select_theme_lang.'.po' : '';

} else if( preg_match('#^skin/#i', $theme_param) ){   // IF THEME SKIN
    
    $skin_name = preg_replace('#^skin/#i', '', $theme_param);

    $theme_skin_path = GML_PATH.'/'.GML_THEME_DIR.'/'.$theme_folder.'/'.GML_SKIN_DIR.'/'.$skin_name;
    
    if( ! is_dir($theme_skin_path) ){
        die(json_encode(array('error'=>__('Invalid request'), 'url'=>GML_URL)));
    }

    $php_files = scanDirRecursive($theme_skin_path, $file_regex, 'file', true);
    
    foreach( $php_files as $php_file ){
        $t_phps[] = $php_file;
    }

    $theme_lang_file = $select_theme_lang ? $theme_skin_path.'/'.GML_LANG_DIR.'/'.$select_theme_lang.'/skin-'.$select_theme_lang.'.po' : '';

} else if( preg_match('#^mobile/skin/#i', $theme_param) ){   // IF THEME MOBILE SKIN

    $skin_name = preg_replace('#^mobile/skin/#i', '', $theme_param);

    $theme_skin_path = GML_PATH.'/'.GML_THEME_DIR.'/'.$theme_folder.'/mobile/'.GML_SKIN_DIR.'/'.$skin_name;

    if( ! is_dir($theme_skin_path) ){
        die(json_encode(array('error'=>__('Invalid request'), 'url'=>GML_URL)));
    }

    $php_files = scanDirRecursive($theme_skin_path, $file_regex, 'file', true);

    foreach( $php_files as $php_file ){

        $t_phps[] = $php_file;
    }

    $theme_lang_file = $select_theme_lang ? $theme_skin_path.'/'.GML_LANG_DIR.'/'.$select_theme_lang.'/skin-'.$select_theme_lang.'.po' : '';

}

if( !$t_phps ){
    die(json_encode(array('error'=>__('No file selected.'), 'url'=>GML_URL)));
}

$trans_arr = array();
$translations = new stdClass();

$i = 0;

$options = array(
'functions' => array(
        '__' => 'translate',
        'e__' => 'translate',
        'p__' => 'translate_with_context',
        'ep__' => 'translate_with_context',
        'n__' => 'n_translate',
        )
);

foreach( $t_phps as $php_file ){
    
    if( $i === 0 ){
        $translations = Translations::fromPhpCodeFile($php_file, $options);
    } else {
        $trans_arr[] = Translations::fromPhpCodeFile($php_file, $options);
    }
    $i++;
}

if( !empty($trans_arr[0]) ){
    foreach( $trans_arr as $key=>$trans ){
        $translations->mergeWith($trans);
    }
}

if( $theme_lang_file && file_exists($theme_lang_file) ){
    //Get the translations of the code that are stored in a po file
    $poTranslations = Translations::fromPoFile( $theme_lang_file );

    $translations->mergeWith($poTranslations, Merge::REFERENCES_OURS);
}

$content = Gettext\Generators\PhpArray_custom::generate($translations);

$exists_content = false;

ob_start();
?>
<table>
<colgroup>
<col width="50%">
<col width="50%">
</colgroup>
<tbody>
<?php
foreach( (array) $content['messages'] as $translates ){
    
    if( empty($translates) ) continue;

    foreach( $translates as $key=>$value ){
        if( empty($key) ) continue;
        
        $exists_content = true;

        $value_txt = isset($value['msg'][0]) ? $value['msg'][0] : '';
        $references = isset($value['references']) ? $value['references'] : array();
        $context =  isset($value['context']) ? $value['context'] : '';
        $attr_context = $context ? '<span class="context">'.$context.'</span>' : '';
        $plural =  isset($value['plural']) ? $value['plural'] : '';

        $d_s = DIRECTORY_SEPARATOR;

        echo '<tr>';
        echo '<th class="txt_left"><div class="print_key">'.$attr_context.$key.'</div>';
        
        echo '<ul class="hide_tooltip">';
            foreach( (array) $references as $path_value ){
                $file_path = preg_replace('/^'.preg_quote(GML_PATH, '/').'/i', '', $path_value[0]);
                echo '<li>'.substr($file_path, 1).' '.$path_value[1].'</li>';
            }
        echo '</ul>';

        echo '</th>';
        echo '<td>';
        echo '<input type="hidden" name="contexts[]" value="'.htmlspecialchars($context, ENT_QUOTES, 'UTF-8').'" >';
        echo '<input type="hidden" name="originals[]" value="'.htmlspecialchars($key, ENT_QUOTES, 'UTF-8').'" >';
        echo '<input type="hidden" name="plurals[]" value="'.htmlspecialchars($plural, ENT_QUOTES, 'UTF-8').'" >';
        echo '<input type="text" name="trans_txt[]" value="'.htmlspecialchars($value_txt, ENT_QUOTES, 'UTF-8').'" class="frm_input" ></td>';
        echo '</tr>';
    }
    
}
?>
</tbody>
</table>
<?php
$content = ob_get_contents();
ob_end_clean();

if( ! $exists_content ){
    die(json_encode(array('error'=>__('No translation to retrieve.'), 'url'=>GML_URL)));
}

die(json_encode(array('error'=>'', 'content'=>$content, 'load_theme_lang'=>$select_theme_lang, 'load_theme_param'=>$theme_param, 'load_theme_folder'=>$theme_folder)));
?>