<?php

namespace Yumi\Commands\Marriages;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Longman\TelegramBot\Commands\AdminCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Yumi\Models\Marriage;
use Yumi\Models\User;

class DivorceCommand extends AdminCommand
{
    protected $name = 'divorce';
    protected $description = 'Расторгнуть брак между пользователями';
    protected $usage = '/divorce <муж> <жена> [дата]';
    protected $private_only = false;

    public function execute(): ServerResponse
    {
        $args = explode(' ', trim($this->getMessage()->getText(true)), 3);
        if (!is_numeric($args[0]) || !is_numeric($args[1])
            || (count($args) === 3
                && !Carbon::canBeCreatedFromFormat($args[2], "!d#m#Y"))
        ) {
            return $this->replyToChat(
                'Введены неправильные параметры',
                ['reply_to_message_id' => $this->getMessage()->getMessageId()]
            );
        }

        $husband_id = (int)$args[0];
        $wife_id = (int)$args[1];

        $users = User::query()->whereIn('id', [$husband_id, $wife_id])->limit(2)
            ->get();
        if ($users->count() < 2) {
            return $this->replyToChat(
                'Некоторые пользователи не были найдены',
                ['reply_to_message_id' => $this->getMessage()->getMessageId()]
            );
        }

        /** @var User $husband */
        $husband = $users->firstWhere('id', '=', $husband_id);
        /** @var User $wife */
        $wife = $users->firstWhere('id', '=', $wife_id);

        /** @var Marriage[]|Collection $marriages */
        $marriages = Marriage::query()
            ->where(static function (Builder $q) use ($husband_id, $wife_id) {
                $q->where('husband_id', $husband_id)
                    ->where('wife_id', $wife_id)
                    ->orWhere('wife_id', $husband_id)
                    ->where('husband_id', $wife_id);
            })
            ->with(['wife', 'husband'])
            ->whereNull('divorced_since')
            ->orderBy('married_since')->get();

        if ($marriages->isEmpty()) {
            return $this->replyToChat(
                'Браков между пользователями не найдено',
                [
                    'reply_to_message_id' => $this->getMessage()->getMessageId(
                    ),
                    'parse_mode' => 'HTML'
                ]
            );
        }

        $date = count($args) === 3 ? Carbon::createFromFormat(
            "!d#m#Y",
            $args[2]
        )
            : Carbon::now();

        /** @var Marriage $marriage */
        $marriage = $marriages->first();
        $marriage->divorced_since = $date;
        $marriage->save();

        return $this->replyToChat(
            sprintf(
                'Брак между %s и %s расторгнут %s (регистрационный номер брака %d)',
                $husband->telegramMention,
                $wife->telegramMention,
                $date->format('d.m.Y H:i:s'),
                $marriage->id
            ),
            [
                'reply_to_message_id' => $this->getMessage()->getMessageId(),
                'parse_mode' => 'HTML'
            ]
        );
    }
}