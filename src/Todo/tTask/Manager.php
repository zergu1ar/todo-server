<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 02.07.17
 * Time: 18:31
 */

namespace Todo\tTask;

use Zergular\Common\AbstractManager;

class Manager extends AbstractManager
{
    protected $tableName = 'todoTask';
    protected $entityName = '\\Todo\\tTask\\Entity';
}