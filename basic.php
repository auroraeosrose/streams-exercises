<?php 

// best if you want the whole file fast
$data = file_get_contents('/home/foo/bar.txt');

// best to limit memory consumption and do chunking
// 8192 is internal chunk size - hardcoded
$fp = fopen('/home/foo/bar.txt', 'r');
while(!feop($fp)) {
	$data .= fread($fp, 8192);
}
fclose($fp);

// most memory efficient but slowest, gets line at a time
$fp = fopen('/home/foo/bar.txt', 'r');
while(!feop($fp)) {
	$data .= fgets($fp);
}
fclose($fp);