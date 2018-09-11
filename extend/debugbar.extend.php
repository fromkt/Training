<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

put_event('tail_sub', 'show_debug_bar');

function show_debug_bar() {

    global $gml, $gml_debug, $l10n, $is_admin;
    
    if( ! get_permission_debug_show() ) return;

    if ( !($is_admin === 'super' && !is_mobile() ) ){
        return;
    }

    $memory_usage = function_exists( 'memory_get_peak_usage' ) ? memory_get_peak_usage() : memory_get_usage();
    $php_run_time = get_microtime()-$gml_debug['begin_time'];

    include_once( GML_PLUGIN_PATH.'/debugbar/debugbar.php' );
}
?>