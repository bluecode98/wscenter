<?php
namespace Protocols;

use Workerman\Connection\ConnectionInterface;
use Workerman\Protocols\Websocket;

/**
 * WebSocket protocol.
 */
class Wstcp extends Websocket
{
    public static function encode($data, ConnectionInterface $connection)
    {
        // echo "wstcp::encode\r\n";

        // 对文件数据进行编码
        if ($data['type'] == 203) {
            $payload        = $data['data'];
            $data['data']   = base64_encode($payload);
        }

        // 打包数据
        $json_str       = json_encode($data);
        $send_data      = parent::encode($json_str, $connection);

        return $send_data;
    }

    public static function decode($buffer, ConnectionInterface $connection)
    {
        // echo "wstcp::decode\r\n";

        // json解码
        $json_str           = parent::decode($buffer, $connection);
        $message            = json_decode($json_str, true);

        if ($message['type'] == 203)
        {
            $payload                = $message['data'];
            // echo "file message:";
            // var_dump($file_message);
            $message['data']        = base64_decode($payload);
        }

        return $message;
    }
}