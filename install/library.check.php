<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(!extension_loaded('gd') || !function_exists('gd_info')) {
    echo '<script>'.PHP_EOL;
    echo 'alert("'.sprintf(__('GD library is required for normal use of %s.'), GML_VERSION).'\n'.__('Without the GD library, Captcha and thumbnail functionality will not work.').'");'.PHP_EOL;
    echo '</script>'.PHP_EOL;
}
?>