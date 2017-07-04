<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 02.07.17
 * Time: 18:31
 */

namespace Todo\Task;

use Zergular\Common\AbstractManager;

class Manager extends AbstractManager
{
    protected $tableName = 'todoTask';
    protected $entityName = '\\Todo\\Task\\Entity';

    /**
     * @param int $userId
     * @return int[]
     */
    public function getOwnIds($userId)
    {
        return $this->persister->select($this->tableName, 'id', ['ownerId' => $userId]);
    }
}