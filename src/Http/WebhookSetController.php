<?php

namespace Yumi\Http;

use Longman\TelegramBot\Exception\TelegramException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WebhookSetController implements Controller
{
    /**
     * @throws TelegramException
     */
    public function __invoke(Request $request): Response
    {
        $result = app()->getTelegram()->setWebhook($_ENV['BOT_HOOK_URI']);
        return new JsonResponse($result->getRawData());
    }
}