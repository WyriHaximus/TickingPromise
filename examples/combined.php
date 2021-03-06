<?php declare(strict_types=1);

require \dirname(__DIR__) . '/vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

\React\Promise\all([
    \WyriHaximus\React\futurePromise($loop)->then(function () {
        return \time();
    }),
    \WyriHaximus\React\tickingPromise($loop, 0.001, function () {
        echo '.';

        return \mt_rand(0, 1000) == 13;
    }),
])->then(function ($time) use ($loop) {
    return \WyriHaximus\React\nextPromise($loop, $time[0]);
})->then(function ($time) use ($loop) {
    return \WyriHaximus\React\timedPromise($loop, 3, $time);
})->then(function ($time): void {
    echo \PHP_EOL;
    echo DateTime::createFromFormat('U', $time)->format('r'), \PHP_EOL;
    echo DateTime::createFromFormat('U', \time())->format('r'), \PHP_EOL;
    echo 'Done', \PHP_EOL;
});

$loop->run();
