<?php declare(strict_types=1);

namespace WyriHaximus\React\Tests;

use ApiClients\Tools\TestUtilities\TestCase;
use React\EventLoop\Factory;
use function WyriHaximus\React\futurePromise;
use function WyriHaximus\React\nextPromise;
use function WyriHaximus\React\tickingFuturePromise;
use function WyriHaximus\React\tickingPromise;
use function WyriHaximus\React\timedPromise;

/**
 * @internal
 */
class FunctionsTest extends TestCase
{
    public function testFuturePromise(): void
    {
        \gc_collect_cycles();

        $inputData = 'foo.bar';

        $loop = Factory::create();

        $promise = futurePromise($loop, $inputData);
        $data = $this->await($promise, $loop);
        self::assertSame($inputData, $data);

        unset($promise);

        self::assertSame(0, \gc_collect_cycles());
    }

    public function testNextPromise(): void
    {
        \gc_collect_cycles();

        $inputData = 'foo.bar';

        $loop = Factory::create();

        $promise = nextPromise($loop, $inputData);
        $data = $this->await($promise, $loop);
        self::assertSame($inputData, $data);

        unset($promise);

        self::assertSame(0, \gc_collect_cycles());
    }

    public function testTimedPromise(): void
    {
        \gc_collect_cycles();

        $inputData = 'foo.bar';

        $loop = Factory::create();

        $promise = timedPromise($loop, 0.23, $inputData);
        $data = $this->await($promise, $loop);
        self::assertSame($inputData, $data);

        unset($promise);

        self::assertSame(0, \gc_collect_cycles());
    }

    public function testTickingPromise(): void
    {
        \gc_collect_cycles();

        $loop = Factory::create();

        $inputData = 'foo.bar';
        $fired = [
            false,
            false,
            false,
        ];
        $i = -1;
        $callback = function ($data) use (&$i, &$fired, $inputData) {
            $this->assertSame($inputData, $data);
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

        $promise = tickingPromise($loop, 1, $callback, $inputData);
        $data = $this->await($promise, $loop);
        self::assertSame($inputData, $data);

        foreach ($fired as $fire) {
            self::assertTrue($fire);
        }
        unset($promise);

        self::assertSame(0, \gc_collect_cycles());
    }

    public function testTickingFuturePromise(): void
    {
        \gc_collect_cycles();

        $loop = Factory::create();
        $promise = tickingFuturePromise($loop, function () {
            return true;
        });
        $this->assertInstanceOf('\React\Promise\Promise', $promise);
        $loop->run();
        unset($promise);

        $this->assertSame(0, \gc_collect_cycles());
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
                    return \str_rot13(\str_rot13($str));
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
     * @param mixed $inputData
     * @param mixed $outputDate
     * @param mixed $function
     */
    public function testFutureFunctionPromise($inputData, $outputDate, $function): void
    {
        \gc_collect_cycles();

        $loop = Factory::create();
        $promise = \WyriHaximus\React\futureFunctionPromise($loop, $inputData, $function);

        $data = $this->await($promise, $loop);
        $this->assertSame($outputDate, $data);

        unset($promise);

        $this->assertSame(0, \gc_collect_cycles());
    }
}
