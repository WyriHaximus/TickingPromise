# TickingPromise

![Continuous Integration](https://github.com/wyrihaximus/TickingPromise/workflows/Continuous%20Integration/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/wyrihaximus/ticking-promise/v/stable.png)](https://packagist.org/packages/wyrihaximus/ticking-promise)
[![Total Downloads](https://poser.pugx.org/wyrihaximus/ticking-promise/downloads.png)](https://packagist.org/packages/wyrihaximus/ticking-promise/stats)
[![Code Coverage](https://coveralls.io/repos/github/WyriHaximus/TickingPromise/badge.svg?branchmaster)](https://coveralls.io/github/WyriHaximus/TickingPromise?branch=master)
[![Type Coverage](https://shepherd.dev/github/WyriHaximus/TickingPromise/coverage.svg)](https://shepherd.dev/github/WyriHaximus/TickingPromise)
[![License](https://poser.pugx.org/wyrihaximus/ticking-promise/license.png)](https://packagist.org/packages/wyrihaximus/ticking-promise)

Wrapping event loop ticks into a promise. 

## Install ##

To install via [Composer](http://getcomposer.org/), use the command below, it will automatically detect the latest version and bind it with `~`.

```
composer require wyrihaximus/ticking-promise 
```

## Example ##

```php
<?php

declare(strict_types=1);

use function WyriHaximus\React\futurePromise;

futurePromise()->then(static function (): void {
    echo 'Done', PHP_EOL;
});
futurePromise()->then(static function (string $message): void {
    echo $message, PHP_EOL;
}, 'Also done');
```

For more examples check the [examples directory](https://github.com/WyriHaximus/TickingPromise/tree/master/examples).

## License ##

Copyright 2021 [Cees-Jan Kiewiet](http://wyrihaximus.net/)

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.
