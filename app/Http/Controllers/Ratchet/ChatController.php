<?php

namespace App\Http\Controllers\Ratchet;

use Ratchet\ConnectionInterface;
use App\Http\Controllers\Controller;
use Ratchet\MessageComponentInterface;

class ChatController extends Controller implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $from)
    {
        $this->clients->attach($from);

        echo "New connection! ({$from->resourceId})\n";
    }

    /* this function is visited before onmessage JavaScript function */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        $data['dt'] = date('Y-m-d h:i:s');

        foreach ($this->clients as $client) {
            /* when this line executed onmessage JavaScript function wil triggere */
            if (!empty($data['msg'])) {

                if ($from == $client) {
                    $data['user'] = 'Me';
                } else {
                    $data['user'] = $data['from'];
                }


                $client->send(json_encode($data));
            }
        }
    }

    public function onClose(ConnectionInterface $from)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($from);

        echo "Connection {$from->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $from, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $from->close();
    }
}
