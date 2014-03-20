<?php 
function safe_feof($fp, &$start = null) {
	$start = microtime(true);
	return feof($fp);
}

$start = null;
$timeout = ini_get('default_socket_timeout');

$fp = fsockopen('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);

// send request
fputs($fp, 'Headers and Request Data');

while(!safe_feof($fp, $start) && (microtime(true) - $start) < $timeout) {
	//get response
	$res = fgets($fp, 1024);
	if('VERIFIED' === $res) {
		// we did it
	} elseif('INVALID' === $res) {
		// something something bad
	}
}

fclose($fp);