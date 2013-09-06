#!/usr/bin/php
<?php
/**
 * Usage:
 * ./consumer.topic.php 'topic.key'
 */

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;

$topic_keys = array_slice($argv,1);

if( empty($topic_keys)) {
    file_put_contents('php://stderr', "Usage: $argv[0] [binding_key]\n");
    exit(1);
}

$conn = new AMQPConnection('localhost', 5672, 'logger', 'logging123', 'loggin');
$channel = $conn->channel();
$channel->exchange_declare('topic.logs', 'topic', false, true, false);

list($queue, ,) = $channel->queue_declare("", false, false, true, false);

foreach($topic_keys as $topic_key) {
    $channel->queue_bind($queue, 'topic.logs', $topic_key);
}

$callback = function($msg) {
    echo '  [x] Processed message with key \'' . $msg->delivery_info['routing_key'] . '\' : ' . $msg->body . "\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

/*
    queue: Queue from where to get the messages
    consumer_tag: Consumer identifier
    no_local: Don't receive messages published by this consumer.
    no_ack: Tells the server if the consumer will acknowledge the messages.
    exclusive: Request exclusive consumer access, meaning only this consumer can access the queue
    nowait:
    callback: A PHP Callback
*/
$channel->basic_consume( $queue, 'log_consumer', false, false, false, false, $callback );

while( count($channel->callbacks) ) {
    $channel->wait();
}

$channel->close();
$conn->close();
