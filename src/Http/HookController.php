<?php

namespace Yumi\Http;

use Longman\TelegramBot\Exception\TelegramException;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Yumi\Commands\Kernel;

class HookController implements Controller
{
    /**
     * @throws TelegramException
     */
    public function __invoke(Request $request): Response
    {
        /** @var Logger $LOG */
        global $TELEGRAM, $LOG;
        $TELEGRAM->addCommandClasses((new Kernel())->commands());
        $LOG->info('Handled hook', $request->toArray());

        return new JsonResponse(['handled' => $TELEGRAM->handle()]);
    }
}
