<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Zergular\Todo\Sockets;
use Predis\ClientInterface;

define('USER_API_URL', 'http://127.0.0.1:8080/');
require_once __DIR__ . '/../vendor/autoload.php';

$builder = new DI\ContainerBuilder();
$builder->addDefinitions([
    Medoo\Medoo::class => DI\object(Medoo\Medoo::class)
        ->constructor([
            'database_type' => 'mysql',
            'database_name' => 'todo',
            'server' => '127.0.0.1',
            'charset' => 'utf8',
            'username' => 'root',
            'password' => 'passwd'
        ]),
    ClientInterface::class => DI\object(Predis\Client::class)
        ->constructor([
            'scheme' => 'tcp',
            'host' => '127.0.0.1',
            'port' => 6379
        ]),
    \Zergular\Todo\CacheInterface::class => DI\object(\Zergular\Todo\Redis::class),
    \Zergular\Todo\UserApiInterface::class => DI\object(\Zergular\Todo\UserApi::class)
        ->constructor(USER_API_URL),
    \Zergular\Todo\ControllerInterface::class => DI\object(\Zergular\Todo\Controller::class),
    \Zergular\Todo\ItemBuilderInterface::class => DI\object(\Zergular\Todo\ItemBuilder::class),
    \Zergular\Todo\Task\TaskManagerInterface::class => DI\object(\Zergular\Todo\Task\Manager::class),
    \Zergular\Todo\Link\LinkManagerInterface::class=>DI\object(\Zergular\Todo\Link\Manager::class),
    \Zergular\Todo\DataProviderInterface::class => DI\object(\Zergular\Todo\DataProvider::class),
    \Zergular\Todo\PermissionManagerInterface::class => DI\object(\Zergular\Todo\PermissionManager::class)
]);

$container = $builder->build();

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Sockets(
                $container->get('Zergular\\Todo\\Controller'),
                $container->get('Zergular\\Todo\\UserApi')
            )
        )
    ),
    8000
);

$server->run();
