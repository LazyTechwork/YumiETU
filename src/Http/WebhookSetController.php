<?php

namespace Yumi\Http;

use Longman\TelegramBot\Exception\TelegramException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WebhookSetController implements Controller
{
    public function __invoke(Request $request): Response
    {
        global $TELEGRAM, $CONFIG;

        try {
            $result = $TELEGRAM->setWebhook($CONFIG['hook_uri']);
            if ($result->isOk()) {
                return new Response($result->toJson());
            }
        } catch (TelegramException $e) {
            return new Response($e->getMessage(), $e->getCode());
        }

        return new Response('Test', 200);
    }

}