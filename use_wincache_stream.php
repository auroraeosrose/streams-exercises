<?php
error_reporting(-1);
ini_set('display_errors', 1);

include __DIR__ . DIRECTORY_SEPARATOR . 'wincache.php';
stream_register_wrapper('wincache','Wincache_Stream');

file_put_contents('wincache://alphabet', 'abcdefghijklmnopqrstuvwxyz');
echo file_get_contents('wincache://alphabet');

$data = fopen('wincache://counting', 'w+');
fwrite($data, 'One Two Three Four');
fseek($data, 4, SEEK_SET);
echo fread($data, 3);
var_dump(fstat($data));
fclose($data);

var_dump(stat('wincache://counting'));

rename('wincache://counting', 'wincache://counting2');

include 'wincache://counting2';