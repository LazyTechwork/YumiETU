<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Yumi\Http\HookController;
use Yumi\Http\InfoController;
//use Yumi\Http\WebhookSetController;

return static function (RouteCollection $routes) {
    $routes->add(
        'info', new Route('/', ['controller' => InfoController::class])
    );
//    $routes->add(
//        'set', new Route('/set', ['controller' => WebhookSetController::class])
//    );
    $routes->add(
        'hook', new Route('/hook', ['controller' => HookController::class])
    );
};