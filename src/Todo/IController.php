<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 02.07.17
 * Time: 12:52
 */

namespace Todo;

interface IController {

    public function getLists($userId);

    public function getList($id);

    public function saveList($params);

    public function saveTask($params);

}