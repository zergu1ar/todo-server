<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 02.07.17
 * Time: 21:53
 */

namespace Todo;

use Zergular\Common\AbstractEntity;
use Todo\Link\Entity as Link;
use Todo\Task\Entity as Task;
use Todo\Task\Manager as taskManager;
use Todo\Link\Manager as linkManager;

class DataProvider
{
    /** @var linkManager */
    private $linkManager;
    /** @var taskManager */
    private $taskManager;

    /**
     * DataProvider constructor.
     * @param taskManager $taskManager
     * @param linkManager $linkManager
     */
    public function __construct(taskManager $taskManager, linkManager $linkManager)
    {
        $this->linkManager = $linkManager;
        $this->taskManager = $taskManager;
    }

    /**
     * @param int|int[] $userId
     *
     * @return int[]
     */
    public function getOwnItemIds($userId)
    {
        return $this->taskManager->getOwnIds($userId);
    }

    /**
     * @param int $userId
     *
     * @return int[]
     */
    public function getSharedItemsIds($userId)
    {
        $userIds = $this->linkManager->getSharedIds($userId);
        return $this->getOwnItemIds($userIds);
    }

    /**
     * @param array $params
     *
     * @return int|null
     */
    public function saveTask($params)
    {
        $task = new Task;
        $task->setName($params['name'])
            ->setOwnerId($params['userId'])
            ->setCompleted(isset($params['completed']) ? intval($params['completed']) : 0)
            ->setId(isset($params['id']) ? intval($params['id']) : NULL);
        $task = $this->taskManager->save($task);
        return $task ? $task->getId() : NULL;
    }

    /**
     * @param int $id
     *
     * @return bool|\PDOStatement
     */
    public function removeTask($id)
    {
        return $this->taskManager->delete(['id' => $id]);
    }

    /**
     * @param int $id
     * @param int $state
     *
     * @return AbstractEntity
     */
    public function setCompleted($id, $state)
    {
        /** @var Task $exists */
        $exists = $this->taskManager->getById($id);
        if ($exists) {
            $exists->setCompleted($state);
            return $this->taskManager->save($exists);
        }
        return NULL;
    }

    /**
     * @param int $ownerId
     * @param int $userId
     *
     * @return AbstractEntity
     */
    public function getLink($ownerId, $userId)
    {
        return $this->linkManager->getOne(['ownerId' => $ownerId, 'userId' => $userId]);
    }

    /**
     * @param int $ownerId
     * @param int $userId
     * @param int $perm
     *
     * @return AbstractEntity
     */
    public function saveShare($ownerId, $userId, $perm)
    {
        /** @var Link $exists */
        $exists = $this->getLink($ownerId, $userId);
        if ($exists) {
            $exists->setPermission($perm);
        } else {
            $exists = new Link;
            $exists->setOwnerId($ownerId)
                ->setUserId($userId)
                ->setPermission($perm);
        }
        return $this->linkManager->save($exists);
    }

    /**
     * @param int $ownerId
     * @param int $userId
     *
     * @return bool|\PDOStatement
     */
    public function removeShare($ownerId, $userId)
    {
        return $this->linkManager->delete(['ownerId' => $ownerId, 'userId' => $userId]);
    }

    /**
     * @param int $id
     *
     * @return AbstractEntity
     */
    public function getTask($id)
    {
        return $this->taskManager->getById($id);
    }

    /**
     * @param int $userId
     *
     * @return AbstractEntity[]
     */
    public function getShareList($userId)
    {
        return $this->linkManager->getAll(['ownerId' => $userId]);
    }
}