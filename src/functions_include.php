<?php

declare(strict_types=1);

namespace WyriHaximus\React;

use function function_exists;

// @codeCoverageIgnoreStart
if (! function_exists('WyriHaximus\React\futurePromise')) {
    require __DIR__ . '/functions.php';
}
// @codeCoverageIgnoreEnd
