<?php declare(strict_types=1);

require \dirname(__DIR__) . '/vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

const WAIT_COUNT = 1337;
const WAIT_INTERVAL = 0.01;

$start = \time();
$count = 0;
echo 'Wait ' . WAIT_COUNT . ' * ' . WAIT_INTERVAL . ' seconds before resolving:', \PHP_EOL;
\WyriHaximus\React\tickingPromise($loop, WAIT_INTERVAL, function ($waitCount) use (&$count, $start) {
    echo '.';

    if (++$count == $waitCount) {
        return $start;
    }

    return false;
}, WAIT_COUNT)->then(function ($start): void {
    echo \PHP_EOL, 'That took ' . (\time() - $start) . ' seconds.', \PHP_EOL;
});

$loop->run();
