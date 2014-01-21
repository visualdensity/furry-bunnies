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

$channel->exchange_declare('phpmelb_direct', 'direct', false, false, false);

#$msg = new AMQPMessage( $message);
$msg = new AMQPMessage( $message , array( 'delivery_mode' => 2 ) );

$channel->basic_publish( $msg, 'phpmelb_direct', $topic_key );
print_r($channel);

echo ' [x] Sent "' . $message . '" tagged with ' . $topic_key . "\n";

$channel->close();
$conn->close();
