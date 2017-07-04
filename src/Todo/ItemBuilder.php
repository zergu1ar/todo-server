<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 02.07.17
 * Time: 14:09
 */

namespace Todo;

use Todo\Task\Manager as taskManager;
use Todo\Link\Manager as linkManager;
use Zergular\Common\AbstractEntity;

class ItemBuilder
{
    /** @var string */
    private $path;
    /** @var ICache */
    private $cache;
    /** @var taskManager */
    private $taskManager;
    /** @var linkManager */
    private $linkManager;
    /** @var UserApi */
    private $userApi;

    /**
     * ItemBuilder constructor.
     * @param taskManager $taskManager
     * @param linkManager $linkManager
     * @param ICache $cache
     * @param UserApi $api
     * @param string $path
     */
    public function __construct(
        taskManager $taskManager,
        linkManager $linkManager,
        ICache $cache,
        UserApi $api,
        $path = 'itemCache'
    )
    {
        $this->taskManager = $taskManager;
        $this->linkManager = $linkManager;
        $this->cache = $cache;
        $this->path = $path;
        $this->userApi = $api;
    }

    /**
     * @param int $itemId
     *
     * @return array|null
     */
    public function get($itemId)
    {
        $slot = $this->cache->get($this->getKeyName($itemId));
        if ($slot) {
            return json_decode($slot, TRUE);
        }
        $item = $this->build($itemId);
        return is_array($item)
            ? $item
            : NULL;
    }

    /**
     * @param int $itemId
     *
     * @return array|null
     */
    private function build($itemId)
    {
        $list = $this->taskManager->getById($itemId);
        if (!$list) {
            return NULL;
        }
        $item = $list->toArray([]);
        $item['owner'] = $this->userApi->getNameById($item['ownerId']);
        $item['share'] = $this->processRows($this->linkManager->getAll(['ownerId' => $item['ownerId']]));
        $this->cache->set($this->getKeyName($itemId), json_encode($item));
        return $item;
    }

    /**
     * @param int $itemId
     */
    public function clean($itemId)
    {
        $this->cache->clean($this->getKeyName($itemId));
    }

    /**
     * @param int $itemId
     *
     * @return string
     */
    private function getKeyName($itemId)
    {
        return $this->path . ':' . $itemId;
    }

    /**
     * @param AbstractEntity[] $records
     *
     * @return array
     */
    private function processRows($records = [])
    {
        $result = [];
        foreach ($records as $record) {
            $result[] = $record->toArray([]);
        }
        return $result;
    }

}