<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 02.07.17
 * Time: 10:51
 */

namespace Todo;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Sockets implements MessageComponentInterface
{

    protected $Todo;

    protected $clients;

    protected $loggedUsers = [];

    public function __construct(IController $todo)
    {
        $this->Todo = $todo;
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $msg = json_decode($msg, TRUE);
        $id = $this->registerUserConnection($msg, $from);
        if (!$id) {
            $from->send($this->sendError('Auth error'));
        }
        //TODO split by methods and implements broadcast send
        if (isset($msg['action'])) {
            switch ($msg['action']):
                case 'getList':
                    $from->send($this->Todo->getLists($id));
                    break;
                case 'saveList':
                    $from->send($this->Todo->saveList($msg));
                    break;
                case 'saveTask':
                    $from->send($this->Todo->saveTask($msg));
                    break;

            endswitch;
        }
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

    private function registerUserConnection($msg, ConnectionInterface $from)
    {
        $this->loggedUsers[$msg['id']][] = $from;
        return $msg['id'];
//        $this->AuthApi->checkAuth($msg['id'], $msg['token']);
    }
}