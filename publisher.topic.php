#!/usr/bin/php
<?php
/**
 * Usage:
 * ./publisher.topic.php 'topic.key' 'message here'
 */

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

require_once __DIR__ . '/config.php';

$topic_key = $argv[1];
$message   = $argv[2];

if(empty($message)) { 
    $message = "Nothing passed!";
}

if(empty($topic_key)) { 
    $topic_key= "no.info";
}

echo 'Message: ' . $message . "\n";
echo 'Topic: ' . $topic_key . "\n";

$conn = new AMQPConnection('localhost', 5672, $rabbit_user, $rabbit_password, $rabbit_vhost);
$channel = $conn->channel();
$channel->exchange_declare('topic.events', 'topic', false, true, false);

$msg = new AMQPMessage( $message , array( 'delivery_mode' => 2 ) );
$channel->basic_publish( $msg, 'topic.events', $topic_key );

echo ' [x] Sent message \'' . $message . '\' with key \'' . $topic_key . "'\n";

$channel->close();
$conn->close();
