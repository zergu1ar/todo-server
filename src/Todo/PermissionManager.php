<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 04.07.17
 * Time: 9:05
 */

namespace Todo;

use Todo\Task\Entity as Task;
use Todo\Link\Entity as Link;

class PermissionManager
{
    /** @var DataProvider */
    private $provider;

    public function __construct(DataProvider $provider)
    {
        $this->provider = $provider;
    }

    public function checkRead($userId, $id)
    {
        /** @var Task $item */
        $item = $this->provider->getTask($id);
        if ($item->getOwnerId() == $userId) {
            return TRUE;
        }
        /** @var Link */
        $link = $this->provider->getLink($item->getOwnerId(), $userId);
        if ($link) {
            return TRUE;
        }
        return FALSE;
    }

    public function checkWrite($userId, $id)
    {
        /** @var Task $item */
        $item = $this->provider->getTask($id);
        if ($item && $item->getOwnerId() != $userId) {
            /** @var Link $link */
            $link = $this->provider->getLink($item->getOwnerId(), $userId);
            if (!$link || $link->getPermission() == 0) {
                return FALSE;
            }
        }
        return TRUE;
    }
}