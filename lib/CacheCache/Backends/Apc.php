<?php
/*
 * This file is part of the CacheCache package.
 *
 * (c) 2012 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CacheCache\Backends;

/**
 * Apc
 */
class Apc extends AbstractBackend
{
    public function exists($id)
    {
        return apc_exists($id);
    }

    public function get($id)
    {
        if (($value = apc_fetch($id)) === false) {
            return null;
        }
        return $value;
    }

    public function add($id, $value, $ttl = null)
    {
        return apc_add($id, $value, $ttl ?: 0);
    }

    public function set($id, $value, $ttl = null)
    {
        return apc_store($id, $value, $ttl ?: 0);
    }

    public function delete($id)
    {
        return apc_delete($id);
    }

    public function delete_by_prefix($prefix)
    {
        $aCacheInfo = apc_cache_info('user');

        $delete_results = array();

        foreach($aCacheInfo['cache_list'] as $_aCacheInfo) {
            
            if( ! isset( $_aCacheInfo['info'] ) ) continue;

            $key = $_aCacheInfo['info'];

            $clearFlag = false;
            // Check key has prefix or not
            $clearFlag = $clearFlag || preg_match('/^' . preg_quote($prefix, '/') . '/', $key);
            // Clear cache
            if ($clearFlag) {
                $delete_results[] = $this->delete($key);
            }
        }

        return $delete_results;
    }


    public function flushAll()
    {
        return apc_clear_cache('user');
    }
}
