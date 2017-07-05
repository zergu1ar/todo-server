<?php

namespace Zergular\Todo;

use Predis\ClientInterface;

/**
 * Class Redis
 * @package Zergular\Todo
 */
class Redis implements CacheInterface
{
    /** @var ClientInterface */
    private $storage;
    /** @var int */
    private $ttl;

    /**
     * Cache constructor.
     * @param ClientInterface $redis
     * @param int $ttl
     */
    public function __construct(ClientInterface $redis, $ttl = 86400)
    {
        $this->storage = $redis;
        $this->ttl = $ttl;
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value)
    {
        return $this->storage->setex($key, $this->ttl, $value);
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        return $this->storage->get($key);
    }

    /**
     * @inheritdoc
     */
    public function clean($key)
    {
        return $this->storage->del([$key]);
    }
}
