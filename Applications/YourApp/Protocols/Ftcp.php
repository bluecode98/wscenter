<?php
namespace Protocols;

use \GatewayWorker\Lib\Gateway;
use Workerman\Connection\ConnectionInterface;

class Ftcp implements \Workerman\Protocols\ProtocolInterface
{
    const PACKAGE_HEAD_LEN = 8;

    public static function packByArr($arr)  {
        $atArr=array();
        foreach ($arr as $k=>$v) {
            $atArr[]=pack($v[0],$v[1]);
        }
        return $atArr;
    }

    public static function input($buffer, ConnectionInterface $connection)
    {
        // echo "ftcp::input\r\n";
        if (strlen($buffer) < self::PACKAGE_HEAD_LEN) {
            return 0;
        }

        $header         = unpack('Lhead_size/Ldata_size', $buffer);
        $total_size     = $header['head_size'] + $header['data_size'] + self::PACKAGE_HEAD_LEN;

        return $total_size;
    }

    public static function encode($data, ConnectionInterface $connection)
    {
        $payload        = isset($data['data']) ? $data['data'] : false;

        // 消息头数据
        $message        = $data;
        unset($message['remote_ip']);      // 删除敏感数据
        unset($message['data']);
        $head_data      = json_encode($message);

        // 打包消息头和数据载荷
        if ($payload) {
            // 有数据载荷
            $size_data      = array(
                'head_size' => array('L', strlen($head_data)),
                'data_size' => array('L', strlen($payload)), // ??????
            );

            // if ($data['type']==201) {
            //     var_dump($size_data);
            // }

            $send_data      = join("", self::packByArr($size_data)) . $head_data . $payload;
        } else {
            // 无数据载荷
            $size_data      = array(
                'head_size' => array('L', strlen($head_data)),
                'data_size' => array('L', 0),
            );
            $send_data      = join("", self::packByArr($size_data)) . $head_data;
        }

        // var_dump($send_data);
        return $send_data;
    }

    public static function decode($buffer, ConnectionInterface $connection)
    {
        // echo "ftcp::decode\r\n";
        $header         = unpack('Lhead_size/Ldata_size', $buffer);

        // 消息头
        $head_data      = substr($buffer, self::PACKAGE_HEAD_LEN, $header['head_size']);
        $message        = json_decode($head_data, true);

        // IP信息
        $message['remote']   = $connection->getRemoteIp();

        // 数据载荷
        if ($header['data_size']>0) {
            $message['data']        = substr($buffer, self::PACKAGE_HEAD_LEN + $header['head_size']);
        }

        // if ($message['type']==203) {
        //     var_dump($message);
        // }

        return $message;
    }
}