<?php
if (!defined('_GNUBOARD_')) exit;

define('GML_HOOK_DEFAULT_PRIORITY', 8);

include_once(dirname(__FILE__) .'/Hook/hook.class.php');
include_once(dirname(__FILE__) .'/Hook/hook.extends.class.php');

function get_hook_class(){

    if( class_exists('GML_Hook') ){
        return GML_Hook::getInstance();
    }

    return null;
}

function put_event($tag, $func, $priority=GML_HOOK_DEFAULT_PRIORITY, $args=0){
    global $gml;

    if( $hook = get_hook_class() ){
        $hook::addAction($tag, $func, $priority, $args);
    }
}

function start_event($tag, $arg = ''){
    global $gml;

    if( $hook = get_hook_class() ){

        $args = array();

        if (
            is_array($arg)
            &&
            isset($arg[0])
            &&
            is_object($arg[0])
            &&
            1 == count($arg)
        ) {
          $args[] =& $arg[0];
        } else {
          $args[] = $arg;
        }

        $numArgs = func_num_args();

        for ($a = 2; $a < $numArgs; $a++) {
          $args[] = func_get_arg($a);
        }

        $hook::doAction($tag, $args, false);
    }
}

function put_replace($tag, $func, $priority=GML_HOOK_DEFAULT_PRIORITY, $args=0){
    global $gml;

    if( $hook = get_hook_class() ){
        return $hook::addFilter($tag, $func, $priority, $args);
    }

    return null;
}

function apply_replace($tag, $arg = ''){
    global $gml;

    if( $hook = get_hook_class() ){

        $args = array();

        if (
            is_array($arg)
            &&
            isset($arg[0])
            &&
            is_object($arg[0])
            &&
            1 == count($arg)
        ) {
          $args[] =& $arg[0];
        } else {
          $args[] = $arg;
        }

        $numArgs = func_num_args();

        for ($a = 2; $a < $numArgs; $a++) {
          $args[] = func_get_arg($a);
        }

        return $hook::apply_filters($tag, $args, false);
    }

    return null;
}

function delete_event($tag, $func, $priority=GML_HOOK_DEFAULT_PRIORITY){

    if( $hook = get_hook_class() ){
        return $hook::remove_action($tag, $func, $priority);
    }

    return null;
}

function delete_replace($tag, $func, $priority=GML_HOOK_DEFAULT_PRIORITY){

    if( $hook = get_hook_class() ){
        return $hook::remove_filter($tag, $func, $priority);
    }

    return null;
}

function get_hook_datas($type='', $is_callback=''){
    if( $hook = get_hook_class() ){
        return $hook::get_properties($type, $is_callback);
    }

    return null;
}
?>