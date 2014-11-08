<?php

namespace WyriHaximus\React;

use React\EventLoop\LoopInterface;
use React\Promise\Deferred;

/**
 * @param LoopInterface $loop
 * @return callable
 */
function futurePromise(LoopInterface $loop)
{
    return function ($result) use ($loop) {

        $deferred = new Deferred();

        $loop->futureTick(function () use ($deferred, $result) {
            $deferred->resolve($result);
        });
        return $deferred->promise();
    };
}

/**
 * @param LoopInterface $loop
 * @return callable
 */
function nextPromise(LoopInterface $loop)
{
    return function ($result) use ($loop) {

        $deferred = new Deferred();

        $loop->nextTick(function () use ($deferred, $result) {
            $deferred->resolve($result);
        });
        return $deferred->promise();
    };
}
