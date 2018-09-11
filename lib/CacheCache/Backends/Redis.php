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

use Predis,
    CacheCache\Backend;

/**
 * Redis
 */
class Redis implements Backend
{
    /** @var Predis\Client */
    protected $redis;

    /**
     * Constructor
     *
     * If $options contains a single key named redis with
     * a Predis\Client instance, it will be used.
     * Otherwise, creates a Predis\Client using the $options
     * array as the constructor argument.
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        if (isset($options['redis'])) {
            $this->redis = $options['redis'];
        } else {
            $this->redis = new Predis\Client($options);
        }
    }

    public function exists($id)
    {
        return $this->redis->exists($id);
    }

    public function get($id)
    {
        return $this->redis->get($id);
    }

    public function getMulti(array $ids)
    {
        $pipe = $this->redis->pipeline();
        array_map(array($pipe, 'get'), $ids);
        return $pipe->execute();
    }

    public function add($id, $value, $ttl = null)
    {
        try {
            $this->redis->setnx($id, $value);
            if ($ttl) {
                $this->redis->expire($id, $ttl);
            }
            return true;
        } catch (Predis\PredisException $e) {
            return false;
        }
    }

    public function set($id, $value, $ttl = null)
    {
        try {
            $this->redis->set($id, $value);
            if ($ttl) {
                $this->redis->expire($id, $ttl);
            }
            return true;
        } catch (Predis\PredisException $e) {
            return false;
        }
    }

    public function setMulti(array $items, $ttl = null)
    {
        $pipe = $this->redis->pipeline();
        foreach ($items as $id => $value) {
            $pipe->set($id, $value);
            if ($ttl) {
                $pipe->expire($id, $ttl);
            }
        }
        $pipe->execute();
        return true;
    }

    // https://stackoverflow.com/questions/32295488/how-to-delete-multiple-redis-keys-with-the-same-pattern-in-php-using-phpredis
    public function delete_by_prefix($key)
    {
        if ( $keys = $this->redis->keys($key) ){
            foreach ($keys as $k) {
                
                if( empty($k) ) continue;

                $this->redis->del($k);
            }
        }
    }

    public function delete($id)
    {
        return $this->redis->del($id);
    }

    public function flushAll()
    {
        $this->redis->flushdb();
    }

    public function supportsPipelines()
    {
        return true;
    }

    public function createPipeline()
    {
        return $this->redis->pipeline();
    }
}
