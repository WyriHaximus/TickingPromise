<?php

namespace WyriHaximus\React\Tests;

use React\EventLoop\Factory;

class FunctionsTest extends \PHPUnit_Framework_TestCase
{
    public function testFuturePromise()
    {
        $loop = $this->getMock('React\EventLoop\StreamSelectLoop', [
            'futureTick',
        ]);

        $loop
            ->expects($this->once())
            ->method('futureTick')
            ->with($this->isType('callable'))
            ->will($this->returnCallback(function ($resolveCb) {
                $resolveCb('foo.bar' . (string)microtime(true));
            }))
        ;

        $promise = \WyriHaximus\React\futurePromise($loop);
        $this->assertInstanceOf('React\Promise\PromiseInterface', $promise);

        $callbackCalled = false;
        $promise->then(function () use (&$callbackCalled) {
            $callbackCalled = true;
        });
        $this->assertTrue($callbackCalled);
    }

    public function testNextPromise()
    {
        $loop = $this->getMock('React\EventLoop\StreamSelectLoop', [
            'nextTick',
        ]);

        $loop
            ->expects($this->once())
            ->method('nextTick')
            ->with($this->isType('callable'))
            ->will($this->returnCallback(function ($resolveCb) {
                $resolveCb('foo.bar' . (string)microtime(true));
            }))
        ;

        $promise = \WyriHaximus\React\nextPromise($loop);
        $this->assertInstanceOf('React\Promise\PromiseInterface', $promise);

        $callbackCalled = false;
        $promise->then(function () use (&$callbackCalled) {
            $callbackCalled = true;
        });
        $this->assertTrue($callbackCalled);
    }

    public function testTimedPromise()
    {
        $loop = $this->getMock('React\EventLoop\StreamSelectLoop', [
            'addTimer',
        ]);

        $loop
            ->expects($this->once())
            ->method('addTimer')
            ->with($this->isType('numeric'), $this->isType('callable'))
            ->will($this->returnCallback(function ($interval, $resolveCb) {
                $resolveCb('foo.bar' . (string)microtime(true));
            }))
        ;

        $promise = \WyriHaximus\React\timedPromise($loop, 123);
        $this->assertInstanceOf('React\Promise\PromiseInterface', $promise);

        $callbackCalled = false;
        $promise->then(function () use (&$callbackCalled) {
            $callbackCalled = true;
        });
        $this->assertTrue($callbackCalled);
    }

    public function testTickingPromise()
    {
        $loop = Factory::create();

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
                    return true;
                    break;
            }
        };

        $promise = \WyriHaximus\React\tickingPromise($loop, 1, $callback);
        $loop->run();
        $this->assertInstanceOf('React\Promise\PromiseInterface', $promise);

        $callbackCalled = false;
        $promise->then(function () use (&$callbackCalled) {
            $callbackCalled = true;
        });
        $this->assertTrue($callbackCalled);
        foreach ($fired as $fire) {
            $this->assertTrue($fire);
        }
    }
}
