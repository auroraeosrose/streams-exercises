<?php 
// should be in the format of $options [$stream_wrapper][$option]
$options = array('http' => array('method' => 'HEAD'));

//create the context
$context = stream_context_create($options);

file_get_contents('http://example.com', false, $context);

// our magically delicious headers
var_dump($http_response_headers);

// set our context as default
stream_set_default_context($context);

include('http://example.com');