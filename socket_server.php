<?php
echo 'Starting Quote Server', PHP_EOL;

// read in our file of star wars and star trek quotes that we're serving
$wars = file(__DIR__ . DIRECTORY_SEPARATOR . 'starwars.txt');
$trek = file(__DIR__ . DIRECTORY_SEPARATOR . 'startrek.txt');
$all = array_merge($wars, $trek);

echo 'Listening...', PHP_EOL;

// Open a server, listen would bomb with udp
$server = stream_socket_server('tcp://127.0.0.1:8000', $errorcode, $errorstring,
                             STREAM_SERVER_BIND | STREAM_SERVER_LISTEN);

if (false === $server) {
    echo 'ERROR: ' , $errorstring, PHP_EOL;
    exit($errorcode);
}

do {
    /* Accept that connection */
    $socket = stream_socket_accept($server);

    //let's see if they have a preference, we only care about 20 bytes or so ;)
    $buf = fread($socket, 20);

    // get our quote based on preference
    if ($buf == 'starwars') {
        $key = array_rand($wars);
        $quote = $wars[$key];
    } else if ($buf == 'startrek') {
        $key = array_rand($trek);
        $quote = $trek[$key];
    } else {
        $key = array_rand($all);
        $quote = $all[$key];
    }

    // send our quote
    echo 'Sending...', PHP_EOL;
    fwrite($socket, $quote);
    fclose($socket);

} while (true);

fclose($server);

echo 'Shutting Down Server', PHP_EOL;