<?php

declare(strict_types=1);

namespace WyriHaximus\React;

use React\EventLoop\Loop;
use React\EventLoop\TimerInterface;
use React\Promise\Deferred;
use React\Promise\Promise;
use React\Promise\PromiseInterface;

/**
 * Promise that resolves once future tick is called.
 *
 * @param mixed $value Value to return on resolve.
 */
function futurePromise($value = null): PromiseInterface
{
    $deferred = new Deferred();
    Loop::futureTick(static function () use ($deferred, $value): void {
        $deferred->resolve($value);
    });

    return $deferred->promise();
}

/**
 * Promise that resolves after $interval has passed.
 *
 * @param float $interval The number of seconds to wait before execution.
 * @param mixed $value    Value to return on resolve.
 */
function timedPromise(float $interval, $value = null): PromiseInterface
{
    $deferred = new Deferred();
    Loop::addTimer($interval, static function () use ($deferred, $value): void {
        $deferred->resolve($value);
    });

    return $deferred->promise();
}

/**
 * Promise that resolves once $check returns something other then false. Runs at periodic $interval.
 *
 * @param float    $interval The number of seconds between each interval to run $check.
 * @param callable $check    Callable to run at the specified $interval.
 * @param mixed    $value    Value to pass into $check on tick.
 */
function tickingPromise(float $interval, callable $check, $value = null): PromiseInterface
{
    $deferred = new Deferred();
    Loop::addPeriodicTimer($interval, static function (TimerInterface $timer) use ($deferred, $check, $value): void {
        $result = $check($value);
        if ($result === false) {
            return;
        }

        Loop::cancelTimer($timer);
        $deferred->resolve($result);
    });

    return $deferred->promise();
}

/**
 * Promise that resolves once $check returns something other then false. Runs at future tick interval.
 *
 * @param callable $check      Callable to run at tick.
 * @param mixed    $value      Value to pass into $check on tick.
 * @param int      $iterations Number of iterations to call $check in one tick.
 */
function tickingFuturePromise(callable $check, $value = null, int $iterations = 1): PromiseInterface
{
    return new Promise(static function (callable $resolve) use ($check, $iterations, $value): void {
        $runCheck = static function () use ($check, &$runCheck, $resolve, $iterations, $value): void {
            for ($i = 0; $i <= $iterations; $i++) {
                $result = $check($value);
                if ($result !== false) {
                    $resolve($result);

                    return;
                }
            }

            futurePromise()->then($runCheck);
        };

        futurePromise()->then($runCheck);
    });
}

/**
 * Sandwich a $function call within two futureTicks.
 *
 * @param mixed    $value    Value to pass into $function.
 * @param callable $function Function to wrap.
 */
function futureFunctionPromise($value, callable $function): PromiseInterface
{
    /** @psalm-suppress MissingClosureParamType */
    return futurePromise($value)->then(static fn ($value): PromiseInterface => futurePromise($function($value)));
}
