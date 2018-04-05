<?php

namespace WyriHaximus\React\Tests;

use React\EventLoop\Factory;

class FunctionsTest extends \PHPUnit_Framework_TestCase
{


    public function testFuturePromise()
    {
        $inputData = 'foo.bar';

        $loop = $this->mockLoop();

        $loop
            ->expects($this->once())
            ->method('futureTick')
            ->with($this->isType('callable'))
            ->will($this->returnCallback(function ($resolveCb) {
                $resolveCb('foo.bar' . (string)microtime(true));
            }))
        ;

        $promise = \WyriHaximus\React\futurePromise($loop, $inputData);
        $this->assertInstanceOf('React\Promise\PromiseInterface', $promise);

        $callbackCalled = false;
        $promise->then(function ($data) use (&$callbackCalled, $inputData) {
            $this->assertSame($inputData, $data);
            $callbackCalled = true;
        });
        $this->assertTrue($callbackCalled);
    }

    public function testNextPromise()
    {
        $inputData = 'foo.bar';
        $loop = $this->mockLoop();

        $loop
            ->expects($this->once())
            ->method('futureTick')
            ->with($this->isType('callable'))
            ->will($this->returnCallback(function ($resolveCb) {
                $resolveCb('foo.bar' . (string)microtime(true));
            }))
        ;

        $promise = \WyriHaximus\React\nextPromise($loop, $inputData);
        $this->assertInstanceOf('React\Promise\PromiseInterface', $promise);

        $callbackCalled = false;
        $promise->then(function ($data) use (&$callbackCalled, $inputData) {
            $this->assertSame($inputData, $data);
            $callbackCalled = true;
        });
        $this->assertTrue($callbackCalled);
    }

    public function testTimedPromise()
    {
        $inputData = 'foo.bar';
        $loop = $this->mockLoop();

        $loop
            ->expects($this->once())
            ->method('addTimer')
            ->with($this->isType('numeric'), $this->isType('callable'))
            ->will($this->returnCallback(function ($interval, $resolveCb) {
                $resolveCb('foo.bar' . (string)microtime(true));
            }))
        ;

        $promise = \WyriHaximus\React\timedPromise($loop, 123, $inputData);
        $this->assertInstanceOf('React\Promise\PromiseInterface', $promise);

        $callbackCalled = false;
        $promise->then(function ($data) use (&$callbackCalled, $inputData) {
            $this->assertSame($inputData, $data);
            $callbackCalled = true;
        });
        $this->assertTrue($callbackCalled);
    }

    public function testTickingPromise()
    {
        $loop = Factory::create();

        $inputData = 'foo.bar';
        $fired = [
            false,
            false,
            false,
        ];
        $i = -1;
        $callback = function($data) use (&$i, &$fired, $inputData) {
            $this->assertSame($inputData, $data);
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

        $promise = \WyriHaximus\React\tickingPromise($loop, 1, $callback, $inputData);
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

    public function testTickingFuturePromise()
    {
        $this->assertInstanceOf('\React\Promise\Promise', \WyriHaximus\React\tickingFuturePromise($this->getMock('React\EventLoop\LoopInterface'), function () {}));
    }

    public function providerFutureFunctionPromise()
    {
        return [
            [
                'foo.bar',
                'rab.oof',
                'strrev',
            ],
            [
                'Qrny jvgu vg!',
                'Deal with it!',
                'str_rot13',
            ],
            [
                'Deal with it!',
                'Deal with it!',
                function ($str) {
                    return str_rot13(str_rot13($str));
                },
            ],
            [
                'abcr',
                'nope',
                'str_rot13',
            ],
            [
                'return "nope! NOPE! nope! NOPE! nope! NOPE! nope! NOPE! nope! NOPE!";',
                'nope! NOPE! nope! NOPE! nope! NOPE! nope! NOPE! nope! NOPE!',
                function ($str) {
                    return eval($str);
                },
            ],
        ];
    }

    /**
     * @dataProvider providerFutureFunctionPromise
     */
    public function testFutureFunctionPromise($inputData, $outputDate, $function)
    {
        $loop = $this->mockLoop();

        $loop
            ->expects($this->any())
            ->method('futureTick')
            ->with($this->isType('callable'))
            ->will($this->returnCallback(function ($resolveCb) {
                $resolveCb('foo.bar' . (string)microtime(true));
            }))
        ;

        $promise = \WyriHaximus\React\futureFunctionPromise($loop, $inputData, $function);
        $this->assertInstanceOf('React\Promise\PromiseInterface', $promise);

        $callbackCalled = false;
        $promise->then(function ($data) use (&$callbackCalled, $outputDate) {
            $this->assertSame($outputDate, $data);
            $callbackCalled = true;
        });
        $this->assertTrue($callbackCalled);
    }

    protected function mockLoop()
    {
        return $this->getMock('React\EventLoop\LoopInterface', [
            'futureTick',
            'nextTick',
            'addReadStream',
            'addWriteStream',
            'removeReadStream',
            'removeWriteStream',
            'removeStream',
            'addTimer',
            'addPeriodicTimer',
            'cancelTimer',
            'isTimerActive',
            'addSignal',
            'removeSignal',
            'run',
            'stop',
            'tick',
        ]);
    }
}
