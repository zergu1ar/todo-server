<?php

namespace Zergular\Todo\Link;

use Zergular\Common\AbstractEntity;

/**
 * Class Entity
 * @package Zergular\Todo\Link
 */
class Entity extends AbstractEntity implements LinkInterface
{
    /** @var int */
    protected $ownerId;
    /** @var int */
    protected $userId;
    /** @var int */
    protected $permission;

    /**
     * @inheritdoc
     */
    public function setOwnerId($id)
    {
        $this->ownerId = $id;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOwnerId()
    {
        return $this->ownerId;
    }

    /**
     * @inheritdoc
     */
    public function setUserId($id)
    {
        $this->userId = $id;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @inheritdoc
     */
    public function setPermission($level)
    {
        $this->permission = $level;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPermission()
    {
        return $this->permission;
    }
}
