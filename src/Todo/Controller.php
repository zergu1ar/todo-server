<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 02.07.17
 * Time: 12:51
 */

// TODO need implement try {} catch blocks for dataGrep
// TODO need implement method to throw data instead of duplicate json_encode
namespace Todo;

class Controller implements IController
{
    /** @var ItemBuilder */
    private $builder;
    /** @var DataProvider */
    private $provider;

    /**
     * Controller constructor.
     * @param ItemBuilder $builder
     * @param DataProvider $finder
     */
    public function __construct(ItemBuilder $builder, DataProvider $finder)
    {
        $this->builder = $builder;
        $this->provider = $finder;
    }

    /**
     * @param int $userId
     * @return string
     */
    public function getLists($userId)
    {
        $ownIds = $this->provider->getOwnItemIds($userId);
        $sharedIds = $this->provider->getSharedItemsIds($userId);
        return json_encode(
            [
                'status' => 'success',
                'data' => [
                    'Self' => $this->getBuildLists($ownIds),
                    'Shared' => $this->getBuildLists($sharedIds)
                ]
            ]
        );
    }

    /**
     * @param int[] $ids
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
     * @return string
     */
    public function getList($id)
    {
        return json_encode(
            [
                'status' => 'success',
                'data' => [
                    $this->builder->get($id)
                ]
            ]
        );
    }

    /**
     * @param array $params
     * @return string
     */
    public function saveList($params)
    {
        // TODO implement PermissionManager::isWritable
        $name = trim(empty($params['name']) ? '' : $params['name']);
        if (empty($name)) {
            return json_encode(
                [
                    'status' => 'error',
                    'data' => 'Name should be filled'
                ]
            );
        }
        $id = $this->provider->saveList($params);
        if ($id) {
            return $this->getList($id);
        }
        return json_encode(
            [
                'status' => 'error',
                'data' => 'Something went wrong'
            ]
        );
    }

    public function saveTask($params)
    {
        // TODO implements PermissionManager::isWritable
        $name = trim(empty($params['name']) ? '' : $params['name']);
        if (empty($name)) {
            return json_encode(
                [
                    'status' => 'error',
                    'data' => 'Name should be filled'
                ]
            );
        }
        if ((int)$params['listId'] == 0) {
            return json_encode(
                [
                    'status' => 'error',
                    'data' => 'List not found'
                ]
            );
        }
        $id = $this->provider->saveTask($params);
        if ($id) {
            return $this->getList($id);
        }
        return json_encode(
            [
                'status' => 'error',
                'data' => 'Something went wrong'
            ]
        );
    }

}