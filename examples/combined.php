<?php declare(strict_types=1);

require \dirname(__DIR__) . '/vendor/autoload.php';

\React\Promise\all([
    \WyriHaximus\React\futurePromise()->then(function () {
        return \time();
    }),
    \WyriHaximus\React\tickingPromise(0.001, function () {
        echo '.';

        return \mt_rand(0, 1000) == 13;
    }),
])->then(function ($time) {
    return \WyriHaximus\React\nextPromise($time[0]);
})->then(function ($time) {
    return \WyriHaximus\React\timedPromise(3, $time);
})->then(function ($time): void {
    echo \PHP_EOL;
    echo DateTime::createFromFormat('U', $time)->format('r'), \PHP_EOL;
    echo DateTime::createFromFormat('U', \time())->format('r'), \PHP_EOL;
    echo 'Done', \PHP_EOL;
});
