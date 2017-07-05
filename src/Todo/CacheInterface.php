<?php

namespace Zergular\Todo;

/**
 * Interface CacheInterface
 * @package Zergular\Todo
 */
interface CacheInterface
{
    /**
     * @param string $key
     *
     * @return string
     */
    public function get($key);

    /**
     * @param string $key
     * @param string $value
     *
     * @return int
     */
    public function set($key, $value);

    /**
     * @param string $key
     *
     * @return int
     */
    public function clean($key);
}
