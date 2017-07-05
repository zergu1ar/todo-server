<?php

namespace Zergular\Todo\Link;

use Zergular\Common\AbstractManager;

/**
 * Class Manager
 * @package Zergular\Todo\Link
 */
class Manager extends AbstractManager implements LinkManagerInterface
{
    /** @var string */
    protected $tableName = 'todoLink';
    /** @var string */
    protected $entityName = '\\Zergular\\Todo\\Link\\Entity';

    /**
     * @inheritdoc
     */
    public function getSharedIds($userId)
    {
        return $this->persister->select($this->tableName, 'ownerId', ['userId' => $userId]);
    }
}
