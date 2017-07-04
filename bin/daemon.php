<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 02.07.17
 * Time: 10:55
 */

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Todo\Sockets;
use \Medoo\Medoo;
use Todo\ICache;
use Todo\Redis;
use Todo\UserApi;

require_once __DIR__ . '/../vendor/autoload.php';

define('USER_API_URL', 'http://127.0.0.1:8080/');

$builder = new DI\ContainerBuilder();
$builder->addDefinitions([
    Medoo::class => DI\object(Medoo::class)
        ->constructor([
            'database_type' => 'mysql',
            'database_name' => 'todo',
            'server' => '127.0.0.1',
            'charset' => 'utf8',
            'username' => 'root',
            'password' => 'passwd'
        ]),
    Predis\Client::class => DI\object(Predis\Client::class)
        ->constructor([
            'scheme' => 'tcp',
            'host' => '127.0.0.1',
            'port' => 6379
        ]),
    ICache::class => DI\object(Redis::class),
    UserApi::class => DI\object(UserApi::class)
        ->constructor(USER_API_URL)
]);

$container = $builder->build();

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Sockets(
                $container->get('\\Todo\\Controller'),
                $container->get('\\Todo\\UserApi')
            )
        )
    ),
    8000
);

$server->run();