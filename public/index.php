<?php

declare(strict_types=1);

use Symfony\Component\HttpFoundation\JsonResponse;
use Yumi\Application;

require __DIR__.'/../vendor/autoload.php';

try {
    (new Application())->handleRequest()->send();
} catch (Exception $e) {
    return (new JsonResponse(
        [
            'exception' => $e->getMessage(), 'file' => $e->getFile(),
            'line' => $e->getLine(), 'trace' => $e->getTrace()
        ]
    ))->send();
}
