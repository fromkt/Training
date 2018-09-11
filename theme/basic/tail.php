<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

if (GML_IS_MOBILE) {
    include_once(GML_THEME_MOBILE_PATH.'/tail.php');
    return;
}
?>

		<?php
		// don't display header, footer, side menu on login/register and etc.
		if (!defined("_DONT_WRAP_IN_CONTAINER_")) {
		?>
		
	    <?php start_event('container_end'); ?>
	    </div>
	</div>
</div>
<!-- } end contents -->

<!-- footer start { -->
<div id="ft">

    <div id="ft_link">
        <div class="ft_inner">
            <a href="<?php echo get_pretty_url('content', 'company') ?>"><?php e__('About Company'); ?></a>
            <a href="<?php echo get_pretty_url('content', 'privacy') ?>"><?php e__('Privacy Policy'); ?></a>
            <a href="<?php echo get_pretty_url('content', 'provision') ?>"><?php e__('Terms of service'); ?></a>
            <a href="<?php echo get_device_change_url(); ?>"><?php e__('View Mobile'); ?></a>

            <div class="lang_var_select">
                <span class="sound_only"><?php e__('Select Site Language'); ?></span>
                <?php echo get_lang_select_html('theme_lang_bar', $lang, 'class="theme_select_lang"', true); ?>
            </div>
        </div>
    </div>

	<div class="ft_inner">
		<div id="ft_catch"><img src="<?php echo GML_IMG_URL; ?>/ft_logo.png" alt="<?php echo GML_VERSION ?>"></div>
		<div id="ft_copy">Copyright &copy; <b><?php e__('Your domain.'); ?></b> All rights reserved.</div>
    </div>
    <button id="top_btn"><i class="fa fa-arrow-up" aria-hidden="true"></i><span class="sound_only"><?php e__('Top'); ?></span></button>
</div>

<?php
    if(GML_DEVICE_BUTTON_DISPLAY && !GML_IS_MOBILE) {

    }
    if ($config['cf_analytics']) {
        echo $config['cf_analytics'];
    }
?>

<!-- } end footer -->
<?php
}
?>

<script>
jQuery(function($) {

    $("#top_btn").on("click", function() {
        $("html, body").animate({scrollTop:0}, '500');
        return false;
    });

    // Font Resize by web Cookie
    font_resize("container", get_cookie("ck_font_resize_rmv_class"), get_cookie("ck_font_resize_add_class"));
});
</script>

<?php include_once(GML_THEME_PATH."/tail.sub.php"); ?>
