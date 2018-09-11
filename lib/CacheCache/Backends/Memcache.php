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
 * Memcache
 */
class Memcache extends AbstractBackend
{
    /** @var \Memcache */
    protected $memcache;

    /**
     * Constructor
     *
     * Possible options:
     *  - memcache: a \Memcache object
     *  - host
     *  - port
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        if (isset($options['memcache'])) {
            $this->memcache = $options['memcache'];
        } else {
            $host = isset($options['host']) ? $options['host'] : 'localhost';
            $port = isset($options['port']) ? $options['port'] : 11211;
            $this->memcache = new \Memcache();
            $this->memcache->addServer($host, $port);
        }
    }

    public function get($id)
    {
        if (($value = $this->memcache->get($id)) === false) {
            return null;
        }
        return $value;
    }

    public function add($id, $value, $ttl = null)
    {
        $ttl = $ttl ?: 0;
        if ($ttl > 0) {
            $ttl = time() + $ttl;
        }
        return $this->memcache->add($id, $value, 0, $ttl);
    }

    public function set($id, $value, $ttl = null)
    {
        $ttl = $ttl ?: 0;
        if ($ttl > 0) {
            $ttl = time() + $ttl;
        }
        return $this->memcache->set($id, $value, 0, $ttl);
    }

    public function delete($id)
    {
        return $this->memcache->delete($id);
    }
    
    // https://stackoverflow.com/questions/10792338/how-to-delete-items-with-same-prefix-key-in-memcached/10792366
    public function delete_by_prefix($prefix)
    {
        $slabs = $this->memcache->getExtendedStats('slabs');

        foreach ($slabs as $serverSlabs) {
            if ($serverSlabs) {
                foreach ($serverSlabs as $slabId => $slabMeta) {
                    if (is_int($slabId)) {
                        try {
                            $cacheDump = $this->memcache->getExtendedStats('cachedump', (int) $slabId, 1000);
                        } catch (Exception $e) {
                            continue;
                        }

                        if (is_array($cacheDump)) {
                            foreach ($cacheDump as $dump) {
                                if (is_array($dump)) {
                                    foreach ($dump as $key => $value) {

                                        $clearFlag = false;
                                        // Check key has prefix or not
                                        $clearFlag = $clearFlag || preg_match('/^' . preg_quote($prefix, '/') . '/', $key);
                                        // Clear cache
                                        if ($clearFlag) {
                                            $this->delete($key);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

    }

    public function flushAll()
    {
        return $this->memcache->flush();
    }
}
