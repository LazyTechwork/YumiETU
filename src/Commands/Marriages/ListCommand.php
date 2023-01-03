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
        logger()->debug(
            'Found '.$marriages->count().' marriages.',
            $marriages->toArray()
        );
        $result = Request::sendMessage([
            'chat_id' => $this->getMessage()->getChat()->getId(),
            'text' => "Список браков:\n".$marriages
                    ->map(static fn(Marriage $marriage) => sprintf(
                        '<a href="tg://user?id=%d">%s</a> и <a href="tg://user?id=%d">%s</a> (%d дней)',
                        $marriage->husband->telegram_id,
                        $marriage->husband->name,
                        $marriage->wife->telegram_id,
                        $marriage->wife->name,
                        $marriage->daysSinceMarriage
                    ))->join("\n"),
            'parse_mode' => 'HTML'
        ]);
        if (!$result->isOk()) {
            logger()->error('Send message failed', $result->getRawData());
        }

        return $result;
    }
}