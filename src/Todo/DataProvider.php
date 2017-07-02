<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 02.07.17
 * Time: 21:53
 */

namespace Todo;

use Todo\tList\Entity as listEntity;
use Todo\tList\Manager as listManager;
use Todo\tLink\Manager as linkManager;

class DataProvider
{
    private $linkManager;
    private $listManager;

    public function __construct(listManager $listManager, linkManager $linkManager)
    {
        $this->linkManager = $linkManager;
        $this->listManager = $listManager;
    }

    public function getOwnItemIds($userId)
    {
        return $this->listManager->getOwnIds($userId);
    }

    public function getSharedItemsIds($userId)
    {
        return $this->linkManager->getSharedIds($userId);
    }

    /**
     * @param array $params
     * @return int
     */
    public function saveList($params)
    {
        $newList = new listEntity;
        $newList->setName($params['name'])
            ->setOwnerId($params['userId'])
            ->setId(empty($params['id']) ? NULL : $params['id']);
        return $this->listManager->save($newList)->getId();
    }
}