<?php

namespace Zergular\Todo;

/**
 * Interface UserApiInterface
 * @package Zergular\Todo
 */
interface UserApiInterface
{
    /**
     * @return string
     */
    public function getApiUrl();

    /**
     * @param string $name
     * @param int $userId
     * @param string $token
     *
     * @return int
     */
    public function getIdByName($name, $userId, $token);

    /**
     * @param int $id
     *
     * @return string
     */
    public function getNameById($id);

    /**
     * @param int $userId
     * @param string $token
     *
     * @return bool
     */
    public function checkAuth($userId, $token);
}
