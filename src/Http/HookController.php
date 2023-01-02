<?php

namespace Yumi\Http;

use Longman\TelegramBot\Exception\TelegramException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HookController implements Controller
{
    /**
     * @throws TelegramException
     */
    public function __invoke(Request $request): Response
    {
        global $TELEGRAM;
        $TELEGRAM->addCommandsPath(__DIR__.'/../Commands');

        return new JsonResponse(['handled' => $TELEGRAM->handle()]);
    }
}
