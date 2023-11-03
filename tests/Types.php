<?php

declare(strict_types=1);

use function PHPStan\Testing\assertType;
use function WyriHaximus\React\futureFunctionPromise;
use function WyriHaximus\React\futurePromise;
use function WyriHaximus\React\tickingFuturePromise;
use function WyriHaximus\React\tickingPromise;
use function WyriHaximus\React\timedPromise;

assertType('React\Promise\PromiseInterface<bool>', futurePromise(true));
assertType('React\Promise\PromiseInterface<null>', futurePromise());

assertType('React\Promise\PromiseInterface<bool>', timedPromise(1, true));
assertType('React\Promise\PromiseInterface<null>', timedPromise(1));

assertType('React\Promise\PromiseInterface<bool>', tickingPromise(1, static function (): void {
}, true));
assertType('React\Promise\PromiseInterface<null>', tickingPromise(1, static function (): void {
}));

assertType('React\Promise\PromiseInterface<bool>', tickingFuturePromise(static function (): void {
}, true));
assertType('React\Promise\PromiseInterface<null>', tickingFuturePromise(static function (): void {
}));

assertType('React\Promise\PromiseInterface<bool>', futureFunctionPromise(true, static function (): void {
}));
