<?php

namespace Zergular\Todo;

use Zergular\Todo\Task\Entity as Task;
use Zergular\Todo\Link\Entity as Link;
use Zergular\Todo\Link\LinkInterface;
use Zergular\Todo\Link\LinkManagerInterface;
use Zergular\Todo\Task\TaskInterface;
use Zergular\Todo\Task\TaskManagerInterface;

/**
 * Class DataProvider
 * @package Zergular\Todo
 */
class DataProvider implements DataProviderInterface
{
    /** @var LinkManagerInterface */
    private $linkManager;
    /** @var TaskManagerInterface */
    private $taskManager;

    /**
     * DataProvider constructor.
     * @param TaskManagerInterface $taskManager
     * @param LinkManagerInterface $linkManager
     */
    public function __construct(TaskManagerInterface $taskManager, LinkManagerInterface $linkManager)
    {
        $this->linkManager = $linkManager;
        $this->taskManager = $taskManager;
    }

    /**
     * @inheritdoc
     */
    public function getOwnItemIds($userId)
    {
        return $this->taskManager->getOwnIds($userId);
    }

    /**
     * @inheritdoc
     */
    public function getSharedItemsIds($userId)
    {
        $userIds = $this->linkManager->getSharedIds($userId);
        return $this->getOwnItemIds($userIds);
    }

    /**
     * @inheritdoc
     */
    public function saveTask($params)
    {
        $task = new Task;
        $task->setName($params['name'])
            ->setOwnerId($params['userId'])
            ->setCompleted(isset($params['completed'])
                ? intval($params['completed'])
                : 0)
            ->setId(isset($params['id'])
                ? intval($params['id'])
                : NULL);
        $task = $this->taskManager->save($task);
        return $task
            ? $task->getId()
            : NULL;
    }

    /**
     * @inheritdoc
     */
    public function removeTask($id)
    {
        return $this->taskManager->delete(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public function setCompleted($id, $state)
    {
        /** @var TaskInterface $exists */
        $exists = $this->taskManager->getById($id);
        if ($exists) {
            $exists->setCompleted($state);
            return $this->taskManager->save($exists);
        }
        return NULL;
    }

    /**
     * @inheritdoc
     */
    public function getLink($ownerId, $userId)
    {
        return $this->linkManager->getOne(['ownerId' => $ownerId, 'userId' => $userId]);
    }

    /**
     * @inheritdoc
     */
    public function saveShare($ownerId, $userId, $perm)
    {
        /** @var LinkInterface $exists */
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
     * @inheritdoc
     */
    public function removeShare($ownerId, $userId)
    {
        return $this->linkManager->delete(['ownerId' => $ownerId, 'userId' => $userId]);
    }

    /**
     * @inheritdoc
     */
    public function getTask($id)
    {
        return $this->taskManager->getById($id);
    }

    /**
     * @inheritdoc
     */
    public function getShareList($userId)
    {
        return $this->linkManager->getAll(['ownerId' => $userId]);
    }
}
