<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 02.07.17
 * Time: 14:09
 */

namespace Todo;

use Todo\tList\Manager as listManager;
use Todo\tTask\Manager as taskManager;
use Todo\tLink\Manager as linkManager;
use Zergular\Common\AbstractEntity;

class ItemBuilder
{
    /** @var string */
    private $path;
    /** @var ICache */
    private $cache;
    /** @var listManager */
    private $listManager;
    /** @var taskManager */
    private $taskManager;
    /** @var linkManager */
    private $linkManager;

    /**
     * ItemBuilder constructor.
     * @param listManager $listManager
     * @param taskManager $taskManager
     * @param linkManager $linkManager
     * @param ICache $cache
     * @param string $path
     */
    public function __construct(
        listManager $listManager,
        taskManager $taskManager,
        linkManager $linkManager,
        ICache $cache,
        $path = 'itemCache'
    )
    {
        $this->listManager = $listManager;
        $this->taskManager = $taskManager;
        $this->linkManager = $linkManager;
        $this->cache = $cache;
        $this->path = $path;
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
            //return json_decode($slot);
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
        $list = $this->listManager->getById($itemId);
        if (!$list) {
            return NULL;
        }
        $item = $list->toArray([]);
        $item['owner'] = NULL; // TODO get call to auth service
        $item['tasks'] = $this->processRows($this->taskManager->getAll(['listId' => $itemId]));
        $item['links'] = $this->processRows($this->linkManager->getAll(['listId' => $itemId]));

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
     * @return string
     */
    private function getKeyName($itemId)
    {
        return $this->path . ':' . $itemId;
    }

    /**
     * @param AbstractEntity[] $records
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