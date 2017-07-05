<?php

namespace Zergular\Todo\Task;

use Zergular\Common\ManagerInterface;

/**
 * Interface TaskManagerInterface
 * @package Zergular\Todo\Task
 */
interface TaskManagerInterface extends ManagerInterface
{
    /**
     * @param int $userId
     *
     * @return int[]
     */
    public function getOwnIds($userId);
}
