<?php
include __DIR__ . DIRECTORY_SEPARATOR . 'clean_filter.php';

stream_filter_register('cleanwords', 'Clean_Filter');

include 'php://filter/read=cleanwords/resource=' . __DIR__ . DIRECTORY_SEPARATOR . 'wikipediatext.txt';