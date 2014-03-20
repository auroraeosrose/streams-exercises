<?php
// we're going to lock ourselves in a loop so we can keep getting quotes over and over
do {
    // our little help message
    echo 'Enter 1 to get a Star Wars Quote', PHP_EOL;
    echo 'Enter 2 to get a Star Trek Quote', PHP_EOL;
    echo 'Enter q to quit', PHP_EOL;
    echo 'Any other choice retrieves a random quote', PHP_EOL;
    echo 'Press enter to continue', PHP_EOL, PHP_EOL;

    // get a line from the console and grab only one character
    $key = trim(fgets(STDIN));
    $key = $key[0];

    // q breaks our loop
    if ('q' === $key) {
        break;
    }

    // request based on what user entered
    if (1 == $key) {
        $string = 'starwars';
    } else if (2 == $key) {
        $string = 'startrek';
    } else {
        $string = 'foobar';
    }

    // Client code starts here
    $client = stream_socket_client('tcp://127.0.0.1:8000', $errorcode, $errorstring, 30,
                               STREAM_CLIENT_CONNECT);

    if (false === $client) {
        echo 'Could not connect,', $errorstring, PHP_EOL;
        die($errorcode);
    }

    fwrite($client, $string);
    echo 'Quote returned: ', fgets($client);
    fclose($client);
} while (true);

