<?php

namespace Yumi\Commands\Marriages;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Yumi\Models\Marriage;

class ListCommand extends UserCommand
{
    protected $name = 'marriages';
    protected $description = 'Список браков';
    protected $usage = '/marriages';

    public function execute(): ServerResponse
    {
        $marriages = Marriage::query()
            ->whereNull('divorced_since')
            ->with(['husband', 'wife'])
            ->orderBy('married_since')
            ->get();
        return Request::sendMessage([
            'chat_id' => $this->getMessage()->getChat()->getId(),
            'text' => "Список браков:\n".$marriages
                    ->map(static fn(Marriage $marriage) => sprintf(
                        '[%s](tg://user?id=%d) и [%s](tg://user?id=%d) (%d дней)',
                        $marriage->husband->name,
                        $marriage->husband->telegram_id,
                        $marriage->wife->name,
                        $marriage->wife->telegram_id,
                        $marriage->daysSinceMarriage
                    ))->join("\n")
        ]);
    }
}