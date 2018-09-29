<?php 
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
use \Workerman\Worker;
use \Workerman\WebServer;
use \GatewayWorker\Gateway;
use \GatewayWorker\BusinessWorker;
use \Workerman\Autoloader;
use \Workerman\Lib\Timer;

$gateway = new Gateway("wstcp://127.0.0.1:8081");
$gateway->name = 'WebsocketGateway';
$gateway->count = 4;
$gateway->lanIp = '127.0.0.1';
$gateway->startPort = 2810;
$gateway->registerAddress = '127.0.0.1:1238';
// $gateway->pingInterval = 55;
// $gateway->pingNotResponseLimit = 4;

if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}

