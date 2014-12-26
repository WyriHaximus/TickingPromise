<?php

namespace WyriHaximus\React;

use React\EventLoop\LoopInterface;
use React\Promise\Deferred;

/**
 * @param LoopInterface $loop
 * @return \React\Promise\Promise
 */
function futurePromise(LoopInterface $loop)
{
    $deferred = new Deferred();
    $loop->futureTick(function () use ($deferred) {
        $deferred->resolve();
    });
    return $deferred->promise();
}

/**
 * @param LoopInterface $loop
 * @return \React\Promise\Promise
 */
function nextPromise(LoopInterface $loop)
{
    $deferred = new Deferred();
    $loop->nextTick(function () use ($deferred) {
        $deferred->resolve();
    });
    return $deferred->promise();
}
