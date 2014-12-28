<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

\WyriHaximus\React\futurePromise($loop)->then(function () {
    echo DateTime::createFromFormat('U', time())->format('r'), PHP_EOL;
});

$loop->run();
