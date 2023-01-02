<?php

declare(strict_types=1);

use Yumi\Application;

require __DIR__.'/../vendor/autoload.php';

(new Application())->handleRequest()->send();
