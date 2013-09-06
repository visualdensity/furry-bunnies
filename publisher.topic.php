#!/usr/bin/php
<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

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

$conn = new AMQPConnection('localhost', 5672, 'logger', 'logging123', 'loggin');
$channel = $conn->channel();
$channel->exchange_declare('topic.logs', 'topic', false, true, false);

$msg = new AMQPMessage( $message , array( 'delivery_mode' => 2 ) );
$channel->basic_publish( $msg, 'topic.logs', $topic_key );

echo ' [x] Sent ' . $message . ' with key ' . $topic_key . "\n";

$channel->close();
$conn->close();
