<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

\WyriHaximus\React\futureFunctionPromise($loop, json_encode([
    'time' => time(),
]), 'json_decode')->then(function ($json) {
    echo DateTime::createFromFormat('U', $json->time)->format('r'), PHP_EOL;
});

$loop->run();
