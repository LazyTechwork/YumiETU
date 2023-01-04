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
            $user->informationInMessage(),
            [
                'reply_to_message_id' => $this->getMessage()->getMessageId(),
                'parse_mode' => 'HTML'
            ]
        );
    }
}