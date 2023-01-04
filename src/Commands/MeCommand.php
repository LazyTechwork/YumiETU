<?php

namespace Yumi\Commands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Yumi\Models\Marriage;
use Yumi\Models\User;

class MeCommand extends UserCommand
{
    protected $name = 'me';
    protected $usage = '/me';
    protected $description = 'Получить информацию о себе';

    public function execute(): ServerResponse
    {
        $user = User::createFromCommand($this);
        $marriages = Marriage::query()
            ->where('wife_id', $user->id)
            ->orWhere('husband_id', $user->id)
            ->orderByDesc('married_since')
            ->with(['wife', 'husband'])
            ->get();

        return $this->replyToChat(
            sprintf(
                "Ваше имя: %s\n"
                ."Дата рождения: %s\n"
                ."Идентификатор: %d\n"
                ."Идентификатор Telegram: %d\n"
                ."Идентификатор ВКонтакте: %s\n"
                ."Акты смены гражданского состояния:\n%s",
                $user->telegramMention,
                $user->birthday ? $user->birthday->format('d.m.Y')
                    : 'не установлена',
                $user->id,
                $user->telegram_id,
                $user->vk_id ?? 'не связан',
                $marriages->map(
                    static fn(Marriage $marriage) => sprintf(
                        'Брак: %s и %s (рег. %d, %s)',
                        $marriage->husband->telegramMention,
                        $marriage->wife->telegramMention,
                        $marriage->id,
                        $marriage->divorced_since !== null ? sprintf(
                            '%s&mdash;%s, в браке %d дней',
                            $marriage->married_since->format('d.m.Y'),
                            $marriage->divorced_since->format('d.m.Y'),
                            $marriage->divorced_since->diffInDays(
                                $marriage->married_since
                            )
                        ) : $marriage->daysSinceMarriage
                    )
                )->join("\n")
            ), [
                'reply_to_message_id' => $this->getMessage()->getMessageId(),
                'parse_mode' => 'HTML'
            ]
        );
    }
}