#!/usr/bin/php
<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

$string        = $argv[1];
$available_key = array( 'black', 'orange', 'blue' );

if(empty($string)) { 
    $string = "Nothing passed!";
}

$connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
$channel    = $connection->channel();

$channel->exchange_declare('direct_logs', 'direct', false, false, false);

$key = $available_key[rand(0,2)];
$msg = new AMQPMessage( $string );

$channel->basic_publish( $msg, 'direct_logs', $key );

echo ' [x] Sent "' . $string . '" to direct_logs:' . $key . "\n";

$channel->close();
$connection->close();
