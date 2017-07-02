<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 02.07.17
 * Time: 14:10
 */

namespace Todo;

interface ICache
{
    public function get($key);

    public function set($key, $value);

    public function clean($key);
}