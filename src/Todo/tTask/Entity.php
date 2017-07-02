<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 02.07.17
 * Time: 18:27
 */

namespace Todo\tTask;

use Zergular\Common\AbstractEntity;

class Entity extends AbstractEntity
{
    /** @var string */
    protected $name;
    /** @var int */
    protected $listId;

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
    public function getListId()
    {
        return $this->listId;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setListId($id)
    {
        $this->listId = $id;
        return $this;
    }
}