<?php declare(strict_types=1);

require \dirname(__DIR__) . '/vendor/autoload.php';

\WyriHaximus\React\futureFunctionPromise(\json_encode([
    'time' => \time(),
]), 'json_decode')->then(function ($json): void {
    echo DateTime::createFromFormat('U', $json->time)->format('r'), \PHP_EOL;
});
