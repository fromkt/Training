<?php
$sub_menu = "100280";
include_once('./_common.php');

if ($is_admin != 'super')
    alert(__('Only the Super administrator can access it.'));     //최고관리자만 접근 가능합니다.

$theme = get_theme_dir();
if($config['cf_theme'] && in_array($config['cf_theme'], $theme))
    array_unshift($theme, $config['cf_theme']);
$theme = array_values(array_unique($theme));
$total_count = count($theme);

// 설정된 테마가 존재하지 않는다면 cf_theme 초기화
if($config['cf_theme'] && !in_array($config['cf_theme'], $theme))
    sql_query(" update {$gml['config_table']} set cf_theme = '' ");

$gml['title'] = __('Theme Setup');
include_once('./admin.head.php');

print_l10n_js_admin('theme_js');
?>

<script src="<?php echo GML_ADMIN_URL; ?>/theme.js"></script>
<div class="local_wr">
    <span class="btn_ov01"><span class="ov_txt"><?php e__('Installed Themes'); ?></span><span class="ov_num">  <?php echo number_format($total_count); ?></span></span>

</div>

<?php if($total_count > 0) { ?>
<ul id="theme_list">
    <?php
    for($i=0; $i<$total_count; $i++) {
        $active_txt = '';
        $info = get_theme_info($theme[$i]);

        $theme_domain = 'theme-'.$theme[$i];
        bind_lang_domain($theme_domain, get_path_lang_dir('theme', GML_PATH.'/'.GML_THEME_DIR.'/'.$theme[$i].'/'.GML_LANG_DIR));

        $name = get_text($info['theme_name']);
        if($info['screenshot'])
            $screenshot = '<img src="'.$info['screenshot'].'" alt="'.$name.'">';
        else
            $screenshot = '<img src="'.GML_ADMIN_URL.'/img/theme_img.jpg" alt="">';

        if($config['cf_theme'] == $theme[$i]) {
            // delete disable button
            // $btn_active = '<button type="button" class="theme_sl theme_deactive btn_04" data-theme="'.$theme[$i].'" '.'data-name="'.$name.'">'.__('Disabled').'</button>';
            $active_txt = '<span class="theme_sl theme_sl_use">'.__('Enable').'</span>';
        } else {
            $tconfig = get_theme_config_value($theme[$i], 'set_default_skin');
            if($tconfig['set_default_skin'])
                $set_default_skin = 'true';
            else
                $set_default_skin = 'false';

            $btn_active = '<button type="button" class="theme_sl theme_active btn_03" data-theme="'.$theme[$i].'" '.'data-name="'.$name.'" data-set_default_skin="'.$set_default_skin.'">'.__('Apply Theme').'</button>';
        }

        $maker = get_text($info['maker']);
        if($info['maker_uri']) {
            $maker = '<a href="'.set_http($info['maker_uri']).'" target="_blank" class="thdt_home">'.$maker.'</a>';
        }

        $license = get_text($info['license']);
        if($info['license_uri']) {
            $license = '<a href="'.set_http($info['license_uri']).'" target="_blank" class="thdt_home">'.$license.'</a>';
        }

    ?>
    <li>
        <div class="theme_wr">
            <div class="tmli_img">
                <?php echo $screenshot; ?><?php echo $active_txt; ?>
            </div>
            <div class="tmli_tit">
                <?php echo __(get_text($info['theme_name']), $theme_domain); ?>
            </div>
            <div class="tmli_detail">
                <p><?php echo __(get_text($info['detail']), $theme_domain); ?></p>
                <table>
                    <tr>
                        <th scope="row">Version</th>
                        <td><?php echo get_text($info['version']); ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Maker</th>
                        <td><?php echo $maker; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">License</th>
                        <td><?php echo $license; ?></td>
                    </tr>
                </table>
            </div>
            <div class="tmli_btn">
                <?php echo $btn_active; ?>
                <a href="./theme_preview.php?theme=<?php echo $theme[$i]; ?>" class="theme_pr btn_05" target="theme_preview"><?php e__('Preview'); ?></a>
            </div>
        </div>
    </li>

    <?php
    }
    ?>
</ul>
<?php } else { ?>
<p class="no_theme"><?php e__('There are no installed themes.');     //설치된 테마가 없습니다. ?></p>
<?php } ?>

<?php
include_once ('./admin.tail.php');
?>
