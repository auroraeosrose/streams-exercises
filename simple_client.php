<?php
error_reporting(-1);
ini_set('display_errors', 1);

$host = '127.0.0.1';
$port = 8000
// Client code starts here
$client = stream_socket_client('tcp://' . $host . ':' . $port, $errorcode, $errorstring, 30);
echo stream_get_contents($client);
fclose($client);