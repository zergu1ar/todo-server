<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 02.07.17
 * Time: 18:27
 */

namespace Todo\Task;

use Zergular\Common\AbstractEntity;

class Entity extends AbstractEntity
{
    /** @var string */
    protected $name;
    /** @var int */
    protected $ownerId;
    /** @var int */
    protected $completed;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * @param int $ownerId
     * @return $this
     */
    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;
        return $this;
    }

    /**
     * @param int $state
     * @return $this
     */
    public function setCompleted($state)
    {
        $this->completed = $state;
        return $this;
    }

    /**
     * @return int
     */
    public function getCompleted()
    {
        return $this->completed;
    }

}