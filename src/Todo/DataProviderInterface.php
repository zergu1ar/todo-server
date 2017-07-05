<?php

namespace Zergular\Todo;

use Zergular\Todo\Link\LinkInterface;
use Zergular\Todo\Task\TaskInterface;

/**
 * Interface DataProviderInterface
 * @package Zergular\Todo
 */
interface DataProviderInterface
{
    /**
     * @param int|int[] $userId
     *
     * @return int[]
     */
    public function getOwnItemIds($userId);

    /**
     * @param int $userId
     *
     * @return int[]
     */
    public function getSharedItemsIds($userId);

    /**
     * @param array $params
     *
     * @return int|null
     */
    public function saveTask($params);

    /**
     * @param int $id
     *
     * @return bool|\Traversable
     */
    public function removeTask($id);

    /**
     * @param int $id
     * @param int $state
     *
     * @return TaskInterface
     */
    public function setCompleted($id, $state);

    /**
     * @param int $ownerId
     * @param int $userId
     *
     * @return LinkInterface
     */
    public function getLink($ownerId, $userId);

    /**
     * @param int $ownerId
     * @param int $userId
     * @param int $perm
     *
     * @return LinkInterface
     */
    public function saveShare($ownerId, $userId, $perm);

    /**
     * @param int $ownerId
     * @param int $userId
     *
     * @return bool|\Traversable
     */
    public function removeShare($ownerId, $userId);

    /**
     * @param int $id
     *
     * @return TaskInterface
     */
    public function getTask($id);

    /**
     * @param int $userId
     *
     * @return LinkInterface[]
     */
    public function getShareList($userId);
}
