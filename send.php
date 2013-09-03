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
$channel = $connection->channel();
$channel->queue_declare('task_queue', false, true, false, false );

$msg = new AMQPMessage($string, array('delivery_mode' => 2) ); //delivery_mode 2 = persistent
$channel->basic_publish($msg, '', 'task_queue');

$channel->close();
$connection->close();
