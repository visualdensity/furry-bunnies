#!/usr/bin/php
<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;

$key = $argv[1];

$connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
list( $queue ) = $channel->queue_declare("");

$channel->queue_bind($queue, 'direct_logs', $key);

print "Queue: " . $queue . "\n";
print "Key: "   . $key. "\n";

$callback = function($msg) {
    echo '  [x] Routing Key:' . $msg->delivery_info['routing_key']  . ' Message: ' . $msg->body . "\n";
};

$channel->basic_consume($queue, '', false, true, false, false, $callback);

while( count($channel->callbacks) ) {
    $channel->wait();
}

$channel->close();
$connection->close();
