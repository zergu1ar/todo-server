<?php

namespace Zergular\Todo;

/**
 * Interface ControllerInterface
 * @package Zergular\Todo
 */
interface ControllerInterface
{
    /**
     * @return array
     */
    public function getConfigs();

    /**
     * @param int $userId
     *
     * @return array
     */
    public function getList($userId);

    /**
     * @param int $id
     *
     * @return array
     */
    public function getTask($id);

    /**
     * @param array $params
     *
     * @return array
     */
    public function saveTask($params);

    /**
     * @param array $params
     *
     * @return array
     */
    public function removeTask($params);

    /**
     * @param array $params
     *
     * @return array
     */
    public function setCompleted($params);

    /**
     * @param array $params
     * @param int $userId
     *
     * @return array
     */
    public function saveShare($params, $userId);

    /**
     * @param array $params
     * @param int $userId
     *
     * @return array
     */
    public function removeShare($params, $userId);

    /**
     * @param array $params
     *
     * @return array
     */
    public function getShareList($params);
}
