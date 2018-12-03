<?php declare(strict_types=1);

require \dirname(__DIR__) . '/vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

\WyriHaximus\React\futurePromise($loop)->then(function (): void {
    echo DateTime::createFromFormat('U', \time())->format('r'), PHP_EOL;
});

$loop->run();
