<?php declare(strict_types=1);

require \dirname(__DIR__) . '/vendor/autoload.php';

\WyriHaximus\React\futurePromise()->then(function (): void {
    echo DateTime::createFromFormat('U', \time())->format('r'), \PHP_EOL;
});
