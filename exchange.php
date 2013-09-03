#!/usr/bin/php
<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

$string = implode(' ', array_slice($argv, 1));

if(empty($string)) { 
    $string = "Nothing passed!";
}

$connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
$channel    = $connection->channel();

$channel->exchange_declare('logs', 'fanout', false, false, false);

$msg = new AMQPMessage( $string, array('delivery_mode' => 2) );
$channel->basic_publish( $msg, 'logs' );

$channel->close();
$connection->close();
