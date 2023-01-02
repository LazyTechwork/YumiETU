<?php

namespace Yumi\Http;

use Longman\TelegramBot\Exception\TelegramException;
use Monolog\Logger;
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
        /** @var Logger $LOG */
        global $TELEGRAM, $LOG;
        $TELEGRAM->addCommandsPath(__DIR__.'/../Commands');
        $LOG->info('Handled hook', $request->toArray());

        return new JsonResponse(['handled' => $TELEGRAM->handle()]);
    }
}
