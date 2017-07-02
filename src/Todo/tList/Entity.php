<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 02.07.17
 * Time: 18:27
 */

namespace Todo\tList;

use Zergular\Common\AbstractEntity;

class Entity extends AbstractEntity
{
    /** @var string */
    protected $name;
    /** @var int */
    protected $ownerId;

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
     * @param int $id
     * @return $this
     */
    public function setOwnerId($id)
    {
        $this->ownerId = $id;
        return $this;
    }

}