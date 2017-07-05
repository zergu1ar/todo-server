<?php

namespace Zergular\Todo;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

/**
 * Class Sockets
 * @package Zergular\Todo
 */
class Sockets implements MessageComponentInterface
{
    /** @var ControllerInterface */
    private $todo;
    /** @var \SplObjectStorage */
    private $clients;
    /** @var array */
    private $loggedUsers = [];
    /** @var UserApiInterface */
    private $userApi;

    const STATUS_SUCCESS = 'success';

    /**
     * Sockets constructor.
     * @param ControllerInterface $controller
     * @param UserApiInterface $api
     */
    public function __construct(ControllerInterface $controller, UserApiInterface $api)
    {
        $this->userApi = $api;
        $this->todo = $controller;
        $this->clients = new \SplObjectStorage;
    }

    /**
     * @inheritdoc
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $conn->send($this->getResponse($this->todo->getConfigs()));
    }

    /**
     * @inheritdoc
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $msg = json_decode($msg, TRUE);
        $id = $this->validUserConnection($msg, $from);
        if (!$id) {
            $from->send($this->getError('Auth error'));
        }
        if (isset($msg['action'])) {
            switch ($msg['action']):
                case 'getList':
                    $from->send($this->getResponse($this->todo->getList($id)));
                    break;
                case 'saveTask':
                    $task = $this->todo->saveTask($msg);
                    $this->onTaskChange($task, $from);
                    break;
                case 'setComplete':
                    $task = $this->todo->setCompleted($msg);
                    $this->onTaskChange($task, $from);
                    break;
                case 'removeTask':
                    $this->onTaskRemove($from, $msg);
                    break;
                case 'getShare':
                    $this->onGetShare($from, $msg);
                    break;
                case 'saveShare':
                    $userId = $this->getUserIdByName($msg);
                    $task = $this->todo->saveShare($msg, $userId);
                    $this->onChangeShare($task, $from, $msg, $userId);
                    break;
                case 'removeShare':
                    $userId = $this->getUserIdByName($msg);
                    $task = $this->todo->removeShare($msg, $userId);
                    $this->onChangeShare($task, $from, $msg, $userId);
                    break;
            endswitch;
        }
    }

    /**
     * @param mixed $data
     *
     * @return string
     */
    private function getResponse($data)
    {
        return json_encode($data);
    }

    /**
     * @param string $error
     *
     * @return string
     */
    private function getError($error)
    {
        return $this->getResponse(
            [
                'status' => 'error',
                'error' => $error
            ]
        );
    }

    /**
     * @param array $msg
     * @param ConnectionInterface $from
     *
     * @return int
     */
    private function validUserConnection($msg, ConnectionInterface $from)
    {
        if ($this->userApi->checkAuth($msg['userId'], $msg['token'])) {

            if (empty($this->loggedUsers[$msg['userId']])) {
                $this->loggedUsers[$msg['userId']] = new \SplObjectStorage();
            }

            if (!$this->loggedUsers[$msg['userId']]->contains($from)) {
                $this->loggedUsers[$msg['userId']]->attach($from);
            }

            return $msg['userId'];
        }
        return 0;
    }

    /**
     * @inheritdoc
     */
    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        foreach ($this->loggedUsers as $user) {
            if ($user->contains($conn)) {
                $user->detach($conn);
                break;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }

    /**
     * @param array $task
     * @param ConnectionInterface $from
     */
    private function onTaskChange($task, $from)
    {
        if ($task && $task['status'] == self::STATUS_SUCCESS) {
            $this->sendBroadcastMessage(
                array_merge(
                    $task['data']['share'],
                    [['userId' => $task['data']['ownerId']]]
                ),
                $task
            );
        }
        $from->send($this->getResponse($task));
    }

    /**
     * @param array $shared
     * @param mixed $message
     */
    private function sendBroadcastMessage($shared, $message)
    {
        if (!is_array($shared)) {
            return;
        }
        foreach ($shared as $share) {
            $storage = $this->loggedUsers[$share['userId']];
            if (!$storage) {
                continue;
            }
            foreach ($storage as $value) {
                $storage->current()->send($this->getResponse($message));
            }
        }
    }

    /**
     * @param ConnectionInterface $from
     * @param array $msg
     */
    private function onTaskRemove($from, $msg)
    {
        $cache = $this->todo->getTask($msg['id']);
        $task = $this->todo->removeTask($msg);
        if ($task && $task['status'] == self::STATUS_SUCCESS && is_array($cache['data'])) {
            $this->sendBroadcastMessage(
                array_merge(
                    $cache['data']['share'],
                    [['userId' => $cache['data']['ownerId']]]
                ),
                $task
            );
        }
        $from->send($this->getResponse($task));
    }

    /**
     * @param array $task
     * @param ConnectionInterface $from
     * @param array $msg
     * @param int $userId
     */
    private function onChangeShare($task, $from, $msg, $userId)
    {
        if ($task && $task['status'] == self::STATUS_SUCCESS) {
            $userTasks = $this->todo->getList($msg['userId']);
            $this->sendBroadcastMessage([['userId' => $userId]], $userTasks);
        }
        $from->send($this->getResponse($task));
    }

    /**
     * @param ConnectionInterface $from
     * @param array $msg
     */
    private function onGetShare($from, $msg)
    {
        $from->send($this->getResponse($this->todo->getShareList($msg)));
    }

    /**
     * @param array $params
     *
     * @return int
     */
    private function getUserIdByName($params)
    {
        return $this->userApi->getIdByName($params['username'], $params['userId'], $params['token']);
    }
}
