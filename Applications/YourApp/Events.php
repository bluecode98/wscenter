<?php
use \GatewayWorker\Lib\Gateway;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{
    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     * 
     * @param int $client_id 连接id
     */
    public static function onConnect($client_id)
    {
        $data      = array(
            'type'      => 102,
            'sender'    => $client_id,
            'target'    => $client_id,
        );
        echo("$client_id connect\r\n");
        Gateway::sendToClient($client_id, $data);
    }
    
   /**
    * 当客户端发来消息时触发
    * @param int $client_id 连接id
    * @param mixed $message 具体消息
    */
   public static function onMessage($client_id, $data)
   {
//       return;

       // do data
       switch($data['type'])
       {
            // live
           case 100:
               //$user_id         = $data['sender'];
               //echo "live [$user_id]\r\n";
               break;

           case 102:
               // 绑定client id和group id
               $user_id         = $data['target'];
               $group           = $data['data'];
               echo "bind user:[$user_id]=>[$group]\r\n";
               Gateway::bindUid($client_id, $user_id);
               Gateway::joinGroup($client_id, $group);
               break;

            case 103:
               // 根据client id发送数据
               $target          = $data['target'];
               $data['sender']  = $client_id;
               echo "send to user:[$target]\r\n";
               Gateway::sendToUid($target, $data);
               break;

            case 104:
               // 查询group id中的用户
               $group           = $data['data'];
               echo "query [$group] list\r\n";
               $client_list     = Gateway::getClientIdListByGroup($group);
               foreach ($client_list as $k=>&$v) {
                    $v    = Gateway::getUidByClientId($k);
               }
               $data['data']    = json_encode($client_list);
               Gateway::sendToClient($client_id, $data);
               break;
  
           default:
               $target          = $data['target'];
               echo "send to client:[$target]\r\n";
               if(Gateway::isOnline($target)) {
                   Gateway::sendToClient($target, $data);
               }
               else {
                   echo "[$target] is offline\r\n";
               }

               break;
       }
   }
   
   /**
    * 当用户断开连接时触发
    * @param int $client_id 连接id
    */
   public static function onClose($client_id)
   {
       echo "$client_id logout\r\n";
   }
}
