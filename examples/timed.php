<?php declare(strict_types=1);

require \dirname(__DIR__) . '/vendor/autoload.php';

echo 'Reporting back in 12 seconds', \PHP_EOL;
echo DateTime::createFromFormat('U', \time())->format('r'), \PHP_EOL;
\WyriHaximus\React\timedPromise(12)->then(function (): void {
    echo DateTime::createFromFormat('U', \time())->format('r'), \PHP_EOL;
    echo 'Done', \PHP_EOL;
});
