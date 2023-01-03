<?php

namespace Yumi\Commands;

use Carbon\Carbon;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Yumi\Models\Statistics;

class StatsCommand extends UserCommand
{
    protected $name = 'stats';
    protected $usage = '/stats [дни/дата]';
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
                )->join("\n");

            return $this->replyToChat(
                "Статистика за несколько дней:\n".$stats,
                [
                    'reply_to_message_id' => $this->getMessage()->getMessageId()
                ]
            );
        } elseif (Carbon::canBeCreatedFromFormat($argument, 'd#m#Y')
            || $argument === ''
        ) {
            $date = $argument === '' ? Carbon::now()->startOfDay()
                : Carbon::createFromFormat('d#m#Y', $argument);
            $stats = Statistics::query()
                ->whereDate('date', $date)
                ->orderByDesc('messages')
                ->with(['user'])
                ->get()->map(
                    static fn(Statistics $stat) => sprintf(
                        '%s: %d',
                        $stat->user->name,
                        $stat->messages
                    )
                )->join("\n");

            return $this->replyToChat(
                "Детализированная статистика за ".$date->format('d.m.Y').":\n"
                .$stats,
                [
                    'reply_to_message_id' => $this->getMessage()->getMessageId()
                ]
            );
        }
        return $this->replyToChat(
            "Введите дату или количество дней. Ваш аргумент ".$argument
            ." не подходит",
            [
                'reply_to_message_id' => $this->getMessage()->getMessageId()
            ]
        );
    }
}