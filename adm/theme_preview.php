<?php
$sub_menu = "100280";
define('_THEME_PREVIEW_', true);
include_once('./_common.php');

$theme_dir = get_theme_dir();

if(!$theme || !in_array($theme, $theme_dir))
    alert_close(__('The theme does not exist or is not valid.'));

$info = get_theme_info($theme);

$arr_mode = array('index', 'list', 'view');
$mode = substr(strip_tags($_GET['mode']), 0, 20);
if(!in_array($mode, $arr_mode))
    $mode = 'index';

$qstr_index  = '&amp;mode=index';
$qstr_list   = '&amp;mode=list';
$qstr_view   = '&amp;mode=view';
$qstr_device = '&amp;mode='.$mode.'&amp;device='.(GML_IS_MOBILE ? 'pc' : 'mobile');

$sql = " select bo_table, wr_parent from {$gml['board_new_table']} order by bn_id desc limit 1 ";
$row = sql_fetch($sql);
$bo_table = $row['bo_table'];
$board = sql_fetch(" select * from {$gml['board_table']} where bo_table = '$bo_table' ");
$write_table = $gml['write_prefix'] . $bo_table;
// theme.config.php 미리보기 게시판 스킨이 설정돼 있다면
$tconfig = get_theme_config_value($theme, 'set_default_skin, preview_board_skin, preview_mobile_board_skin');
if($mode == 'list' || $mode == 'view') {
    $board['bo_skin'] = $tconfig['preview_board_skin'];
    $board['bo_mobile_skin'] = $tconfig['preview_mobile_board_skin'];
}

// 스킨경로
if (GML_IS_MOBILE) {
    $board_skin_path    = get_skin_path('board', $board['bo_mobile_skin']);
    $board_skin_url     = get_skin_url('board', $board['bo_mobile_skin']);
    $member_skin_path   = get_skin_path('member', $config['cf_mobile_member_skin']);
    $member_skin_url    = get_skin_url('member', $config['cf_mobile_member_skin']);
    $new_skin_path      = get_skin_path('new', $config['cf_mobile_new_skin']);
    $new_skin_url       = get_skin_url('new', $config['cf_mobile_new_skin']);
    $search_skin_path   = get_skin_path('search', $config['cf_mobile_search_skin']);
    $search_skin_url    = get_skin_url('search', $config['cf_mobile_search_skin']);
    $connect_skin_path  = get_skin_path('connect', $config['cf_mobile_connect_skin']);
    $connect_skin_url   = get_skin_url('connect', $config['cf_mobile_connect_skin']);
    $faq_skin_path      = get_skin_path('faq', $config['cf_mobile_faq_skin']);
    $faq_skin_url       = get_skin_url('faq', $config['cf_mobile_faq_skin']);
} else {
    $board_skin_path    = get_skin_path('board', $board['bo_skin']);
    $board_skin_url     = get_skin_url('board', $board['bo_skin']);
    $member_skin_path   = get_skin_path('member', $config['cf_member_skin']);
    $member_skin_url    = get_skin_url('member', $config['cf_member_skin']);
    $new_skin_path      = get_skin_path('new', $config['cf_new_skin']);
    $new_skin_url       = get_skin_url('new', $config['cf_new_skin']);
    $search_skin_path   = get_skin_path('search', $config['cf_search_skin']);
    $search_skin_url    = get_skin_url('search', $config['cf_search_skin']);
    $connect_skin_path  = get_skin_path('connect', $config['cf_connect_skin']);
    $connect_skin_url   = get_skin_url('connect', $config['cf_connect_skin']);
    $faq_skin_path      = get_skin_path('faq', $config['cf_faq_skin']);
    $faq_skin_url       = get_skin_url('faq', $config['cf_faq_skin']);
}

$conf = sql_fetch(" select cf_theme from {$gml['config_table']} ");
$name = get_text($info['theme_name']);
if($conf['cf_theme'] != $theme) {
    if($tconfig['set_default_skin'])
        $set_default_skin = 'true';
    else
        $set_default_skin = 'false';

    $btn_active = '<li><button type="button" class="theme_sl theme_active" data-theme="'.$theme.'" '.'data-name="'.$name.'" data-set_default_skin="'.$set_default_skin.'">'.__('Apply Theme').'</button></li>';
} else {
    $btn_active = '';
}

$gml['title'] = sprintf(__('Preview of %s theme'), get_text($info['theme_name']));
require_once(GML_THEME_PATH.'/head.sub.php');

print_l10n_js_admin('theme_js');
?>

<link rel="stylesheet" href="<?php echo GML_ADMIN_URL; ?>/css/theme.css">
<script src="<?php echo GML_ADMIN_URL; ?>/theme.js"></script>

<section id="preview_item">
    <ul>
        <li><a href="./theme_preview.php?theme=<?php echo $theme.$qstr_index; ?>"><?php e__('Index Screen'); ?></a></li>
        <li><a href="./theme_preview.php?theme=<?php echo $theme.$qstr_list; ?>"><?php e__('List of postings'); ?></a></li>
        <li><a href="./theme_preview.php?theme=<?php echo $theme.$qstr_view; ?>"><?php e__('View postings'); ?></a></li>
        <li><a href="./theme_preview.php?theme=<?php echo $theme.$qstr_device; ?>"><?php echo (GML_IS_MOBILE ? __('PC Version') : __('Mobile Version')); ?></a></li>
        <?php echo $btn_active; ?>
    </ul>
</section>

<section id="preview_content">
    <?php
    switch($mode) {
        case 'list':
            include(GML_BBS_PATH.'/board.php');
            break;
        case 'view':
            $wr_id = $row['wr_parent'];
            $write = get_write($write_table, $wr_id);
            include(GML_BBS_PATH.'/board.php');
            break;
        default:
            include(GML_PATH.'/index.php');
            break;
    }
    ?>
</section>

<?php
require_once(GML_PATH.'/tail.sub.php');
?>
