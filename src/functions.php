<?php

declare(strict_types=1);

namespace WyriHaximus\React;

use React\EventLoop\Loop;
use React\EventLoop\TimerInterface;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;

/**
 * @param T $value Value to return on resolve.
 *
 * @return PromiseInterface<T>
 *
 * @template T
 * Promise that resolves once future tick is called.
 *
 * @phpstan-ignore ergebnis.noParameterWithNullDefaultValue
 */
function futurePromise(mixed $value = null): PromiseInterface
{
    /** @var Deferred<T> $deferred */
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
 * @param T     $value    Value to return on resolve.
 *
 * @return PromiseInterface<T>
 *
 * @template T
 *
 * @phpstan-ignore ergebnis.noParameterWithNullDefaultValue
 */
function timedPromise(float $interval, mixed $value = null): PromiseInterface
{
    /** @var Deferred<T> $deferred */
    $deferred = new Deferred();
    Loop::addTimer($interval, static function () use ($deferred, $value): void {
        $deferred->resolve($value);
    });

    return $deferred->promise();
}

/**
 * Promise that resolves once $check returns something other then false. Runs at periodic $interval.
 *
 * @param float                          $interval The number of seconds between each interval to run $check.
 * @param callable(mixed): (false|mixed) $check    Callable to run at the specified $interval.
 * @param T                              $value    Value to pass into $check on tick.
 *
 * @return PromiseInterface<T>
 *
 * @template T
 *
 * @phpstan-ignore ergebnis.noParameterWithNullDefaultValue
 */
function tickingPromise(float $interval, callable $check, mixed $value = null): PromiseInterface
{
    /** @var Deferred<T> $deferred */
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
 * @param (callable(?mixed): (T|false)) $check      Callable to run at tick.
 * @param mixed                         $value      Value to pass into $check on tick.
 * @param int                           $iterations Number of iterations to call $check in one tick.
 *
 * @return PromiseInterface<(T is void ? null : T)>
 *
 * @template T
 *
 * @phpstan-ignore ergebnis.noParameterWithNullDefaultValue
 */
function tickingFuturePromise(callable $check, mixed $value = null, int $iterations = 1): PromiseInterface
{
    /** @var Deferred<T> $deferred */
    $deferred = new Deferred();
    $runCheck = static function () use ($check, &$runCheck, $deferred, $iterations, $value): void {
        for ($i = 0; $i <= $iterations; $i++) {
            $result = $check($value);
            if ($result !== false) {
                $runCheck = null;
                $deferred->resolve($result);

                return;
            }
        }

        futurePromise()->then($runCheck);
    };

    futurePromise()->then($runCheck);

    return $deferred->promise();
}

/**
 * Sandwich a $function call within two futureTicks.
 *
 * @param T                $value    Value to pass into $function.
 * @param (callable(T): T) $function Function to wrap.
 *
 * @return PromiseInterface<T>
 *
 * @template T
 */
function futureFunctionPromise(mixed $value, callable $function): PromiseInterface
{
    /**
     * @param T $value
     *
     * @return PromiseInterface<T>
     */
    $handler = static fn (mixed $value): PromiseInterface => futurePromise($function($value));

    return futurePromise($value)->then($handler);
}
