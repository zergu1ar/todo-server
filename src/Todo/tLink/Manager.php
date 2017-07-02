<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 02.07.17
 * Time: 19:27
 */

namespace Todo\tLink;

use Zergular\Common\AbstractManager;

class Manager extends AbstractManager
{
    /** @var string */
    protected $tableName = 'todoLink';
    /** @var string */
    protected $entityName = '\\Todo\\tLink\\Entity';

    /**
     * @param int $userId
     * @return array
     */
    public function getSharedIds($userId)
    {
        return $this->persister->select($this->tableName, 'listId', ['userId' => $userId]);
    }
}