<?php

namespace WyriHaximus\React;

use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\Timer;
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

/**
 * @param LoopInterface $loop     ReactPHP event loop.
 * @param integer       $interval The number of seconds between each interval to run $check.
 * @param callable      $check    Callable to run at the specified $interval.
 *
 * @return \React\Promise\Promise
 */
function tickingPromise(LoopInterface $loop, $interval, callable $check)
{
    $deferred = new Deferred();
    $loop->addPeriodicTimer($interval, function (Timer $timer) use ($deferred, $check) {
        $deferred->progress(time());
        $result = $check();
        if ($result !== false) {
            $timer->cancel();
            $deferred->resolve($result);
        }
    });
    return $deferred->promise();
}
