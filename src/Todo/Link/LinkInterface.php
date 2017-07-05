<?php

namespace Zergular\Todo\Link;

use Zergular\Common\EntityInterface;

/**
 * Interface LinkInterface
 * @package Zergular\Todo\Link
 */
interface LinkInterface extends EntityInterface
{
    /**
     * @param int $id
     *
     * @return LinkInterface
     */
    public function setOwnerId($id);

    /**
     * @return int
     */
    public function getOwnerId();

    /**
     * @param int $id
     *
     * @return LinkInterface
     */
    public function setUserId($id);

    /**
     * @return int
     */
    public function getUserId();

    /**
     * @param int $level
     * @return LinkInterface
     */
    public function setPermission($level);

    /**
     * @return int
     */
    public function getPermission();
}
