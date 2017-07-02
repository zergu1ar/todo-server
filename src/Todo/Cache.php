<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 02.07.17
 * Time: 20:46
 */

namespace Todo;

use Predis\Client;

class Cache implements ICache
{
    /** @var Client */
    private $storage;
    /** @var int */
    private $ttl;

    /**
     * Cache constructor.
     * @param Client $redis
     * @param int $ttl
     */
    public function __construct(Client $redis, $ttl = 86400)
    {
        $this->storage = $redis;
        $this->ttl = $ttl;
    }

    /**
     * @param string $key
     * @param string $value
     * @return int
     */
    public function set($key, $value)
    {
        return $this->storage->setex($key, $this->ttl, $value);
    }

    /**
     * @param string $key
     * @return string
     */
    public function get($key)
    {
        return $this->storage->get($key);
    }

    /**
     * @param string $key
     * @return int
     */
    public function clean($key)
    {
        return $this->storage->del([$key]);
    }

}