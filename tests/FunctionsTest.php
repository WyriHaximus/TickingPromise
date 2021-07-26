<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\React;

use React\EventLoop\Loop;
use WyriHaximus\AsyncTestUtilities\AsyncTestCase;

use function gc_collect_cycles;
use function str_rot13;
use function WyriHaximus\React\futureFunctionPromise;
use function WyriHaximus\React\futurePromise;
use function WyriHaximus\React\tickingFuturePromise;
use function WyriHaximus\React\tickingPromise;
use function WyriHaximus\React\timedPromise;

final class FunctionsTest extends AsyncTestCase
{
    public function testFuturePromise(): void
    {
        gc_collect_cycles();

        $inputData = 'foo.bar';

        $promise = futurePromise($inputData);
        $data    = $this->await($promise);
        self::assertSame($inputData, $data);

        unset($promise);

        self::assertSame(0, gc_collect_cycles());
    }

    public function testTimedPromise(): void
    {
        gc_collect_cycles();

        $inputData = 'foo.bar';

        $promise = timedPromise(0.23, $inputData);
        $data    = $this->await($promise);
        self::assertSame($inputData, $data);

        unset($promise);

        self::assertSame(0, gc_collect_cycles());
    }

    public function testTickingFuturePromise(): void
    {
        gc_collect_cycles();

        $promise = tickingFuturePromise(static fn (): bool => true);
        Loop::run();
        gc_collect_cycles();
        unset($promise);

        self::assertSame(0, gc_collect_cycles());
    }

    public function testTickingPromise(): void
    {
        gc_collect_cycles();

        $inputData = 'foo.bar';
        $fired     = [
            false,
            false,
            false,
        ];
        $i         = -1;
        $callback  = static function ($data) use (&$i, &$fired, $inputData) {
            self::assertSame($inputData, $data);
            $i++;
            $fired[$i] = true;
            if ($i === 0 || $i === 1) {
                    return false;
            }

            return 'foo.bar';
        };

        $promise = tickingPromise(1, $callback, $inputData);
        $data    = $this->await($promise);
        self::assertSame($inputData, $data);

        foreach ($fired as $fire) {
            self::assertTrue($fire);
        }

        unset($promise);

        self::assertSame(0, gc_collect_cycles());
    }

    /**
     * @return iterable<array<mixed>>
     */
    public function providerFutureFunctionPromise(): iterable
    {
        yield [
            'foo.bar',
            'rab.oof',
            'strrev',
        ];

        yield [
            'Qrny jvgu vg!',
            'Deal with it!',
            'str_rot13',
        ];

        yield [
            'Deal with it!',
            'Deal with it!',
            static fn (string $str): string => str_rot13(str_rot13($str)),
        ];

        yield [
            'abcr',
            'nope',
            'str_rot13',
        ];

        yield [
            'return "nope! NOPE! nope! NOPE! nope! NOPE! nope! NOPE! nope! NOPE!";',
            'nope! NOPE! nope! NOPE! nope! NOPE! nope! NOPE! nope! NOPE!',
            static fn (string $str): string => eval($str), // @phpstan-ignore-line
        ];
    }

    /**
     * @param mixed $inputData
     * @param mixed $outputDate
     * @param mixed $function
     *
     * @dataProvider providerFutureFunctionPromise
     */
    public function testFutureFunctionPromise($inputData, $outputDate, $function): void
    {
        gc_collect_cycles();

        $promise = futureFunctionPromise($inputData, $function);

        $data = $this->await($promise);
        self::assertSame($outputDate, $data);

        unset($promise);

        self::assertSame(0, gc_collect_cycles());
    }
}
