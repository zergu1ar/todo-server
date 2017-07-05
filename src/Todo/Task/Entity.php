<?php

namespace Zergular\Todo\Task;

use Zergular\Common\AbstractEntity;

/**
 * Class Entity
 * @package Zergular\Todo\Task
 */
class Entity extends AbstractEntity implements TaskInterface
{
    /** @var string */
    protected $name;
    /** @var int */
    protected $ownerId;
    /** @var int */
    protected $completed;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;
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
    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setCompleted($state)
    {
        $this->completed = $state;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCompleted()
    {
        return $this->completed;
    }
}
