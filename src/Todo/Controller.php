<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 02.07.17
 * Time: 12:51
 */

namespace Todo;

use Todo\Link\Entity;

class Controller implements IController
{
    /** @var ItemBuilder */
    private $builder;
    /** @var DataProvider */
    private $provider;
    /** @var PermissionManager */
    private $permission;
    /** @var UserApi */
    private $userApi;

    /**
     * Controller constructor.
     * @param ItemBuilder $builder
     * @param DataProvider $finder
     */
    public function __construct(ItemBuilder $builder, DataProvider $finder, UserApi $api, PermissionManager $perm)
    {
        $this->builder = $builder;
        $this->provider = $finder;
        $this->permission = $perm;
        $this->userApi = $api;
    }

    /**
     * @return array
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
     * @param int $userId
     *
     * @return array
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
     * @param int $id
     *
     * @return array
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
     * @param array $params
     *
     * @return array
     */
    public function saveTask($params)
    {
        if (!$this->permission->checkWrite($params['userId'], $params['id'])) {
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
     * @param array $params
     *
     * @return array
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
     * @param array $params
     *
     * @return array
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
     * @param array $params
     * @param int $userId
     *
     * @return array
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
     * @param array $params
     * @param int $userId
     *
     * @return array
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
     * @param array $params
     *
     * @return array
     */
    public function getShareList($params)
    {
        /** @var Entity[] $list */
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