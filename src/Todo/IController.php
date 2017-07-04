<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 02.07.17
 * Time: 12:52
 */

namespace Todo;

interface IController {

    public function getConfigs();

    public function getList($userId);

    public function getTask($id);

    public function saveTask($params);

    public function removeTask($params);

    public function setCompleted($params);

    public function saveShare($params, $userId);

    public function removeShare($params, $userId);

    public function getShareList($params);

}