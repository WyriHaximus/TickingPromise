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
}
