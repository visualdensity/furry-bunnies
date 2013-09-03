#!/usr/bin/php
<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;

$connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
list( $queue ) = $channel->queue_declare("");

$channel->queue_bind($queue, 'logs');
print "Queue: " . $queue . "\n";

$callback = function($msg) {
    echo '  [x] Message body: ' . $msg->body . "\n";
};

$channel->basic_consume($queue, '', false, true, false, false, $callback);

while( count($channel->callbacks) ) {
    $channel->wait();
}

$channel->close();
$connection->close();
