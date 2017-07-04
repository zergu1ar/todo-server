<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 02.07.17
 * Time: 18:35
 */

namespace Todo\Link;

use Zergular\Common\AbstractEntity;

class Entity extends AbstractEntity
{
    /** @var int */
    protected $ownerId;
    /** @var int */
    protected $userId;
    /** @var int */
    protected $permission;

    /**
     * @param int $id
     * @return $this
     */
    public function setOwnerId($id)
    {
        $this->ownerId = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getOwnerId()
    {
        return $this->ownerId;
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