<?php

namespace WyriHaximus\React;

use React\EventLoop\LoopInterface;
use React\Promise\Deferred;

class TickingFuturePromise
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
     * Deferred to resolve once $check has returned a value.
     *
     * @var Deferred
     */
    protected $deferred;

    /**
     * Factory used by tickingFuturePromise, see there for more details.
     *
     * @param LoopInterface $loop  ReactPHP event loop.
     * @param callable      $check Callable to run at the future tick.
     *
     * @return mixed
     */
    public static function create(LoopInterface $loop, callable $check)
    {
        return (new self($loop, $check))->run();
    }

    /**
     * Hidden constructor, let the factory handle it.
     *
     * @param LoopInterface $loop  ReactPHP event loop.
     * @param callable      $check Callable to run at the future tick.
     */
    private function __construct(LoopInterface $loop, callable $check)
    {
        $this->loop = $loop;
        $this->check = $check;
        $this->deferred = new Deferred();
    }

    /**
     * Run the ticking future promise.
     *
     * @return \React\Promise\Promise
     */
    protected function run()
    {
        futurePromise($this->loop)->then(function () {
            $this->check();
        });
        return $this->deferred->promise();
    }

    /**
     * Run the $check callable and resolve when needed.
     *
     * @return void
     */
    protected function check()
    {
        $check = $this->check;
        $result = $check();
        if ($result !== false) {
            $this->deferred->resolve($result);
            return;
        }

        futurePromise($this->loop)->then(function () {
            $this->check();
        });
    }
}
