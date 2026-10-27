<?php

declare(strict_types=1);

use function WyriHaximus\React\futurePromise;

require __DIR__ . '/../vendor/autoload.php';

futurePromise()->then(static function (): void {
    echo DateTime::createFromFormat('U', time())->format('r'), PHP_EOL;
});
