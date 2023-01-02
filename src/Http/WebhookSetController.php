<?php

namespace Yumi\Http;

use Longman\TelegramBot\Exception\TelegramException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Yumi\Application;

class WebhookSetController implements Controller
{
    /**
     * @throws TelegramException
     */
    public function __invoke(Application $application): Response
    {
        $result = app()->getTelegram()->setWebhook($_ENV['BOT_HOOK_URI']);
        return new JsonResponse($result->getRawData());
    }
}