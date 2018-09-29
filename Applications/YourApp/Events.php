<?php
use \GatewayWorker\Lib\Gateway;

class Events
{
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
    
   public static function onMessage($client_id, $data)
   {
       switch($data['type'])
       {
           case 100:
               //$user_id         = $data['sender'];
               //echo "live [$user_id]\r\n";
               break;

           case 102:
               $user_id         = $data['target'];
               $group           = $data['data'];
               echo "bind user:[$user_id]=>[$group]\r\n";
               Gateway::bindUid($client_id, $user_id);
               Gateway::joinGroup($client_id, $group);
               break;

            case 103:
               $target          = $data['target'];
               $data['sender']  = $client_id;
               echo "send to user:[$target]\r\n";
               Gateway::sendToUid($target, $data);
               break;

            case 104:
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
   
   public static function onClose($client_id)
   {
       echo "$client_id logout\r\n";
   }
}
