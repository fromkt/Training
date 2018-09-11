<?php
if (!defined('_GNUBOARD_')) exit;

function cache_manage_setup($options=array()){

    if( ! (defined('GML_USE_CACHE') && GML_USE_CACHE) ) return;

    spl_autoload_register(function($className) {
        if (substr($className, 0, 10) === 'CacheCache') {
            $filename = str_replace('\\', DIRECTORY_SEPARATOR, trim($className, '\\')) . '.php';

            require_once $filename;
        }
    });

    // read http://maximebf.github.io/CacheCache/

    $logger = apply_replace('cache_manage_setup_logger', null);

    $gml_cache_type = isset($options['gml_cache_type']) ? $options['gml_cache_type'] : strtolower(GML_CACHE_TYPE);

    switch ($gml_cache_type) {
        case 'apc' :
            CacheCache\CacheManager::setup(new CacheCache\Backends\Apc(), $logger);
            break;
        case 'apcu' :
            CacheCache\CacheManager::setup(new CacheCache\Backends\Apcu(), $logger);
            break;
        case 'redis' :

            if( !isset($options['redis']) ){
                $args = array(
                    'scheme' => 'tcp',
                    'host'   => '127.0.0.1',
                    'port'   => '6379',
                    );
                $options = apply_replace('gml_predis_client_options', array_merge($args, $options), $args, $options, $gml_cache_type);

                require_once GML_LIB_PATH.'/Predis/Autoloader.php';

                Predis\Autoloader::register();
            }

            CacheCache\CacheManager::setup(new CacheCache\Backends\Redis($options), $logger);
            break;
        case 'memcache' :

            $options = apply_replace('gml_predis_client_options', $options, array(), $options, $gml_cache_type);

            CacheCache\CacheManager::setup(new CacheCache\Backends\Memcache($options), $logger);
            break;
        case 'memcached' :

            $options = apply_replace('gml_predis_client_options', $options, array(), $options, $gml_cache_type);

            CacheCache\CacheManager::setup(new CacheCache\Backends\Memcached($options), $logger);
            break;
        case 'memory' :
            CacheCache\CacheManager::setup(new CacheCache\Backends\Memory(), $logger);
            break;
        case 'session' :
            CacheCache\CacheManager::setup(new CacheCache\Backends\Session(), $logger);
            break;
        case 'file' :
        default :
            $args = array(
                'dir'=>GML_DATA_CACHE_PATH,
                'id_as_filename'=>1,
                'file_extension'=>'.php',
                );
            $options = apply_replace('gml_predis_client_options', array_merge($args, $options), $args, $options, $gml_cache_type);
            CacheCache\CacheManager::setup(new CacheCache\Backends\File($options), $logger);
    }
}

function get_cachemanage_instance(){

    static $cache = null;

    if( ! (defined('GML_USE_CACHE') && GML_USE_CACHE) ) return $cache;

    if( ! class_exists('CacheCache\CacheManager') ) return $cache;

    if( $cache === null ){
        $cache = CacheCache\CacheManager::get();
    } else {
        return $cache;
    }

    return $cache;
}

function gml_cache_secret_key(){
    static $str = '';

    if( $str ) return $str;

    $str = substr(md5(GML_ENCRYPT_ADD_STRING.$_SERVER['SERVER_SOFTWARE'].$_SERVER['DOCUMENT_ROOT']), 0, 6);

    return $str;
}

function gml_latest_cache_data($bo_table, $cache_list=array(), $find_wr_id=0){
    static $cache = array();

    if( $bo_table && $cache_list && ! isset($cache[$bo_table]) ){
        foreach( (array) $cache_list as $wr ){
            if( empty($wr) || ! isset($wr['wr_id']) ) continue;
            $cache[$bo_table][$wr['wr_id']] = $wr;
        }
    }
    
    if( $find_wr_id && isset($cache[$bo_table][$find_wr_id]) ){
        return $cache[$bo_table][$find_wr_id];
    }
}

function gml_add_cache($key, $save_data, $ttl = null){
    if( $cache = get_cachemanage_instance() ){

        if( strtolower(GML_CACHE_TYPE) === 'redis' ){
            $save_data = base64_encode(serialize($save_data));
        }

        $cache->add($key, $save_data, $ttl);
    }
}

function gml_set_cache($key, $save_data, $ttl = null){
    if( $cache = get_cachemanage_instance() ){

        if( strtolower(GML_CACHE_TYPE) === 'redis' ){
            $save_data = base64_encode(serialize($save_data));
        }

        $cache->set($key, $save_data, $ttl);
    }
}

function gml_get_cache($key){
    if( $cache = get_cachemanage_instance() ){

        if( strtolower(GML_CACHE_TYPE) === 'redis' ){
            
            $return_data = $cache->get($key);
            return $return_data ? unserialize(base64_decode( $return_data )) : null;
        }

        return $cache->get($key);
    }
    
    return false;
}

function gml_delete_cache($key){
    if( $cache = get_cachemanage_instance() ){
        return $cache->delete($key);
    }
    
    return false;
}

function gml_delete_all_cache(){

    global $gml;

    $board_tables = array();

    $sql = " select bo_table from {$gml['board_table']} ";
    $result = sql_query($sql);

    while ($row = sql_fetch_array($result)) {
        $board_tables[] = $row['bo_table'];

        delete_cache_latest($row['bo_table']);
    }

    start_event('adm_cache_delete', $board_tables);

}

function gml_delete_cache_by_prefix($key){
    if( $cache = get_cachemanage_instance() ){
        
        $gml_cache_type = strtolower(GML_CACHE_TYPE);

        if( $gml_cache_type === 'redis' ){

            return $cache->delete_by_prefix($key.'*');

        } else if ( $gml_cache_type === 'apc' && class_exists('APCUIterator') ){
            
            $return_value = '';

            foreach(new APCUIterator('/^'.preg_quote($key, '/').'/') as $apcu_cache){

                if( ! isset($apcu_cache['key']) || empty($apcu_cache['key']) ) continue;

                $return_value = $cache->delete($apcu_cache['key']);
            }

            return $return_value;

        } else if ( $gml_cache_type === 'file' ){
            $files = glob(GML_DATA_CACHE_PATH.'/'.$key.'*');

            foreach( (array) $files as $filename){
                if(empty($filename)) continue;

                unlink($filename);
            }
        } else if ( $gml_cache_type === 'memcache' || $gml_cache_type === 'memcached' || $gml_cache_type === 'apcu' || $gml_cache_type === 'session' ){
            return $cache->delete_by_prefix($key);
        }

        return $cache->delete('latest-'.$key.'-*');
    }
    
    return false;
}
?>