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
                ->selectRaw('sum(messages) as messages, date')
                ->whereDate('date', '>=', Carbon::now()->subDays($days))
                ->groupBy('date')
                ->get()->map(
                    static fn($it) => sprintf(
                        "%s: %d",
                        $it->date->format('d.m.Y'),
                        $it->messages
                    )
                );

            return $this->replyToChat(
                $stats,
                [
                    'reply_to_message_id' => $this->getMessage()->getMessageId()
                ]
            );
        }
        return Request::emptyResponse();
    }
}