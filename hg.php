<?php

$path = '/opt/local/bin/hg';

$hg = $path . "branches -y -R " . escapeshellarg('https://bitbucket.org/egaga/open-vim');

$pipe = popen($cmd . " 2>&1", 'r');

$branches = array();

while ($line = fgets($$pipe)) {
    list($branch, $revstr) = preg_split('/\s+/', $line);
    list($num, $rev) = explode(':', $revstr, 2);
    $branches[$branch] = $rev;
}
$pipe = null;

var_dump($branches);