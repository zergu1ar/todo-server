<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 02.07.17
 * Time: 18:31
 */

namespace Todo\tList;

use Zergular\Common\AbstractManager;

class Manager extends AbstractManager
{
    /** @var string */
    protected $tableName = 'todoList';
    /** @var string */
    protected $entityName = '\\Todo\\tList\\Entity';

    /**
     * @param int $userId
     * @return array
     */
    public function getOwnIds($userId)
    {
        return $this->persister->select($this->tableName, 'id', ['ownerId' => $userId]);
    }
}