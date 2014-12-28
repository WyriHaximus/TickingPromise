<?php

namespace WyriHaximus\React;

use React\EventLoop\LoopInterface;
use React\Promise\Deferred;

/**
 * @param LoopInterface $loop ReactPHP event loop.
 *
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
 * @param LoopInterface $loop ReactPHP event loop.
 *
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

/**
 * @param LoopInterface $loop     ReactPHP event loop.
 * @param integer       $interval The number of seconds to wait before execution.
 *
 * @return \React\Promise\Promise
 */
function timedPromise(LoopInterface $loop, $interval)
{
    $deferred = new Deferred();
    $loop->addTimer($interval, function () use ($deferred) {
        $deferred->resolve();
    });
    return $deferred->promise();
}
