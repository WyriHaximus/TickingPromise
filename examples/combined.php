<?php

declare(strict_types=1);

use function React\Promise\all;
use function WyriHaximus\React\futurePromise;
use function WyriHaximus\React\nextPromise;
use function WyriHaximus\React\tickingPromise;
use function WyriHaximus\React\timedPromise;

require dirname(__DIR__) . '/vendor/autoload.php';

all([
    futurePromise()->then(static function () {
        return time();
    }),
    tickingPromise(0.001, static function () {
        echo '.';

        return mt_rand(0, 1000) === 13;
    }),
])->then(static function ($time) {
    return nextPromise($time[0]);
})->then(static function ($time) {
    return timedPromise(3, $time);
})->then(static function ($time): void {
    echo PHP_EOL;
    echo DateTime::createFromFormat('U', $time)->format('r'), PHP_EOL;
    echo DateTime::createFromFormat('U', time())->format('r'), PHP_EOL;
    echo 'Done', PHP_EOL;
});
