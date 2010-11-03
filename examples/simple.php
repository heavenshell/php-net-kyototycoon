<?php
// Start KyotoTycoon port 19780
// $ ktserver -port 19780
error_reporting(E_ALL | E_STRICT);
$src = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src';
set_include_path(get_include_path() . $src);

require_once 'Net/KyotoTycoon.php';
$kt = new \Net\KyotoTycoon(array('port' => 19780));
$kt->set('test_php', 'Hello KyotoTycoon!!');
var_dump($kt->get('test_php'));
$kt->clear();
