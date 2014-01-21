#!/usr/bin/php
<?php
/**
 * Usage:
 * ./consumer.topic.php 'topic.key' 'queue'
 */

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;

require_once __DIR__ . '/config.php';

$topic_key = $argv[1];
$queue_name = $argv[2];

if( empty($topic_key)) {
    file_put_contents('php://stderr', "Usage: $argv[0] [binding_key]\n");
    exit(1);
}

if(empty($queue_name)) {
    $message = "No queue declared!";
}

$conn = new AMQPConnection('localhost', 5672, $rabbit_user, $rabbit_password, $rabbit_vhost);
$channel = $conn->channel();

$callback = function($msg) {
    echo '  [-] message key \'' . $msg->delivery_info['routing_key'] . '\' : ' . $msg->body . "\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

$channel->basic_consume( $queue_name, 'direct_queue_consumer', false, false, false, false, $callback );

while( count($channel->callbacks) ) {
    $channel->wait();
}

$channel->close();
$conn->close();
