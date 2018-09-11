<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
    </div>
</div>

<?php if(defined('_INDEX_')) { ?>
	<?php echo poll('basic'); // 설문조사 ?>
	<?php echo visit('basic'); // 방문자수 ?>
<?php } ?>

<div id="ft">
    <div id="ft_copy">
        <div id="ft_company">
            <a href="<?php echo get_pretty_url('content', 'company') ?>"><?php e__('About Company'); ?></a>
            <a href="<?php echo get_pretty_url('content', 'privacy') ?>"><?php e__('Privacy Policy'); ?></a>
            <a href="<?php echo get_pretty_url('content', 'provision') ?>"><?php e__('Terms of service'); ?></a>
        </div>
        Copyright &copy; <b><?php e__('Your domain.'); ?></b> All rights reserved.<br>

        <div class="lang_var_select">
            <span class="sound_only"><?php e__('Select Site Language'); ?></span>
            <?php echo get_lang_select_html('theme_lang_bar', $lang, 'class="theme_select_lang"', true); ?>
        </div>
    </div>
    <button type="button" id="top_btn"><i class="fa fa-arrow-up" aria-hidden="true"></i><span class="sound_only"><?php e__('Top'); ?></span></button>
    <?php
    if(GML_DEVICE_BUTTON_DISPLAY && GML_IS_MOBILE) { ?>
    <a href="<?php echo get_device_change_url(); ?>" id="device_change"><?php e__('View PC'); ?></a>
    <?php
    }

    if ($config['cf_analytics']) {
        echo $config['cf_analytics'];
    }
    ?>
</div>


<script>
jQuery(function($) {

    $( document ).ready( function() {

        // 폰트 리사이즈 쿠키있으면 실행 Font Resize by web Cookie
        font_resize("container", get_cookie("ck_font_resize_rmv_class"), get_cookie("ck_font_resize_add_class"));

        //상단으로 Go to Top
        $("#top_btn").on("click", function() {
            $("html, body").animate({scrollTop:0}, '500');
            return false;
        });

    });
});
</script>

<?php
include_once(GML_THEME_PATH."/tail.sub.php");
?>
