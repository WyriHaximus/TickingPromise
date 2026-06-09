<?php

declare(strict_types=1);

use React\Promise\PromiseInterface;

use function React\Promise\all;
use function WyriHaximus\React\futurePromise;
use function WyriHaximus\React\nextPromise;
use function WyriHaximus\React\tickingPromise;
use function WyriHaximus\React\timedPromise;

require dirname(__DIR__) . '/vendor/autoload.php';

all([
    futurePromise()->then(static fn (): int => time()),
    tickingPromise(0.001, static function (): bool {
        echo '.';

        return mt_rand(0, 1000) === 13;
    }),
])->then(static fn ($time) => nextPromise($time[0]))->then(static fn ($time): PromiseInterface => timedPromise(3, $time))->then(static function ($time): void {
    echo PHP_EOL;
    echo DateTime::createFromFormat('U', $time)->format('r'), PHP_EOL;
    echo DateTime::createFromFormat('U', time())->format('r'), PHP_EOL;
    echo 'Done', PHP_EOL;
});
