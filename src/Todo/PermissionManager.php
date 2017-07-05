<?php

namespace Zergular\Todo;

use Zergular\Todo\Link\LinkInterface;
use Zergular\Todo\Task\TaskInterface;

/**
 * Class PermissionManager
 * @package Zergular\Todo
 */
class PermissionManager implements PermissionManagerInterface
{
    /** @var DataProviderInterface */
    private $provider;

    /**
     * PermissionManager constructor.
     * @param DataProviderInterface $provider
     */
    public function __construct(DataProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @inheritdoc
     */
    public function checkRead($userId, $id)
    {
        /** @var TaskInterface $item */
        $item = $this->provider->getTask($id);
        if ($item->getOwnerId() == $userId) {
            return TRUE;
        }
        return $this->provider->getLink($item->getOwnerId(), $userId) instanceof LinkInterface;
    }

    /**
     * @inheritdoc
     */
    public function checkWrite($userId, $id)
    {
        /** @var TaskInterface $item */
        $item = $this->provider->getTask($id);
        if ($item && $item->getOwnerId() != $userId) {
            /** @var LinkInterface $link */
            $link = $this->provider->getLink($item->getOwnerId(), $userId);
            if (!($link instanceof LinkInterface) || $link->getPermission() == 0) {
                return FALSE;
            }
        }
        return TRUE;
    }
}
