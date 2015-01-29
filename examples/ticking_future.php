<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

const WAIT_COUNT = 307200;

$start = time();
$count = 0;
echo 'Wait ' . WAIT_COUNT . ' ticks before resolving:', PHP_EOL;
\WyriHaximus\React\tickingFuturePromise($loop, function($waitCount) use (&$count, $start) {
    echo '.';

    if (++$count == $waitCount) {
        return $start;
    }

    return false;
}, WAIT_COUNT)->then(function ($start) {
    echo PHP_EOL, 'That took ' . (time() - $start) . ' seconds.', PHP_EOL;
}, function ($exception) {
    echo $exception->getMessage(), PHP_EOL;
});

$loop->run();
