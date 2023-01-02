<?php

namespace Yumi\Http;

use Longman\TelegramBot\Exception\TelegramException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Yumi\Application;


class HookController implements Controller
{
    /**
     * @throws TelegramException
     */
    public function __invoke(Application $application): Response
    {
        return new JsonResponse(['handled' => app()->getTelegram()->handle()]);
    }
}
