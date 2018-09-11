<?php
@header('Content-Type: text/html; charset=utf-8');
@header('X-Robots-Tag: noindex');
include_once ('./install.header.php');

$title = GML_VERSION." ".__('Verify Licenses')." 1/3";
include_once ('./install.inc.php');
?>

<?php
if ($exists_data_dir && $write_data_dir) {
    // 필수 모듈 체크
    require_once('./library.check.php');

    if( $lang === 'ko_KR' ){
        $license_text = implode('', file('../LICENSE.txt'));
    } else {
        ob_start();
        require_once('./license.php');
        $license_text = ob_get_contents();
        ob_end_clean();
    }
?>
<form action="./install_config.php" method="post" onsubmit="return frm_submit(this);">

<div class="ins_inner ins_inner2" id="license">
    <div class="ins_left">

        <h1><?php e__('License'); ?></h1>
        <div class="ins_ta ins_license">
            <textarea name="textarea" id="ins_license" readonly><?php echo $license_text; ?></textarea>
        </div>

        <div id="ins_agree">
            <input type="checkbox" name="agree" value="agree" id="agree">
            <label for="agree"><?php e__('I agree'); ?></label>
        </div>

        <div class="inner_btn">
            <input type="submit" value="<?php e__('Next'); ?>">
        </div>
    </div>
    <div class="ins_right">
        <i class="fa fa-check-circle"></i>
        <h2><?php e__('License'); ?></h2>
        <p>
            <strong class="st_strong"><?php e__('Be sure to check the License details.'); ?></strong><br>
        </p>
        <div class="ins_progress">
            <h3><?php e__('Installation order'); ?></h3>
            <ol>
                <li class="plogress_sl"><span class="ins_num">1</span><span class="ins_text"><?php e__('License'); ?></span></li>
                <li><span class="ins_num">2</span><span class="ins_text"><?php e__('Entering Information'); ?></span></li>
                <li><span class="ins_num">3</span><span class="ins_text"><?php e__('Install'); ?></span></li>
            </ol>
        </div>
    </div>
</div>

</form>

<?php
get_localize_script('install_index',
array(
'agree_msg'=>__('You must accept the license to install it.'),  // 라이센스 내용에 동의하셔야 설치가 가능합니다.
),
true);
?>
<script>
function frm_submit(f)
{
    if (!f.agree.checked) {
        alert( install_index.agree_msg );
        return false;
    }
    return true;
}
</script>
<?php
} // if
?>

<?php
include_once ('./install.inc2.php');
?>
