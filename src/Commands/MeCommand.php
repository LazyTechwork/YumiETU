<?php

namespace Yumi\Commands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Yumi\Models\User;

class MeCommand extends UserCommand
{
    protected $name = 'me';
    protected $usage = '/me';
    protected $description = 'Получить информацию о себе';

    public function execute(): ServerResponse
    {
        $user = User::createFromCommand($this);

        return $this->replyToChat(
            sprintf(
                "Ваше имя: %s\n"
                ."Дата рождения: %s\n"
                ."Идентификатор: %d\n"
                ."Идентификатор Telegram: %d\n"
                ."Идентификатор ВКонтакте: %d",
                $user->telegramMention,
                $user->birthday ? $user->birthday->format('d.m.Y')
                    : 'не установлена',
                $user->id,
                $user->telegram_id,
                $user->vk_id ?? 'не связан'
            ), [
                'reply_to_message_id' => $this->getMessage()->getMessageId(),
                'parse_mode' => 'HTML'
            ]
        );
    }
}