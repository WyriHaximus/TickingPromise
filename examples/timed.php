<?php

declare(strict_types=1);

use function WyriHaximus\React\timedPromise;

require dirname(__DIR__) . '/vendor/autoload.php';

echo 'Reporting back in 12 seconds', PHP_EOL;
echo DateTime::createFromFormat('U', time())->format('r'), PHP_EOL;
timedPromise(12)->then(static function (): void {
    echo DateTime::createFromFormat('U', time())->format('r'), PHP_EOL;
    echo 'Done', PHP_EOL;
});
