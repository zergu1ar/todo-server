<?php

namespace Zergular\Todo;

/**
 * Interface PermissionManagerInterface
 * @package Zergular\Todo
 */
interface PermissionManagerInterface
{
    /**
     * @param int $userId
     * @param int $id
     *
     * @return bool
     */
    public function checkRead($userId, $id);

    /**
     * @param int $userId
     * @param int $id
     * @return bool
     */
    public function checkWrite($userId, $id);
}
