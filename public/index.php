<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

require __DIR__.'/../vendor/autoload.php';

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
    /** @var Response $response */
    $response = (new $params['controller'])($request);
    return $response->send();
} catch (ResourceNotFoundException $notFoundException) {
    return (new Response('Not found.', 404))->send();
} catch (Exception $e) {
    return (new Response('Internal server error.', 500))->send();
}