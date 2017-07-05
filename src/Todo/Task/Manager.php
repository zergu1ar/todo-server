<?php

namespace Zergular\Todo\Task;

use Zergular\Common\AbstractManager;

/**
 * Class Manager
 * @package Zergular\Todo\Task
 */
class Manager extends AbstractManager implements TaskManagerInterface
{
    /** @var string */
    protected $tableName = 'todoTask';
    /** @var string */
    protected $entityName = '\\Zergular\\Todo\\Task\\Entity';

    /**
     * @inheritdoc
     */
    public function getOwnIds($userId)
    {
        return $this->persister->select($this->tableName, 'id', ['ownerId' => $userId]);
    }
}
