<?php

declare(strict_types=1);

namespace WyriHaximus\React;

use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;
use React\Promise\Deferred;
use React\Promise\Promise;
use React\Promise\PromiseInterface;

/**
 * Promise that resolves once future tick is called.
 *
 * @param LoopInterface $loop  ReactPHP event loop.
 * @param mixed         $value Value to return on resolve.
 */
function futurePromise(LoopInterface $loop, $value = null): PromiseInterface
{
    $deferred = new Deferred();
    $loop->futureTick(static function () use ($deferred, $value): void {
        $deferred->resolve($value);
    });

    return $deferred->promise();
}

/**
 * Promise that resolves after $interval has passed.
 *
 * @param LoopInterface $loop     ReactPHP event loop.
 * @param float         $interval The number of seconds to wait before execution.
 * @param mixed         $value    Value to return on resolve.
 */
function timedPromise(LoopInterface $loop, float $interval, $value = null): PromiseInterface
{
    $deferred = new Deferred();
    $loop->addTimer($interval, static function () use ($deferred, $value): void {
        $deferred->resolve($value);
    });

    return $deferred->promise();
}

/**
 * Promise that resolves once $check returns something other then false. Runs at periodic $interval.
 *
 * @param LoopInterface $loop     ReactPHP event loop.
 * @param float         $interval The number of seconds between each interval to run $check.
 * @param callable      $check    Callable to run at the specified $interval.
 * @param mixed         $value    Value to pass into $check on tick.
 */
function tickingPromise(LoopInterface $loop, float $interval, callable $check, $value = null): PromiseInterface
{
    $deferred = new Deferred();
    $loop->addPeriodicTimer($interval, static function (TimerInterface $timer) use ($deferred, $check, $value, $loop): void {
        $result = $check($value);
        if ($result === false) {
            return;
        }

        $loop->cancelTimer($timer);
        $deferred->resolve($result);
    });

    return $deferred->promise();
}

/**
 * Promise that resolves once $check returns something other then false. Runs at future tick interval.
 *
 * @param LoopInterface $loop       ReactPHP event loop.
 * @param callable      $check      Callable to run at tick.
 * @param mixed         $value      Value to pass into $check on tick.
 * @param int           $iterations Number of iterations to call $check in one tick.
 */
function tickingFuturePromise(LoopInterface $loop, callable $check, $value = null, int $iterations = 1): PromiseInterface
{
    return new Promise(static function (callable $resolve) use ($loop, $check, $iterations, $value): void {
        $runCheck = static function () use ($loop, $check, &$runCheck, $resolve, $iterations, $value): void {
            for ($i = 0; $i <= $iterations; $i++) {
                $result = $check($value);
                if ($result !== false) {
                    $resolve($result);

                    return;
                }
            }

            futurePromise($loop)->then($runCheck);
        };

        futurePromise($loop)->then($runCheck);
    });
}

/**
 * Sandwich a $function call within two futureTicks.
 *
 * @param LoopInterface $loop     ReactPHP event loop.
 * @param mixed         $value    Value to pass into $function.
 * @param callable      $function Function to wrap.
 */
function futureFunctionPromise(LoopInterface $loop, $value, callable $function): PromiseInterface
{
    /** @psalm-suppress MissingClosureParamType */
    return futurePromise($loop, $value)->then(static fn ($value): PromiseInterface => futurePromise($loop, $function($value)));
}
