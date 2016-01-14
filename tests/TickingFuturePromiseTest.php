<?php

namespace WyriHaximus\React;

use React\EventLoop\Factory;

class TickingFuturePromiseTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $fired = [
            false,
            false,
            false,
        ];
        $i = -1;

        $callback = function() use (&$i, &$fired) {
            $i++;
            $fired[$i] = true;
            switch($i)
            {
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
        $promise->then(function ($result) use (&$callbackCalled) {
            $this->assertSame('foo.bar', $result);
            $callbackCalled = true;
        });
        $this->assertTrue($callbackCalled);
        foreach ($fired as $fire) {
            $this->assertTrue($fire);
        }
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
     */
    public function testCreateIterations($iterations)
    {
        $fired = [
            false,
        ];
        for ($j = 0; $j < $iterations; $j++) {
            $fired[] = false;
        }
        $i = -1;

        $callback = function() use (&$i, &$fired, $iterations) {
            $i++;
            $fired[$i] = true;
            switch($i)
            {
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
        $promise->then(function ($result) use (&$callbackCalled) {
            $this->assertSame('foo.bar', $result);
            $callbackCalled = true;
        });
        $this->assertTrue($callbackCalled);
        foreach ($fired as $fire) {
            $this->assertTrue($fire);
        }
    }

    public function provideInvalidIterations()
    {
        return [
            [-1],
            ['abc'],
            [new \stdClass],
            [true],
            [false],
            [null],
        ];
    }

    /**
     * @dataProvider provideInvalidIterations
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Iterations must be an integer above zero
     */
    public function testInvalidIterations($iterations)
    {
        TickingFuturePromise::create(Factory::create(), function () {}, null, $iterations);
    }
}
