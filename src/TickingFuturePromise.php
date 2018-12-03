<?php declare(strict_types=1);

namespace WyriHaximus\React;

use React\EventLoop\LoopInterface;
use React\Promise\Deferred;

// @codingStandardsIgnoreStart
class TickingFuturePromise
// @codingStandardsIgnoreEnd
{
    /**
     * ReactPHP event loop.
     *
     * @var LoopInterface
     */
    protected $loop;

    /**
     * Callable to run at the future tick.
     *
     * @var callable
     */
    protected $check;

    /**
     * The value to pass into $check at each tick.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Number of iterations to call $check in one tick.
     *
     * @var int
     */
    protected $iterations;

    /**
     * Deferred to resolve once $check has returned a value.
     *
     * @var Deferred
     */
    protected $deferred;

    /**
     * Hidden constructor, let the factory handle it.
     *
     * @param LoopInterface $loop       ReactPHP event loop.
     * @param callable      $check      Callable to run at the future tick.
     * @param mixed         $value      Value to pass into $check on tick.
     * @param int           $iterations Number of iterations to call $check in one tick.
     */
    private function __construct(LoopInterface $loop, callable $check, $value, $iterations)
    {
        if (!\is_integer($iterations) || $iterations < 1) {
            throw new \InvalidArgumentException('Iterations must be an integer above zero');
        }

        $this->loop = $loop;
        $this->check = $check;
        $this->value = $value;
        $this->iterations = $iterations;
        $this->deferred = new Deferred();
    }

    /**
     * Factory used by tickingFuturePromise, see there for more details.
     *
     * @param LoopInterface $loop       ReactPHP event loop.
     * @param callable      $check      Callable to run at the future tick.
     * @param mixed         $value      Value to pass into $check on tick.
     * @param int           $iterations Number of iterations to call $check in one tick.
     *
     * @return mixed
     */
    public static function create(LoopInterface $loop, callable $check, $value = null, $iterations = 1)
    {
        return (new self($loop, $check, $value, $iterations))->run();
    }

    /**
     * Run the ticking future promise.
     *
     * @return \React\Promise\Promise
     */
    protected function run()
    {
        futurePromise($this->loop)->then(function (): void {
            $this->check();
        });

        return $this->deferred->promise();
    }

    /**
     * Run the $check callable and resolve when needed.
     *
     */
    protected function check(): void
    {
        $check = $this->check;

        for ($i = 0; $i <= $this->iterations; $i++) {
            $result = $check($this->value);
            if ($result !== false) {
                $this->deferred->resolve($result);

                return;
            }
        }

        futurePromise($this->loop)->then(function (): void {
            $this->check();
        });
    }
}
