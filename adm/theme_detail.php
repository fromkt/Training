<?php
$sub_menu = "100280";
include_once('./_common.php');

if ($is_admin != 'super')
    die(__('Only the Super administrator can access it.'));

$theme = trim($_POST['theme']);
$theme_dir = get_theme_dir();

if(!in_array($theme, $theme_dir))
    die(__('The selected theme is not installed.'));

$info = get_theme_info($theme);

if($info['screenshot'])
    $screenshot = '<img src="'.$info['screenshot'].'" alt="'.$name.'">';
else
    $screenshot = '<img src="'.GML_ADMIN_URL.'/img/theme_img.jpg" alt="">';

$name = get_text($info['theme_name']);
if($info['theme_uri']) {
    $name = '<a href="'.set_http($info['theme_uri']).'" target="_blank" class="thdt_home">'.$name.'</a>';
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

<div id="theme_detail">
    <h2><?php echo $name; ?></h2>
    <div class="theme_dt_img"><?php echo $screenshot; ?></div>
    <div class="theme_dt_if">
        <p><?php echo get_text($info['detail']); ?></p>
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
        <div class="theme_dt_btn">
        <a href="./theme_preview.php?theme=<?php echo $theme; ?>" class="theme_pr btn_03" target="theme_preview btn"><?php e__('Preview'); ?></a>
        <button type="button" class="close_btn btn"><i class="fa fa-times" aria-hidden="true"></i></button>
        </div>
    </div>
</div>

<script>
$(".close_btn").on("click", function() {
    $("#theme_detail").remove();
});
</script>