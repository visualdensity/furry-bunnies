<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;

$queue = implode(' ', array_slice($argv, 1));

if(empty($queue)) { 
    $queue = "No queue specified!";
    exit(1);
}

$connection = new AMQPConnection( 'localhost', 5672, 'guest', 'guest' );
$channel = $connection->channel();
$channel->queue_declare($queue, false, true, false, false);

echo ' [x] Waiting for messages in ' . $queue . '...';
echo "\n";

$callback = function($msg) {
    echo ' [x] Received ' . $msg->body . "\n";
    sleep( substr_count($msg->body, '.' ));
    echo " [x] Done! \n";

    print "Body: "; print_r($msg->body);  print "\n";
    $msg->delivery_info['channel']->basic_ack(
        $msg->delivery_info['delivery_tag']
    );
};

$channel->basic_qos( null, 1, null );
$channel->basic_consume($queue, '', false, true, false, false, $callback);

while( count($channel->callbacks)  ) {
    $channel->wait();
}


