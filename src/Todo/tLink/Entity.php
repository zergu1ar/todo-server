<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 02.07.17
 * Time: 18:35
 */

namespace Todo\tLink;

use Zergular\Common\AbstractEntity;

class Entity extends AbstractEntity
{
    /** @var int */
    protected $listId;
    /** @var int */
    protected $userId;
    /** @var int */
    protected $permission;

    /**
     * @param int $id
     * @return $this
     */
    public function setListId($id)
    {
        $this->listId = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getListId()
    {
        return $this->listId;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setUserId($id)
    {
        $this->userId = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $level
     * @return $this
     */
    public function setPermission($level)
    {
        $this->permission = $level;
        return $this;
    }

    /**
     * @return int
     */
    public function getPermission()
    {
        return $this->permission;
    }
}