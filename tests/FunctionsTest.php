<?php

namespace WyriHaximus\React\Tests;

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
}
