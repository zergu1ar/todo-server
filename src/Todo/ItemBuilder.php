<?php

namespace Zergular\Todo;

use Zergular\Todo\Task\TaskManagerInterface;
use Zergular\Todo\Link\LinkManagerInterface;
use Zergular\Common\AbstractEntity;

/**
 * Class ItemBuilder
 * @package Zergular\Todo
 */
class ItemBuilder implements ItemBuilderInterface
{
    /** @var string */
    private $path;
    /** @var CacheInterface */
    private $cache;
    /** @var TaskManagerInterface */
    private $taskManager;
    /** @var LinkManagerInterface */
    private $linkManager;
    /** @var UserApiInterface */
    private $userApi;

    /**
     * ItemBuilder constructor.
     * @param TaskManagerInterface $taskManager
     * @param LinkManagerInterface $linkManager
     * @param CacheInterface $cache
     * @param UserApiInterface $api
     * @param string $path
     */
    public function __construct(
        TaskManagerInterface $taskManager,
        LinkManagerInterface $linkManager,
        CacheInterface $cache,
        UserApiInterface $api,
        $path = 'itemCache'
    ) {
        $this->taskManager = $taskManager;
        $this->linkManager = $linkManager;
        $this->cache = $cache;
        $this->path = $path;
        $this->userApi = $api;
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
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