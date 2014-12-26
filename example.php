<?php

require 'vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

\WyriHaximus\React\futurePromise($loop)->then(function () {
    return time();
})->then(function ($time) use ($loop) {
    return \WyriHaximus\React\futurePromise($loop)->then(function () use ($time) {
        return $time;
    });
})->then(function ($time) {
    echo $time, PHP_EOL;
})->then(function () {
    echo 'Done', PHP_EOL;
});

$loop->run();
