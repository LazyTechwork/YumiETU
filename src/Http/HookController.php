<?php

namespace Yumi\Http;

use Longman\TelegramBot\Exception\TelegramException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HookController implements Controller
{
    public function __invoke(Request $request): Response
    {
        global $TELEGRAM;
        try {
            $TELEGRAM->addCommandsPath(__DIR__.'/../Commands');

            return new JsonResponse(['handled' => $TELEGRAM->handle()]);
        } catch (TelegramException $e) {
            return new Response($e->getMessage(), $e->getCode());
        }
    }
}