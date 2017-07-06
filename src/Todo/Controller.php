<?php

namespace Zergular\Todo;

use Zergular\Todo\Link\LinkInterface;

/**
 * Class Controller
 * @package Zergular\Todo
 */
class Controller implements ControllerInterface
{
    /** @var ItemBuilderInterface */
    private $builder;
    /** @var DataProviderInterface */
    private $provider;
    /** @var PermissionManagerInterface */
    private $permission;
    /** @var UserApiInterface */
    private $userApi;

    /**
     * Controller constructor.
     * @param ItemBuilderInterface $builder
     * @param DataProviderInterface $finder
     * @param UserApiInterface $api
     * @param PermissionManagerInterface $perm
     */
    public function __construct(
        ItemBuilderInterface $builder,
        DataProviderInterface $finder,
        UserApiInterface $api,
        PermissionManagerInterface $perm
    ) {
        $this->builder = $builder;
        $this->provider = $finder;
        $this->permission = $perm;
        $this->userApi = $api;
    }

    /**
     * @inheritdoc
     */
    public function getConfigs()
    {
        return [
            'type' => 'config',
            'status' => 'success',
            'data' => [
                'userServiceUrl' => $this->userApi->getApiUrl()
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function getList($userId)
    {
        $ownIds = $this->provider->getOwnItemIds($userId);
        $sharedIds = $this->provider->getSharedItemsIds($userId);
        return [
            'type' => 'list',
            'status' => 'success',
            'data' => [
                'Self' => $this->getBuildLists($ownIds),
                'Shared' => $this->getBuildLists($sharedIds)
            ]
        ];
    }

    /**
     * @return array
     */
    private function unknownErrorResponse()
    {
        return [
            'status' => 'error',
            'data' => 'Unknown error'
        ];
    }

    /**
     * @param int[] $ids
     *
     * @return array
     */
    private function getBuildLists($ids)
    {
        $tasks = [];
        foreach ($ids as $id) {
            $tasks[] = $this->builder->get($id);
        }
        return $tasks;
    }

    /**
     * @inheritdoc
     */
    public function getTask($id)
    {
        return [
            'status' => 'success',
            'type' => 'task',
            'data' => $this->builder->get($id)
        ];
    }

    /**
     * @return array
     */
    private function notAllowed()
    {
        return [
            'status' => 'error',
            'data' => 'Not allowed action'
        ];
    }

    /**
     * @inheritdoc
     */
    public function saveTask($params)
    {
        $id = empty($params['id']) ? 0 : $params['id'];
        if (!$this->permission->checkWrite($params['userId'], $id)) {
            return $this->notAllowed();
        }
        $name = trim(empty($params['name']) ? '' : $params['name']);
        if (empty($name)) {
            return [
                'type' => 'saveTask',
                'status' => 'error',
                'data' => 'Name should be filled'
            ];
        }
        if(!empty($params['id'])) {
            $cache = $this->getTask($params['id']);
            $params['userId'] = $cache['data']['ownerId'];
        }
        $id = $this->provider->saveTask($params);
        if ($id) {
            $this->builder->clean($id);
            return $this->getTask($id);
        }
        return $this->unknownErrorResponse();
    }

    /**
     * @inheritdoc
     */
    public function removeTask($params)
    {
        $id = empty($params['id']) ? 0 : $params['id'];
        if (!$id || !$this->permission->checkWrite($params['userId'], $id)) {
            return $this->notAllowed();
        }
        if ($this->provider->removeTask($id)) {
            $this->builder->clean($id);
            return [
                'type' => 'removeTask',
                'status' => 'success',
                'data' => ['id' => $id]
            ];
        }
        return $this->unknownErrorResponse();
    }

    /**
     * @inheritdoc
     */
    public function setCompleted($params)
    {
        $id = empty($params['id']) ? 0 : $params['id'];
        if (!$id || !$this->permission->checkWrite($params['userId'], $id)) {
            return $this->notAllowed();
        }
        $state = empty($params['state']) ? 0 : 1;
        if ($this->provider->setCompleted($id, $state)) {
            $this->builder->clean($id);
            return $this->getTask($id);
        }
        return $this->unknownErrorResponse();
    }

    /**
     * @inheritdoc
     */
    public function saveShare($params, $userId)
    {
        $permission = empty($params['permission']) ? 0 : 1;
        if ($userId) {
            $this->provider->saveShare($params['userId'], $userId, $permission);
            $this->cleanCacheByUserId($params['userId']);
            return $this->getShareList($params);
        }
        return $this->unknownErrorResponse();
    }

    /**
     * @param int $userId
     */
    private function cleanCacheByUserId($userId)
    {
        $ids = $this->provider->getOwnItemIds($userId);
        foreach ($ids as $id) {
            $this->builder->clean($id);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeShare($params, $userId)
    {
        if ($userId) {
            $this->provider->removeShare($params['userId'], $userId);
            $this->cleanCacheByUserId($params['userId']);
            return [
                'type' => 'shareRemove',
                'status' => 'success',
                'data' => ['userId' => $userId]
            ];
        }
        return $this->unknownErrorResponse();
    }

    /**
     * @inheritdoc
     */
    public function getShareList($params)
    {
        /** @var LinkInterface[] $list */
        $list = $this->provider->getShareList($params['userId']);
        $result = [
            'type' => 'shareList',
            'status' => 'success',
            'data' => []
        ];
        foreach ($list as $share) {
            $share = $share->toArray();
            $share['username'] = $this->userApi->getNameById($share['userId']);
            $result['data'][] = $share;
        }
        return $result;
    }
}
