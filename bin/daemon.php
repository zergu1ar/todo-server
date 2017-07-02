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
use Todo\Cache;

require_once '../vendor/autoload.php';


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
            'host'   => '127.0.0.1',
            'port'   => 6379
        ]),
    ICache::class => DI\object(Cache::class)
]);

$container = $builder->build();


$controller = $container->get('\\Todo\\Controller');
var_dump($controller->getLists(1));

die;
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Sockets($controller)
        )
    ),
    8080
);

$server->run();