<?php

declare(strict_types=1);

use function WyriHaximus\React\futureFunctionPromise;

require dirname(__DIR__) . '/vendor/autoload.php';

futureFunctionPromise(json_encode([
    'time' => time(),
]), 'json_decode')->then(static function ($json): void {
    echo DateTime::createFromFormat('U', $json->time)->format('r'), PHP_EOL;
});
