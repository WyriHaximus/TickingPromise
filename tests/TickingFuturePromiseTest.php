<?php declare(strict_types=1);

namespace WyriHaximus\React;

use ApiClients\Tools\TestUtilities\TestCase;
use React\EventLoop\Factory;

/**
 * @internal
 */
final class TickingFuturePromiseTest extends TestCase
{
    public function testCreate(): void
    {
        \gc_collect_cycles();

        $fired = [
            false,
            false,
            false,
        ];
        $i = -1;

        $callback = function () use (&$i, &$fired) {
            $i++;
            $fired[$i] = true;
            switch ($i) {
                case 0:
                case 1:
                    return false;
                    break;
                default:
                case 2:
                    return 'foo.bar';
                    break;
            }
        };

        $loop = Factory::create();

        $promise = TickingFuturePromise::create($loop, $callback);
        $loop->run();
        $this->assertInstanceOf('React\Promise\PromiseInterface', $promise);

        $callbackCalled = false;
        $promise->then(function ($result) use (&$callbackCalled): void {
            $this->assertSame('foo.bar', $result);
            $callbackCalled = true;
        });
        $this->assertTrue($callbackCalled);
        foreach ($fired as $fire) {
            $this->assertTrue($fire);
        }

        unset($promise);

        $this->assertSame(0, \gc_collect_cycles());
    }

    public function provideCreateIterations()
    {
        return [
            [1],
            [2],
            [250],
            [30000],
        ];
    }

    /**
     * @dataProvider provideCreateIterations
     * @param mixed $iterations
     */
    public function testCreateIterations($iterations): void
    {
        \gc_collect_cycles();

        $fired = [
            false,
        ];
        for ($j = 0; $j < $iterations; $j++) {
            $fired[] = false;
        }
        $i = -1;

        $callback = function () use (&$i, &$fired, $iterations) {
            $i++;
            $fired[$i] = true;
            switch ($i) {
                case $iterations:
                    return 'foo.bar';
                    break;
                default:
                    return false;
                    break;
            }
        };

        $loop = Factory::create();

        $promise = TickingFuturePromise::create($loop, $callback, null, $iterations);
        $loop->run();
        $this->assertInstanceOf('React\Promise\PromiseInterface', $promise);

        $callbackCalled = false;
        $promise->then(function ($result) use (&$callbackCalled): void {
            $this->assertSame('foo.bar', $result);
            $callbackCalled = true;
        });
        $this->assertTrue($callbackCalled);
        foreach ($fired as $fire) {
            $this->assertTrue($fire);
        }

        unset($promise);

        $this->assertSame(0, \gc_collect_cycles());
    }

    public function provideInvalidIterations()
    {
        return [
            [-1],
            ['abc'],
            [new \stdClass()],
            [true],
            [false],
            [null],
        ];
    }

    /**
     * @dataProvider provideInvalidIterations
     * @param mixed $iterations
     */
    public function testInvalidIterations($iterations): void
    {
        $this->expectException('\InvalidArgumentException', 'Iterations must be an integer above zero');

        \gc_collect_cycles();

        $promise = TickingFuturePromise::create(Factory::create(), function (): void {
        }, null, $iterations);
        unset($promise);

        $this->assertSame(0, \gc_collect_cycles());
    }
}
