<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Zergular\Todo\Sockets;
use Predis\ClientInterface;
use Zergular\Common\Config;

require_once __DIR__ . '/../vendor/autoload.php';

Config::setDir(__DIR__ .'/../config/');
$db = Config::get('db');
$redis = Config::get('redis');

$builder = new DI\ContainerBuilder();
$builder->addDefinitions([
    Medoo\Medoo::class => DI\object(Medoo\Medoo::class)
        ->constructor($db),
    ClientInterface::class => DI\object(Predis\Client::class)
        ->constructor($redis),
    \Zergular\Todo\CacheInterface::class => DI\object(\Zergular\Todo\Redis::class),
    \Zergular\Todo\UserApiInterface::class => DI\object(\Zergular\Todo\UserApi::class)
        ->constructor(Config::get('user')['api_url']),
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
