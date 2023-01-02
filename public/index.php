<?php

declare(strict_types=1);

use Longman\TelegramBot\Telegram;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

require __DIR__.'/../vendor/autoload.php';

$logger = new Logger('MAIN');
$logger->pushHandler(
    new StreamHandler(__DIR__.'/../logs/yumi-log.log', Level::Warning)
);

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../.env');

$CONFIG = [
    'api_key' => $_ENV['BOT_API_KEY'],
    'username' => $_ENV['BOT_USERNAME'],
    'hook_uri' => $_ENV['BOT_HOOK_URI']
];

global $CONFIG;

$routes = new RouteCollection();

/** @var Closure $routes_function */
$routes_function = include __DIR__.'/../routes.php';
$routes_function($routes);

$request = Request::createFromGlobals();
$context = new RequestContext();
$context->fromRequest($request);

$matcher = new UrlMatcher($routes, $context);

try {
    $params = $matcher->match($request->getPathInfo());
    $TELEGRAM = new Telegram($CONFIG['api_key'], $CONFIG['username']);
    global $TELEGRAM;
    /** @var Response $response */
    $response = (new $params['controller'])($request);
    return $response->send();
} catch (ResourceNotFoundException $notFoundException) {
    return (new Response('Not found.', 404))->send();
} catch (Exception $e) {
    $logger->error($e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(), 'trace' => $e->getTrace()
    ]);
    return (new JsonResponse(
        [
            'exception' => $e->getMessage(), 'file' => $e->getFile(),
            'line' => $e->getLine(), 'trace' => $e->getTrace()
        ]
    ))->send();
}
