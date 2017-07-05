<?php

namespace Zergular\Todo;

/**
 * Interface ItemBuilderInterface
 * @package Zergular\Todo
 */
interface ItemBuilderInterface
{
    /**
     * @param int $itemId
     *
     * @return array|null
     */
    public function get($itemId);

    /**
     * @param int $itemId
     */
    public function clean($itemId);
}
