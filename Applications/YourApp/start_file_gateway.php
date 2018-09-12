<?php 
use \Workerman\Worker;
use \Workerman\WebServer;
use \GatewayWorker\Gateway;
use \GatewayWorker\BusinessWorker;
use \Workerman\Autoloader;
use \Workerman\Lib\Timer;

require_once __DIR__ . '/../../vendor/autoload.php';

$context = array(
    'ssl' => array(
        'local_cert'  => '/root/wscenter/keys/server.crt',
        'local_pk'    => '/root/wscenter/keys/server.key',
        'cafile'      => '/root/wscenter/keys/server.crt',
        'verify_peer' => true,
        'allow_self_signed' => true, 
    )
);

$gateway = new Gateway("ftcp://0.0.0.0:25", $context);
$gateway->transport = 'ssl';
$gateway->name = 'SMTPGateway';
$gateway->count = 4;
$gateway->lanIp = '127.0.0.1';
$gateway->startPort = 2910;
$gateway->registerAddress = '127.0.0.1:91238';
$gateway->pingInterval = 55;
$gateway->pingNotResponseLimit = 4;

$gateway2 = new Gateway("ftcp://0.0.0.0:443", $context);
$gateway2->transport = 'ssl';
$gateway2->name = 'SSLGateway';
$gateway2->count = 4;
$gateway2->lanIp = '127.0.0.1';
$gateway2->startPort = 2920;
$gateway2->registerAddress = '127.0.0.1:91238';
$gateway2->pingInterval = 55;
$gateway2->pingNotResponseLimit = 4;


if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}

