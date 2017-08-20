<?php

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

    // Make sure composer dependencies have been installed
   // require __DIR__ . '/vendor/autoload.php';

/**
 * chat.php
 * Send any incoming messages to all connected clients (except sender)
 */
class Chat implements MessageComponentInterface {
    protected $clients;
    protected $data;
    
    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->data=[];
    }

    public function onOpen(ConnectionInterface $conn) {
         // Store the new connection to send messages to later
         //$this->clients->attach($conn);
         //ddd($_POST['mode']);
         echo "New connection! ({$conn->resourceId})\n";
       

    }

    public function onMessage(ConnectionInterface $from, $msg) {
          //var_dump(json_decode($msg,true)['tokenYou']);
          
          $u = json_decode($msg,true);
          if(array_key_exists('me', $u)){
             $from->resourceId=$u['me'];
            $this->clients->attach($from);
          }else{
            foreach ($this->clients as $client) {
                if ($from !== $client and $client->resourceId==json_decode($msg,true)['toa']) {
                    $client->send($msg);
                }
            }

        }//end else
               
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);

    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }

}

?>