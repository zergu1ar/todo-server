<?php

namespace Zergular\Todo\Link;

use Zergular\Common\ManagerInterface;

/**
 * Interface LinkManagerInterface
 * @package Zergular\Todo\Link
 */
interface LinkManagerInterface extends ManagerInterface
{
    /**
     * @param int $userId
     *
     * @return array
     */
    public function getSharedIds($userId);
}
