<?php

namespace Yumi\Commands;

use Carbon\Carbon;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Yumi\Models\Statistics;

class StatsCommand extends UserCommand
{
    protected $name = 'stats';
    protected $usage = '/stats <дни/дата>';
    protected $description = 'Статистика за определённое количество дней или за определённую дату';

    public function execute(): ServerResponse
    {
        $argument = trim($this->getMessage()->getText(true));
        if (is_numeric($argument)) {
            $days = (int)$argument;
            $stats = Statistics::query()
                ->whereDate('date', '>=', Carbon::now()->subDays($days))
                ->groupBy('date', 'messages')
                ->sum('messages');

            return $this->replyToChat(
                sprintf(
                    '<pre>%s</pre>',
                    json_encode($stats, JSON_PRETTY_PRINT)
                ),
                [
                    'reply_to_message_id' => $this->getMessage()->getMessageId()
                ]
            );
        }
        return Request::emptyResponse();
    }
}