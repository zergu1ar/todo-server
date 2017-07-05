<?php

namespace Zergular\Todo\Task;

use Zergular\Common\EntityInterface;

/**
 * Interface TaskInterface
 * @package Zergular\Todo\Task
 */
interface TaskInterface extends EntityInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return TaskInterface
     */
    public function setName($name);

    /**
     * @return int
     */
    public function getOwnerId();

    /**
     * @param int $ownerId
     * @return TaskInterface
     */
    public function setOwnerId($ownerId);

    /**
     * @param int $state
     *
     * @return TaskInterface
     */
    public function setCompleted($state);

    /**
     * @return int
     */
    public function getCompleted();
}
